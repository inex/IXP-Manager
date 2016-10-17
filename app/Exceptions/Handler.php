<?php namespace IXP\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Validation\ValidationException;

class Handler extends ExceptionHandler {

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        Symfony\Component\HttpKernel\Exception\HttpException::class,
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
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
     * @return \Illuminate\Http\Response
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
        if( $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException ) {
            \App::make('ZendFramework')->run();
            die();
        }
        else if ($this->isHttpException($e))
        {
            return $this->renderHttpException($e);
        }
        else
        {
            return parent::render($request, $e);
        }
    }

}
