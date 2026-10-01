<?php
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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

declare(strict_types=1);

namespace IXP\Http\Middleware;

use Closure;
use IXP\Services\Auth\SessionGuard;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use IXP\Utils\View\Alert\Alert;
use IXP\Utils\View\Alert\Container as AlertContainer;

/**
 * This middleware is used to verify the integrity of a remember me cookie when the user has a logged in session.
 * The remember me cookie must belong to the user who is logged in, otherwise they will be logged out.
 */
class HandleFailedRememberMeIntegrityCheck
{
    /**
     * @param \Closure(\Illuminate\Http\Request): (Response) $next
     */
    public function handle( Request $request, Closure $next ): Response
    {
        $guard = Auth::guard();

        if ( $guard instanceof SessionGuard ) {
            $guard->check();

            // Not testing check()'s result directly, just whether we've detected remember me integrity failure
            if ( $guard->isRememberMeIntegrityFailed() ) {
                if( $request->expectsJson() || $request->ajax() ) {
                    return response()->json( [ 'error' => 'Session expired' ], 401 );
                }

                AlertContainer::push(
                    'Your login session has expired or was terminated from another device. Please log in again.',
                    Alert::WARNING
                );

                return redirect()->route('login@showForm');
            }
        }

        return $next($request);
    }
}