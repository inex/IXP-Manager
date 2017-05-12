<?php

namespace IXP\Http\Controllers\Api\V4;


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

use D2EM;

use Entities\{
    VlanInterface as VlanInterfaceEntity
};

use Illuminate\Http\JsonResponse;

/**
 * VlanInterface API Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class VlanInterfaceController extends Controller
{


    /**
     * Get all Layer2Address for a VlanInterface
     *
     * @param int $id VlanInterface ID
     * @return  JsonResponse
     */
    public function getL2A( int $id ) : JsonResponse{

        /** @var VlanInterfaceEntity $vli */
        if( !( $vli =  D2EM::getRepository( VlanInterfaceEntity::class )->find( $id ) ) ){
            return abort( 404 );
        }

        $l2as = [];

        foreach( $vli->getLayer2Addresses() as $l2a ) {
            $l2as[ $l2a->getId() ] = $l2a->getMac();
        }

        return response()->json( $l2as );
    }

    /**
     * Delete a Vlan Interface and the Layer2Address associated
     *
     * @param   int $id ID of the SflowReceiver
     * @return  JsonResponse
     */
    public function delete( int $id ): JsonResponse{
        /** @var VlanInterfaceEntity $vli */
        if( !( $vli = D2EM::getRepository( VlanInterfaceEntity::class )->find( $id ) ) ) {
            return abort( '404' );
        }

        foreach( $vli->getLayer2Addresses() as $l2a ) {
            D2EM::remove( $l2a );
        }

        D2EM::remove( $vli );
        D2EM::flush();

        return response()->json( [ 'success' => true, 'message' => 'The Vlan Interface and all the Layer2address associated have been deleted successfully.' ] );
    }
}
