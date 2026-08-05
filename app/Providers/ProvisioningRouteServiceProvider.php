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
 *   - No `web` middleware, therefore no session cookie and no CSRF token. The latter is
 *     precisely why the existing POST endpoints under `admin/api/v4` cannot be called from
 *     outside a browser at all: VerifyCsrfToken excepts `login` and nothing else.
 *   - Authentication is by API key alone (`api/v4` => apibase + apiauth) and restricted to
 *     superusers via `assert.privilege`.
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
        Route::group( [
            'middleware' => [
                ForceJsonResponse::class,
                'api/v4',
                'assert.privilege:' . User::AUTH_SUPERUSER,
            ],
            'namespace'  => $this->apiNamespace,
            'prefix'     => 'admin/api/v4/provisioning',
        ], function() {
            require base_path( 'routes/apiv4-provisioning.php' );
        } );
    }
}
