<?php

namespace IXP\Http\Controllers\Api\V4;

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

use GuzzleHttp\Client as GuzzleHttp;
use GuzzleHttp\Exception\RequestException;

/**
 * RipeAtlasController
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   APIv4
 * @package    IXP\Http\Controllers\Api\V4
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class RipeAtlasController extends Controller
{
    /**
     * Get the detail of an Atlas measurement via Ripe atlas API
     *
     * @param int $atlasId
     *
     * @return array
     *
     * @throws
     */
    public function getAtlasMeasurementDetail( int $atlasId ): array
    {
        $apiUrl = "https://atlas.ripe.net/api/v2/measurements/" . $atlasId . "?format=json";

        $client = new GuzzleHttp();

        try {
            $req = $client->request( 'GET', $apiUrl );

            if( $req->getStatusCode() === 200 ) {
                return [ 'error' => false, 'response' => $req->getBody()->getContents() ];

            }
        } catch (RequestException $e) {
            if( $e->hasResponse() ) {
                return [ 'error' => true, 'response' => (string)$e->getResponse()->getBody() ];
            }

            return [ 'error' => true, 'response' => $e->getMessage() ];
        }

        return [ 'error' => true, 'response' => "Atlas measurement information API request failed for {$atlasId}" ];
    }

    /**
     * Get the detail of an Atlas probe via Ripe atlas API
     *
     * @param int $atlasid
     *
     * @return array
     *
     * @throws
     */
    public function getAtlasProbeDetail( int $atlasid ): array
    {
        $apiUrl = "https://atlas.ripe.net/api/v2/probes/" . $atlasid . "?format=json";

        $client = new GuzzleHttp();

        try {
            $req = $client->request( 'GET', $apiUrl );

            if( $req->getStatusCode() === 200 ) {
                return [ 'error' => false, 'response' => $req->getBody()->getContents() ];
            }

        } catch ( RequestException $e ) {
            if( $e->hasResponse() ) {
                return [ 'error' => true, 'response' => (string)$e->getResponse()->getBody() ];
            }

            return [ 'error' => true, 'response' => $e->getMessage() ];
        }

        return [ 'error' => true, 'response' => "Atlas Probe info API request failed for {$atlasid}" ];
    }
}