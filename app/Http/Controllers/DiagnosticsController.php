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

    /**
     * Run the diagnostics suite
     */
    public function customer( Customer $customer, Diagnostics $diagnostics ): View
    {
        $resultSets = [];

        $resultSets[] = $diagnostics->getCustomerDiagnostics($customer);
        $resultSets[] = $diagnostics->getCustomerIrrdbDiagnostics($customer);

        foreach( $customer->virtualInterfaces as $vi ) {
            $resultSets[] = $diagnostics->getVirtualInterfaceDiagnostics( $vi );

//            // get the Physical Interface Diagnostics Data and integrate here into the VI array
//            foreach( $vi->physicalInterfaces as $pi ) {
//                $resultSets[] = $diagnostics->getPhysicalInterfaceDiagnostics( $pi );
//            }
//
//            // get the Vlan Interface Diagnostics data
//            $protocols = [4,6];
//            foreach( $vi->vlanInterfaces as $vli ) {
//                foreach( $protocols as $protocol ) {
//
//                    // if the protocol disabled, there is no diagnostics info
//                    $protocolCellEnabled = "ipv" . $protocol . "enabled";
//                    if($vli->$protocolCellEnabled) {
//                        $resultSets[] = $diagnostics->getVlanInterfaceDiagnostics( $vli, $protocol );
//                    }
//
//                }
//
//            }

        }

        $_badges = [];
        $enabledBadges = ['Fatal','Error'];
        foreach(DiagnosticResult::$RESULT_TYPES_TEXT as $result => $text) {
            $plainResult = new DiagnosticResult(
                name: '',
                result: $result,
                narrative: '',
            );
            //$enable = ' tw-opacity-40';
            $enable = '';
            if(in_array($text, $enabledBadges)) {
                //$enable = '';
            }
            $badgeExtension = '<span data-target="'.$text.'" class="badgeButton '.$enable.' hover:tw-opacity-80 tw-cursor-pointer ';
            $_badges[$text] = str_replace('<span class="',$badgeExtension,$plainResult->badge());
        }

        info("badges:\n".var_export($_badges, true));

        return view( 'diagnostics.results')->with([
            "badges" => $_badges,
            "customer" => $customer,
            "resultSets"  => $resultSets,
        ]);
    }


}
