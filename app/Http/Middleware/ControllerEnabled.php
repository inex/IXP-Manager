<?php

namespace IXP\Http\Middleware;

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


use Auth, Closure, Log, Route;

use Entities\{
    User as UserEntity
};

use IXP\Utils\View\Alert\Alert;
use IXP\Utils\View\Alert\Container as AlertContainer;

/**
 * Middleware: Ensure the controller has not been disabled
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\Doctrine2Frontend
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */

class ControllerEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle( $request, Closure $next )
    {
        // not everything belongs here:
        if( !strpos( Route::currentRouteAction(), '@' ) ) {
            return $next($request);
        }

        // get the class and method that has been called:
        list( $controller, $method ) = explode('@', Route::currentRouteAction() );

        // reformat controller name to exclude 'IXP\Http\Controllers' and then replace remaining '\' with -
        if( substr( $controller, 0, 20 ) == 'IXP\\Http\\Controllers' ) {
            $controller = substr( $controller, 21 );
        } else if( substr( $controller, 0, 21 ) == '\\IXP\\Http\\Controllers' ) {
            $controller = substr( $controller, 22 );
        }

        if( substr( $controller, -10 ) == 'Controller' ) {
            $controller = substr( $controller, 0, -10 );
        }

        $bits = explode( '\\', $controller );

        $name = '';
        foreach( $bits as $b ) {
            if( $b === '' ) {
                continue;
            }

            $name .= kebab_case( strtolower( $b ) ) . '-';
        }
        $name = substr( $name, 0, -1 );

        // is the controller enabled?
        if( config( 'ixp_fe.frontend.disabled.' . $name, false ) ) {
            AlertContainer::push(  "This controller has been disabled (see: config/ixp_fe.php).", Alert::DANGER );
            return redirect( '' );
        }

        return $next($request);
    }
}
