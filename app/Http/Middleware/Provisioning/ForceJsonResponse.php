<?php

namespace IXP\Http\Middleware\Provisioning;

/*
 * Copyright (C) 2026 KleyReX. All Rights Reserved.
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

/**
 * Middleware: ForceJsonResponse
 *
 * The provisioning API is consumed by machines only. Without an `Accept: application/json`
 * request header, Laravel renders framework-generated errors as HTML - and, worse, turns a
 * ValidationException into a 302 redirect rather than a 422 JSON body. That is invisible to
 * a client which only inspects the status code.
 *
 * Rather than requiring every caller to remember the header, we set it here, before the
 * `api/v4` middleware group runs.
 *
 * @author     KleyReX
 * @category   IXP
 * @package    IXP\Http\Middleware\Provisioning
 * @copyright  Copyright (C) 2026 KleyReX
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ForceJsonResponse
{
    /**
     * Force JSON content negotiation for provisioning API requests.
     *
     * @param  Request  $r
     * @param  Closure  $next
     *
     * @return mixed
     */
    public function handle( Request $r, Closure $next )
    {
        $r->headers->set( 'Accept', 'application/json' );

        return $next( $r );
    }
}
