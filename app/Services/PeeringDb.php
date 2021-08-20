<?php

namespace IXP\Services;

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

use Log;

use GuzzleHttp\{
    Client as GuzzleHttp,
    Exception\RequestException
};

/**
 * PeeringDb
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   PeeringDB
 * @package    IXP\Services\PeeringDb
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PeeringDb
{
    /**
     * Get network information by ASN
     *
     * Returns one of two arrays:
     *
     * [ 'net' => (json decoded network information directly from PeeringDB) ]
     * [ 'error' => (some error message) ]
     *
     * @param string $asn
     *
     * @return array
     */
    public function getNetworkByAsn( $asn = null ): array
    {
        $asn = trim( $asn );

        if( !is_numeric( $asn ) || $asn <= 0 ) {
            return [ 'error' => "Invalid ASN provided: " . $asn ];
        }

        $client = new GuzzleHttp();

        try {
            // find network by ASN
            $req = $client->request( 'GET', $this->generateBasePeeringDbUrl( "/net.json?asn={$asn}" ) );

            if( $req->getStatusCode() === 200 ) {
                $pdb_network_id = json_decode( $req->getBody()->getContents(), false )->data[ 0 ]->id;
                $req = $client->request( 'GET', $this->generateBasePeeringDbUrl( "/net/{$pdb_network_id}.json" ) );

                if( $req->getStatusCode() === 200 ) {
                    return [ 'net' => json_decode( $req->getBody()->getContents(), false )->data[ 0 ] ];
                }
            } else if( $req->getStatusCode() === 404 ) {
                return [ 'error' => "No network with AS{$asn} found in PeeringDB" ];
            }
        } catch (RequestException $e) {
            if( $e->hasResponse() ) {
                return [ 'error' => json_decode( (string) $e->getResponse()->getBody(), true ) ];
            }
            return [ 'error' => $e->getMessage() ];
        }
        return [ 'error' => 'Unable to query PeeringDb / get result from PeeringDb. Check if PeeringDB is up / functioning.' ];
    }

    /**
     * @param string $query
     *
     * @return string
     */
    private function generateBasePeeringDbUrl( string $query = "" ): string
    {
        $credentials = '';
        if( ( $un = config( 'ixp_api.peeringDB.username' ) ) === null || ( $pw = config( 'ixp_api.peeringDB.password' ) ) === null ) {
            Log::warning( 'PeeringDb username / password not set in .env. Only public data will be retrieved.' );
        } else {
            $credentials = urlencode( $un ) . ":" . urlencode( $pw ) . "@";
        }

        return sprintf( config( 'ixp_api.peeringDB.url' ), $credentials ) . $query;
    }
}