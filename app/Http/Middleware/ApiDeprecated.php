<?php

namespace IXP\Http\Middleware;

/*
 * Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Closure;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


/**
 * Middleware: Log uses of deprecated APIs
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP\Http\Middleware
 * @copyright  Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ApiDeprecated
{
    /**
     * Handle an incoming request.
     *
     * @param   Request     $r
     * @param   Closure     $next
     * @return mixed
     */
    public function handle( Request $r, Closure $next )
    {

        Log::notice( 'UNPREPENDED/DEPRECATED usage of API request ' . $r->path() . ' from ' . ixp_get_client_ip() );

        return $next( $r );
    }
}
