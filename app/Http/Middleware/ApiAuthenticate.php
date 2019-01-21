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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */


use Auth;
use Closure;
use D2EM;

use Illuminate\Contracts\Auth\Guard;

/**
 * Middleware: ApiAuthenticate
 *
 * Check for IXP Manager token credentials with API access requests
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Providers
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ApiAuthenticate {

	/**
	 * Authenticate protected APIv4 calls
	 *
	 * API key can be passed in the header (preferred) or on the URL.
	 *
	 *     curl -X GET -H "X-IXP-Manager-API-Key: mySuperSecretApiKey" http://ixpv.dev/api/v4/test
	 *     wget http://ixpv.dev/api/v4/test?apikey=mySuperSecretApiKey
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		// are we already logged in?
		if( !Auth::check() ) {

			// find API key. Prefer header to URL:
			$apikey = false;
			if( $request->header('X-IXP-Manager-API-Key') ) {
				$apikey = $request->header('X-IXP-Manager-API-Key');
			} else if( $request->input('apikey') ) {
				$apikey = $request->input('apikey');
			}

			if( !$apikey ) {
				return response('Unauthorized.', 401);
	        }

	        try {
	            $key = D2EM::createQuery(
	                    "SELECT a FROM \\Entities\\ApiKey a WHERE a.apiKey = ?1" )
	                ->setParameter( 1, $apikey )
	                ->useResultCache( true, 3600, 'oss_d2u_user_apikey_' . $apikey )
	                ->getSingleResult();
	        } catch( \Doctrine\ORM\NoResultException $e ) {
	            return response( 'Valid API key required', 403 );
	        }

	        Auth::onceUsingId( $key->getUser()->getId() );

	        $key->setLastseenAt( new \DateTime() );
	        $key->setLastseenFrom( $_SERVER['REMOTE_ADDR'] );
	        D2EM::flush();
		}
		
		return $next($request);
	}

}
