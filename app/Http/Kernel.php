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
        Middleware\UrlResolver::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \IXP\Http\Middleware\TrustProxies::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
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
        'auth'       => Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings'   => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can'        => \Illuminate\Auth\Middleware\Authorize::class,
        'guest'      => Middleware\RedirectIfAuthenticated::class,
        'throttle'   => \Illuminate\Routing\Middleware\ThrottleRequests::class,

        'apiauth'            => Middleware\ApiAuthenticate::class,
        'apimaybeauth'       => Middleware\ApiMaybeAuthenticate::class,
        'assert.privilege'   => Middleware\AssertUserPrivilege::class,
        'controller-enabled' => Middleware\ControllerEnabled::class,
        'doctrine2frontend'  => Middleware\Doctrine2Frontend::class,
        'grapher'            => Middleware\Services\Grapher::class,
        'patch-panel-port'   => Middleware\PatchPanelPort::class,
    ];

}
