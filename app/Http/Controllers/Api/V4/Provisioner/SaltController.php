<?php

declare(strict_types=1);

namespace IXP\Http\Controllers\Api\V4\Provisioner;

/*
 * Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use D2EM;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use IXP\Http\Controllers\Api\V4\Controller;

use IXP\Tasks\Salt\SwitchConfigurationGenerator as SwitchConfigurationGenerator;

/**
 * SaltController
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   APIv4
 * @package    IXP\Http\Controllers\Api\V4\Provisioner
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SaltController extends Controller {

    /**
     * Generate a Salt configuration file for a given switchid
     *
     * This just takes one argument: the router handle to generate the configuration for. All
     * other parameters are defined by the handle's array in config/router.php.
     *
     * @return Response
     */
    public function forSwitch( Request $request, int $switchid ): Response {

        /** @var \Entities\Switcher $switch */
        if( !( $switch = D2EM::getRepository('Entities\Switcher')->find( $switchid ) ) ) {
            abort( 404, "Unknown switchID" );
        }

        $configView = ( new SwitchConfigurationGenerator( $switch ) );

        return response( $configView->render(), 200 )
                ->header('Content-Type', 'text/html; charset=utf-8');
    }

    /**
     * Generate a Salt configuration file for a given switchid
     *
     * This just takes one argument: the router handle to generate the configuration for. All
     * other parameters are defined by the handle's array in config/router.php.
     *
     * @return Response
     */
    public function forSwitchByname( Request $request, string $switchname ): Response {

        if( !( $switch = D2EM::getRepository('Entities\Switcher')->findOneBy(['name' => $switchname]) ) ) {
            abort( 404, "Unknown switch" );
        }

        return $this->forSwitch( $request, $switch->getId() );
    }

}
