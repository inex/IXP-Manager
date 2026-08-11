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

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Validation\ValidationException;

use IXP\Events\User\UserCreated as UserCreatedEvent;

use IXP\Http\Controllers\Interfaces\Common;

use IXP\Http\Requests\Api\V4\Provisioning\StoreOnboarding;

use IXP\Models\{
    CompanyBillingDetail,
    CompanyRegisteredDetail,
    Customer,
    CustomerToUser,
    PhysicalInterface,
    ProvisioningReference,
    SwitchPort,
    User,
    VirtualInterface,
    Vlan,
    VlanInterface
};

use IXP\Support\Provisioning\IpAllocationException;
use IXP\Support\Provisioning\IpAllocator;

/**
 * Provisioning API - Onboarding Controller
 *
 * Creates a member, an optional portal user and an optional connection in one transaction.
 *
 * The individual endpoints exist and can be called in sequence; this one exists because that
 * sequence is not atomic. Customer creation and user creation are separate writes, so a caller
 * whose second request fails is left with a half-provisioned member and no clean way back. Here
 * either the whole order lands or none of it does.
 *
 * It does not orchestrate anything beyond the database. Switch configuration, route server
 * config and reverse DNS are the caller's business - this endpoint only makes the IXP Manager
 * side of an order indivisible.
 *
 * Idempotency: a caller which passes `reference` and then retries after a timeout gets the
 * original result back rather than a duplicate member.
 *
 * @author     KleyReX
 * @category   APIv4
 * @package    IXP\Http\Controllers\Api\V4\Provisioning
 * @copyright  Copyright (C) 2026 KleyReX
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class OnboardingController extends Common
{
    /**
     * Provision a member, and optionally a user and a connection.
     *
     * @param  StoreOnboarding  $r
     *
     * @return JsonResponse
     */
    public function store( StoreOnboarding $r ): JsonResponse
    {
        $reference = $r->input( 'reference' );

        if( $reference && ( $existing = ProvisioningReference::customerFor( $reference ) ) ) {
            return response()->json( [
                'created'   => false,
                'reference' => $reference,
                'member'    => [ 'id' => $existing->id, 'shortname' => $existing->shortname ],
            ] );
        }

        try {
            $result = DB::transaction( fn() => $this->provision( $r, $reference ) , attempts: 3 );
        } catch( IpAllocationException $e ) {
            return response()->json( [ 'message' => $e->getMessage() ], 422 );
        } catch( ValidationException $e ) {
            return response()->json( [ 'message' => $e->getMessage(), 'errors' => $e->errors() ], 422 );
        } catch( UniqueConstraintViolationException $e ) {
            // Which unique index was hit matters. The reference index means a retry of the
            // same order raced itself; anything else - most likely switchportid, which is
            // unique on physicalinterface - means a different order took the resource first.
            //
            // Telling the second case "use a new reference" would be actively harmful: the
            // caller would burn an order number and retry onto the same occupied port.
            if( $reference && ( $existing = ProvisioningReference::customerFor( $reference ) ) ) {
                return response()->json( [
                    'created'   => false,
                    'reference' => $reference,
                    'member'    => [ 'id' => $existing->id, 'shortname' => $existing->shortname ],
                ] );
            }

            if( $reference && str_contains( $e->getMessage(), 'provisioning_references' ) ) {
                // The reference is taken but its customer is gone - deleted after the fact.
                return response()->json( [
                    'message' => 'This reference has been used before and the member it created no longer '
                        . 'exists. Use a new reference.',
                ], 409 );
            }

            return response()->json( [
                'message' => 'Another request took one of the resources for this order while it was being '
                    . 'processed - most likely the switch port. Nothing was created. Choose a different '
                    . 'port and retry with the same reference.',
            ], 409 );
        }

        Cache::forget( 'admin_home_customers' );

        // Events after the commit, never inside it: an onboarding which is rolled back must
        // not have sent the member a welcome email telling them to set a password.
        if( $result[ 'user' ] instanceof User ) {
            event( new UserCreatedEvent( $result[ 'user' ] ) );
        }

        Log::notice( "Provisioning API: onboarded {$result['customer']->name} (id {$result['customer']->id})"
            . ( $reference ? " for reference {$reference}" : '' ) . ' as ' . Auth::user()->username );

        return response()->json( [
            'created'    => true,
            'reference'  => $reference,
            'member'     => [ 'id' => $result[ 'customer' ]->id, 'shortname' => $result[ 'customer' ]->shortname ],
            'user'       => $result[ 'user' ] ? [ 'id' => $result[ 'user' ]->id, 'username' => $result[ 'user' ]->username ] : null,
            'connection' => $result[ 'vi' ] ? [ 'id' => $result[ 'vi' ]->id ] : null,
        ], 201 );
    }

    /**
     * Do the work. Runs inside the caller's transaction.
     *
     * @param  StoreOnboarding  $r
     * @param  string|null      $reference
     *
     * @return array{customer: Customer, user: User|null, vi: VirtualInterface|null}
     *
     * @throws IpAllocationException
     */
    private function provision( StoreOnboarding $r, ?string $reference ): array
    {
        // Only ever the validated payload: $r->all() would carry through anything the caller
        // chose to send, and Customer, VirtualInterface and PhysicalInterface all have fillable
        // columns with no rule attached - lastupdatedby, channelgroup, notes among them.
        $validated = $r->validated();

        $cust = $this->createCustomer( $validated[ 'member' ] );
        $user = isset( $validated[ 'user' ] ) ? $this->createUser( $validated[ 'user' ], $cust ) : null;
        $vi   = isset( $validated[ 'connection' ] ) ? $this->createConnection( $validated[ 'connection' ], $cust ) : null;

        if( $reference ) {
            ProvisioningReference::create( [
                'reference'  => $reference,
                'model_type' => Customer::class,
                'model_id'   => $cust->id,
                'created_by' => Auth::id(),
            ] );
        }

        return [ 'customer' => $cust, 'user' => $user, 'vi' => $vi ];
    }

    /**
     * @param  array  $member  the validated member section
     *
     * @return Customer
     */
    private function createCustomer( array $member ): Customer
    {
        $bdetail = CompanyBillingDetail::create( [ 'purchaseOrderRequired' => 0 ] );
        $rdetail = CompanyRegisteredDetail::create( [ 'registeredName' => $member[ 'name' ] ] );

        return Customer::create( array_merge( $member, [
            'reseller'                     => !empty( $member[ 'isResold' ] ) ? ( $member[ 'reseller' ] ?? null ) : null,
            'company_registered_detail_id' => $rdetail->id,
            'company_billing_details_id'   => $bdetail->id,
            'creator'                      => Auth::id(),
        ] ) );
    }

    /**
     * @param  array     $input  the validated user section
     * @param  Customer  $cust
     *
     * @return User
     */
    private function createUser( array $input, Customer $cust ): User
    {
        $user = new User;
        $user->creator          = Auth::user()->username;
        $user->password         = Hash::make( Str::random( 16 ) );
        $user->name             = $input[ 'name' ];
        $user->authorisedMobile = $input[ 'authorisedMobile' ] ?? null;
        $user->username         = strtolower( (string)$input[ 'username' ] );
        $user->email            = strtolower( (string)$input[ 'email' ] );
        $user->disabled         = ( $input[ 'enabled' ] ?? true ) ? 0 : 1;
        $user->lastupdatedby    = Auth::id();
        $user->custid           = $cust->id;
        $user->save();

        $c2u = new CustomerToUser;
        $c2u->customer_id       = $cust->id;
        $c2u->user_id           = $user->id;
        $c2u->privs             = $input[ 'privs' ];
        $c2u->extra_attributes  = [ 'created_by' => [ 'type' => 'api', 'user_id' => Auth::id() ] ];
        $c2u->save();

        return $user;
    }

    /**
     * @param  array     $input  the validated connection section
     * @param  Customer  $cust
     *
     * @return VirtualInterface
     *
     * @throws IpAllocationException
     */
    private function createConnection( array $input, Customer $cust ): VirtualInterface
    {
        $vlan = Vlan::findOrFail( $input[ 'vlanid' ] );

        // Same gate as ConnectionController, so both entry points accept exactly the same
        // ports - including the check that the port belongs to the switch named:
        if( $error = ConnectionController::switchPortRejection( $input[ 'switch' ] ?? null, $input[ 'switchportid' ] ?? null ) ) {
            throw new IpAllocationException( $error );
        }

        $sp = SwitchPort::findOrFail( $input[ 'switchportid' ] );

        $vi = VirtualInterface::create( array_merge( $input, [ 'custid' => $cust->id ] ) );

        PhysicalInterface::create( array_merge( $input, [
            'virtualinterfaceid' => $vi->id,
            'status'             => $input[ 'status' ] ?? PhysicalInterface::STATUS_CONNECTED,
            'duplex'             => $input[ 'duplex' ] ?? 'full',
        ] ) );

        $sp->update( [ 'type' => SwitchPort::TYPE_PEERING ] );

        $allocator = new IpAllocator();
        $resolved  = $input;

        foreach( [ false, true ] as $ipv6 ) {
            $field = ( $ipv6 ? 'ipv6' : 'ipv4' ) . 'address';

            $ip = $allocator->claim( $vlan, $input[ $field ] ?? null, $ipv6 );

            $resolved[ $field ] = $ip?->address;
        }

        $vli = new VlanInterface( array_merge( $input, [
            'virtualinterfaceid' => $vi->id,
            'busyhost'           => (bool)( $input[ 'busyhost' ] ?? false ),
        ] ) );

        $ipRequest = Request::create( '/', 'POST', $resolved );

        if( !$this->setIp( $ipRequest, $vlan, $vli, false ) || !$this->setIp( $ipRequest, $vlan, $vli, true ) ) {
            throw new IpAllocationException(
                'An address became unavailable while the connection was being created. Please retry.'
            );
        }

        $vli->save();

        $vi->refresh();
        $this->setBundleDetails( $vi );
        $vi->save();

        return $vi;
    }
}
