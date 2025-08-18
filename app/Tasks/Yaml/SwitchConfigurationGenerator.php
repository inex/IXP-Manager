<?php

namespace IXP\Tasks\Yaml;

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

use IXP\Models\{
    PhysicalInterface,
    Switcher,
    VirtualInterface
};

/**
 * ConfigurationGenerator
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   Tasks
 * @package    IXP\Tasks\Router
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SwitchConfigurationGenerator
{
    /**
     *
     * @var Switcher
     */
    private $switch = null;

    public function __construct( Switcher $switch )
    {
        $this->setSwitch( $switch );
    }

    /**
     * Set the switch
     *
     * @param Switcher $switch
     */
    public function setSwitch( Switcher $switch ): static
    {
        $this->switch = $switch;
        return $this;
    }

    /**
     * Get the switch options array
     *
     * @return Switcher
     */
    public function getSwitch(): Switcher
    {
        return $this->switch;
    }

    /**
     * Generate and return the configuration
     *
     * @return (((((mixed|null|string)[]|int|mixed|null)[]|mixed|null|string)[]|bool|int|mixed|null|string)[]|mixed)[][]
     *
     * @throws
     *
     * @psalm-return array{layer2interfaces: list{0?: array{type: 'core'|'edge', description: null|string, dot1q: bool|null, stp?: bool, cost?: int|null, preference?: int|null, virtualinterfaceid: int|null, corebundleid?: int, lagframing: bool|null, lagindex?: int, vlans: list{0?: array{number: int|mixed|null, macaddresses: list{0?: mixed|null|string,...}, vlaninterfaceid?: int, ipaddresses?: array{ipv4?: null|string, ipv6?: null|string}}|mixed,...}, name: null|string, lagmaster?: bool, fastlacp?: bool, lagmembers?: list<null|string>, shutdown: bool, autoneg?: bool, speed?: int|null, status?: mixed|string, rate_limit?: int|null}|mixed,...}}
     */
    public function generate(): array
    {
        $ports = [];
        $visProcessed = [];
        $cbsProcessed = [];

        foreach( $this->getSwitch()->switchPorts as $sp ) {
            // is the port in use?
            if( !( $pi = $sp->physicalInterface ) ) {
                continue;
            }

            // don't emit ports which aren't ready for production
            if( $pi->statusAwaitingXConnect() ) {
                continue;
            }

            if( ( $sp->typeUnset() || $sp->typePeering() ) && !in_array( $pi->virtualinterfaceid, $visProcessed )  ) {
                $ports = array_merge( $ports, $this->processVirtualInterface( $pi->virtualInterface ));
                $visProcessed[] = $pi->virtualinterfaceid;
            } else if( $sp->typeCore() && !in_array( $pi->virtualinterfaceid, $cbsProcessed ) && $pi->coreBundle() && $pi->coreBundle()->typeL2Lag() ) {
                $ports = array_merge( $ports, $this->processCoreBundleInterface( $pi ) );
                $cbsProcessed[] = $pi->virtualinterfaceid;
            }
        }
        return array( 'layer2interfaces' => $ports );
    }

    /**
     * @param VirtualInterface $vi
     *
     * @return ((((null|string)[]|int|null)[]|null|string)[]|bool|int|mixed|null|string)[][]
     *
     * @psalm-return list{0?: array{type: 'edge', description: null|string, dot1q: bool|null, virtualinterfaceid: int, lagframing: bool, lagindex?: int, vlans: non-empty-list<array{ipaddresses: array{ipv4?: null|string, ipv6?: null|string}, macaddresses: list<null|string>, number: int|null, vlaninterfaceid: int}>, name: null|string, lagmaster?: bool, fastlacp?: bool, lagmembers?: list<null|string>, shutdown: bool, status: mixed|string, autoneg?: bool, speed?: int|null, rate_limit?: int|null}, 1?: array{type: 'edge', description: null|string, dot1q: bool|null, virtualinterfaceid: int, lagframing: bool, lagindex?: int, vlans: non-empty-list<array{ipaddresses: array{ipv4?: null|string, ipv6?: null|string}, macaddresses: list<null|string>, number: int|null, vlaninterfaceid: int}>, name: null|string, lagmaster: bool, fastlacp: bool, shutdown: bool, status: mixed|string, autoneg: bool, speed: int|null, rate_limit: int|null, lagmembers?: list<null|string>},...}
     */
    private function processVirtualInterface( VirtualInterface $vi ): array
    {
        $p                       = [];
        $p['type']               = 'edge';
        $p['description']        = $vi->customer->abbreviatedName;
        $p['dot1q']              = $vi->trunk;
        $p['virtualinterfaceid'] = $vi->id;
        $p['lagframing']         = $vi->lag_framing;
        if( $vi->channelgroup ) {
            $p['lagindex'] = $vi->channelgroup;
        }

        $p['vlans'] = [];

        foreach( $vi->vlanInterfaces as $vli ) {
            $v = [];
            $v[ 'number' ] = $vli->vlan->number;
            $v[ 'vlaninterfaceid' ] = $vli->id;
            
            $v[ 'macaddresses' ] = [];
            foreach( $vli->layer2addresses as $mac ) {
                $v[ 'macaddresses' ][] = $mac->macFormatted( ':' );
            }

            $v['ipaddresses'] = [];

            if( $vli->ipv4enabled && $vli->ipv4address ) {
                $v[ 'ipaddresses' ][ 'ipv4' ] = $vli->ipv4address->address;
            }

            if( $vli->ipv6enabled && $vli->ipv6address ) {
                $v[ 'ipaddresses' ]['ipv6'] = $vli->ipv6address->address;
            }
            $p[ 'vlans' ][] = $v;
        }

        // return nothing if there are no vlans defined on the port
        if( empty( $p[ 'vlans' ] ) ) {
            return [];
        }

        // we now have the base port config. If this is not a LAG, just return it:
        if( !$vi->lag_framing ) {
            /** @var PhysicalInterface $pi */
            $pi = $vi->physicalInterfaces()->first();
            $p['shutdown']           = !$pi->isConnectedOrQuarantine();
            $p['status']             = $pi->apiStatus();
            $p['name']               = $pi->switchPort->ifName;
            $p['speed']              = $pi->speed;
            $p['rate_limit']         = $pi->rate_limit;
            $p['autoneg']            = $pi->autoneg;
            return [ $p ];
        }

        $ports = [];

        // bundle definition:
        $p['name']      = $vi->bundleName();
        $p['lagmaster'] = true;
        $p['fastlacp']  = $vi->fastlacp;
        $p['lagmembers']= [];
        $p['shutdown']  = true;
        $p['status']    = PhysicalInterface::$APISTATES[ PhysicalInterface::STATUS_NOTCONNECTED ];

        $lagquarantinestatus = true;
        // build up list of physical ports associated with this lag master
        foreach( $vi->physicalInterfaces as $pi ) {
            if( !$pi->switchPort ) {
                continue;
            }
            $p[ 'lagmembers' ][] = $pi->switchPort->ifName;

            // if any bundle members are up, the LAG is up
            if( $pi->isConnectedOrQuarantine() ) {
                $p['shutdown'] = false;
            }
            // if any bundle members are connected then status is connected
            // if all bundle members are quarantine then status is quarantine
            // otherwise status remains as default: notconnected
            if( $pi->statusConnected() ) {
                $p['status'] = $pi->apiStatus();
            }
            if( $pi->status !== PhysicalInterface::STATUS_QUARANTINE ) {
                $lagquarantinestatus = false;
            }
        }

        if ($lagquarantinestatus) {
            $p['status'] = PhysicalInterface::$APISTATES[ PhysicalInterface::STATUS_QUARANTINE ];
        }

        $ports[]        = $p;

        unset( $p['lagmembers'] );

        // interface definitions:
        foreach( $vi->physicalInterfaces as $pi ) {
            if( !$pi->switchPort ) {
                continue;
            }
            $p['shutdown']   = !$pi->isConnectedOrQuarantine();
            $p['status']     = $pi->apiStatus();
            $p['name']       = $pi->switchPort->ifName;
            $p['lagmaster']  = false;
            $p['autoneg']    = $pi->autoneg;
            $p['speed']      = $pi->speed;
            $p['rate_limit'] = $pi->rate_limit;
            $ports[] = $p;
        }

        return $ports;
    }

    /**
     * @param PhysicalInterface $pi
     *
     * @return (((array|mixed)[]|null|string)[]|bool|int|null|string)[][]
     *
     * @psalm-return list{0: array{type: 'core', description: string, dot1q: bool|null, stp: bool, cost: int|null, preference: int|null, virtualinterfaceid: int|null, corebundleid: int, lagframing: bool|null, lagindex?: int, vlans: list{0?: array{number: mixed, macaddresses: array<never, never>},...}, name: null|string, lagmaster?: bool, fastlacp?: bool, lagmembers?: list<null|string>, shutdown: bool, autoneg?: bool, speed?: int|null}, 1?: array{type: 'core', description: string, dot1q: bool|null, stp: bool, cost: int|null, preference: int|null, virtualinterfaceid: int|null, corebundleid: int, lagframing: bool|null, lagindex?: int, vlans: list{0?: array{number: mixed, macaddresses: array<never, never>},...}, name: null|string, lagmaster: bool, fastlacp: bool, shutdown: bool, autoneg: bool, speed: int|null, lagmembers?: list<null|string>},...}
     */
    private function processCoreBundleInterface( PhysicalInterface $pi ): array
    {
        $vi = $pi->virtualInterface;
        $ci = $pi->coreInterface;
        $cl = $ci->coreLink();
        $cb = $cl->coreBundle;

        // side a or side b?
        if( $cl->core_interface_sidea_id === $ci->id ) {
            $sideFn = 'coreInterfaceSideA';
        } else {
            $sideFn = 'coreInterfaceSideB';
        }

        $p                       = [];
        $p['type']               = 'core';
        $p['description']        = $cb->description;
        $p['dot1q']              = $vi->trunk;
        $p['stp']                = $cb->stp;
        $p['cost']               = $cb->cost;
        $p['preference']         = $cb->preference;
        $p['virtualinterfaceid'] = $vi->id;
        $p['corebundleid']       = $cb->id;
        $p['lagframing']         = $vi->lag_framing;
        if( $vi->channelgroup ) {
            $p['lagindex'] = $vi->channelgroup;
        }

        $p['vlans'] = [];

        foreach( $pi->switchPort->switcher->infrastructureModel->vlans as $vlan ) {
            $v = [];
            $v[ 'number' ]          = $vlan->number;
            $v[ 'macaddresses' ]    = [];
            $p[ 'vlans' ][]         = $v;
        }

        // we now have the base port config. If this is not a LAG, just return it:
        if( !$vi->lag_framing ) {
            $p['shutdown']           = !$pi->isConnectedOrQuarantine();
            $p['name']               = $pi->switchPort->ifName;
            $p['speed']              = $pi->speed;
            $p['autoneg']            = $pi->autoneg;
            return [ $p ];
        }

        $ports = [];

        // bundle definition:
        $p['name']      = $vi->bundleName();
        $p['lagmaster'] = true;
        $p['fastlacp']  = $vi->fastlacp;
        $p['lagmembers']= [];
        $p['shutdown']  = true;

        // build up list of physical ports associated with this lag master
        foreach( $cb->corelinks as $_cl ) {
            /** @var PhysicalInterface $_pi */
            $_pi = $_cl->$sideFn->physicalInterface;
            if( !$_pi->switchPort ) {
                continue;
            }
            $p['lagmembers'][]= $_pi->switchPort->ifName;

            // if any bundle members are up, the LAG is up
            if( $_pi->isConnectedOrQuarantine() ) {
                $p['shutdown'] = false;
            }
        }

        // but if the bundle is down, the whole lot is down
        if( !$cb->enabled ) {
            $p['shutdown'] = true;
        }

        $ports[]        = $p;

        unset( $p[ 'lagmembers' ] );

        // interface definitions:
        foreach( $cb->corelinks as $_cl ) {
            /** @var PhysicalInterface $_pi */
            $_pi = $_cl->$sideFn->physicalInterface;

            if( !$_pi->switchPort ) {
                continue;
            }
            $p['shutdown']   = !$pi->isConnectedOrQuarantine();
            $p['name']       = $_pi->switchPort->ifName;
            $p['lagmaster']  = false;
            $p['autoneg']    = $_pi->autoneg;
            $p['speed']      = $_pi->speed;
            $ports[] = $p;
        }

        return $ports;
    }
}