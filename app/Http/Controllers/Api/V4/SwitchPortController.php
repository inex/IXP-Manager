<?php

namespace IXP\Http\Controllers\Api\V4;

use D2EM;

use Entities\{
    SwitchPort
};

use Illuminate\Http\JsonResponse;

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
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     *
     * @params  $request instance of the current HTTP request
     * @return  JsonResponse JSON physicalInterface object
     */
    public function physicalInterface(int $id): JsonResponse {
        if( !($switchPort = D2EM::getRepository(SwitchPort::class)->find($id))){
            abort( 404, 'No such switchport' );
        }

        if($phyInterface = $switchPort->getPhysicalInterface()){
            return response()->json([
                'physicalInterfaceFound'    => true,
                'physicalInterface'         => $phyInterface->getId(),
            ]);
        }

        return response()->json(array('physicalInterfaceFound' => false));
    }


}
