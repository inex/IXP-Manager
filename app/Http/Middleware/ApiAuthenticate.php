<?php

namespace IXP\Http\Middleware;

/*
 * Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Illuminate\Support\Facades\Log;
use IXP\Models\Aggregators\ApiKeyAggregator;
use IXP\Models\ApiKey;
use IXP\Models\User;

/**
 * Middleware: ApiAuthenticate
 *
 * Check for IXP Manager token credentials with API access requests
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Middleware
 * @copyright  Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ApiAuthenticate
{
	/**
	 * Authenticate protected APIv4 calls
	 *
	 * API key can be passed in the header (preferred) or on the URL (deprecated)
	 *
	 *     curl -X GET -H "X-IXP-Manager-API-Key: mySuperSecretApiKey" http://ixpv.dev/api/v4/test
	 *     DEPRECATED: wget http://ixpv.dev/api/v4/test?apikey=mySuperSecretApiKey
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
            $logIdentifier = null;
            
			if( $r->header('X-IXP-Manager-API-Key') ) {
				$apikey = $r->header('X-IXP-Manager-API-Key');
			} else if( $r->apikey ) {
                // use always because normal deferred functions only run when the status code < 400
                defer(function() use ($r, &$logIdentifier) {
                    Log::notice( 'DEPRECATED usage of API Key in GET parameter (' . $logIdentifier . '): ' . $r->path() . ' from ' . ixp_get_client_ip() );
                })->always();

                if ( config('ixp_api.allow_apikeys_get_parameter') ) {
                    $apikey = $r->apikey;
                }
			}

			if( !$apikey ) {
				return response('Unauthorized.', 401);
	        }

            /** @var ApiKey $key */

            // modern or legacy handling?
            if( str_starts_with( $apikey, ApiKey::PREFIX ) ) {

                $key = ApiKeyAggregator::authenticate( $apikey );

                if( !$key instanceof ApiKey ) {
                    // if it's not an ApiKey object, it's an error response
                    return $key;
                }
                // record a token identifier in case we need to report an APIKEY via get parameter
                $logIdentifier = "API Token Identifier: " . $key->token_identifier;
            } else {
                // legacy

                if( !( $key = ApiKey::where( 'api_key', $apikey )->with( 'user.customer' )->first() ) ) {
                    return response( 'Valid API key required', 401 );
                }

                // record Api Key ID in case we need to report a APIKEY via get parameter
                $logIdentifier = "API Key ID: " . $key->id;
            }

            if( $key->expires->isPast() ) {
                return response( 'API key expired', 401 );
            }

            // Check if user is disabled
            if( $key->user->disabled ){
                return response( 'User is disabled', 401 );
            }

            // Check if default customer is disabled
            if( $key->user->customer()->active()->notDeleted()->doesntExist() ){
                return response( ucfirst( config( 'ixp_fe.lang.customer.one' ) ) . ' of the user is disabled', 401 );
            }


            Auth::onceUsingId( $key->user_id );

            $key->updateLastSeen();

		} else {
            /** @var User $us */
            $us = Auth::user();

            if( $us->disabled ){
                return response( 'User is disabled', 401 );
            } elseif( $us->customer()->active()->notDeleted()->doesntExist() ){// Check if default customer is disabled
                return response( ucfirst( config( 'ixp_fe.lang.customer.one' ) ) . ' of the user is disabled', 401 );
            }
        }

		return $next( $r );
	}
}