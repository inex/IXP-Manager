<?php

namespace IXP\Http\Middleware;

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

use Illuminate\Http\Request;

use IXP\Support\Google2FAAuthenticator;

use Closure, Session;

/**
 * Middleware: Google 2FA
 *
 * Based on: https://github.com/antonioribeiro/google2fa-laravel/blob/master/src/Middleware.php
 *
 * @category   IXP
 * @package    IXP\Http\Middleware
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Google2FA
{
    /**
     * @var array List of routes to exclude from 2fa
     */
    protected $except = [
        '2fa/superuser-verification',
        '2fa/enable',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle( $request, Closure $next )
    {
        if( !$this->inExceptArray( $request ) ) {

            // Force the superuser to enable 2FA
            if( $request->user()->is2FARequired() ) {

                // If we come from the login page then redirect to the 2FA verification form. Otherwise logout:
                if( request()->headers->get('referer', '' ) === route( 'login@login' ) ) {
                    return redirect( route( '2fa@superuser-verification' ) );
                }

                return redirect( route( 'login@logout' ) );
            }

            $authenticator = app(Google2FAAuthenticator::class)->boot( $request );

            if( $authenticator->isAuthenticated() ) {
                return $next( $request );
            }

            return $authenticator->makeRequestOneTimePasswordResponse();
        }

        return $next( $request );
    }

    /**
     * Determine if the request has a URI that should not pass through 2FA
     *
     * @param Request $request
     * @return bool
     */
    protected function inExceptArray( $request ): bool
    {
        foreach( $this->except as $except ) {
            if( $except !== '/' ) {
                $except = trim( $except, '/' );
            }

            if( $request->fullUrlIs( $except ) || $request->is( $except ) ) {
                return true;
            }
        }

        return false;
    }

}