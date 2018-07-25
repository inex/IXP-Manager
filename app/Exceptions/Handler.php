<?php namespace IXP\Exceptions;

use Exception,Redirect;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use IXP\Utils\View\Alert\Alert;
use IXP\Utils\View\Alert\Container as AlertContainer;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Illuminate\Auth\Access\AuthorizationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     *
     * @return void
     *
     * @throws
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $e)
    {
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
        if( $e instanceof NotFoundHttpException ) {

            \App::make('ZendFramework')->run();
            die();

        } else if( $this->isHttpException($e) ) {

            return $this->renderHttpException($e);

        } else if( $e instanceof AuthorizationException && request()->route()->action['middleware'] != 'grapher' ) {
            //AlertContainer::push( "Please login below.", Alert::DANGER );

            // store in session url for a redirection after login
            //$request->session()->put( "url.redirect.after.login", $request->path() );

            // TEMPORARY : using classic php session to be able to get the session in the ZEND auth
            $_SESSION["url.redirect.after.login"] = $request->path();

            return Redirect::to( '' );
        } else {

            return parent::render($request, $e);

        }
    }

}
