<?php


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

namespace IXP\Http\Controllers\Api\V4;

use D2EM;

use Entities\{
    Layer2Address as Layer2AddressEntity,
    VlanInterface as VlanInterfaceEntity
};

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class Layer2AddressController extends Controller {

    /**
     * Add a mac address to a Vlan Interface
     *
     * @param   Request $request instance of the current HTTP request
     * @return  JsonResponse
     */
    public function add ( Request $request ): JsonResponse{
        if( !( $request->input( 'mac' ) ) ||  !( $vli = D2EM::getRepository( VlanInterfaceEntity::class )->find( $request->input( 'id' ) ) ) ) {
            return abort( '404' );
        }
        /** @var VlanInterfaceEntity $vli */
        $mac = preg_replace( "/[^a-f0-9]/i", '' , strtolower( $request->input( 'mac' ) ) );

        if( strlen( $mac ) !== 12 ){
            return response()->json( [ 'success' => false, 'message' => 'The MAC address has a bad format!' ] );
        }

        if( ! D2EM::getRepository( Layer2AddressEntity::class )->isMacExisting( $mac, $vli->getId() ) ) {
            /** @var Layer2AddressEntity $l2a */
            $l2a = new Layer2AddressEntity();
            $l2a->setMac( $mac );
            $l2a->setVlanInterface( $vli );
            $l2a->setCreatedAt( new \DateTime );

            D2EM::persist( $l2a );
            D2EM::flush();

            return response()->json( [ 'success' => true, 'message' => 'The MAC address has been added successfully.' ] );
        } else {
            return response()->json( [ 'success' => false, 'message' => 'The MAC address already exist for the current Vlan Interface!' ] );
        }
    }


    /**
     * Delete a mac address from a Vlan Interface
     *
     * @param   int $id ID of the Layer2Interface
     * @return  JsonResponse
     */
    public function delete ( int $id ): JsonResponse{
        if( !( $id ) ||  !( $l2a = D2EM::getRepository( Layer2AddressEntity::class )->find( $id ) ) ) {
            return abort( '404' );
        }
        /** @var Layer2AddressEntity $l2a */
        $l2a->getVlanInterface()->removeLayer2Address( $l2a );

        D2EM::remove( $l2a );
        D2EM::flush();

        return response()->json( [ 'success' => true, 'message' => 'The MAC address has been deleted successfully.' ] );
    }

    /**
     * Get the layer2Interface detail
     *
     * @param   int $id ID of the Layer2Interface
     * @return  JsonResponse
     */
    public function detail ( int $id ): JsonResponse{
        if( !( $id ) ||  !( $l2a = D2EM::getRepository(Layer2AddressEntity::class)->find( $id ) ) ) {
            return abort( '404' );
        }
        /** @var Layer2AddressEntity $l2a */
        return response()->json( $l2a->jsonArray() );
    }
}