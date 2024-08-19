<?php

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
use IXP\Services\Diagnostics\Suite;

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

class VirtualInterfaceDiagnosticSuite extends Suite
{
    public function __construct(
        private readonly VirtualInterface $vi,
    ) {
        $this->name        = 'Virtual Interface #' . $vi->id;
        $this->description = "Virtual interfaces general diagnostics.";
        $this->type        = 'INTERFACE';
    }

    /**
     * Run the diagnostics suite
     */
    public function run(): VirtualInterfaceDiagnosticSuite
    {
        $this->results = [];

        $this->results[] = $this->portType();
        $this->results[] = $this->sameSwitch();
        $this->results[] = $this->lag();
//
//        $this->results[] = $this->virtualInterfaceVlanTrunk();
//
//        if($this->vi->lag_framing) {
//            $this->results[] = $this->virtualInterfaceLagFraming();
//        }
//
//        $this->results[] = $this->virtualInterfaceMtu($this->vi);

        return $this;
    }


    /**
     * Examine the virtual interface type and provide information on it.
     *
     * @return DiagnosticResult
     */
    private function portType(): DiagnosticResult {

        $mainName = 'Switch Port Type(s)';

        if( $this->vi->physicalInterfaces->isEmpty() ) {

            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_ERROR,
                narrative: "Can not determine type as there are no physical interfaces for this virtual interface.",
            );

        }

        $piTypes = [];
        foreach( $this->vi->physicalInterfaces as $pi ) {
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

        if( $this->vi->typePeering() ) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_GOOD,
                narrative: "The physical interface(s) type is: peering",
            );
        }

        return new DiagnosticResult(
            name: $mainName,
            result: DiagnosticResult::TYPE_WARN,
            narrative: "The physical interface(s) type is: " . $this->vi->resolveType(),
        );

    }


    /**
     * Examine the virtual interface physical interfaces position in switch and provide information on it.
     *
     * @return DiagnosticResult
     */
    private function sameSwitch(): DiagnosticResult {
        $mainName = 'Physical Interfaces on the Same Switch';

        if( count( $this->vi->physicalInterfaces ) <= 1 ) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_TRACE,
                narrative: "N/A as this virtual interface does not have multiple physical interfaces.",
            );
        }

        if( $this->vi->sameSwitchForEachPI() ) {
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
     * @return DiagnosticResult
     */
    private function lag(): DiagnosticResult {

        $mainName = 'LAG/LACP Configuration';

        $countPis = count( $this->vi->physicalInterfaces );
        $lacpMode = $this->vi->fastlacp ? 'fast' : 'slow';

        if( $countPis > 1 ) {

            if( $this->vi->lag_framing ) {

                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_INFO,
                    narrative: "Configured as a LAG with " . $lacpMode . " timeout",
                );

            } else {   // !$this->vi->lag_framing

                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_ERROR,
                    narrative: "Multiple physical interfaces but not configured as a LAG",
                );
            }

        } else if( $countPis === 1 ) {

            if( $this->vi->lag_framing ) {

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
     * Examine the virtual interface VLAN and Trunks and provide information on it.
     *
     * @param VirtualInterface $vi
     * @return DiagnosticResult
     */
    private function virtualInterfaceVlanTrunk(VirtualInterface $vi): DiagnosticResult {
        $mainName = 'VLANs vs Trunks';
        $countVL = count($vi->vlanInterfaces);

        if ($countVL > 1 && !$vi->trunk) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_ERROR,
                narrative: "Multiple vlan interfaces exists but not configured as a trunk port",
            );
        } else if ($countVL > 1 && $vi->trunk) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_INFO,
                narrative: "Configured as a trunk port",
            );
        } else if ($countVL === 1 && $vi->trunk) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_INFO,
                narrative: "Configured as a trunk port but only one vlan interface exists",
            );
        } else if ($countVL === 1 && !$vi->trunk) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_ERROR,
                narrative: "Single vlan interface but not configured as a trunk port",
            );
        } else {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_WARN,
                narrative: "No VLAN interfaces configured for this connection",
            );
        }

    }


    /**
     * Examine the virtual interface LAG framing and provide information on it.
     *
     * @param VirtualInterface $vi
     * @return DiagnosticResult
     */
    private function virtualInterfaceLagFraming(VirtualInterface $vi): DiagnosticResult {
        $mainName = 'LAG Framing';

        if(!$vi->name || !$vi->channelgroup) {
            $narratives = [];
            if(!$vi->name) {
                $narratives[] = "no name";
            } else if(!$vi->channelgroup) {
                $narratives[] = "no channelgroup";
            }
            $narrative = implode(' and ', $narratives);
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_ERROR,
                narrative: "LAG framing set but " . $narrative,
            );
        } else {
            $bundleName = $vi->bundleName();
            $bundleNamesCount = VirtualInterface::selectRaw("COUNT( CONCAT('name','channelgroup') ) as bundleCount")
                ->whereRaw("CONCAT('name','channelgroup') = '$bundleName' ")->first()->bundleCount;

            if($bundleNamesCount > 1) {
                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_ERROR,
                    narrative: "LAG interface configured as: <b>" . $bundleName . "</b> but it isn't unique",
                );
            } else {
                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_INFO,
                    narrative: "LAG interface configured as: <b>" . $bundleName . "</b> and it is unique",
                );
            }
        }

    }


    /**
     * Examine the virtual interface MTU values and provide information on it.
     *
     * @param VirtualInterface $vi
     * @return DiagnosticResult
     */
    private function virtualInterfaceMtu(VirtualInterface $vi): DiagnosticResult {
        $mainName = 'MTU';

        $viMtu = $vi->mtu;

        if(is_null($viMtu) || $viMtu === 1500 || $viMtu === 9000) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_INFO,
                narrative: "Mtu is set to " . (is_null($viMtu) ? 'null' : $viMtu),
            );
        } else if ($viMtu < 1500) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_ERROR,
                narrative: "Mtu is lower than 1500",
            );
        } else {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_WARN,
                narrative: "Non-standard mtu is set to " . $viMtu,
            );
        }

    }


}