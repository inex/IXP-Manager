<?php namespace IXP\Http\Middleware;

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

use Auth, Closure, D2EM;
use Illuminate\Auth\Recaller;
use Illuminate\Contracts\Auth\Guard;

use Entities\{
    CustomerToUser as CustomerToUserEntity
};

use IXP\Utils\View\Alert\Alert;
use IXP\Utils\View\Alert\Container as AlertContainer;

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
				return redirect()->guest(route( "login@showForm" ) );
			}
		}

        if( !Auth::user()->getCustomer() || !D2EM::getRepository( CustomerToUserEntity::class)->findOneBy( [ "user" => Auth::user() , "customer" => Auth::user()->getCustomer() ] ) ){
            Auth::logout();
            return redirect()->guest(route( "login@showForm" ) );
        }

        // Check if user is disabled
        if( Auth::getUser()->getDisabled() ){
            AlertContainer::push( 'Your account is disabled.', Alert::DANGER );
            Auth::logout();
            return redirect()->guest( route( "login@showForm" ) );
        }

        // Check if default customer is disabled
        if( !Auth::getUser()->getCustomer()->isActive() ){
            Auth::logout();
            return redirect()->guest( route( "login@showForm" ) );
        }

		return $next($request);
	}

}
