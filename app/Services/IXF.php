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
use Throwable;
use Illuminate\Http\Client\Response as HttpResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


/**
 * IXF
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @category   IX-F
 * @copyright  Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXF
{

    /** @const Cache key for IXs  */
    public const CACHE_KEY_IXS        = 'api-v4-ixf-ixs';


    /**
     * @var string|null An error message if the API call to PeeringDB failed
     */
    public ?string $error = null;

    /**
     * @var int The HTTP response code from the peeringDB api call
     */
    public int $status = 0;

    /**
     * @var Exception If the api call threw an exception, it is caught and stored here.
     */
    public ?Exception $exception = null;




    /**
     * Get all PeeringDB IXPs
     *
     * @param ?array $fields Optional fields to restrict it to. Default is [ ixf_id, name, city, country ]
     * @return array
     * @throws Exception On Error.
 */
    public function ixps( ?array $fields = null ): array
    {
        if( !$fields ) {
            $fields = [
                'ixf_id'  => 'id',
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

        $ixs = $this->execute( config( 'ixp_api.IXPDB.ixp_api' ) );

        if( $ixs === null ) {
            if( $this->exception ) {
                throw $this->exception;
            } else {
                throw new Exception( 'IXF ixps error' );
            }
        }

        foreach( $ixs->json() as $ix ) {

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
     * Make the API call to IX-F
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

        try {
            $response = Http::accept( 'application/json' )
                ->withUserAgent( 'IXP-Manager/' . APPLICATION_VERSION )
                ->get( $query );

            $this->status = $response->status();

            switch( $response->status() ) {

                case 200:
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
            'https://api.ixpdb.net/v1/provider/list'  => Http::response( file_get_contents( base_path('data/ci/known-good/ix-f/provider.json') ), 200 ),
        ]);
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