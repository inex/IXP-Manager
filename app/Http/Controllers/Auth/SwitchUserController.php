<?php

namespace IXP\Http\Controllers\Auth;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Auth;

use Illuminate\Http\RedirectResponse;

use Illuminate\Foundation\Auth\AuthenticatesUsers;

use IXP\Http\Controllers\Controller;

use IXP\Models\User;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * SwitchUserController
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller/Auth
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SwitchUserController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Allow to switch users
     *
     * @param User $user
     *
     * @return RedirectResponse
     */
    public function switch( User $user ): RedirectResponse
    {
        if( !Auth::getUser()->isSuperUser() ) {
            AlertContainer::push( "You are not allowed to switch users!", Alert::DANGER );
            return redirect()->to( "/" );
        }

        if( !$user->customer || $user->customers()->count() < 1 ) {
            AlertContainer::push( "This user doesnt have customer associated.", Alert::DANGER );
            return redirect()->to( "/" );
        }
        
        session()->put( 'switched_user_from', Auth::id() );
        session()->put( 'redirect_after_switch_back', request()->headers->get('referer', "" ) );

        Auth::login( $user );

        AlertContainer::push( "You are now logged in as {$user->username} ". "(" . Auth::getUser()->name . ")", Alert::SUCCESS );

        return redirect()->to( "/" );
    }

    /**
     * Allow to switch back users
     *
     * @return RedirectResponse
     */
    public function switchBack(): RedirectResponse
    {
        if( !session()->exists( "switched_user_from" ) ) {
            AlertContainer::push( "You are not currently logged in as another user. You are logged in as: " . Auth::getUser()->username . "( " . Auth::getUser()->name . " )", Alert::DANGER );
            return redirect()->to( "/" );
        }

        $redirect = "/";

        if( !( $user = User::find( session()->get( "switched_user_from" ) ) ) ) {
            $this->logout( request() );
            return redirect()->to( "/" );
        }

        Auth::login( $user );

        session()->remove( "switched_user_from" );

        AlertContainer::push( "You are now logged in as {$user->username}.", Alert::SUCCESS );

        if( session()->exists( "redirect_after_switch_back" ) ) {
            $redirect = session()->get( "redirect_after_switch_back" );
            session()->remove( "redirect_after_switch_back" );
        }

        return redirect()->to( $redirect );
    }
}