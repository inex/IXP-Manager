<?php

namespace IXP\Http\Controllers\Api\V4;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use App, D2EM;

use Entities\{
    Customer,
    PatchPanel
};

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


/**
 * Customer API v4 Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Customers
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CustomerController extends Controller
{

    /**
     * Get network information from PeeringDb by ASN
     *
     * For return information:
     * @see \IXP\Services\PeeringDb::getNetworkByAsn()
     *
     * @param   string  $asn
     * @return  JsonResponse
     */
    public function queryPeeringDbWithAsn( string $asn ): JsonResponse {
        return response()->json( App::make( "IXP\Services\PeeringDb" )->getNetworkByAsn( $asn ) );
    }



    /**
     * Get the switches for a customer
     *
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     *
     * @params  $request instance of the current HTTP request
     * @return  array switches [id => name]
     */
    public function switches(Request $request, int $id): JsonResponse{
        if( !($customer = D2EM::getRepository( Customer::class )->find( $id ) ) ){
            abort( 404, 'No such customer' );
        }

        if( !($patchPanel = D2EM::getRepository( PatchPanel::class )->find( $request->input('patch_panel_id') ) ) ){
            abort( 404, 'No such patch panel' );
        }

        $switches = [];
        foreach($customer->getVirtualInterfaces() as $vi){
            foreach($vi->getPhysicalInterfaces() as $pi){
                $switch = $pi->getSwitchPort()->getSwitcher();
                if($switch->getCabinet()->getLocation()->getId() == $patchPanel->getCabinet()->getLocation()->getId()){
                    $switches[$switch->getId()] = $switch->getName();
                }
            }
        }
        return response()->json(['switchesFound' => boolval(count($switches)), 'switches' => $switches]);
    }

}