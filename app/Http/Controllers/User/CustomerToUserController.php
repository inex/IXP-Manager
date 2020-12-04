<?php

namespace IXP\Http\Controllers\User;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Auth, D2EM, Log, Redirect;

use Former;
use Illuminate\View\View;
use IXP\Events\User\UserAddedToCustomer as UserAddedToCustomerEvent;

use Entities\{
    Customer            as CustomerEntity,
    CustomerToUser      as CustomerToUserEntity,
    User                as UserEntity,
    UserLoginHistory    as UserLoginHistoryEntity
};


use Illuminate\Http\{
    JsonResponse,
    RedirectResponse,
    Request

};

use IXP\Http\Controllers\Controller;
use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use IXP\Http\Requests\User\{
    CustomerToUser          as StoreCustomerToUser,
    DeleteCustomerToUser    as DeleteCustomerToUser,
};


/**
 * CustomerToUser Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CustomerToUserController extends Controller
{
    /**
     * Allow to display the form to add a customer to user object
     *
     * @param string $email
     *
     * @return  View
     */
    public function add( string $email ): View {
        $old = request()->old();

        // search user via email address
        if( !( $listUsers = D2EM::getRepository( UserEntity::class )->findBy( [ 'email' => $email ] ) ) ) {
            abort(404, 'User not found');
        }

        Former::populate([
            'custid'                => array_key_exists( 'custid',              $old ) ? $old['custid']             : request()->input( "cust" ),
            'linkCancel'            => array_key_exists( 'linkCancel',          $old ) ? $old['linkCancel']         : request()->headers->get( 'referer', "" ),
        ]);

        return view( 'customer2user/add' )->with([
            'listUsers'             => $listUsers,
            'custs'                 => D2EM::getRepository( CustomerEntity::class )->getAsArray(),
            'privs'                 => $this->getAllowedPrivs(),
            'c'                     => D2EM::getRepository( CustomerEntity::class)->find( request()->input( "cust", false ) ) ?? false,
        ]);
    }

    /**
     * Function to store A customerToUser object
     *
     * @param StoreCustomerToUser $request
     *
     * @return redirect
     *
     * @throws
     */
    public function store( StoreCustomerToUser $request )
    {
        /** @var CustomerToUserEntity $c2u */
        $c2u = new CustomerToUserEntity;
        $c2u->setCustomer( $request->cust );
        $c2u->setUser( $request->existingUser );
        $c2u->setPrivs( $request->input( 'privs' ) );
        $c2u->setCreatedAt( now() );
        $c2u->setExtraAttributes( [ "created_by" => [ "type" => "user" , "user_id" => $request->existingUser ] ] );

        D2EM::persist( $c2u );
        D2EM::flush();

        event( new UserAddedToCustomerEvent( $c2u ) );

        $redirect = session()->get( "user_post_store_redirect" );
        session()->remove( "user_post_store_redirect" );

        Log::notice( Auth::getUser()->username . ' added ' . $request->existingUser->getUsername() . ' via CustomerToUser ID [' . $c2u->getId() . '] to ' . $request->cust->getName() );

        AlertContainer::push( $request->existingUser->getName() . '/' . $request->existingUser->getUsername() . ' has been added to ' . $request->cust->getName(), Alert::SUCCESS );

        // retrieve the customer ID
        if( strpos( $redirect, "customer/overview" ) ) {
            return redirect( route( 'customer@overview' , [ 'id' => $c2u->getCustomer()->getId() , 'tab' => 'users' ] ) );
        }

        return redirect( route( "user@list" )  );
    }


    /**
     * Function to Update privs for a CustomerToUser
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws
     */
    public function updatePrivs( Request $request ): JsonResponse
    {
        $extraMessage = null;
        /** @var $c2u CustomerToUserEntity */
        if( !( $c2u = D2EM::getRepository(CustomerToUserEntity::class)->find( $request->input( "id" ) ) ) ) {
            return abort( '404' , 'Unknown customer/user association');
        }

        if( in_array( $request->input( "privs" ) , UserEntity::$PRIVILEGES_ALL ) ) {
            return abort( '404', 'Unknown privilege requested' );
        }

        if( $request->input( 'privs' ) == UserEntity::AUTH_SUPERUSER ) {
            if( !Auth::getUser()->isSuperUser() ) {
                return response()->json( [ 'success' => false, 'message' => "You are not allowed to set the super user privilege" ] );
            }

            if( !$c2u->getCustomer()->isTypeInternal() ) {
                return response()->json( [ 'success' => false, 'message' => "You are not allowed to set super user privileges for non-internal (IXP) customer types" ] );
            }

            $extraMessage = "Please note that you have given this user full administrative access (super user privilege).";
        }

        $c2u->setPrivs( $request->input( "privs" ) );
        D2EM::flush();

        return response()->json( [ 'success' => true, 'message' => "The user's privilege has been updated.", "extraMessage" => $extraMessage ] );
    }

    /**
     * Function to Delete a customer to user link
     *
     * @param DeleteCustomerToUser $request
     *
     * @return RedirectResponse
     *
     * @throws
     */
    public function delete( DeleteCustomerToUser $request )
    {
        $disassociatedUser     = $request->c2u->getUser();
        $disassociatedCustomer = $request->c2u->getCustomer();

        // Store the initial Customer before the deletion
        $initialCustomer = Auth::getUser()->customer;

        $disassociatedUser->removeCustomer( $request->c2u );

        // Delete User login history
        D2EM::getRepository( UserLoginHistoryEntity::class )->deleteUserLoginHistory( $request->c2u->getId() );

        D2EM::remove( $request->c2u );

        // then reset default customer
        if( $disassociatedUser->getCustomer()->getId() == $disassociatedCustomer->getId() ){
            $disassociatedUser->setCustomer( $disassociatedUser->getCustomers() ? $disassociatedUser->getCustomers()[0] : null );
        }

        D2EM::flush();

        AlertContainer::push( $disassociatedUser->getName()  . '/' . $disassociatedUser->getUsername() . ' has been removed from ' . $disassociatedCustomer->getName(), Alert::SUCCESS );
        Log::notice( Auth::getUser()->username." deleted customer2user" . $disassociatedCustomer->getName() . '/' . $disassociatedUser->getName() );

        // If the user deleted itself and is logged in as the same customer:
        if( $request->user()->getId() == $disassociatedUser->getId() && $initialCustomer->getId() == $disassociatedCustomer->getId() ) {
            Auth::logout();
            return Redirect::to( route( "login@showForm" ) );
        }

        // retrieve the customer ID
        if( strpos( $request->headers->get( 'referer', "" ), "customer/overview" ) !== false ) {
            return Redirect::to( route( "customer@overview" , [ "id" => $disassociatedCustomer->getId() , "tab" => "users" ] ) );
        }

        return Redirect::to( route( "user@list" ) );
    }
}