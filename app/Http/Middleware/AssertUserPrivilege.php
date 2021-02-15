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

/**
 * Middleware: Assert an authenticated user is of a given privilege
 *
 * Check for IXP Manager token credentials with API access requests
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Middleware
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class AssertUserPrivilege
{
    /**
     * Handle an incoming request.
     *
     * @param Request   $r
     * @param Closure   $next
     * @param int       $privilege
     *
     * @return mixed
     */
    public function handle( Request $r, Closure $next, int $privilege )
    {
        if( Auth::getUser()->privs() !== $privilege ) {
            return response( 'Insufficient permissions', 403 );
        }

        return $next( $r );
    }
}