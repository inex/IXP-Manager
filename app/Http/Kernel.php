<?php

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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

namespace IXP\Http;

use Illuminate\Auth\Middleware\{
    AuthenticateWithBasicAuth,
    Authorize
};

use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

use Illuminate\Foundation\Http\Middleware\{
    CheckForMaintenanceMode,
    ConvertEmptyStringsToNull,
    ValidatePostSize
};

use Illuminate\Routing\Middleware\{
    SubstituteBindings,
    ThrottleRequests
};

use Illuminate\Session\Middleware\StartSession;

use Illuminate\View\Middleware\ShareErrorsFromSession;

use IXP\Http\Middleware\TrustProxies;

class Kernel extends HttpKernel {

    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        CheckForMaintenanceMode::class,
        ValidatePostSize::class,
        Middleware\TrimStrings::class,
        ConvertEmptyStringsToNull::class,
        TrustProxies::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            Middleware\EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            Middleware\VerifyCsrfToken::class,
            SubstituteBindings::class,
            Middleware\ControllerEnabled::class,
        ],

        'public/api/v4' => [
            'throttle:60,1',
            'bindings',
            'apimaybeauth',
            Middleware\ControllerEnabled::class,
        ],

        'api/v4' => [
            'throttle:60,1',
            'bindings',
            'apiauth',
            Middleware\ControllerEnabled::class,
        ],

        'd2frontend' => [
            'web',
            'doctrine2frontend',
        ],

        'grapher' => [
            Middleware\EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            Middleware\ControllerEnabled::class,
            Middleware\Services\Grapher::class,
        ],

        'lookingglass' => [
            Middleware\ControllerEnabled::class,
            Middleware\Services\LookingGlass::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth'                  => Middleware\Authenticate::class,
        'auth.basic'            => AuthenticateWithBasicAuth::class,
        'bindings'              => SubstituteBindings::class,
        'can'                   => Authorize::class,
        'guest'                 => Middleware\RedirectIfAuthenticated::class,
        'throttle'              => ThrottleRequests::class,
        'apiauth'               => Middleware\ApiAuthenticate::class,
        'apimaybeauth'          => Middleware\ApiMaybeAuthenticate::class,
        'assert.privilege'      => Middleware\AssertUserPrivilege::class,
        'controller-enabled'    => Middleware\ControllerEnabled::class,
        'doctrine2frontend'     => Middleware\Doctrine2Frontend::class,
        'grapher'               => Middleware\Services\Grapher::class,
        'patch-panel-port'      => Middleware\PatchPanelPort::class,
        'rs-prefixes'           => Middleware\RsPrefixes::class,
    ];

}
