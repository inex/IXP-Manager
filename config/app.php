<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services your application utilizes. Set this in your ".env" file.
    |
    */
    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => env('APP_DEBUG'),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env( 'APP_URL', 'http://localhost' ),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => env( 'APP_TIMEZONE', 'UTC' ),

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => env( 'APP_LOCALE', 'en' ),

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY', 'SomeRandomString'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log settings for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Settings: "single", "daily", "syslog", "errorlog"
    |
    */

    'log' => env('APP_LOG', 'daily'),

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
         Illuminate\Auth\AuthServiceProvider::class,
         Illuminate\Broadcasting\BroadcastServiceProvider::class,
         Illuminate\Bus\BusServiceProvider::class,
         Illuminate\Cache\CacheServiceProvider::class,
         Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
         Illuminate\Cookie\CookieServiceProvider::class,
         Illuminate\Database\DatabaseServiceProvider::class,
         Illuminate\Encryption\EncryptionServiceProvider::class,
         Illuminate\Filesystem\FilesystemServiceProvider::class,
         Illuminate\Foundation\Providers\FoundationServiceProvider::class,
         Illuminate\Hashing\HashServiceProvider::class,
         Illuminate\Mail\MailServiceProvider::class,
         Illuminate\Pagination\PaginationServiceProvider::class,
         Illuminate\Pipeline\PipelineServiceProvider::class,
         Illuminate\Queue\QueueServiceProvider::class,
         Illuminate\Redis\RedisServiceProvider::class,
         Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
         Illuminate\Session\SessionServiceProvider::class,
         Illuminate\Translation\TranslationServiceProvider::class,
         Illuminate\Validation\ValidationServiceProvider::class,
         Illuminate\View\ViewServiceProvider::class,

        /*
         * Debug
         */
         Barryvdh\Debugbar\ServiceProvider::class,

        /*
         * Application Service Providers...
         */
        IXP\Providers\AppServiceProvider::class,
        IXP\Providers\AuthServiceProvider::class,
        IXP\Providers\EventServiceProvider::class,
        IXP\Providers\RouteServiceProvider::class,


        IXP\Providers\HelpdeskServiceProvider::class,
        IXP\Providers\ZendFrameworkServiceProvider::class,
        IXP\Providers\GrapherServiceProvider::class,
        IXP\Providers\LookingGlassServiceProvider::class,
        IXP\Providers\FoilServiceProvider::class,

        /*
         * Third party providers
         */
        LaravelDoctrine\ORM\DoctrineServiceProvider::class,
        Intervention\Image\ImageServiceProvider::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [

        'App'       => 'Illuminate\Support\Facades\App',
        'Artisan'   => 'Illuminate\Support\Facades\Artisan',
        'Auth'      => 'Illuminate\Support\Facades\Auth',
        'Blade'     => 'Illuminate\Support\Facades\Blade',
        'Bus'       => 'Illuminate\Support\Facades\Bus',
        'Cache'     => 'Illuminate\Support\Facades\Cache',
        'Config'    => 'Illuminate\Support\Facades\Config',
        'Cookie'    => 'Illuminate\Support\Facades\Cookie',
        'Crypt'     => 'Illuminate\Support\Facades\Crypt',
        'DB'        => 'Illuminate\Support\Facades\DB',
        'Eloquent'  => 'Illuminate\Database\Eloquent\Model',
        'Event'     => 'Illuminate\Support\Facades\Event',
        'File'      => 'Illuminate\Support\Facades\File',
        'Hash'      => 'Illuminate\Support\Facades\Hash',
        'Input'     => 'Illuminate\Support\Facades\Input',
        'Inspiring' => 'Illuminate\Foundation\Inspiring',
        'Lang'      => 'Illuminate\Support\Facades\Lang',
        'Log'       => 'Illuminate\Support\Facades\Log',
        'Mail'      => 'Illuminate\Support\Facades\Mail',
        'Password'  => 'Illuminate\Support\Facades\Password',
        'Queue'     => 'Illuminate\Support\Facades\Queue',
        'Redirect'  => 'Illuminate\Support\Facades\Redirect',
        'Redis'     => 'Illuminate\Support\Facades\Redis',
        'Request'   => 'Illuminate\Support\Facades\Request',
        'Response'  => 'Illuminate\Support\Facades\Response',
        'Route'     => 'Illuminate\Support\Facades\Route',
        'Schema'    => 'Illuminate\Support\Facades\Schema',
        'Session'   => 'Illuminate\Support\Facades\Session',
        'Storage'   => 'Illuminate\Support\Facades\Storage',
        'URL'       => 'Illuminate\Support\Facades\URL',
        'Validator' => 'Illuminate\Support\Facades\Validator',
        'View'      => 'Illuminate\Support\Facades\View',

        'D2EM'      => LaravelDoctrine\ORM\Facades\EntityManager::class,
        'Registry'  => LaravelDoctrine\ORM\Facades\Registry::class,
        'Doctrine'  => LaravelDoctrine\ORM\Facades\Doctrine::class,

        'Grapher'   => IXP\Support\Facades\Grapher::class,
        'Image'     => Intervention\Image\Facades\Image::class,

        'Debugbar' => Barryvdh\Debugbar\Facade::class,
    ],

];
