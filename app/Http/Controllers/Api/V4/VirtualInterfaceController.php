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
    VirtualInterface as VirtualInterfaceEntity, PhysicalInterface as PhysicalInterfaceEntity, SwitchPort as SwitchPortEntity, VlanInterface as VlanInterfaceEntity, VlanInterface
};

use Illuminate\Http\JsonResponse;

/**
 * VlanInterface API Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class VirtualInterfaceController extends Controller
{
    /**
     * Delete a Virtual Interface
     *
     * @param   int $id ID of the VirtualInterface
     * @return  JsonResponse
     */
    public function delete( int $id ): JsonResponse{
        /** @var VirtualInterfaceEntity $vi */
        if( !( $vi = D2EM::getRepository( VirtualInterfaceEntity::class )->find( $id ) ) ) {
            return abort( '404' );
        }

        foreach( $vi->getPhysicalInterfaces() as $pi) {
            /** @var PhysicalInterfaceEntity $pi */
            $vi->removePhysicalInterface( $pi );

            if( $pi->getSwitchPort()->getType() == SwitchPortEntity::TYPE_PEERING && $pi->getFanoutPhysicalInterface() )
            {
                $pi->getSwitchPort()->setPhysicalInterface( null );
                $pi->getFanoutPhysicalInterface()->getSwitchPort()->setType( \Entities\SwitchPort::TYPE_PEERING );
            }
            else if( $pi->getSwitchPort()->getType() == SwitchPortEntity::TYPE_FANOUT && $pi->getPeeringPhysicalInterface() )
            {
                $this->removeRelatedInterface( $pi );

                $pi->getPeeringPhysicalInterface()->setFanoutPhysicalInterface( null );
            }
            D2EM::remove( $pi );

            if( $pi->getRelatedInterface() )
                $this->removeRelatedInterface( $pi );
        }


        foreach( $vi->getVlanInterfaces() as $vli ) {
            /** @var VlanInterfaceEntity $vli */
            foreach( $vli->getLayer2Addresses() as $l2a) {
                D2EM::remove( $l2a );
            }

            $vi->removeVlanInterface( $vli );
            D2EM::remove( $vli );
        }

        foreach( $vi->getMACAddresses() as $mac){
            D2EM::remove( $mac );
        }


        D2EM::remove( $vi );
        D2EM::flush();

        return response()->json( [ 'success' => true, 'message' => 'The Virtual Interface has been deleted successfully.' ] );
    }

    private function removeRelatedInterface( $pi ){
        /** @var PhysicalInterfaceEntity $pi */
        $pi->getRelatedInterface()->getSwitchPort()->setPhysicalInterface( null );
        if( count( $pi->getRelatedInterface()->getVirtualInterface()->getPhysicalInterfaces() ) == 1 )
        {
            foreach( $pi->getRelatedInterface()->getVirtualInterface()->getVlanInterfaces() as $vli ) {
                /** @var VlanInterface $vli */
                foreach( $vli->getLayer2Addresses() as $l2a ) {
                    D2EM::remove( $l2a );
                }

                D2EM::remove( $vli );
            }

            D2EM::remove( $pi->getRelatedInterface()->getVirtualInterface() );
            D2EM::remove( $pi->getRelatedInterface() );
        }
        else
        {
            D2EM::remove( $pi->getRelatedInterface() );
        }
    }
}
