<?php

namespace IXP\Http\Controllers\Api\V4;

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

use Cache;

use IXP\Models\Asn;
use IXP\Services\PeeringDb;
use Illuminate\Http\{Request,Response};

use IXP\Utils\Whois\{
    Whois,
    WhoisHost,
};

/**
 * WhoisController
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   APIv4
 * @package    IXP\Http\Controllers\Api\V4\Provisioner
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class WhoisController extends Controller
{
    /**
     * API call to do a Whois looking on an AS number
     *
     * @param string    $asn The AS number
     *
     * @return Response
     */
    public function asn( string $asn ): Response
    {
        $asn = Asn::where( 'asn', '=', $asn )->first();
        if ( $asn === null ) {
            return response( 'ASN not found in store', 404 )
                ->header( 'Content-Type', 'text/plain' );
        }

        $contents  = "Number  : $asn->asn\n";
        $contents .= "Name    : $asn->name\n";
        $contents .= "Class:  : $asn->class\n";
        $contents .= "Country : $asn->country_code\n";
        return response( $contents, 200 )
            ->header( 'Content-Type', 'text/plain' );
    }

    /**
     * API call to do a Whois looking on a prefix
     *
     * @param Whois         $whois  A whois instance
     * @param string        $prefix The IP address element of the prefix
     * @param string|null   $mask   The mask length
     *
     * @return Response
     */
    public function prefix( #[WhoisHost('prefix')] Whois $whois, string $prefix, ?string $mask = null ): Response
    {
        // Don't append slash unless we're sending the mask also
        $response = Cache::remember( 'api-v4-whois-prefix-' . $prefix . '-' . $mask, config('ixp_api.whois.cache_ttl'),
            fn() => $whois->whois( $prefix . ($mask ? "/$mask" : ""))
        );

        return response( $response, 200 )->header('Content-Type', 'text/plain');
    }
}
