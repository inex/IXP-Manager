<?php

namespace IXP\Http\Controllers\Auth;

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

use Auth, Log;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;

use IXP\Http\Controllers\Controller;

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
 * SwitchUserController
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\Auth
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SwitchUserController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Allow to switch users
     *
     * @param CustomerToUser $c2u
     *
     * @return RedirectResponse
     */
    public function switch( CustomerToUser $c2u ): RedirectResponse
    {
        if( !Auth::getUser()->isSuperUser() ) {
            AlertContainer::push( "You are not allowed to switch users!", Alert::DANGER );
            return redirect()->to( "/" );
        }

        $user = $c2u->user;

        if( $user->disabled ){
            AlertContainer::push( "You cannot login as this user", Alert::DANGER );
            return redirect( '/' );
        }

        session()->put( 'switched_user_from', Auth::id() );
        session()->put( 'switched_c2u_to', $c2u->id );
        session()->put( 'switched_customer_from', $user->custid );
        session()->put( 'redirect_after_switch_back', request()->headers->get('referer', "" ) );

        // Temporary change the default customer for the user
        $user->custid = $c2u->customer_id;
        $user->save();

        Auth::login( $user );

        Log::notice( Auth::getUser()->username . '(' . Auth::getUser()->name . ') logged as the user ' . $user->username . '(' . $user->name . ')' . ' for the customer ' . $user->customer->name  );
        AlertContainer::push( "You are now logged in as {$user->username} " . " (" . Auth::getUser()->name . ") for the " . config( 'ixp_fe.lang.customer.one' ) . ' ' . $user->customer->name, Alert::SUCCESS );
        return redirect( '/' );
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

        // Get the previous default customer for the user
        if( $c2u = CustomerToUser::find( session()->get( "switched_c2u_to" ) ) ) {
            $switchedTo = $c2u->user;

            if( !( $cust = Customer::find( session()->get( "switched_customer_from" ) ) ) ) {
                $cust = $c2u->customer;
            }
            $switchedTo->custid = $cust->id;
            $switchedTo->save();
        }

        Auth::login( $user );

        session()->remove( "switched_user_from" );
        session()->remove( "switched_c2u_to" );
        session()->remove( "switched_customer_from" );

        AlertContainer::push( "You are now logged in as {$user->username} " . "(" . Auth::getUser()->name . ") for the " . config( 'ixp_fe.lang.customer.one' ) . ' ' . $user->customer->name, Alert::SUCCESS );

        if( session()->exists( "redirect_after_switch_back" ) ) {
            $redirect = session()->get( "redirect_after_switch_back" );
            session()->remove( "redirect_after_switch_back" );
        }

        return redirect( $redirect );
    }
}