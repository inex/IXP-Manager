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
use Illuminate\Support\Facades\Log;

/**
 * Middleware: RequireApiKey
 *
 * Insists on an API key and refuses ambient browser credentials.
 *
 * The `api/v4` middleware group starts a session, and ApiAuthenticate only looks for a key
 * when Auth::check() is false. A browser already logged in as a superuser therefore reaches
 * these endpoints on its session cookie - and because the group deliberately omits `web`,
 * without a CSRF token either. For read-only export endpoints that is tolerable; for
 * endpoints which create members it is not, because it makes them reachable by any page that
 * can persuade an administrator's browser to issue a request.
 *
 * Runs before the `api/v4` group so that a stale session is gone before ApiAuthenticate looks
 * at it.
 *
 * @author     KleyReX
 * @category   IXP
 * @package    IXP\Http\Middleware\Provisioning
 * @copyright  Copyright (C) 2026 KleyReX
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class RequireApiKey
{
    /**
     * @param  Request  $r
     * @param  Closure  $next
     *
     * @return mixed
     */
    public function handle( Request $r, Closure $next )
    {
        if( !config( 'ixp_api.provisioning.require_api_key', true ) ) {
            return $next( $r );
        }

        if( !$r->header( 'X-IXP-Manager-API-Key' ) ) {
            Log::warning(
                'Provisioning API: request without an API key from ' . $r->ip() . ' to ' . $r->path()
            );

            return response()->json( [
                'message' => 'These endpoints require an API key in the X-IXP-Manager-API-Key header. '
                    . 'A browser session is not accepted here.',
            ], 401 );
        }

        return $next( $r );
    }
}
