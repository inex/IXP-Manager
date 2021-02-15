<?php

namespace IXP\Http\Middleware;

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

use Auth, Closure, Log, Route;

use Illuminate\Http\Request;

use IXP\Models\User;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * Middleware: Manage Eloquent2Frontend filters, etc
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Middleware
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Eloquent2Frontend
{
    /**
     * Handle an incoming request.
     *
     * @param   Request     $r
     * @param   Closure     $next
     *
     * @return mixed
     */
    public function handle( Request $r, Closure $next )
    {
        // get the class and method that has been called:
        [ $controller, $method ] = explode('@', Route::currentRouteAction() );

        // what's the user's privilege?
        $user_priv = Auth::check() ? Auth::getUser()->privs() : User::AUTH_PUBLIC;

        // first check - do we have the necessary privileges to access this?
        if( $user_priv < $controller::$minimum_privilege ) {
            AlertContainer::push(  "You do not have the required privileges to access this function.", Alert::DANGER );
            Log::info( ( Auth::check() ? Auth::getUser()->username : 'Anonymous user' ) . " tried to access {$controller}@{$method} but does not have the required privileges" );
            return redirect( '' );
        }

        return $next( $r );
    }
}