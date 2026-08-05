<?php

namespace IXP\Http\Controllers\Api\V4\Provisioning;

/*
 * Copyright (C) 2026 KleyReX. All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Auth, Cache, Hash, Log;

use Carbon\Carbon;
use DateTimeInterface;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use IXP\Events\User\UserCreated as UserCreatedEvent;

use IXP\Http\Controllers\Api\V4\Controller;

use IXP\Http\Requests\Api\V4\Provisioning\{
    StoreCustomer,
    StoreUser
};

use IXP\Models\{
    CompanyBillingDetail,
    CompanyRegisteredDetail,
    Customer,
    CustomerToUser,
    User
};

/**
 * Provisioning API - Member Controller
 *
 * Creates members (customers) and their portal users.
 *
 * The two store methods mirror the web controllers rather than calling them: those return
 * redirects and push flash messages, neither of which a machine caller can use. What matters
 * is that the resulting database state is identical, which CustomerContractTest and
 * UserContractTest assert directly by posting the same payload to both entry points and
 * diffing the rows.
 *
 * @see \IXP\Http\Controllers\Customer\CustomerController::store()   the web equivalent
 * @see \IXP\Http\Controllers\User\UserController::store()           the web equivalent
 *
 * @author     KleyReX
 * @category   APIv4
 * @package    IXP\Http\Controllers\Api\V4\Provisioning
 * @copyright  Copyright (C) 2026 KleyReX
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class MemberController extends Controller
{
    /**
     * Create a member.
     *
     * Mirrors CustomerController@store, including the two detail records it creates
     * alongside the customer, and wraps the three inserts in a transaction - the web path
     * does not, so a failure there can leave orphaned detail rows behind.
     *
     * @param  StoreCustomer  $r
     *
     * @return JsonResponse
     */
    public function storeCustomer( StoreCustomer $r ): JsonResponse
    {
        $cust = DB::transaction( function() use ( $r ) {
            $bdetail = CompanyBillingDetail::create( [ 'purchaseOrderRequired' => 0 ] );
            $rdetail = CompanyRegisteredDetail::create( [ 'registeredName' => $r->input( 'name' ) ] );

            return Customer::create( array_merge( $r->validated(), [
                'reseller'                     => $r->boolean( 'isResold' ) ? $r->input( 'reseller' ) : null,
                'company_registered_detail_id' => $rdetail->id,
                'company_billing_details_id'   => $bdetail->id,
                'creator'                      => Auth::id(),
            ] ) );
        } );

        Cache::forget( 'admin_home_customers' );

        Log::notice( 'Provisioning API: created ' . config( 'ixp_fe.lang.customer.one' )
            . " {$cust->name} (id {$cust->id}) as " . Auth::user()->username );

        return response()->json( [ 'member' => $this->memberAsArray( $cust ) ], 201 );
    }

    /**
     * Read a member.
     *
     * @param  Customer  $cust
     *
     * @return JsonResponse
     */
    public function showCustomer( Customer $cust ): JsonResponse
    {
        return response()->json( [ 'member' => $this->memberAsArray( $cust ) ] );
    }

    /**
     * Create a portal user for a member.
     *
     * Mirrors UserController@store. The password is random and never returned: the account is
     * activated by the welcome email, which carries a password reset token. Firing
     * UserCreatedEvent is therefore not optional - without it the user has no way in.
     *
     * @param  StoreUser  $r
     * @param  Customer   $cust
     *
     * @return JsonResponse
     */
    public function storeUser( StoreUser $r, Customer $cust ): JsonResponse
    {
        $user = DB::transaction( function() use ( $r, $cust ) {
            $user = new User;
            $user->creator          = Auth::user()->username;
            $user->password         = Hash::make( Str::random( 16 ) );
            $user->name             = $r->input( 'name' );
            $user->authorisedMobile = $r->input( 'authorisedMobile' );
            $user->username         = strtolower( $r->string( 'username' )->toString() );
            $user->email            = strtolower( $r->string( 'email' )->toString() );

            // the web form posts `disabled` and inverts it; the API takes `enabled` and
            // defaults to an enabled account, which is the only sensible default when the
            // very next thing that happens is a welcome email:
            $user->disabled         = $r->boolean( 'enabled', true ) ? 0 : 1;

            $user->lastupdatedby    = Auth::id();
            $user->custid           = $cust->id;
            $user->save();

            $c2u = new CustomerToUser;
            $c2u->customer_id       = $cust->id;
            $c2u->user_id           = $user->id;
            $c2u->privs             = $r->input( 'privs' );
            $c2u->extra_attributes  = [ 'created_by' => [ 'type' => 'api', 'user_id' => Auth::id() ] ];
            $c2u->save();

            return $user;
        } );

        // only once the transaction has committed, or a later failure would still have sent
        // the welcome email:
        event( new UserCreatedEvent( $user ) );

        Log::notice( "Provisioning API: created user {$user->username} for {$cust->name} as "
            . Auth::user()->username );

        return response()->json( [ 'user' => $this->userAsArray( $user, (int)$r->input( 'privs' ) ) ], 201 );
    }

    /**
     * Representation of a member for API responses.
     *
     * Deliberately an explicit field list rather than the model's toArray(): the response
     * shape is a contract with the caller and should not change because a column was added.
     *
     * @param  Customer  $cust
     *
     * @return array
     */
    private function memberAsArray( Customer $cust ): array
    {
        return [
            'id'                => $cust->id,
            'name'              => $cust->name,
            'shortname'         => $cust->shortname,
            'abbreviatedName'   => $cust->abbreviatedName,
            'type'              => $cust->type,
            'status'            => $cust->status,
            'autsys'            => $cust->autsys,
            'maxprefixes'       => $cust->maxprefixes,
            'maxprefixesv6'     => $cust->maxprefixesv6,
            'peeringemail'      => $cust->peeringemail,
            'peeringmacro'      => $cust->peeringmacro,
            'peeringmacrov6'    => $cust->peeringmacrov6,
            'peeringpolicy'     => $cust->peeringpolicy,
            'irrdb'             => $cust->irrdb,
            'datejoin'          => $this->asDate( $cust->datejoin ),
            'dateleave'         => $this->asDate( $cust->dateleave ),
            'reseller'          => $cust->reseller,
            'isReseller'        => (bool)$cust->isReseller,
        ];
    }

    /**
     * Normalise a date attribute to Y-m-d for API responses.
     *
     * Customer declares datejoin/dateleave in $dates, which Laravel no longer honours - the
     * attributes therefore arrive as raw strings, not Carbon instances. Accept either, so
     * that this keeps working if upstream moves them to $casts.
     *
     * @param  mixed  $value
     *
     * @return string|null
     */
    private function asDate( mixed $value ): ?string
    {
        if( $value === null || $value === '' ) {
            return null;
        }

        return $value instanceof DateTimeInterface
            ? $value->format( 'Y-m-d' )
            : Carbon::parse( $value )->format( 'Y-m-d' );
    }

    /**
     * Representation of a user for API responses.
     *
     * @param  User  $user
     * @param  int   $privs
     *
     * @return array
     */
    private function userAsArray( User $user, int $privs ): array
    {
        return [
            'id'                => $user->id,
            'name'              => $user->name,
            'username'          => $user->username,
            'email'             => $user->email,
            'custid'            => $user->custid,
            'privs'             => $privs,
            'enabled'           => !$user->disabled,
        ];
    }
}
