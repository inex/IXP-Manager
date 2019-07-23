<?php

namespace IXP\Http\Controllers\Api\V4;

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

use Illuminate\Http\{Request,Response};

use IXP\Utils\Whois;

use Cache;

class WhoisController extends Controller {



    /**
     * API call to do a Whois looking on an AS number
     *
     * @param Request $r
     * @param string $asn The AS number
     * @return Response
     */
    public function asn( Request $r, string $asn ) {

        $response = Cache::remember( 'api-v4-whois-asn-' . $asn, config('ixp_api.whois.cache_ttl'), function () use ($asn) {

            $whois = new Whois( config( 'ixp_api.whois.asn.host' ), config( 'ixp_api.whois.asn.port' ) );

            $response = $whois->whois( 'AS' . (int)$asn );

            // nicer error message than PeeringDB's
            if( $whois->host() === 'whois.peeringdb.com' && strpos( strtolower( $response ), "network matching query does not exist" ) !== false ) {

                // sigh, nothing in PeeringDB. Try Team Cymru (which is asn2 by default) to get at least some info.
                $whois = new Whois( config( 'ixp_api.whois.asn2.host' ), config( 'ixp_api.whois.asn2.port' ) );
                $response = $whois->whois( 'AS' . (int)$asn );
                $response = "{$asn} does not appear to have a record in PeeringDB.\n\nTrying {$whois->host()}:\n\n" . $response;
            }

            return $response;
        });

        return response( $response, 200 )->header('Content-Type', 'text/plain');
    }


    /**
     * API call to do a Whois looking on a prefix
     *
     * @param Request $r
     * @param string $prefix The IP address element of the prefix
     * @param string $mask The mask length
     * @return Response
     */
    public function prefix( Request $r, string $prefix, string $mask ) {

        $response = Cache::remember( 'api-v4-whois-prefix-' . $prefix . '-' . $mask, config('ixp_api.whois.cache_ttl'), function () use ($prefix, $mask) {

            $whois = new Whois( config('ixp_api.whois.prefix.host'), config('ixp_api.whois.prefix.port') );
            return $whois->whois( $prefix .'/' . $mask );
        });

        return response( $response, 200 )->header('Content-Type', 'text/plain');
    }
}
