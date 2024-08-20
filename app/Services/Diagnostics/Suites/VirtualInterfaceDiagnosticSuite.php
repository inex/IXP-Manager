<?php
/** @noinspection UnknownColumnInspection */
/** @noinspection UnknownTableOrViewInspection */

namespace IXP\Services\Diagnostics\Suites;

/*
 * Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use IXP\Models\SwitchPort;
use IXP\Models\VirtualInterface;
use IXP\Services\Diagnostics\DiagnosticResult;
use IXP\Services\Diagnostics\DiagnosticSuite;

/**
 * Diagnostics Service - Virtual Interfaces Suite
 *
 * @author     Barry O'Donovan  <barry@opensolutions.ie>
 * @author     Laszlo Kiss      <laszlo@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Services\Diagnostics
 * @copyright  Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */

class VirtualInterfaceDiagnosticSuite extends DiagnosticSuite
{
    public function __construct(
        private readonly VirtualInterface $vi,
    ) {
        $this->name        = 'Virtual Interface #' . $vi->id;
        $this->description = "Virtual interfaces general diagnostics.";
        $this->type        = 'INTERFACE';

        parent::__construct();
    }

    /**
     * Run the diagnostics suite
     */
    public function run(): VirtualInterfaceDiagnosticSuite
    {
        $this->results->add( $this->portType( $this->vi ) );
        $this->results->add( $this->sameSwitch( $this->vi ) );
        $this->results->add( $this->lag( $this->vi ) );
        $this->results->add( $this->lagName( $this->vi ) );
        $this->results->add( $this->trunk( $this->vi ) );
        $this->results->add( $this->mtu( $this->vi ) );

        return $this;
    }


    /**
     * Examine the virtual interface type and provide information on it.
     *
     * @param VirtualInterface $vi
     * @return DiagnosticResult
     */
    public function portType( VirtualInterface $vi ): DiagnosticResult {

        $mainName = 'Switch Port Type(s)';

        if( $vi->physicalInterfaces->isEmpty() ) {

            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_ERROR,
                narrative: "Can not determine type as there are no physical interfaces for this virtual interface.",
            );

        }

        $piTypes = [];
        foreach( $vi->physicalInterfaces as $pi ) {
            $piTypes[] = $pi->switchPort->type;
        }

        $piTypes = array_unique($piTypes);

