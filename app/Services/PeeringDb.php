<?php

namespace IXP\Services;

/*
 * Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Exception;
use Illuminate\Http\Client\Response as HttpResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


/**
 * PeeringDb
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   PeeringDB
 * @package    IXP\Services\PeeringDb
 * @copyright  Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PeeringDb
{

    /** @const Cache key for IXs  */
    public const CACHE_KEY_IXS        = 'api-v4-peeringdb-ixs';
    /** @const Cache key for facilities  */
    public const CACHE_KEY_FACILITIES = 'api-v4-peeringdb-facilities';


    /**
     * @var string|null An error message if the API call to PeeringDB failed
     */
    public ?string $error = null;

    /**
     * @var int The HTTP response code from the peeringDB api call
     */
    public int $status = 0;

    /**
     * @var ?Exception If the api call threw an exception, it is caught and stored here.
     */
    public ?Exception $exception;




    /**
     * Get network information by ASN
     *
     * Returns one of two arrays:
     *
     * [ 'net' => (json decoded network information directly from PeeringDB) ]
     * [ 'error' => (some error message) ]
     *
     * @param int $asn
     * @return array|false Successful or not
     */
    public function getNetworkByAsn( int $asn ): array|false
    {
        $response = $this->execute(
            $this->generateBasePeeringDbUrl( "/net.json?asn={$asn}&depth=2" )
        );

        if( $response->ok() ) {
            return $response->json()['data'][0];
        }

        if( $response->notFound() ) {
            $this->error = "No network with AS{$asn} found in PeeringDB";
            return false;
        }

        $this->error = $response->json()[ 'message' ] ?? 'Error';
        return false;
    }



    /**
     * Get all PeeringDB IXPs
     *
     * @param ?array $fields Optional fields to restrict it to. Default is [ pdb_id, name, city, country ]
     * @return array
     * @throws Exception On Error.
     */
    public function ixps( ?array $fields = null ): array
    {
        if( !$fields ) {
            $fields = [
                'pdb_id'  => 'id',
                'name'    => 'name',
                'city'    => 'city',
                'country' => 'country',
            ];
        }

        $cache_key = self::CACHE_KEY_IXS . '-' . implode( ':', $fields );

        if( Cache::has( $cache_key ) ) {
            return Cache::get( $cache_key );
        }

        $ixps = [];

        $ixs = $this->execute(
            $this->generateBasePeeringDbUrl( '/ix.json' )
        );

        if( $ixs === null ) {
            throw $this->exception;
        }

        foreach( $ixs->json()['data'] as $ix ) {

            $row = [];
            foreach( $fields as $want => $have ) {
                $row[ $want ] = $ix[$have];
            }
            $ixps[ $ix['id'] ] = $row;

        }

        Cache::put( $cache_key, $ixps, config( 'ixp_api.peeringDB.api_cache_ttl' ) );

        return $ixps;
    }




    /**
     * Get all PeeringDB facilities
     *
     * @param ?array $fields Optional fields to restrict it to. Default is [ id, name ]
     * @return array
     * @throws Exception On Error.
     */
    public function facilities( ?array $fields = null ): array
    {
        if( !$fields ) {
            $fields = [
                'id'      => 'id',
                'name'    => 'name',
                'city'    => 'city',
                'country' => 'country',
            ];
        }

        $cache_key = self::CACHE_KEY_FACILITIES . '-' . implode( ':', $fields );

        if( Cache::has( $cache_key ) ) {
            return Cache::get( $cache_key );
        }

        $facilities = [];

        $facs = $this->execute(
            $this->generateBasePeeringDbUrl( '/fac.json' )
        );

        if( $facs === null ) {
            throw $this->exception;
        }

        foreach( $facs->json()['data'] as $fac ) {

            $row = [];
            foreach( $fields as $want => $have ) {
                $row[ $want ] = $fac[$have];
            }
            $facilities[ $fac['id'] ] = $row;

        }

        Cache::put( $cache_key, $facilities, config( 'ixp_api.peeringDB.api_cache_ttl' ) );

        return $facilities;
    }


    /**
     * Takes a response from $this->getNetworkByAsn and formats it into an ASCII table for display in a <pre></pre>
     *
     * @param array $net
     * @return string
     */
    public function netAsAscii( array $net ): string {
        return <<<ENDWHOIS
PeeringDB Network Details of AS{$net['asn']}
==================================================

Name:            {$net['name']}
                 {$net['aka']}
                 {$net['name_long']}
Website:         {$net['website']}

Peering Policy:  {$net['policy_general']}

Notes: 

{$net['notes']}

==================================================

ENDWHOIS;

    }


    /**
     * Make the API call to PeeringDB
     *
     * Returns the response, sets the $status member and sets an appropriate member $error message.
     *
     * If null returned, then query failed and exception message in $error member and exception in $exception.
     *
     * @param string $query The PeeringDB URL for making the request.
     * @return HttpResponse|null
     */
    private function execute( string $query ): ?HttpResponse
    {
        $this->reset();

        // Typically testing Http::fake() belongs in unit test classes but we require it here as
        // we are using Laravel Dusk browser tests which make a new http request.
        if( app_env_is('testing') ) {
            $this->fake($query);
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
            $response = Http::withHeaders( $headers )
                ->accept( 'application/json' )
                ->get( $query );

            $this->status = $response->status();

            switch( $response->status() ) {

                case 200:
                    return $response;

                case 429:
                    $this->error = "Too many requests - PeeringDB throttling applied.";
                    return $response;

                default:
                    $this->error = $response->json()[ 'message' ] ?? 'Error';
                    return $response;
            }
        } catch( Exception $e ) {
            $this->error = $e->getMessage();
            $this->exception = $e;

            return null;
        }
    }


    /**
     * Fake the API calls to PeeringDB for testing
     *
     * Typically testing Http::fake() belongs in unit test classes but we require it here as
     * we are using Laravel Dusk browser tests which make a new http request.
     *
     * @return void
     */
    private function fake(): void
    {
        Http::fake([
            '*peeringdb.com/api/ix.json*'  => Http::response( file_get_contents( base_path('data/ci/known-good/peeringdb/ix.json') ), 200 ),
            '*peeringdb.com/api/fac.json*' => Http::response( file_get_contents( base_path('data/ci/known-good/peeringdb/fac.json') ), 200 ),
        ]);
    }


    /**
     * Generate the PeeringDB URL for making the request.
     *
     * @param string $query API endpoint
     * @return string Full URL including username/password authentication if appropriate
     */
    private function generateBasePeeringDbUrl( string $query = "" ): string
    {
        if( config( 'ixp_api.peeringDB.api-key' ) ) {
            $credentials = '';
        } else if( ( $un = config( 'ixp_api.peeringDB.username' ) ) === null || ( $pw = config( 'ixp_api.peeringDB.password' ) ) === null ) {
            Log::warning( 'Neither PeeringDb API Key nor deprecated username / password set in .env. Only public data will be retrieved. Please set an API Key' );
            $credentials = '';
        } else {
            $credentials = urlencode( $un ) . ":" . urlencode( $pw ) . "@";
        }

        // e.g. https://username:password@www.peeringdb.com/api/ix.json
        return sprintf( config( 'ixp_api.peeringDB.url' ), $credentials ) . $query;
    }


    /**
     * Reset class members.
     * @return void
     */
    private function reset(): void
    {
        $this->error = null;
        $this->exception = null;
        $this->status = 0;
    }

}