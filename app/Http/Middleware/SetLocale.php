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

use App, Auth, Closure;

use Illuminate\Http\Request;

use IXP\Models\User;

/**
 * Middleware: set the application locale for the current request
 *
 * The locale is resolved, in order of precedence, from:
 *
 *   1. the logged in user's own preference  - user.prefs['locale']
 *   2. their current customer's default     - cust.prefs['locale']
 *   3. the instance default                 - config('app.locale') / APP_LOCALE
 *
 * Anything not listed in config('ixp_fe.locales') is ignored so that a stale
 * or hand-edited preference cannot break the UI - we just fall back to the
 * instance default.
 *
 * Note this is registered in the 'web' middleware group and so runs after
 * StartSession but before the 'auth' route middleware. Auth::user() resolves
 * lazily from the session at that point, which is exactly what we want: it
 * works for both authenticated and guest requests.
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Middleware
 * @copyright  Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SetLocale
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
        if( ( $locale = $this->resolve() ) !== null ) {
            App::setLocale( $locale );
        }

        return $next( $r );
    }

    /**
     * Resolve the locale to use for this request.
     *
     * Returns null when we have nothing better than the default that the
     * framework has already set from config('app.locale').
     *
     * @return string|null
     */
    private function resolve(): ?string
    {
        /** @var User|null $user */
        $user = Auth::user();

        if( !$user ) {
            return null;
        }

        if( $this->isAvailable( $locale = $user->locale() ) ) {
            return $locale;
        }

        // no personal preference (or an unavailable one) - fall back to the
        // default of the customer they are currently logged in as:
        if( $this->isAvailable( $locale = $user->customer?->locale() ) ) {
            return $locale;
        }

        return null;
    }

    /**
     * Is the given locale one this instance offers?
     *
     * @param   string|null $locale
     *
     * @return bool
     */
    private function isAvailable( ?string $locale ): bool
    {
        return $locale !== null && array_key_exists( $locale, config( 'ixp_fe.locales', [] ) );
    }
}
