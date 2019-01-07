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
        if( $this->isHttpException($e) ) {
            return $this->renderHttpException($e);
        } else if( $e instanceof AuthorizationException && request()->route()->action['middleware'] != 'grapher' ) {
            return Redirect::to( '' );
        } else {
            return parent::render($request, $e);
        }
    }

}
