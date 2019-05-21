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

use Auth, Cache, D2EM, Former, Hash, Log, Redirect, Route, Validator;

use IXP\Events\User\Welcome as WelcomeEvent;

use Entities\{
    Customer as CustomerEntity,
    CustomerToUser as CustomerToUserEntity,
    User as UserEntity
};

use Illuminate\Http\{
    Request,
    RedirectResponse
};

use IXP\Http\Controllers\Controller;
use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use Illuminate\View\View;


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

    public function store( Request $request )
    {
        $this->feParams->nameSingular = "Customer2User";

        /** @var CustomerEntity $cust */
        $cust = D2EM::getRepository( CustomerEntity::class )->find( $this->input( 'custid' ) );

        /** @var UserEntity $existingUser */
        $existingUser = D2EM::getRepository( UserEntity::class )->find( $request->input( 'existingUserId' ) );

        $this->object = new CustomerToUserEntity;
        D2EM::persist( $this->object );

        $this->object->setCustomer(         $cust );
        $this->object->setUser(             $existingUser                   );
        $this->object->setPrivs(            $request->input( 'privs' ) );
        $this->object->setCreatedAt(        new \DateTime                   );
        $this->object->setExtraAttributes(  [ "created_by" => [ "type" => "user" , "user_id" => $existingUser->getId() ] ] );

        D2EM::flush();

        event( new WelcomeEvent( $cust, $existingUser, false, true ) );

        $redirect = session()->get( "user_post_store_redirect" );
        session()->remove( "user_post_store_redirect" );

        // retrieve the customer ID
        if( strpos( $redirect, "customer/overview" ) ) {
            return route( 'customer@overview' , [ 'id' => $this->object->getCustomer()->getId() , 'tab' => 'users' ] );
        } else {
            return redirect( route( "user@list" )  );
        }

    }







    public function delete(){
        // Store the Customer that we are loggued in
        $logguedCustomer = Auth::getUser()->getCustomer();

        // Delete the customer2user link
        /** @var CustomerToUserEntity $c2u  */
        if( !( $c2u = D2EM::getRepository( CustomerToUserEntity::class )->findOneBy( [ "user" => $this->object , "customer" => $c ] ) ) ) {
            return abort( '404', 'Customer2User not found' );
        }

        if( $this->object->getCustomer()->getId() == $c->getId() ){
            // setting an available new default customer
            $this->object->setCustomer( $this->object->getCustomers()[0] );
        }

        $this->object->removeCustomer( $c2u );

        foreach( $c2u->getUserLoginHistory() as $userLogin ){
            D2EM::remove( $userLogin );
        }

        D2EM::remove( $c2u );

        D2EM::flush();

        AlertContainer::push( 'The link customer/user ( ' . $c->getName() . '/' . $this->object->getName() . ' ) has been deleted.', Alert::SUCCESS );

        Log::notice( Auth::getUser()->getUsername()." deleted customer2user" . $c->getName() . '/' . $this->object->getName() );


        // If the user delete itself and is loggued as the same customer logout
        if( Auth::getUser()->getId() == $this->object->getId() && $logguedCustomer->getId() == $c->getId() ){
            Auth::logout();
        }

    }
}