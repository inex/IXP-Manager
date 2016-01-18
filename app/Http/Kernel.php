<?php namespace IXP\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel {

    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \IXP\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \IXP\Http\Middleware\VerifyCsrfToken::class,
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => 'IXP\Http\Middleware\Authenticate',
        'auth.basic' => 'Illuminate\Auth\Middleware\AuthenticateWithBasicAuth',
        'guest' => 'IXP\Http\Middleware\RedirectIfAuthenticated',
    ];


    /**
     * Zend Framework 1 fallback
      *
      * IXP Manager is transitioning from ZF1 to Laravel as a framework. It
      * will take some time to move over everything and this will be done on
       * an as needed basis. Realistically this means there may be some ZF1
      * crud for the foreseeable future.
      *
      * We'll revert to ZF1 handling when Laravel throws a 404:
      */
     public function handle( $request )
    {
        // remove CSRF middleware as it's not available in ZF1
        $this->middleware = array_diff($this->middleware, ['IXP\Http\Middleware\VerifyCsrfToken'] );

        try
        {
            return $this->sendRequestThroughRouter($request);
        }
        catch( \Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e )
        {
            // Define path to application directory
            defined('APPLICATION_PATH')
                || define('APPLICATION_PATH', realpath( __DIR__ . '/../../application' ) );

            // Define application environment
            if( php_sapi_name() == 'cli-server' ) {
                // running under PHP's built in web server: php -S
                // as such, .htaccess is not processed
                include( __DIR__ . '/../../bin/utils.inc' );
                define( 'APPLICATION_ENV', scriptutils_get_application_env() );
            } else {
                // probably Apache or other web server
                defined('APPLICATION_ENV')
                    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
            }

            /** Zend_Application */
            require_once 'Zend/Application.php';

            require_once( APPLICATION_PATH . '/../library/IXP/Version.php' );

            // Create application, bootstrap, and run
            $application = new \Zend_Application(
                APPLICATION_ENV,
                APPLICATION_PATH . '/configs/application.ini'
            );

            $application->bootstrap()->run();
            die();
        }
    }

}
