<?php

namespace IXP\Http\Controllers\Auth;

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

use Auth, D2EM;

use Entities\{
    Customer            as CustomerEntity,
    CustomerToUser      as CustomerToUserEntity,
    User                as UserEntity,
    UserLoginHistory    as UserLoginHistoryEntity
};

use IXP\Http\Controllers\Controller;
use IXP\Utils\View\Alert\Alert;
use IXP\Utils\View\Alert\Container as AlertContainer;


/**
 * Switch Customer Controller
 *
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 *
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SwitchCustomerController extends Controller
{

    public function switch( int $id ){

        /** @var $user UserEntity */
        if( !( $cust = D2EM::getRepository( CustomerEntity::class )->find( $id ) ) ){
            abort( "404", "Unknown Customer" );
        }

        $user = Auth::getUser();

        /** @var $c2u CustomerToUserEntity */
        if( !( $c2u = D2EM::getRepository( CustomerToUserEntity::class )->findOneBy( [ "customer" => $cust, "user" => $user ] ) ) ){
            AlertContainer::push( "You are not allowed to access to this " . config( "ixp_fe.lang.customer.one" ) . ".", Alert::DANGER );

            return redirect()->to( "/" );
        }

        // Check if the selected customer is active
        if( !$c2u->getCustomer()->isActive() ){
            AlertContainer::push( "You are not allowed to access to this " . config( "ixp_fe.lang.customer.one" ) . ".", Alert::DANGER );
            return redirect()->to( "/" );
        }

        $c2u->setLastLoginAt(  new \DateTime );
        $c2u->setLastLoginFrom( $this->getIp() );

        if( config( "ixp_fe.login_history.enabled" ) ) {

            $log = new UserLoginHistoryEntity;
            D2EM::persist( $log );



            $log->setAt(    new \DateTime() );
            $log->setIp(    $this->getIp() );
            $log->setCustomerToUser(  $c2u  );

            D2EM::flush();
        }

        $user->setCustomer( $cust );

        D2EM::flush();

        AlertContainer::push( "You are now logged in for {$cust->getName()}.", Alert::SUCCESS );

        return redirect()->to( "/" );
    }

}