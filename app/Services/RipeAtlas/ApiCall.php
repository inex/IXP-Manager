<?php

namespace IXP\Services\RipeAtlas;

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

use GuzzleHttp\{
    Client as GuzzleHttp,
    Exception\RequestException
};
use Illuminate\Support\Facades\Log;

/**
 * RipeAtlas Api Calls
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Services\RipeAtlas
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ApiCall
{
    /**
     * Call the Ripe Atlas Api and return the Probes list for the protocol and Asn
     *
     * @param $customer
     * @param $protocol
     *
     * @return array
     *
     * @throws
     */
    public function queryAtlasForProbes( $customer, $protocol ): array
    {
        $client = new GuzzleHttp();
        $asn    = $customer->autsys;
        $name   = $customer->name;

        try {
            $req = $client->request( 'GET', "https://atlas.ripe.net/api/v2/probes/?asn_v{$protocol}={$asn}&is_public=true&status=1" );

            if( $req->getStatusCode() === 200 ) {
                return [ 'error' => false, 'response' => json_decode( $req->getBody()->getContents(), false, 512,
                    JSON_THROW_ON_ERROR)
                ];
            }

            if( $req->getStatusCode() === 404 ) {
                return [ 'error' => true, 'response' => "Probe Atlas API request not found for {$name}/ASN{$asn}" ];
            }
        } catch (RequestException $e) {
            if( $e->hasResponse() ) {
                return [ 'error' => true, 'response' => json_decode((string) $e->getResponse()->getBody(), true, 512,
                    JSON_THROW_ON_ERROR)
                ];
            }
            return [ 'error' => true, 'response' => $e->getMessage() ];
        }
        return [ 'error' => true, 'response' => "Probe Atlas API request failed for {$name}/ASN{$asn}" ];
    }

    /**
     * Call the Ripe Atlas Api and return the Probes list for the protocol and Asn
     *
     * @param $fromASN
     * @param $target
     * @param $protocol
     *
     * @return int|null
     *
     * @throws
     */
    public function requestAtlasTraceroute( $fromASN, $target, $protocol ): ?int
    {
        $query = [
            'definitions' => [ [
                'target'      => $target,
                'description' => 'IXP Asymmentric routing detector',
                'type'        => 'traceroute',
                'af'          => $protocol,
                'protocol'    => 'ICMP',
                'is_oneoff'   => true,
                'is_public'   => true
            ] ],
            'probes' => [ [
                'requested' => 1,
                'type'      => 'asn',
                'value'     => $fromASN
            ] ]
        ];

        // use key 'http' even if you send the request to https://...
        $options = [
            'http' => [
                'header'  => "Content-Type: application/json",
                'method'  => 'POST',
                'content' => json_encode( $query, JSON_THROW_ON_ERROR )
            ]
        ];

        $context = stream_context_create( $options );

        try {
            $result = file_get_contents('https://atlas.ripe.net/api/v2/measurements/?key=' . config( "ixp_api.atlas_measurement_key" ) , false, $context);
            $response = json_decode( $result, false, 512, JSON_THROW_ON_ERROR);
            return $response->measurements[0];
        } catch( \Exception $e ) {
            /*if( $this->isVerbose() ) {
                $this->error( "  - FAILED: " . json_encode( $query ) );
            }*/
        }

        return null;
    }

    /**
     * Call the Ripe Atlas measurement Api and return the measurement info
     *
     * @param int $atlasId
     *
     * @return array
     *
     * @throws
     */
    public function updateAtlasMeasurement( int $atlasId ): array
    {
        $apiUrl = "https://atlas.ripe.net/api/v2/measurements/" . $atlasId;

        //        if( $this->isVerbose() ) {
        //            $this->info( 'Checking result for measurement ' . $m->$getAtlasIdFn() . ' [' . $apiUrl . ']'  );
        //        }

        $client = new GuzzleHttp();

        try {
            $req = $client->request( 'GET', $apiUrl );

            if( $req->getStatusCode() === 200 ) {
                return [ 'error' => false, 'response' => json_decode($req->getBody()->getContents(), false, 512,
                    JSON_THROW_ON_ERROR)
                ];
            }

        } catch (RequestException $e) {
            if( $e->hasResponse() ) {
                return [ 'error' => true, 'response' => json_decode((string) $e->getResponse()->getBody(), false, 512,
                    JSON_THROW_ON_ERROR)
                ];
            }
            return [ 'error' => true, 'response' => $e->getMessage() ];
        }
        return [ 'error' => true, 'response' => "Update Atlas measurement API request failed for {$atlasId}" ];
    }

    /**
     * Get the Atlas measurements from the API
     *
     * @return array
     *
     * @throws
     */
    public function myAtlasMeasurements(): array
    {
        $client = new GuzzleHttp();
        try {
            // Get measurements with Status "Ongoing" (2)
            $req = $client->request( 'GET', 'https://atlas.ripe.net/api/v2/measurements/my/?key=' . config('ixp_api.atlas_measurement_key') . '&status=2&page_size=500' );
            if( $req->getStatusCode() === 200 ) {
                return [ 'error' => false, 'response' => json_decode( $req->getBody()->getContents(), false, 512,
                    JSON_THROW_ON_ERROR)
                ];
            }

            if( $req->getStatusCode() === 404 ) {
                return [ 'error' => true, 'response' => "Ripe Atlas API request not found" ];
            }
        } catch (RequestException $e) {
            if( $e->hasResponse() ) {
                return [ 'error' => true, 'response' => json_decode((string) $e->getResponse()->getBody(), false, 512,
                    JSON_THROW_ON_ERROR)
                ];
            }
            return [ 'error' => true, 'response' => $e->getMessage() ];
        }
        return [ 'error' => true, 'response' => "Ripe Atlas API request failed" ];
    }


    /**
     * Stop a measurement
     *
     * @param  int  $atlasId
     *
     * @return void
     *
     */
    public function atlasStopMeasurement( int $atlasId ): void
    {
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, "https://atlas.ripe.net/api/v2/measurements/" . $atlasId . "/?key=" . config('ixp_api.atlas_measurement_key' ) );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "DELETE" );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $result = curl_exec( $ch );
        curl_close( $ch );
        Log::alert( $result );
    }
}