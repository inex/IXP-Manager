<?php

namespace IXP\Http\Controllers\User;

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
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

use Auth, Former, Log;

use Exception;
use Illuminate\Http\{
    JsonResponse,
    RedirectResponse,
    Request
};

use Illuminate\View\View;

use IXP\Events\User\UserAddedToCustomer as UserAddedToCustomerEvent;

use IXP\Http\Controllers\Controller;

use IXP\Http\Requests\User\CustomerToUser\{
    Store   as StoreCustomerToUser,
    Delete  as DeleteCustomerToUser
};

use IXP\Models\{
    Customer,
    CustomerToUser,
    User
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * CustomerToUser Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\User
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CustomerToUserController extends Controller
{
    /**
     * Allow to display the form to add a customer to user object
     *
     * @param string    $email
     * @param Request   $r
     *
     * @return  View
     */
    public function create( Request $r, string $email ): View
    {
        if( Auth::getUser()->isCustUser() ){
            abort( 403, 'Action no allowed' );
        }
        // search user via email address
        if( !( $listUsers = User::where( 'email', strtolower( trim( $email ) ) )->get() ) ) {
            abort(404, 'User not found');
        }

        Former::populate([
            'customer_id'           => $r->old( 'custid',       $r->cust ),
            'linkCancel'            => $r->old( 'linkCancel',   $r->headers->get( 'referer', "" ) ),
        ]);

        return view( 'customer2user/add' )->with([
            'listUsers'             => $listUsers,
            'custs'                 => Customer::orderBy( 'name' )->get(),
            'privs'                 => $this->getAllowedPrivs(),
            'c'                     => Customer::find( $r->cust ) ?: false,
        ]);
    }

    /**
     * Function to store A customerToUser object
     *
     * @param StoreCustomerToUser $r
     *
     * @return RedirectResponse
     */
    public function store( StoreCustomerToUser $r ): RedirectResponse
    {
        $c2u = new CustomerToUser;
        $c2u->customer_id       = $r->cust->id;
        $c2u->user_id           = $r->user_id;
        $c2u->privs             = $r->privs;
        $c2u->extra_attributes  = [ "created_by" => [ "type" => "user" , "user_id" => $r->user_id ] ];
        $c2u->save();

        event( new UserAddedToCustomerEvent( $c2u ) );

        $redirect = session()->get( "user_post_store_redirect" );
        session()->remove( "user_post_store_redirect" );

        $user = $c2u->user;

        Log::notice( Auth::getUser()->username . ' created ' . $user->username . ' via CustomerToUser ID [' . $c2u->id . '] to ' . $c2u->customer->name );
        AlertContainer::push( $user->name . '/' . $user->username . ' has been created to ' . $c2u->customer->name, Alert::SUCCESS );

        // retrieve the customer ID
        if( strpos( $redirect, "customer/overview" ) ) {
            return redirect( route( 'customer@overview' , [ 'cust' => $c2u->customer_id , 'tab' => 'users' ] ) );
        }

        return redirect( route( "user@list" )  );
    }

    /**
     * Function to Update privs for a CustomerToUser
     *
     * @param Request $r
     *
     * @return JsonResponse
     */
    public function updatePrivs( Request $r ): JsonResponse
    {
        /** @var CustomerToUser $c2u */
        $c2u = CustomerToUser::findOrFail( $r->id );

        if( in_array( (int)$r->privs , User::$PRIVILEGES_ALL, true ) ) {
            return response()->json( [ 'success' => false, 'message' => "Unknown privilege requested" ] );
        }

        if( (int)$r->privs === User::AUTH_SUPERUSER ) {
            if( !Auth::user()->isSuperUser() ) {
                return response()->json( [ 'success' => false, 'message' => "You are not allowed to set the super user privilege" ] );
            }

            if( !$c2u->customer->typeInternal() ) {
                return response()->json( [ 'success' => false, 'message' => "You are not allowed to set super user privileges for non-internal (IXP) customer types" ] );
            }

            $extraMessage = "Please note that you have given this user full administrative access (super user privilege).";
        }

        $c2u->privs =  $r->privs;
        $c2u->save();

        return response()->json( [ 'success' => true, 'message' => "The user's privilege has been updated.", "extraMessage" => $extraMessage ?? null ] );
    }

    /**
     * Function to Delete a customer to user link
     *
     * @param  DeleteCustomerToUser  $r
     * @param  CustomerToUser  $c2u
     *
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function delete( DeleteCustomerToUser $r, CustomerToUser $c2u ): RedirectResponse
    {
        $disassociatedUser  = $c2u->user;
        $disassociatedCust  = $c2u->customer;

        // Store the initial Customer before the deletion
        $initialCust = Auth::user()->customer;

        // Delete User login history
        $c2u->userLoginHistories()->delete();
        $c2u->delete();

        // then reset default customer
        if( $disassociatedUser->custid === $disassociatedCust->id ){
            $disassociatedUser->custid = $disassociatedUser->customers() ? $disassociatedUser->customers()->first()->id : null;
            $disassociatedUser->save();
        }

        AlertContainer::push( $disassociatedUser->name  . '/' . $disassociatedUser->username . ' deleted from ' . $disassociatedCust->name, Alert::SUCCESS );
        Log::notice( Auth::getUser()->username." deleted customer2user" . $disassociatedCust->name . '/' . $disassociatedUser->name );

        // If the user deleted itself and is logged in as the same customer:
        if( $r->user()->id === $disassociatedUser->id && $initialCust->id === $disassociatedCust->id ) {
            Auth::logout();
            return redirect( route( "login@showForm" ) );
        }

        // retrieve the customer ID
        if( strpos( $r->headers->get( 'referer', "" ), "customer/overview" ) !== false ) {
            return redirect( route( "customer@overview" , [ 'cust' => $disassociatedCust->id , "tab" => "users" ] ) );
        }

        return redirect( route( "user@list" ) );
    }
}