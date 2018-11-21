<?php namespace IXP\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class Authenticate {

	/**
	 * The Guard implementation.
	 *
	 * @var Guard
	 */
	protected $auth;

	/**
	 * Create a new filter instance.
	 *
	 * @param  Guard  $auth
	 * @return void
	 */
	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if ($this->auth->guest())
		{
			if ($request->ajax())
			{
				return response('Unauthorized.', 401);
			}
			else
			{
                // store in session url for a redirection after login
                //$request->session()->put( "url.redirect.after.login", $request->path() );

                // TEMPORARY : using classic php session to be able to get the session in the ZEND auth
                $_SESSION["url.redirect.after.login"] = $request->path();

				return redirect()->guest('auth/login');
			}
		}

		return $next($request);
	}

}
