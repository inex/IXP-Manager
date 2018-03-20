<?php

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
