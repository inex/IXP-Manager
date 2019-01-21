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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */


use D2EM;

use Entities\{
    SwitchPort
};

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * PatchPanelPort Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   PatchPanel
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SwitchPortController extends Controller {


    /**
     * Get the customer for a switch port
     *
     * @param   int $id The ID of the switchport to query
     * @return  JsonResponse JSON customer object
     */
    public function customer( int $id ): JsonResponse {

        if( !( $sp = D2EM::getRepository( SwitchPort::class )->find( $id ) ) ) {
            abort( 404, 'No such switchport' );
        }

        /** @var SwitchPort $sp */
        if( $pi = $sp->getPhysicalInterface() ) {
            if( $vi = $pi->getVirtualInterface() ) {
                return response()->json([
                    'customerFound' => true,
                    'id'            => $vi->getCustomer()->getId(),
                    'name'          => $vi->getCustomer()->getName(),
                ]);
            }
        }

        return response()->json(['customerFound' => false]);
    }

    /**
     * Check if the switch port has a physical interface set
     *
     * @param   int $id  Id of the switchport
     * @return  JsonResponse JSON response
     */
    public function physicalInterface(int $id): JsonResponse {
        /** @var SwitchPort $sp */
        if( !( $sp = D2EM::getRepository(SwitchPort::class)->find($id) ) ) {
            abort( 404, 'No such switchport' );
        }

        if( ( $pi = $sp->getPhysicalInterface() ) ){
            return response()->json([
                'physInt' => [
                    'id'         => $pi->getId(),
                    'status'     => $pi->getStatus(),
                    'statusText' => $pi->resolveStatus(),
                ]
            ]);
        }

        return response()->json([]);
    }


}
