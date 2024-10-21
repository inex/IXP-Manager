<?php

namespace IXP\Http\Controllers;

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

use Illuminate\Http\Request;
use Illuminate\View\View;
use IXP\Exceptions\GeneralException;
use IXP\Models\Customer;
use IXP\Services\Diagnostics;
use IXP\Services\Diagnostics\DiagnosticResult;


/**
 * Diagnostics Controller
 * @author     Laszlo Kiss <laszlo@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers
 * @copyright  Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class DiagnosticsController extends Controller
{
    public array $badgeTypes;

    public function __construct() {
        $this->badgeTypes = [
            DiagnosticResult::TYPE_FATAL   => 'tw-border-red-600 tw-bg-red-600',
            DiagnosticResult::TYPE_ERROR   => 'tw-border-red-400 tw-bg-red-400',
            DiagnosticResult::TYPE_WARN    => 'tw-border-amber-400 tw-bg-amber-400',
            DiagnosticResult::TYPE_INFO    => 'tw-border-teal-400 tw-bg-teal-400',
            DiagnosticResult::TYPE_UNKNOWN => 'tw-border-black-600 tw-bg-red-600',
            DiagnosticResult::TYPE_DEBUG   => 'tw-border-gray-400 tw-bg-gray-400',
            DiagnosticResult::TYPE_TRACE   => 'tw-border-gray-300 tw-bg-gray-300',
            DiagnosticResult::TYPE_GOOD    => 'tw-border-lime-500 tw-bg-lime-500',
        ];
    }

    private function generateBadges() {
        $badges = [];
        $enabledBadges = [
            DiagnosticResult::TYPE_FATAL,
            DiagnosticResult::TYPE_ERROR,
            DiagnosticResult::TYPE_WARN,
            DiagnosticResult::TYPE_INFO,
            DiagnosticResult::TYPE_UNKNOWN,
            //DiagnosticResult::TYPE_DEBUG,
            //DiagnosticResult::TYPE_TRACE,
            DiagnosticResult::TYPE_GOOD,
        ];

        foreach(DiagnosticResult::$RESULT_TYPES_TEXT as $result => $text) {

            $plainResult = new DiagnosticResult(
                name: '',
                result: $result,
                narrative: '',
            );
            $enable = ' tw-opacity-40';
            if(in_array($result, $enabledBadges)) {
                $enable = '';
            }

            $badgeExtension = '<span data-target="'.$text.'" data-status="'.$result.'" class="badgeButton '.$enable.' hover:tw-opacity-80 tw-cursor-pointer ';

            $badges[$text] = str_replace('<span class="',$badgeExtension,$plainResult->badge());
        }

        return $badges;
    }

    /**
     * Run the diagnostics suite
     */
    public function customer( Customer $customer, Diagnostics $diagnostics ): View
    {
        $resultSets = [];

        $resultSets[] = $diagnostics->getCustomerDiagnostics($customer);

        foreach( $customer->virtualInterfaces as $vi ) {
            $viSet = $diagnostics->getVirtualInterfaceDiagnostics( $vi );

            // get the Physical Interface Diagnostics Data and integrate here into the VI array
            foreach( $vi->physicalInterfaces as $pi ) {
                $viSet->addSubset( $diagnostics->getPhysicalInterfaceDiagnostics( $pi ) );
                $viSet->addSubset( $diagnostics->getTransceiverDiagnostics( $pi ) );
            }

            // get the Vlan Interface Diagnostics data
            $protocols = [4,6];
            foreach( $vi->vlanInterfaces as $vli ) {
                $viSet->addSubset( $diagnostics->getVlanInterfaceL2Diagnostics( $vli ) );

                foreach( $protocols as $protocol ) {
                    // if the protocol disabled, there is no diagnostics info
                    $protocolCellEnabled = "ipv" . $protocol . "enabled";
                    if($vli->$protocolCellEnabled) {
                        $viSet->addSubset( $diagnostics->getVlanInterfaceL3Diagnostics( $vli, $protocol ) );
                        $viSet->addSubset( $diagnostics->getRouterBgpSessionsDiagnostics( $vli, $protocol ) );
                    }

                }

            }

            $resultSets[] = $viSet;

        }


        // former view: diagnostics.results (still works)
        return view( 'diagnostics.newresults')->with([
            "badgeTypes" => $this->badgeTypes,
            "badges" => $this->generateBadges(),
            "customer" => $customer,
            "resultSets"  => $resultSets,
        ]);
    }


    /**
     * Run the diagnostics suite
     */
    public function irrdb( Customer $customer, Diagnostics $diagnostics ): View
    {
        $resultSets = [];

        $resultSets[] = $diagnostics->getCustomerIrrdbDiagnostics($customer);

        return view( 'diagnostics.results')->with([
            "badgeTypes" => $this->badgeTypes,
            "badges" => $this->generateBadges(),
            "customer" => $customer,
            "resultSets"  => $resultSets,
        ]);
    }


}
