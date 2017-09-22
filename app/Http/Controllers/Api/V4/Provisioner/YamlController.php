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

use Entities\Switcher as SwitcherEntity;

use IXP\Tasks\Yaml\SwitchConfigurationGenerator as SwitchConfigurationGenerator;

/**
 * YamlController
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   APIv4
 * @package    IXP\Http\Controllers\Api\V4\Provisioner
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class YamlController extends Controller {

    /**
     * Generate a Yaml configuration file for a given switchid
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
                ->header('Content-Type', 'text/plain; charset=utf-8');
    }

    /**
     * Generate a Yaml configuration file for a given switchid
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

    /**
     * Generate a Yaml file for a given vlanid
     *
     * This just takes one argument: the router handle to generate the configuration for. All
     * other parameters are defined by the handle's array in config/router.php.
     *
     * @return View
     */
    public function vlanForSwitch( int $sid ) {
        return view( 'api/v4/provisioner/yaml/vlanForSwitch' )->with([
            'sList'         =>          D2EM::getRepository(SwitcherEntity::class )->getAllVlan( $sid )
        ]);
    }

    /**
     * Generate a Yaml file of the core link interfaces for a given switch id
     *
     * This just takes one argument: the router handle to generate the configuration for. All
     * other parameters are defined by the handle's array in config/router.php.
     *
     * @return View
     */
    public function coreLinkForSwitch( int $switchid ) {

        /** @var \Entities\Switcher $switch */
        if( !( $switch = D2EM::getRepository('Entities\Switcher')->find( $switchid ) ) ) {
            abort( 404, "Unknown switchID" );
        }

        $listCis = D2EM::getRepository(SwitcherEntity::class )->getAllCoreLinkInterfaces( $switch->getId() );

        return view( 'api/v4/provisioner/yaml/interfacesIp' )->with([
            'cis'         => $listCis,
            'switch'      => $switch
        ]);

    }

    /**
     * Generate a Yaml file of the core link interfaces for a given switch name
     *
     * This just takes one argument: the router name to generate the configuration for. All
     * other parameters are handled by the coreLinkForSwitch() function.
     *
     * @return View
     */
    public function coreLinkForSwitchByName( string $switchname ) {

        if( !( $switch = D2EM::getRepository('Entities\Switcher')->findOneBy(['name' => $switchname]) ) ) {
            abort( 404, "Unknown switch" );
        }

        return $this->coreLinkForSwitch( $switch->getId() );
    }


    /**
     * Generate a Yaml file of the BGP for a given switch id
     *
     * This just takes one argument: the router handle to generate the configuration for. All
     * other parameters are defined by the handle's array in config/router.php.
     *
     * @return View
     */
    public function bgpForSwitch( int $switchid ) {

        /** @var \Entities\Switcher $switch */
        if( !( $switch = D2EM::getRepository(SwitcherEntity::class )->find( $switchid ) ) ) {
            abort( 404, "Unknown switchID" );
        }

        $listFlood = D2EM::getRepository(SwitcherEntity::class )->getFloodList( $switch->getId(), true );

        $listNeighbors = D2EM::getRepository(SwitcherEntity::class )->getAllNeighbors( $switch->getId() );

        $listVls = D2EM::getRepository(SwitcherEntity::class )->getAllVlanInInsfrascture( $switch->getId() );

        return view( 'api/v4/provisioner/yaml/bgp' )->with([
            'neighbors'             => $listNeighbors,
            'floods'                => $listFlood,
            'vls'                   => $listVls,
            'switch'                => $switch
        ]);
    }

    /**
     * Generate a Yaml file of the BGP for a given switch name
     *
     * This just takes one argument: the router name to generate the configuration for. All
     * other parameters are handled by the coreLinkForSwitch() function.
     *
     * @return View
     */
    public function bgpForSwitchByName( string $switchname ) {

        if( !( $switch = D2EM::getRepository(SwitcherEntity::class )->findOneBy(['name' => $switchname]) ) ) {
            abort( 404, "Unknown switch" );
        }

        return $this->bgpForSwitch( $switch->getId() );
    }

}
