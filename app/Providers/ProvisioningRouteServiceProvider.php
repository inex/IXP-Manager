<?php

namespace IXP\Providers;

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

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

use IXP\Http\Middleware\Provisioning\ForceJsonResponse;
use IXP\Http\Middleware\Provisioning\RequireApiKey;
use IXP\Http\Middleware\Provisioning\RestrictSourceAddress;
use IXP\Models\User;

/**
 * ProvisioningRouteServiceProvider
 *
 * Registers the provisioning API routes - stateless, machine-to-machine endpoints for
 * creating members, users and connections from an external system such as an ordering or
 * billing platform.
 *
 * These deliberately mirror RouteServiceProvider::mapApiExternalAuthSuperuserRoutes()
 * rather than mapApiAuthSuperuserRoutes():
 *
 *   - No `web` middleware, so no CSRF token is required. That is precisely why the existing
 *     POST endpoints under `admin/api/v4` cannot be called from outside a browser at all:
 *     VerifyCsrfToken excepts `login` and nothing else.
 *   - Authentication is by API key (`api/v4` => apibase + apiauth), restricted to superusers
 *     via `assert.privilege`.
 *
 * Four things guard them, all configurable under `ixp_api.provisioning`:
 *
 *   1. **Off by default.** Without `IXP_API_PROVISIONING_ENABLED=true` the routes are never
 *      registered. An installation which has not asked for these endpoints does not have
 *      them - an upgrade cannot quietly expose an IXP to something it did not choose.
 *   2. **An API key is required.** `apibase` starts a session and ApiAuthenticate only looks
 *      for a key when `Auth::check()` is false, so without RequireApiKey a browser already
 *      logged in as a superuser would reach these endpoints on its cookie - and, `web` being
 *      absent, without a CSRF token. Tolerable for the read-only export endpoints this group
 *      is modelled on; not for endpoints which create members.
 *   3. **Source addresses can be restricted**, both globally and per key. The latter honours
 *      `api_keys.allowed_ips`, a column which exists and is exposed in the UI but which
 *      nothing in IXP Manager has ever read.
 *   4. **Rate limited** per key, defaulting to 60 requests a minute.
 *
 * None of that replaces restricting `/admin` at the web server, which is what the prefix
 * introduced in v7.1.0 is for. It is a second lock on the same door.
 *
 * The `unsecured_api_access` fallback of the upstream provider is deliberately NOT
 * reproduced: these endpoints write, so they must never be reachable without a key.
 *
 * This is kept as a separate provider - rather than another map*() method on
 * RouteServiceProvider - so that the feature stays additive. Registering this class in
 * config/app.php is the only change made to a pre-existing file.
 *
 * Note that $namespace is deliberately left null: it would otherwise be applied globally
 * by setRootControllerNamespace(). The controller namespace is scoped to the route group
 * instead.
 *
 * @author     KleyReX
 * @category   IXP
 * @package    IXP\Providers
 * @copyright  Copyright (C) 2026 KleyReX
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ProvisioningRouteServiceProvider extends ServiceProvider
{
    /**
     * The controller namespace for the provisioning API route group.
     *
     * @var string
     */
    protected string $apiNamespace = 'IXP\Http\Controllers\Api\V4\Provisioning';

    /**
     * Define the routes for the provisioning API.
     *
     * Called by the framework via loadRoutes().
     *
     * @return void
     */
    public function map(): void
    {
        // Off unless the operator has switched it on. An installation which has not asked for
        // these endpoints does not have them - the routes are never registered, so there is
        // nothing to reach and nothing to secure.
        if( !config( 'ixp_api.provisioning.enabled', false ) ) {
            return;
        }

        Route::group( [
            'middleware' => $this->middleware(),
            'namespace'  => $this->apiNamespace,
            'prefix'     => 'admin/api/v4/provisioning',
        ], function() {
            require base_path( 'routes/apiv4-provisioning.php' );
        } );
    }

    /**
     * The middleware stack, in the order it has to run.
     *
     * RequireApiKey comes before `api/v4` so that a browser session is rejected before
     * ApiAuthenticate has a chance to accept it. RestrictSourceAddress comes after, because
     * it needs the resolved key to read that key's own allow-list.
     *
     * @return array<int,string>
     */
    private function middleware(): array
    {
        $stack = [
            ForceJsonResponse::class,
            RequireApiKey::class,
            'api/v4',
            'assert.privilege:' . User::AUTH_SUPERUSER,
            RestrictSourceAddress::class,
        ];

        if( ( $limit = config( "ixp_api.provisioning.rate_limit", 60 ) ) > 0 ) {
            $stack[] = "throttle:{$limit},1";
        }

        return $stack;
    }
}
