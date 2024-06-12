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

use Carbon\Carbon;

use Illuminate\Support\Facades\App;
use IXP\Models\Router;

use IXP\Tasks\Router\ConfigurationGenerator as RouterConfigurationGenerator;

use Illuminate\Http\{
    JsonResponse,
    Response
};

/**
 * RouterController
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   APIv4
 * @package    IXP\Http\Controllers\Api\V4
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class RouterController extends Controller
{
    /**
     * Generate a configuration.
     *
     * This just takes one argument: the router handle to generate the configuration for.
     *
     * @param string $handle
     *
     * @return Response
     *
     * @throws
     */
    public function genConfig( string $handle ): Response
    {
        if( !( $router = Router::whereHandle( $handle )->first() ) ) {
            abort( 404, "Unknown router handle" );
        }

        $configView = ( new RouterConfigurationGenerator( $router ) )->render()->render();

        /** @psalm-suppress UndefinedConstant */
        \Illuminate\Support\Facades\Log::info( sprintf( "Generated router configuration for %s and used %0.1f MB ( %0.1f MB real) of memory in %0.3f seconds.",
                $router->handle, memory_get_peak_usage() / 1024 / 1024, memory_get_peak_usage( true ) / 1024 / 1024,
                microtime( true ) - LARAVEL_START )
        );

        return response( $configView, 200 )
            ->header('Content-Type', 'text/plain');
    }


    /**
     *
     * @param string $handle Handle of the router that we want
     *
     * @return JsonResponse
     */
    public function getCanUpdate( string $handle ) : JsonResponse
    {
        if( !( $r = Router::whereHandle( $handle )->first() ) ) {
            abort( 404, "Unknown router handle" );
        }

        return response()->json();
        //return response()->json( $this->lastUpdatedArray( $r ) );
    }

    /**
     * Get 'last_updated' for the router with the handle provided
     *
     * Returns the JSON version of the array:
     *
     *     [
     *         'last_update_started'      => '2017-05-23T13:50:25+00:00',
     *         'last_update_started_unix' => 1495547425
     *         'last_updated'             => '2017-05-23T13:50:45+00:00',
     *         'last_updated_unix'        => 1495547445
     *     ]
     *
     * @param string $handle Handle of the router that we want
     *
     * @return JsonResponse
     */
    public function getLastUpdated( string $handle ) : JsonResponse
    {
        if( !( $r = Router::whereHandle( $handle )->first() ) ) {
            abort( 404, "Unknown router handle" );
        }

        return response()->json( $this->lastUpdatedArray( $r ) );
    }

    /**
     * Set `last_update_started` to the current datetime (now) if
     * we can update the router.
     *
     * @param string $handle Handle of the router that we want
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|JsonResponse|Response
     */
    public function getUpdateLock( string $handle )
    {
        if( !( $r = Router::whereHandle( $handle )->first() ) ) {
            abort( 404, "Unknown router handle" );
        }

        if( $r->canUpdate( true ) ) {
            $r->refresh();
            return response()->json( $this->lastUpdatedArray( $r ) );
        }

        return response( 'Router not available for update', 423 )
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Set `last_updated` to the current datetime (now)
     *
     * @param string $handle Handle of the router that we want
     *
     * @return JsonResponse
     */
    public function setLastUpdated( string $handle ): JsonResponse
    {
        if( !( $r = Router::whereHandle( $handle )->first() ) ) {
            abort( 404, "Unknown router handle" );
        }

        $r->update( [ 'last_updated' => now() ] );

        return response()->json( $this->lastUpdatedArray( $r ) );
    }

    /**
     * Get 'last_updated' for all routers
     *
     * Returns the JSON version of the array:
     *
     *     [
     *         'handle' => [
     *             'last_updated'      => '2017-05-23T13:50:45+00:00',
     *             'last_updated_unix' => 1495547445
     *          ],
     *          ...
     *     ]
     *
     * @return JsonResponse
     */
    public function getAllLastUpdated(): JsonResponse
    {
        $result = [];
        foreach( Router::all() as $r ) {
            $result[ $r->handle ] = $this->lastUpdatedArray( $r );
        }
        return response()->json( $result );
    }

    /**
     * Get 'last_updated' for all routers where the last updated time exceeds the given number of seconds
     *
     * Returns the JSON version of the array:
     *
     *     [
     *         'handle' => [
     *             'last_updated'      => '2017-05-23T13:50:45+00:00',
     *             'last_updated_unix' => 1495547445
     *          ],
     *          ...
     *     ]
     *
     * @param int $threshold
     *
     * @return JsonResponse
     */
    public function getAllLastUpdatedBefore( int $threshold ): JsonResponse
    {
        $result = [];
        foreach( Router::all() as $r ) {
            if( $r->last_updated && $r->lastUpdatedGreaterThanSeconds( $threshold ) ) {
                $result[ $r->handle ] = $this->lastUpdatedArray( $r );
            }
        }

        return response()->json( $result );
    }

    /**
     * Find any routers that are stuck in a configuration upgrade / locked for longer than $threashold seconds
     *
     * Returns the JSON version of the array:
     *
     *     [
     *         "handle" => [
     *             "last_update_started"      => "2024-05-23T19:55:29+01:00",
     *             "last_update_started_unix" => 1716490529,
     *             "last_updated"             => '2024-05-23T19:55:28+01:00',
     *             "last_updated_unix"        => 1716490528
     *          ],
     *          ...
     *     ]
     *
     * @param int $threshold
     *
     * @return JsonResponse
     */
    public function getAllLockedLongerThan( int $threshold ): JsonResponse
    {
        $result = [];
        foreach( Router::all() as $r ) {

            if( $r->pause_updates ) {
                continue; // skip paused routers
            }

            if( !$r->last_update_started && !$r->last_updated ) {
                continue; // never updated / never used
            }

            if( $r->last_update_started && $r->last_updated && $r->last_updated->gte( $r->last_update_started ) ) {
                continue;
            }

            if( !$r->last_updated && $r->last_update_started->diffInSeconds( Carbon::now() ) > $threshold ) {
                $result[ $r->handle ] = $this->lastUpdatedArray( $r );
                continue;
            }

            if( $r->last_updated && $r->last_updated->diffInSeconds( $r->last_update_started ) >= $threshold ) {
                $result[ $r->handle ] = $this->lastUpdatedArray( $r );
            }

        }

        return response()->json( $result );
    }

    /**
     * Format the router's last updated datetime as an array
     *
     * @param Router $r
     *
     * @return array
     */
    private function lastUpdatedArray( Router $r ): array
    {
        return [
            'last_update_started'      => $r->last_update_started ? $r->last_update_started->toIso8601String() : null,
            'last_update_started_unix' => $r->last_update_started ? $r->last_update_started->timestamp : null,

            'last_updated'             => $r->last_updated ? $r->last_updated->toIso8601String() : null,
            'last_updated_unix'        => $r->last_updated ? $r->last_updated->timestamp : null,
        ];
    }
}