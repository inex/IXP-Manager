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

use Auth, D2EM;

use Entities\{
    Layer2Address as Layer2AddressEntity,
    VlanInterface as VlanInterfaceEntity
};

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use IXP\Events\Layer2Address\Added as Layer2AddressAddedEvent;
use IXP\Events\Layer2Address\Deleted as Layer2AddressDeletedEvent;

/**
 * Layer2Address API Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Layer2AddressController extends Controller {

    /**
     * Add a mac address to a VLAN Interface
     *
     * @param   Request $request instance of the current HTTP request
     * @return  JsonResponse
     * @throws \LaravelDoctrine\ORM\Facades\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function add( Request $request ): JsonResponse {
        /** @var VlanInterfaceEntity $vli */
        if( !( $vli = D2EM::getRepository( VlanInterfaceEntity::class )->find( $request->input( 'vliid' ) ) ) ) {
            return abort( 404, 'VLAN interface not found' );
        }

        if( !Auth::user()->isSuperUser() ) {
            if( !config( 'ixp_fe.layer2-addresses.customer_can_edit' ) ) {
                abort( 404 );
            }

            if( Auth::user()->getCustomer()->getId() != $vli->getVirtualInterface()->getCustomer()->getId() ) {
                abort( 403, 'VLI / Customer mismatch' );
            }

            if( count( $vli->getLayer2Addresses() ) >= config( 'ixp_fe.layer2-addresses.customer_params.max_addresses' ) ) {
                return response()->json( [ 'danger' => false, 'message' => 'The maximum possible MAC addresses have been configured. Please delete a MAC before adding.' ] );
            }
        }

        $mac = preg_replace( "/[^a-f0-9]/i", '' , strtolower( $request->input( 'mac', '' ) ) );
        if( strlen( $mac ) !== 12 ) {
            return response()->json( [ 'danger' => false, 'message' => 'Invalid or missing MAC addresses' ] );
        }

        if( D2EM::getRepository( Layer2AddressEntity::class )->existsInVlan( $mac, $vli->getVlan()->getId() ) ) {
            return response()->json( [ 'danger' => false, 'message' => 'The MAC address already exists within this IXP VLAN' ] );
        }

        $l2a = new Layer2AddressEntity();
        $l2a->setMac( $mac )
            ->setVlanInterface( $vli )
            ->setCreatedAt( new \DateTime );

        D2EM::persist( $l2a );
        D2EM::flush();

        event( new Layer2AddressAddedEvent( $l2a, Auth::getUser() ) );

        return response()->json( [ 'success' => true, 'message' => 'The MAC address has been added successfully.' ] );
    }

    /**
     * Get the layer2Interface detail
     *
     * @param   int $id ID of the Layer2Interface
     * @return  JsonResponse
     */
    public function detail( int $id ): JsonResponse{
        if( !( $l2a = D2EM::getRepository(Layer2AddressEntity::class)->find( $id ) ) ) {
            return abort( '404' );
        }
        /** @var Layer2AddressEntity $l2a */
        return response()->json( $l2a->jsonArray() );
    }

    /**
     * Delete a mac address from a Vlan Interface
     *
     * @param   int $id ID of the Layer2Address
     * @return  JsonResponse
     * @throws
     */
    public function delete( int $id ): JsonResponse{
        /** @var Layer2AddressEntity $l2a */
        if( !( $l2a = D2EM::getRepository( Layer2AddressEntity::class )->find( $id ) ) ) {
            return abort( '404' );
        }

        if( !Auth::user()->isSuperUser() ) {
            if( !config( 'ixp_fe.layer2-addresses.customer_can_edit' ) ) {
                abort( 404 );
            }

            if( Auth::user()->getCustomer()->getId() != $l2a->getVlanInterface()->getVirtualInterface()->getCustomer()->getId() ) {
                abort( 403, 'MAC address / Customer mismatch' );
            }

            if( count( $l2a->getVlanInterface()->getLayer2Addresses() ) <= config( 'ixp_fe.layer2-addresses.customer_params.min_addresses' ) ) {
                return response()->json( [ 'danger' => false, 'message' => 'The minimum possible MAC addresses have been configured. Please add a MAC before deleting.' ] );
            }
        }

        $l2a->getVlanInterface()->removeLayer2Address( $l2a );
        $macaddress = $l2a->getMacFormattedWithColons();
        $vli        = $l2a->getVlanInterface();

        D2EM::remove( $l2a );
        D2EM::flush();

        event( new Layer2AddressDeletedEvent( $macaddress, $vli, Auth::user() ) );

        return response()->json( [ 'success' => true, 'message' => 'The MAC address has been deleted.' ] );
    }

}
