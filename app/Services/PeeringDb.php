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

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


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
     * @var array|null Network information from PeeringDB lookup
     */
    public ?array $net = null;

    public ?string $error = null;

    public int $status = 0;

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
     * @return bool Successful or not
     */
    public function getNetworkByAsn( $asn = null ): bool
    {
        $asn = (int)trim( $asn );

        // reset in case of reuse
        $this->reset();

        if( !is_numeric( $asn ) || $asn <= 0 ) {
            $this->error = "Invalid ASN provided: $asn";
            return false;
        }

        // api key?
        if( config( 'ixp_api.peeringDB.api-key' ) ) {
            $headers = [
                'Authorization' => 'Api-Key ' . config( 'ixp_api.peeringDB.api-key' ),
            ];
        } else {
            $headers = [];
            Log::warning( 'PeeringDB has no API key set in .env - see https://docs.peeringdb.com/howto/api_keys/ and set IXP_API_PEERING_DB_API_KEY in .env' );
        }

        try {
            // find network by ASN
            $response = Http::withHeaders( $headers )
                ->accept( 'application/json' )
                ->get( $this->generateBasePeeringDbUrl( "/net.json?asn={$asn}&depth=2" ) );

            $this->status = $response->status();

            switch( $response->status() ) {

                case 200:
                    $this->net = $response->json()['data'][0];
                    return true;

                case 404:
                    $this->error = "No network with AS{$asn} found in PeeringDB";
                    return false;

                case 429:
                    $this->error = "Too many requests - PeeringDB throttling applied.";
                    return false;

                default:
                    $this->error = $response->json()[ 'message' ] ?? 'Error';
                    return false;
            }
        } catch( \Exception $e ) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function netAsAscii(): string {
        return <<<ENDWHOIS
PeeringDB Network Details of AS{$this->net['asn']}
==================================================

Name:            {$this->net['name']}
                 {$this->net['aka']}
                 {$this->net['name_long']}
Website:         {$this->net['website']}

Peering Policy:  {$this->net['policy_general']}

Notes: 

{$this->net['notes']}

==================================================

ENDWHOIS;

    }

    /**
     * @param string $query
     *
     * @return string
     */
    private function generateBasePeeringDbUrl( string $query = "" ): string
    {
        $credentials = '';
        if( ( ( $un = config( 'ixp_api.peeringDB.username' ) ) === null || ( $pw = config( 'ixp_api.peeringDB.password' ) ) === null ) && config( 'ixp_api.peeringDB.api-key' ) === null ) {
            Log::warning( 'Neither PeeringDb API Key nor deprecated username / password set in .env. Only public data will be retrieved. Please set an API Key' );
        } else {
            $credentials = urlencode( $un ) . ":" . urlencode( $pw ) . "@";
        }

        return sprintf( config( 'ixp_api.peeringDB.url' ), $credentials ) . $query;
    }


    private function reset(): void
    {
        $this->net = null;
        $this->error = null;
        $this->status = 0;
    }

}