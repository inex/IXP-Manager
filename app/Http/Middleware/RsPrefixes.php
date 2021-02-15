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

use Auth, Closure;

use Illuminate\Http\Request;

use IXP\Exceptions\GeneralException;

use IXP\Models\User;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * Middleware: Manage RsPrefixes access
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Middleware
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class RsPrefixes
{
    /**
     * Handle an incoming request.
     *
     * @param   Request     $r
     * @param   Closure     $next
     *
     * @return mixed
     *
     * @throws
     */
    public function handle( Request $r, Closure $next )
    {
        // there are only two routes for rs prefixes - authorise each one as follows:
        if( $r->is( 'rs-prefixes/list' ) ) {
            if( (int)config( 'ixp_fe.rs-prefixes.access' ) === User::AUTH_PUBLIC ) {
                return $next( $r );
            }

            if( Auth::guest() || config( 'ixp_fe.rs-prefixes.access' ) > Auth::getUser()->privs() ) {
                AlertContainer::push(  "You do not have the required privileges to access this function.", Alert::DANGER );
                return redirect( '' );
            }
        } else if( $r->is( 'rs-prefixes/view/*' ) ) {
            if( (int)config( 'ixp_fe.rs-prefixes.access' ) === User::AUTH_PUBLIC ) {
                return $next( $r );
            }

            if( Auth::check() ) {
                if( (int)config( 'ixp_fe.rs-prefixes.access' ) <= Auth::getUser()->privs() ) {
                    return $next( $r );
                }

                if( Auth::getUser()->custid === $r->cust->id ) {
                    return $next( $r );
                }
            }

            AlertContainer::push( "You do not have the required privileges to access this function.", Alert::DANGER );
            return redirect( '' );
        } else {
            throw new GeneralException( 'Unknown route server prefix route in middleware' );
        }

        return $next( $r );
    }
}