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
    User as UserEntity
};

use IXP\Http\Controllers\Controller;
use IXP\Utils\View\Alert\Alert;
use IXP\Utils\View\Alert\Container as AlertContainer;

use Illuminate\Foundation\Auth\AuthenticatesUsers;

/**
 * Switch User Controller
 *
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 *
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SwitchUserController extends Controller
{

    use AuthenticatesUsers;

    public function switch( int $id ){

        if( !Auth::getUser()->isSuperUser() ){
            AlertContainer::push( "You are not allowed to switch users!", Alert::DANGER );
            return redirect()->to( "/" );
        }

        /** @var $user UserEntity */
        if( !( $nuser = D2EM::getRepository( UserEntity::class )->find( $id ) ) ){
            abort( "404", "Unknown User" );
        }
        
        $user = Auth::getUser();

        if( !$nuser->getCustomer() || count( $nuser->getCustomers() ) < 1 ){
            AlertContainer::push( "This user doesnt have customer associated.", Alert::DANGER );
            return redirect()->to( "/" );
        }

        if( $user->getDisabled() ){
            AlertContainer::push( "You cannot login as this user", Alert::DANGER );
            return redirect( '/' );
        }
        
        session()->put( "switched_user_from", $user->getId() );
        session()->put( "redirect_after_switch_back", request()->headers->get('referer', "" ) );

        Auth::login( $nuser );

        AlertContainer::push( "You are now logged in as {$nuser->getUsername()} ". "(" . Auth::getUser()->getName() . ")", Alert::SUCCESS );

        return redirect()->to( "/" );
    }

    public function switchBack(){

        if( !session()->exists( "switched_user_from" ) ){
            AlertContainer::push( "You are not currently logged in as another user. You are logged in as: " . Auth::getUser()->getUsername() . "( " . Auth::getUser()->getName() . " )", Alert::DANGER );
            return redirect()->to( "/" );
        }

        $redirect = "/";

        /** @var $user UserEntity */
        if( !( $nuser = D2EM::getRepository( UserEntity::class )->find( session()->get( "switched_user_from" ) ) ) ){
            $this->logout( request() );
            return redirect()->to( "/" );
        }

        Auth::login( $nuser );

        session()->remove( "switched_user_from" );

        AlertContainer::push( "You are now logged in as {$nuser->getUsername()}.", Alert::SUCCESS );

        if( session()->exists( "redirect_after_switch_back" ) ){
            $redirect = session()->get( "redirect_after_switch_back" );
            session()->remove( "redirect_after_switch_back" );
        }

        return redirect()->to( $redirect );

    }


}