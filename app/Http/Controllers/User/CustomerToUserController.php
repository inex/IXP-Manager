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

use IXP\Events\User\Welcome as WelcomeEvent;

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
     * @throws
     */
    public function store( StoreCustomerToUser $request )
    {
        /** @var CustomerEntity $cust */
        $cust = D2EM::getRepository( CustomerEntity::class )->find( $request->input( 'custid' ) );

        /** @var UserEntity $existingUser */
        $existingUser = D2EM::getRepository( UserEntity::class )->find( $request->input( 'existingUserId' ) );

        /** @var CustomerToUserEntity $c2u */
        $c2u = new CustomerToUserEntity;
        D2EM::persist( $c2u );

        $c2u->setCustomer(         $cust );
        $c2u->setUser(             $existingUser                   );
        $c2u->setPrivs(            $request->input( 'privs' ) );
        $c2u->setCreatedAt(        new \DateTime                   );
        $c2u->setExtraAttributes(  [ "created_by" => [ "type" => "user" , "user_id" => $existingUser->getId() ] ] );

        D2EM::flush();

        event( new WelcomeEvent( $cust, $existingUser, false, true ) );

        $redirect = session()->get( "user_post_store_redirect" );
        session()->remove( "user_post_store_redirect" );

        Log::notice( Auth::user()->getUsername() . ' added a CustomerToUser with ID ' . $c2u->getId() );


        AlertContainer::push( "The link customer/user ( " . $cust->getName() . "/" . $existingUser->getName() . " ) has been added." , Alert::SUCCESS );

        // retrieve the customer ID
        if( strpos( $redirect, "customer/overview" ) ) {
            return redirect( route( 'customer@overview' , [ 'id' => $c2u->getCustomer()->getId() , 'tab' => 'users' ] ) );
        } else {
            return redirect( route( "user@list" )  );
        }

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
            return abort( '404' , 'Unknown CustomerTo User ');
        }

        if( in_array( $request->input( "privs" ) , UserEntity::$PRIVILEGES_ALL ) ) {
            return abort( '404', 'Unknown privs' );
        }

        if( $request->input( 'privs' ) == UserEntity::AUTH_SUPERUSER )
        {

            if( !Auth::getUser()->isSuperUser() || Auth::getUser()->isSuperUser() && !$c2u->getCustomer()->isTypeInternal() )
            {
                return response()->json( [ 'success' => false, 'message' => "You are not allowed to set this User as a Super User for " . $c2u->getCustomer()->getName() ] );
            }
        }

        $c2u->setPrivs( $request->input( "privs" ) );
        D2EM::flush();

        return response()->json( [ 'success' => true, 'message' => 'The privs has been updated.' ] );
    }







    public function delete( Request $request )
    {

        // Delete the customer2user link
        /** @var CustomerToUserEntity $c2u  */
        if( !( $c2u = D2EM::getRepository( CustomerToUserEntity::class )->find( $request->input( "id" ) ) ) ) {
            return abort( '404', 'Customer2User not found' );
        }

        if( !Auth::getUser()->isSuperUser() ) {
            if( $c2u->getCustomer()->getId() != Auth::getUser()->getCustomer()->getId() ) {
                Log::notice( Auth::getUser()->getUsername() . " tried to delete other customer user " . $c2u->getUser()->getName() . " from " . $c2u->getCustomer()->getName() );
                abort( 401, 'You are not authorised to delete this user. The administrators have been notified.' );
            }
        }



        /** @var UserEntity $user */
        $user   = $c2u->getUser();
        /** @var CustomerEntity $c */
        $c      = $c2u->getCustomer();
        // Store the Customer that we are loggued in
        $logguedCustomer = Auth::getUser()->getCustomer();

        // If the User default customer is the customer that we delete
        if( $user->getCustomer()->getId() == $c->getId() ){
            // setting an available new default customer
            $user->setCustomer( $user->getCustomers()[0] );
        }

        $user->removeCustomer( $c2u );

        foreach( $c2u->getUserLoginHistory() as $userLogin ){
            D2EM::remove( $userLogin );
        }

        D2EM::remove( $c2u );

        D2EM::flush();

        AlertContainer::push( 'The link customer/user ( ' . $c->getName() . '/' . $user->getName() . ' ) has been deleted.', Alert::SUCCESS );

        Log::notice( Auth::getUser()->getUsername()." deleted customer2user" . $c->getName() . '/' . $user->getName() );

        // If the user delete itself and is loggued as the same customer logout
        if( Auth::getUser()->getId() == $user->getId() && $logguedCustomer->getId() == $c->getId() ){
            Auth::logout();
        }

        // If user not logged in redirect to the login form ( this happen when the user delete itself)
        if( !Auth::check() ){
            return Redirect::to( route( "login@showForm" ) );
        }

        // retrieve the customer ID
        if( strpos( request()->headers->get( 'referer', "" ), "customer/overview" ) !== false ) {
            return Redirect::to( route( "customer@overview" , [ "id" => $c->getId() , "tab" => "users" ] ) );
        }

        return Redirect::to( route( "user@list" ) );

    }
}