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

use PragmaRX\Google2FALaravel\Support\Authenticator as GoogleAuthenticator;

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
     * @var array List of route names to exclude from 2fa
     */
    protected $excludes = [
        '2fa@configure',
        '2fa@enable',
        'login@logout',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  Request  $r
     * @param  Closure  $next
     * @return mixed
     */
    public function handle( Request $r, Closure $next )
    {
        // if we're not logged in then move on:
        if( !Auth::check() ) {
            return $next( $r );
        }

        if( in_array( $r->route()->getName(), $this->excludes, true ) ) {
            return $next( $r );
        }

        // Force the superuser to enable 2FA
        if( $r->user()->is2faEnforced() ) {
            return redirect( route( '2fa@configure' ) );
        }

        $authenticator = new GoogleAuthenticator( $r );

        if( !Auth::getUser()->user2FA || !Auth::getUser()->user2FA->enabled || $authenticator->isAuthenticated() ) {
            return $next( $r );
        }

        return $authenticator->makeRequestOneTimePasswordResponse();
    }
}