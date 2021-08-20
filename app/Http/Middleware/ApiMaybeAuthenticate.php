<?php

namespace IXP\Http\Middleware;

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Auth, Closure;

use Illuminate\Http\Request;

use IXP\Models\ApiKey;
use IXP\Models\User;

/**
 * Middleware: ApiMaybeAuthenticate
 *
 * Check for IXP Manager token credentials with API access requests - use this when authentication is optional
 * such as when auth'd users get greater detail (e.g. IX-F Member List Export)
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Middleware
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ApiMaybeAuthenticate
{
	/**
	 * Authenticate if credentials present and valid
	 *
	 * API key can be passed in the header (preferred) or on the URL.
	 *
	 *     curl -X GET -H "X-IXP-Manager-API-Key: mySuperSecretApiKey" http://ixpv.dev/api/v4/test
	 *     wget http://ixpv.dev/api/v4/test?apikey=mySuperSecretApiKey
	 *
	 * @param   Request     $r
	 * @param   Closure     $next
     *
	 * @return mixed
     *
     * @throws
	 */
	public function handle( Request $r, Closure $next )
	{
		// are we already logged in?
		if( !Auth::check() ) {
			// find API key. Prefer header to URL:
			$apikey = false;
			if( $r->header('X-IXP-Manager-API-Key') ) {
				$apikey = $r->header('X-IXP-Manager-API-Key');
			} else if( $r->apikey ) {
				$apikey = $r->apikey;
			}

			if( $apikey ) {
			    if( !( $key = ApiKey::where( 'apiKey', $apikey )->with( 'user.customer' )->first() ) ) {
                    return response( 'Valid API key required', 403 );
                }

                if( $key->expires && now() > $key->expires ) {
                    return response( 'API key expired', 403 );
                }

                // Check if user is disabled
                if( $key->user->disabled ){
                    return response( 'User is disabled', 403 );
                }

                // Check if default customer is disabled
                if( $key->user->customer()->active()->notDeleted()->doesntExist() ){
                    return response( ucfirst( config( 'ixp_fe.lang.customer.one' ) ) . ' of the user is disabled', 403 );
                }

                Auth::onceUsingId( $key->user_id );

                $key->update( [
                    'lastseenAt'    => now(),
                    'lastseenFrom'  => ixp_get_client_ip(),
                ] );
            }
		} elseif( Auth::user()->disabled ){
            return response( 'User is disabled', 403 );
        }

        // Check if default customer is disabled
        if( Auth::check() && Auth::user()->customer()->active()->notDeleted()->doesntExist() ){
            return response( ucfirst( config( 'ixp_fe.lang.customer.one' ) ) . ' of the user is disabled', 403 );
        }

		return $next( $r );
	}
}