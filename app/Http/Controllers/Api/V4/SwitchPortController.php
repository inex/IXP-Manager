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


}
