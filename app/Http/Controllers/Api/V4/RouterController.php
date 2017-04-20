<?php

declare(strict_types=1);
namespace IXP\Http\Controllers\Api\V4;

/*
 * Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Illuminate\Http\Response;
use IXP\Tasks\Router\ConfigurationGenerator as RouterConfigurationGenerator;

use Entities\{
    Router as RouterEntity
};

use D2EM;

/**
 * RouterController
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   APIv4
 * @package    IXP\Http\Controllers\Api\V4
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class RouterController extends Controller {

    /**
     * Generate a configuration.
     *
     * This just takes one argument: the router handle to generate the configuration for.
     *
     * @return Response
     */
    public function genConfig( string $handle ): Response {
        if( !( $router = D2EM::getRepository( RouterEntity::class )->findOneBy( [ 'handle' => $handle ] ) ) ) {
            abort( 404, "Unknown router handle" );
        }

        $configView = ( new RouterConfigurationGenerator( $router ) )->render();

        return response( $configView->render(), 200 )
                ->header('Content-Type', 'text/plain; charset=utf-8');
    }

}
