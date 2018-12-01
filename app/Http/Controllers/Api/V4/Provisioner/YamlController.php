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

use Doctrine\ORM\NonUniqueResultException;
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
     * Generate a formatted output version of the given structure.
     *
     * This takes two arguments: the array structure and the output format.
     *
     * @return http response
     */
    public function structuredResponse ( $array, $format ) {

        $output = null;
        $contenttype = 'text/plain; charset=utf-8';
        $httpresponse = 200;

        switch ($format) {
            case 'yaml':
                $output = yaml_emit ( $array, YAML_UTF8_ENCODING );
                break;
            case 'json':
                $output = json_encode ( $array, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )."\n";
                $contenttype = 'application/json';
                break;
        }

        if (!$output) {
            $httpresponse = 200;
        }

        return response( $output, $httpresponse )->header('Content-Type', $contenttype);
    }

    /**
     * Generate a Yaml configuration file for a given switchid
     *
     * This just takes one argument: the router handle to generate the configuration for. All
     * other parameters are defined by the handle's array in config/router.php.
     *
     * @return Response
     */
    public function forSwitch( Request $request, int $switchid, string $format = null ): Response {

        /** @var \Entities\Switcher $switch */
        if( !( $switch = D2EM::getRepository('Entities\Switcher')->find( $switchid ) ) ) {
            abort( 404, "Unknown switchID" );
        }

        return $this->structuredResponse( (new SwitchConfigurationGenerator( $switch ) )->generate(), $format );
    }

    /**
     * Generate a Yaml configuration file for a given switchid
     *
     * This just takes one argument: the router handle to generate the configuration for. All
     * other parameters are defined by the handle's array in config/router.php.
     *
     * @return Response
     */
    public function forSwitchByname( Request $request, string $switchname, string $format = null ): Response {

        if( !( $switch = D2EM::getRepository('Entities\Switcher')->findOneBy(['name' => $switchname]) ) ) {
            abort( 404, "Unknown switch" );
        }

        return $this->forSwitch( $request, $switch->getId(), $format );
    }

    /**
     * Generate a Yaml file of the vlans for a given switch id
     *
     * This takes one argument: the router handle to generate the vlans for. All
     * other parameters are defined by the handle's array in config/router.php.
     *
     * @return View
     */
    public function vlansForSwitch( int $switchid, string $format = null ) {

        /** @var \Entities\Switcher $switch */
        if( !( $switch = D2EM::getRepository(SwitcherEntity::class )->find( $switchid ) ) ) {
            abort( 404, "Unknown switchID" );
        }

        $listVlans['vlans'] = D2EM::getRepository(SwitcherEntity::class )->getAllVlansInInfrastructure( $switch->getId() );

        return $this->structuredResponse( $listVlans, $format );
    }

    /**
     * Generate a Yaml file of the vlans for a given switch name
     *
     * This just takes one argument: the router name to generate the configuration for. All
     * other parameters are handled by the vlansForSwitch() function.
     *
     * @return View
     */
    public function vlansForSwitchByName( string $switchname, string $format = null ) {

        if( !( $switch = D2EM::getRepository(SwitcherEntity::class )->findOneBy(['name' => $switchname]) ) ) {
            abort( 404, "Unknown switch" );
        }

        return $this->vlansForSwitch( $switch->getId(), $format );
    }

    /**
     * Restructure the output from showSwitch.
     *
     * @return Array
     */
    public function showSwitchRestructureOutput( array $data ) {
        $output = [];

        foreach (['name', 'asn', 'hostname', 'loopback_ip', 'loopback_name', 'ipv4addr',
                    'ipv6addr', 'model', 'active', 'os', 'id'] as $key) {
            if (!is_null ($data[$key]) && $data[$key] !== '') {
                $output[$key] = $data[$key];
            }
        }

        if ($data['mgmt_mac_address']) { $output['macaddress']    = implode(':', str_split($data['mgmt_mac_address'], 2)); }
        if ($data['serialNumber'])     { $output['serial']        = $data['serialNumber'];     }
        if ($data['lastPolled'])       { $output['lastpolled']    = $data['lastPolled']->format('c'); }
        if ($data['osVersion'])        { $output['osversion']     = $data['osVersion'];        }
        if ($data['snmppasswd'])       { $output['snmpcommunity'] = $data['snmppasswd'];       }

        return array("switch" => $output);
    }

    /**
     * Generate a Yaml/JSON response for a switch
     *
     * @return View
     */
    public function showSwitch( int $switchid, string $format = null ) {

        /** @var \Entities\Switcher $switch */
        try {
            $switch = D2EM::createQuery( 'SELECT s FROM Entities\Switcher s WHERE s.id = :id' )->setParameter( 'id', $switchid )->getSingleResult( \Doctrine\ORM\Query::HYDRATE_ARRAY );
        } catch( \Doctrine\ORM\NoResultException $e ) {
            abort( 404, "Unknown switch" );
        } catch( NonUniqueResultException $e ) {
            abort( 404, "Unknown switch" );
        }

        return $this->structuredResponse( $this->showSwitchRestructureOutput($switch), $format );
    }

    /**
     * Generate a Yaml/JSON response for a switch
     *
     * @return View
     */
    public function showSwitchByName( string $switchname, string $format = null ) {

        /** @var \Entities\Switcher $switch */
        try {
            $switch = D2EM::createQuery('SELECT s FROM Entities\Switcher s WHERE s.name = :name' )->setParameter('name',$switchname)->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY );
        } catch( \Doctrine\ORM\NoResultException $e ) {
            abort( 404, "Unknown switch" );
        } catch( NonUniqueResultException $e ) {
            abort( 404, "Unknown switch" );
        }

        return $this->structuredResponse( $this->showSwitchRestructureOutput($switch), $format );
    }

    /**
     * Generate a Yaml file of the core link interfaces for a given switch id
     *
     * This just takes one argument: the router handle to generate the configuration for. All
     * other parameters are defined by the handle's array in config/router.php.
     *
     * @return View
     */
    public function coreLinkForSwitch( int $switchid, string $format = null ) {

        /** @var \Entities\Switcher $switch */
        if( !( $switch = D2EM::getRepository('Entities\Switcher')->find( $switchid ) ) ) {
            abort( 404, "Unknown switchID" );
        }

        $interfaces['layer3interfaces'] = array_merge (
            D2EM::getRepository(SwitcherEntity::class )->getAllCoreLinkInterfaces( $switch->getId() ),
            D2EM::getRepository(SwitcherEntity::class )->getLoopbackInfo( $switch->getId() )
        );

        return $this->structuredResponse( $interfaces, $format );
    }

    /**
     * Generate a Yaml file of the core link interfaces for a given switch name
     *
     * This just takes one argument: the router name to generate the configuration for. All
     * other parameters are handled by the coreLinkForSwitch() function.
     *
     * @return View
     */
    public function coreLinkForSwitchByName( string $switchname, string $format = null ) {

        if( !( $switch = D2EM::getRepository('Entities\Switcher')->findOneBy(['name' => $switchname]) ) ) {
            abort( 404, "Unknown switch" );
        }

        return $this->coreLinkForSwitch( $switch->getId(), $format );
    }


    /**
     * Generate a Yaml file of the BGP for a given switch id
     *
     * This just takes one argument: the router handle to generate the configuration for. All
     * other parameters are defined by the handle's array in config/router.php.
     *
     * @return View
     */
    public function bgpForSwitch( int $switchid, string $format = null ) {

        /** @var \Entities\Switcher $switch */
        if( !( $switch = D2EM::getRepository(SwitcherEntity::class )->find( $switchid ) ) ) {
            abort( 404, "Unknown switchID" );
        }

        $listFlood = D2EM::getRepository(SwitcherEntity::class )->getFloodList( $switch->getId(), true );

        $listNeighbors = D2EM::getRepository(SwitcherEntity::class )->getAllNeighbors( $switch->getId() );

        $listAdjacentASNs = D2EM::getRepository(SwitcherEntity::class )->getAdjacentASNInfo( $switch->getId() );

        $out['bgp']['floodlist'] = $listFlood;
        $out['bgp']['adjacentasns'] = $listAdjacentASNs;
        $out['bgp']['routerid'] = $switch->getLoopbackIp();
        $out['bgp']['local_as'] = $switch->getAsn();

        $pgentry = null;
        foreach( $listNeighbors as $neighbor ) {
            $n = [];
            $n['description'] = $neighbor['description'];
            $n['remote_as'] = $neighbor['asn'];
            $n['cost'] = $neighbor['cost'];
            $n['preference'] = $neighbor['preference'];
            $pgentry[$neighbor['ip']] = $n;
        }

        # XXX replace pg-ebgp-ipv4-ixp with dynamic value
        $out['bgp']['out']['pg-ebgp-ipv4-ixp']['neighbors'] = $pgentry;

        return $this->structuredResponse( $out, $format );
    }

    /**
     * Generate a Yaml file of the BGP for a given switch name
     *
     * This just takes one argument: the router name to generate the configuration for. All
     * other parameters are handled by the coreLinkForSwitch() function.
     *
     * @return View
     */
    public function bgpForSwitchByName( string $switchname, string $format = null ) {

        if( !( $switch = D2EM::getRepository(SwitcherEntity::class )->findOneBy(['name' => $switchname]) ) ) {
            abort( 404, "Unknown switch" );
        }

        return $this->bgpForSwitch( $switch->getId(), $format );
    }

}
