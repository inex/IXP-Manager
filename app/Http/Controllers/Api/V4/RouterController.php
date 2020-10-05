<?php

namespace IXP\Http\Controllers\Api\V4;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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
use  D2EM;

use IXP\Models\Router;

use IXP\Tasks\Router\ConfigurationGenerator as RouterConfigurationGenerator;

use Illuminate\Http\{
    JsonResponse,
    Response
};

use Entities\{
    Router as RouterEntity
};

/**
 * RouterController
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   APIv4
 * @package    IXP\Http\Controllers\Api\V4
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
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
        if( !( $router = Router::whereHandle( $handle )->get()->first() ) ) {
            abort( 404, "Unknown router handle" );
        }

        $configView = ( new RouterConfigurationGenerator( $router ) )->render();

        return response( $configView->render(), 200 )
                ->header('Content-Type', 'text/plain; charset=utf-8');
    }

    /**
     * Set `last_updated` to the current datetime (now)
     *
     * @param string $handle Handle of the router that we want
     * @return JsonResponse
     */
    public function setLastUpdated( string $handle ) : JsonResponse {
        if( !( $rt = D2EM::getRepository( RouterEntity::class )->findOneBy( [ 'handle' => $handle ] ) ) ) {
            abort( 404, "Unknown router handle" );
        }

        $rt->setLastUpdated(new \DateTime);
        D2EM::flush();

        return response()->json( $this->getLastUpdatedArray( $rt ) );
    }

    /**
     * Get 'last_updated' for the router with the handle provided
     *
     * Returns the JSON version of the array:
     *
     *     [
     *         'last_updated'      => '2017-05-23T13:50:45+00:00',
     *         'last_updated_unix' => 1495547445
     *     ]
     *
     * @param string $handle Handle of the router that we want
     * @return JsonResponse
     */
    public function getLastUpdated( string $handle ) : JsonResponse {
        /** @var RouterEntity $rt */
        if( !( $rt = D2EM::getRepository( RouterEntity::class )->findOneBy( [ 'handle' => $handle ] ) ) ) {
            abort( 404, "Unknown router handle" );
        }

        return response()->json( $this->getLastUpdatedArray( $rt ) );
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
    public function getAllLastUpdated() : JsonResponse {
        $routers = D2EM::getRepository( RouterEntity::class )->findAll();
        $result = [];

        /** @var RouterEntity $rt */
        foreach( $routers as $rt ) {
            $result[ $rt->getHandle() ] = $this->getLastUpdatedArray( $rt );
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
     * @return JsonResponse
     */
    public function getAllLastUpdatedBefore( int $threshold ) : JsonResponse {
        $routers = D2EM::getRepository( RouterEntity::class )->findAll();
        $result = [];

        /** @var RouterEntity $rt */
        foreach( $routers as $rt ) {
            if( $rt->getLastUpdated() && $rt->lastUpdatedGreaterThanSeconds( $threshold ) ) {
                $result[ $rt->getHandle() ] = $this->getLastUpdatedArray( $rt );
            }
        }

        return response()->json( $result );
    }


    /**
     * Format the router's last updated datetime as an array
     * @param RouterEntity $r
     * @return array
     */
    private function getLastUpdatedArray( RouterEntity $rt ) {
        return [
            'last_updated'      => $rt->getLastUpdated() ? $rt->getLastUpdatedCarbon()->toIso8601String() : null,
            'last_updated_unix' => $rt->getLastUpdated() ? $rt->getLastUpdatedCarbon()->timestamp : null,
        ];
    }
}
