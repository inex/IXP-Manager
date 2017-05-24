<?php

namespace IXP\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel {

    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \IXP\Http\Middleware\UrlResolver::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \IXP\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \IXP\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \IXP\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'public/api/v4' => [
            'throttle:60,1',
            'bindings',
            'apimaybeauth'
        ],

        'api/v4' => [
            'throttle:60,1',
            'bindings',
            'apiauth'
        ],

        'grapher' => [
            \IXP\Http\Middleware\Services\Grapher::class,
        ],

        'lookingglass' => [
            \IXP\Http\Middleware\Services\LookingGlass::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth'       => \IXP\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings'   => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can'        => \Illuminate\Auth\Middleware\Authorize::class,
        'guest'      => \IXP\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle'   => \Illuminate\Routing\Middleware\ThrottleRequests::class,

        'apiauth'          => \IXP\Http\Middleware\ApiAuthenticate::class,
        'apimaybeauth'     => \IXP\Http\Middleware\ApiMaybeAuthenticate::class,
        'assert.privilege' => \IXP\Http\Middleware\AssertUserPrivilege::class,
        'grapher'          => \IXP\Http\Middleware\Services\Grapher::class,
        'patch-panel-port' => \IXP\Http\Middleware\PatchPanelPort::class,
    ];

}
