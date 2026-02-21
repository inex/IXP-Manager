<?php

namespace IXP\Services;

/*
 * Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public Licence as published by the Free
 * Software Foundation, version v2.0 of the Licence.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public Licence for
 * more details.
 *
 * You should have received a copy of the GNU General Public Licence v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response as HttpResponse;
use Illuminate\Support\Facades\Http;
use IXP\Exceptions\ConfigurationException;


/**
 * PeeringDb
 *
 * @author     Barry O'Donovan  <barry@opensolutions.ie>
 * @category   Ripe
 * @copyright  Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class RipeRestApi
{
    /** @const RIPE Production Database  */
    public const string RIPE_DB_PRODUCTION        = 'https://rest.db.ripe.net';
    
    /** @const RIPE Test Database  */
    public const string RIPE_DB_TEST              = 'https://rest-test.db.ripe.net';


    private bool $testmode = false;
    
    
    /**
     * Enable test mode for RIPE REST API
     */
    public function enableTestMode(): static
    {
        $this->testmode = true;
        return $this;
    }
    
    /**
     * @throws ConfigurationException
     */
    public function checkConfiguration(): void
    {
        if( !config( 'ixp_api.rir.ripe_api_key' ) ) {
            throw new ConfigurationException( 'RIPE REST API is not set in configuration' );
        }
    }
    
    /**
     * Update an object into the RIPE database
     *
     * $jsonData = [
     *     'type' => 'aut-num',
     *     'key' => 'AS66500',
     *     'data' => [
     *         <RIPE Object>
     *     ]
     * ]
     * @throws ConnectionException
     */
    public function updateObject( array $jsonData ): HttpResponse
    {
        $url = $this->testmode ? self::RIPE_DB_TEST : self::RIPE_DB_PRODUCTION;
        $url .= '/' . ( $this->testmode ? 'TEST' : 'RIPE') . '/' . $jsonData['type'] . '/' . $jsonData['key'];
        
        $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . config( 'ixp_api.rir.ripe_api_key' )
            ])
            ->withUserAgent( 'IXP-Manager/' . APPLICATION_VERSION )
            ->put( $url, $jsonData['data'] );
        
        return $response;
    }
    
    
    

}