        if( count($piTypes) > 1 ) {

            $piTypesDesc = [];
            foreach( $piTypes as $piType ) {
                $piTypesDesc[] = SwitchPort::$TYPES[ $piType ];
            }

            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_ERROR,
                narrative: "There are multiple physical interfaces but they have mixed types: " . implode(', ', $piTypesDesc),
            );
        }

        if( $vi->typePeering() ) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_GOOD,
                narrative: "The physical interface(s) type is: peering",
            );
        }

        return new DiagnosticResult(
            name: $mainName,
            result: DiagnosticResult::TYPE_WARN,
            narrative: "The physical interface(s) type is: " . $vi->resolveType(),
        );

    }


    /**
     * Examine the virtual interface physical interfaces position in switch and provide information on it.
     *
     * @param VirtualInterface $vi
     * @return DiagnosticResult
     */
    public function sameSwitch( VirtualInterface $vi ): DiagnosticResult {
        $mainName = 'Physical Interfaces on the Same Switch';

        if( count( $vi->physicalInterfaces ) <= 1 ) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_TRACE,
                narrative: "N/A as this virtual interface does not have multiple physical interfaces.",
            );
        }

        if( $vi->sameSwitchForEachPI() ) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_DEBUG,
                narrative: "The physical interfaces of this virtual interface are connected to the same switch",
            );
        }

        return new DiagnosticResult(
            name: $mainName,
            result: DiagnosticResult::TYPE_ERROR,
            narrative: "The physical interfaces are on different switches",
        );
    }


    /**
     * Examine the virtual interface LAG and provide information on it.
     *
     * @param VirtualInterface $vi
     * @return DiagnosticResult
     */
    public function lag( VirtualInterface $vi ): DiagnosticResult {

        $mainName = 'LAG/LACP Configuration';

        $numPis = count( $vi->physicalInterfaces );
        $lacpMode = $vi->fastlacp ? 'fast' : 'slow';

        if( $numPis > 1 ) {

            if( $vi->lag_framing ) {

                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_INFO,
                    narrative: "Configured as a LAG with " . $lacpMode . " timeout",
                );

            } else {   // !$vi->lag_framing

                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_ERROR,
                    narrative: "Multiple physical interfaces but not configured as a LAG",
                );
            }

        } else if( $numPis === 1 ) {

            if( $vi->lag_framing ) {

                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_INFO,
                    narrative: "Configured as a single member LAG with " . $lacpMode . " timeout",
                );

            } else {       // !$this->vi->lag_framing

                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_TRACE,
                    narrative: "Single physical interface and not configured as a LAG",
                );
            }
        }

        // no physical interfaces
        return new DiagnosticResult(
            name: $mainName,
            result: DiagnosticResult::TYPE_TRACE,
            narrative: "No physical interfaces configured for this connection",
        );
    }


    /**
     * Examine the virtual interface LAG framing and provide information on it.
     *
     * @param VirtualInterface $vi
     * @return DiagnosticResult
     */
    public function lagName(VirtualInterface $vi): DiagnosticResult {

        $mainName = 'LAG Interface Naming';

        if( !$vi->lag_framing && count( $vi->physicalInterfaces ) <= 1 ) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_TRACE,
                narrative: "N/A - LAG framing not set and <= 1 physical interface",
            );
        }

        if( !$vi->name || !$vi->channelgroup ) {

            $narratives = [];

            if(!$vi->name) {
                $narratives[] = "no name";
            } else if(!$vi->channelgroup) {
                $narratives[] = "no channel group number";
            }

            $narrative = implode(', ', $narratives);

            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_ERROR,
                narrative: "LAG framing required but " . $narrative . " defined on the virtual interface",
            );
        }


        $switchIds = SwitchPort::join( 'physicalinterface', 'switchport.id', '=', 'physicalinterface.switchportid' )
            ->join( 'virtualinterface', 'virtualinterface.id', '=', 'physicalinterface.virtualinterfaceid' )
            ->where( 'virtualinterface.id', $vi->id )
            ->pluck('switchport.switchid');

        $bundleNamesCount = VirtualInterface::select('virtualinterface.id')
            ->where( 'virtualinterface.name', $vi->name )
            ->where( 'virtualinterface.channelgroup', $vi->channelgroup )
            ->join( 'physicalinterface', 'virtualinterface.id', '=', 'physicalinterface.virtualinterfaceid' )
            ->join( 'switchport', 'physicalinterface.switchportid', '=', 'switchport.id' )
            ->whereIn( 'switchport.switchid', $switchIds )
            ->groupBy('virtualinterface.id')
            ->get()->toArray();

        if( count($bundleNamesCount) > 1 ) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_ERROR,
                narrative: "LAG interface bundle name (" . $vi->bundleName() . ") is not unique for the switch",
            );
        } else {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_DEBUG,
                narrative: "LAG interface bundle name (" . $vi->bundleName() . ") is unique for the switch",
            );
        }
    }


    /**
     * Examine the virtual interface VLAN and Trunks and provide information on it.
     *
     * @param VirtualInterface $vi
     * @return DiagnosticResult
     */
    public function trunk( VirtualInterface $vi ): DiagnosticResult {
        $mainName = 'Trunk / 802.1q Configuration';

        $numVlis = count( $vi->vlanInterfaces );

        if( $numVlis > 1 ) {

            if( $vi->trunk ) {
                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_DEBUG,
                    narrative: "More than one vlan interface and so correctly configured as a trunk port",
                );

            } else {   // !$vi->trunk

                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_ERROR,
                    narrative: "Multiple vlan interfaces exist but virtual interface is not configured as a trunk port",
                );

            }

        } else if( $numVlis === 1 ) {

            if( $vi->trunk ) {

                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_INFO,
                    narrative: "Configured as a trunk port while there is only one vlan interface",
                );

            } else {     // !$vi->trunk

                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_DEBUG,
                    narrative: "Single vlan interface and not configured as a trunk port",
                );

            }
        }

        return new DiagnosticResult(
            name: $mainName,
            result: DiagnosticResult::TYPE_WARN,
            narrative: "No VLAN interfaces configured for this virtual interface",
        );

    }



    /**
     * Examine the virtual interface MTU values and provide information on it.
     *
     * @param VirtualInterface $vi
     * @return DiagnosticResult
     */
    public function mtu(VirtualInterface $vi): DiagnosticResult {
        $mainName = 'L2 MTU';

        if(is_null($vi->mtu) || $vi->mtu === 1500 || $vi->mtu >= 9000) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_DEBUG,
                narrative: "L2 MTU is set to " . ( $vi->mtu ?: 'null' ),
            );
        } else if ($vi->mtu < 1500) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_ERROR,
                narrative: "L2 MTU on virtual interface is <1500 (" . $vi->mtu . ")",
            );
        }

        return new DiagnosticResult(
            name: $mainName,
            result: DiagnosticResult::TYPE_WARN,
            narrative: "Non-standard L2 MTU on the virtual interface: " . $vi->mtu,
        );
    }


}