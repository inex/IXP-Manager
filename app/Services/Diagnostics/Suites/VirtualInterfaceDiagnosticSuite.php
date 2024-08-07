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

use IXP\Models\Customer;
use IXP\Models\VirtualInterface;
use IXP\Services\Diagnostics;
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
    public const string DIAGNOSTIC_SUITE_NAME = 'Virtual Interfaces Overview';

    public const string DIAGNOSTIC_SUITE_DESCRIPTION = "Virtual Interfaces overview diagnostics.";

    public const string DIAGNOSTIC_SUITE_TYPE = 'VIRTUAL_INTERFACE';

    private \Illuminate\Database\Eloquent\Collection $virtualInterfaces;

    public function __construct(
        private Customer $customer
    ) {
        $this->virtualInterfaces = $this->customer->virtualInterfaces;
    }

    /**
     * Run the diagnostics suite
     */
    public function run(): VirtualInterfaceDiagnosticSuite
    {
        $this->results = [];

        // ordering here will determine order on view
        foreach ($this->virtualInterfaces as $virtualInterface) {
            $this->results[$virtualInterface["id"]][] = $this->virtualInterfaceType($virtualInterface);

            if(count($virtualInterface->physicalInterfaces) > 0) {
                $this->results[$virtualInterface["id"]][] = $this->virtualInterfaceSameSwitch($virtualInterface);
            }

            $this->results[$virtualInterface["id"]][] = $this->virtualInterfaceLAG($virtualInterface);

            $this->results[$virtualInterface["id"]][] = $this->virtualInterfaceVlanTrunk($virtualInterface);

            if($virtualInterface->lag_framing) {
                $this->results[$virtualInterface["id"]][] = $this->virtualInterfaceLagFraming($virtualInterface);
            }

            $this->results[$virtualInterface["id"]][] = $this->virtualInterfaceMtu($virtualInterface);

            // get the Physical Interface Diagnostics Data and integrate here into the VI array
            if(count($virtualInterface->physicalInterfaces) > 0) {
                $this->results[$virtualInterface["id"]]["PhysicalInterfaceDiagnostics"] = ( new Diagnostics() )->getPhysicalInterfaceDiagnostics($virtualInterface);
            }

        }

        return $this;
    }


    /**
     * Examine the virtual interface type and provide information on it.
     *
     * @param VirtualInterface $virtualInterface
     * @return DiagnosticResult
     */
    private function virtualInterfaceType(VirtualInterface $virtualInterface): DiagnosticResult {
        $mainName = 'Virtual Interface Type';
        if( $virtualInterface->physicalInterfaces->isNotEmpty() ) {
            $piTypes = [];
            foreach($virtualInterface->physicalInterfaces as $physicalInterface) {
                $piTypes[] = $physicalInterface->switchPort->type;
            }

            if(count($piTypes) > 1) {
                $uniqueTypes = array_unique($piTypes);
                if(count($uniqueTypes) > 1) {
                    return new DiagnosticResult(
                        name: $mainName,
                        result: DiagnosticResult::TYPE_WARNING,
                        narrative: "Physical Interfaces are NOT the same type",
                    );
                } else {
                    return new DiagnosticResult(
                        name: $mainName,
                        result: DiagnosticResult::TYPE_OKAY,
                        narrative: "All Physical Interfaces are the same type",
                    );
                }
            } else {
                if($virtualInterface->typePeering()) {
                    return new DiagnosticResult(
                        name: $mainName,
                        result: DiagnosticResult::TYPE_OKAY,
                        narrative: "Peering Interface type",
                    );
                } else {
                    $resolveType = $virtualInterface->resolveType();
                    return new DiagnosticResult(
                        name: $mainName,
                        result: DiagnosticResult::TYPE_INFO,
                        narrative: "Port is type ".$resolveType,
                    );
                }
            }

        }

        return new DiagnosticResult(
            name: $mainName,
            result: DiagnosticResult::TYPE_ERROR,
            narrative: "There are no physical interfaces for this virtual interface.",
        );
    }


    /**
     * Examine the virtual interface physical interfaces position in switch and provide information on it.
     *
     * @param VirtualInterface $virtualInterface
     * @return DiagnosticResult
     */
    private function virtualInterfaceSameSwitch(VirtualInterface $virtualInterface): DiagnosticResult {
        $mainName = 'Physical Interfaces Position';

        if($virtualInterface->sameSwitchForEachPI()) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_OKAY,
                narrative: "The Physical Interfaces are in the same Switch",
            );
        }

        return new DiagnosticResult(
            name: $mainName,
            result: DiagnosticResult::TYPE_ERROR,
            narrative: "The Physical Interfaces are not in the same Switch",
        );
    }


    /**
     * Examine the virtual interface LAG and provide information on it.
     *
     * @param VirtualInterface $virtualInterface
     * @return DiagnosticResult
     */
    private function virtualInterfaceLAG(VirtualInterface $virtualInterface): DiagnosticResult {
        $mainName = 'LAG';
        $countPI = count($virtualInterface->physicalInterfaces);
        $lastLACP = $virtualInterface->fastlacp ? 'fast' : 'slow';

        if ($countPI > 1 && !$virtualInterface->lag_framing) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_ERROR,
                narrative: "Multiple physical interfaces but not configured as a LAG",
            );
        } else if ($countPI > 1 && $virtualInterface->lag_framing) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_INFO,
                narrative: "Configured as a LAG with ".$lastLACP." LACP",
            );
        } else if ($countPI === 1 && $virtualInterface->lag_framing) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_INFO,
                narrative: "Configured as a single member LAG with ".$lastLACP." LACP",
            );
        } else if ($countPI === 1 && !$virtualInterface->lag_framing) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_ERROR,
                narrative: "Single physical interface but not configured as a LAG",
            );
        } else {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_WARNING,
                narrative: "No physical interfaces configured for this connection",
            );
        }

    }


    /**
     * Examine the virtual interface VLAN and Trunks and provide information on it.
     *
     * @param VirtualInterface $virtualInterface
     * @return DiagnosticResult
     */
    private function virtualInterfaceVlanTrunk(VirtualInterface $virtualInterface): DiagnosticResult {
        $mainName = 'VLANs vs Trunks';
        $countVL = count($virtualInterface->vlanInterfaces);

        if ($countVL > 1 && !$virtualInterface->trunk) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_ERROR,
                narrative: "Multiple vlan interfaces exists but not configured as a trunk port",
            );
        } else if ($countVL > 1 && $virtualInterface->trunk) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_INFO,
                narrative: "Configured as a trunk port",
            );
        } else if ($countVL === 1 && $virtualInterface->trunk) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_INFO,
                narrative: "Configured as a trunk port but only one vlan interface exists",
            );
        } else if ($countVL === 1 && !$virtualInterface->trunk) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_ERROR,
                narrative: "Single vlan interface but not configured as a trunk port",
            );
        } else {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_WARNING,
                narrative: "No VLAN interfaces configured for this connection",
            );
        }

    }


    /**
     * Examine the virtual interface LAG framing and provide information on it.
     *
     * @param VirtualInterface $virtualInterface
     * @return DiagnosticResult
     */
    private function virtualInterfaceLagFraming(VirtualInterface $virtualInterface): DiagnosticResult {
        $mainName = 'LAG Framing';

        if(!$virtualInterface->name || !$virtualInterface->channelgroup) {
            $narratives = [];
            if(!$virtualInterface->name) {
                $narratives[] = "no name";
            } else if(!$virtualInterface->channelgroup) {
                $narratives[] = "no channelgroup";
            }
            $narrative = implode(' and ', $narratives);
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_ERROR,
                narrative: "LAG framing set but " . $narrative,
            );
        } else {
            $bundleName = $virtualInterface->bundleName();
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
     * @param VirtualInterface $virtualInterface
     * @return DiagnosticResult
     */
    private function virtualInterfaceMtu(VirtualInterface $virtualInterface): DiagnosticResult {
        $mainName = 'MTU';

        $viMtu = $virtualInterface->mtu;

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
                result: DiagnosticResult::TYPE_WARNING,
                narrative: "Non-standard mtu is set to " . $viMtu,
            );
        }

    }


}