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

use IXP\Events\User\UserAddedToCustomer as UserAddedToCustomerEvent;

use Entities\{
    Customer        as CustomerEntity,
    CustomerToUser  as CustomerToUserEntity,
    User            as UserEntity
};

use Illuminate\Http\{
    JsonResponse,
    Request
};

use IXP\Http\Controllers\Controller;
use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use IXP\Http\Requests\User\{
    CustomerToUser as StoreCustomerToUser
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
        /** @var CustomerEntity $cust */
        $cust = D2EM::getRepository( CustomerEntity::class )->find( $request->input( 'custid' ) );

        /** @var UserEntity $user */
        $user = D2EM::getRepository( UserEntity::class )->find( $request->input( 'existingUserId' ) );

        // check that this user can effect these changes
        abort_if( !Auth::user()->isSuperUser() && $user->getCustomer()->getId() != $cust->getId(), 403 );
        abort_if( Auth::user()->isCustUser() , 403 );

        /** @var CustomerToUserEntity $c2u */
        $c2u = new CustomerToUserEntity;
        $c2u->setCustomer( $cust );
        $c2u->setUser( $user );
        $c2u->setPrivs( $request->input( 'privs' ) );
        $c2u->setCreatedAt( now() );
        $c2u->setExtraAttributes( [ "created_by" => [ "type" => "user" , "user_id" => $user->getId() ] ] );

        D2EM::persist( $c2u );
        D2EM::flush();

        event( new UserAddedToCustomerEvent( $c2u ) );

        $redirect = session()->get( "user_post_store_redirect" );
        session()->remove( "user_post_store_redirect" );

        Log::notice( Auth::user()->getUsername() . ' added ' . $user->getUsername() . ' via CustomerToUser ID [' . $c2u->getId() . '] to ' . $cust->getName() );

        AlertContainer::push( $user->getName() . '/' . $user->getUsername() . ' has been added to ' . $cust->getName(), Alert::SUCCESS );

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
        /** @var $c2u CustomerToUserEntity */
        if( !( $c2u = D2EM::getRepository(CustomerToUserEntity::class)->find( $request->input( "id" ) ) ) ) {
            return abort( '404' , 'Unknown customer/user association');
        }

        if( in_array( $request->input( "privs" ) , UserEntity::$PRIVILEGES_ALL ) ) {
            return abort( '404', 'Unknown privilege requested' );
        }

        if( $request->input( 'privs' ) == UserEntity::AUTH_SUPERUSER )
        {
            if( !Auth::getUser()->isSuperUser() ) {
                return response()->json( [ 'success' => false, 'message' => "You are not allowed to set the super user privilege" ] );
            }

            if( !$c2u->getCustomer()->isTypeInternal() ) {
                return response()->json( [ 'success' => false, 'message' => "You are not allowed to set super user privileges for non-internal (IXP) customer types" ] );
            }

            //FIXME -> this message is also required in the response
            // AlertContainer::push( 'Please note that you have given this user full administrative access (super user privilege).', Alert::WARNING );
        }

        $c2u->setPrivs( $request->input( "privs" ) );
        D2EM::flush();

        return response()->json( [ 'success' => true, 'message' => "The user's privilege has been updated." ] );
    }

    /**
     * Function to Delete a customer to user link
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws
     */
    public function delete( Request $request )
    {
        // Delete the customer2user link
        /** @var CustomerToUserEntity $c2u  */
        if( !( $c2u = D2EM::getRepository( CustomerToUserEntity::class )->find( $request->input( "id" ) ) ) ) {
            return abort( '404', 'Customer/user association not found' );
        }

        if( !Auth::getUser()->isSuperUser() ) {
            if( $c2u->getCustomer()->getId() != $request->user()->getCustomer()->getId() ) {
                Log::notice( Auth::getUser()->getUsername() . " tried to delete another customer's user: " . $c2u->getUser()->getName() . " from " . $c2u->getCustomer()->getName() );
                abort( 403, 'You are not authorised to delete this user. The administrators have been notified.' );
            }
        }

        // FIXME: rename to $disassociatedUser and $disassociatedCustomer
        $user = $c2u->getUser();
        $c = $c2u->getCustomer();

        $user->removeCustomer( $c2u );

        // FIXME: inefficient -> create repository function with single SQL DELETE
        foreach( $c2u->getUserLoginHistory() as $userLogin ) {
            D2EM::remove( $userLogin );
        }

        D2EM::remove( $c2u );
        D2EM::flush();

        AlertContainer::push( $user->getName()  . '/' . $user->getUsername() . ' has been removed from ' . $c->getName(), Alert::SUCCESS );
        Log::notice( Auth::getUser()->getUsername()." deleted customer2user" . $c->getName() . '/' . $user->getName() );

        // a) If the user's default customer is the customer that we deleted; or
        // b) If the user deleted itself and is logged in as the same customer:
        // then reset default customer and logout
        if( ( $user->getCustomer()->getId() == $c->getId() )
                || ( $request->user()->getId() == $user->getId() && $request->user()->getCustomer()->getId() == $c->getId() ) ) {

            $user->setCustomer( $user->getCustomers() ? $user->getCustomers()[0] : null );
            D2EM::flush();
            Auth::logout();
            return Redirect::to( route( "login@showForm" ) );
        }

        // retrieve the customer ID
        if( strpos( $request->headers->get( 'referer', "" ), "customer/overview" ) !== false ) {
            return Redirect::to( route( "customer@overview" , [ "id" => $c->getId() , "tab" => "users" ] ) );
        }

        return Redirect::to( route( "user@list" ) );
    }
}