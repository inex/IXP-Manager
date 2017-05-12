<?php

namespace IXP\Http\Controllers\Api\V4;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use D2EM;

use Entities\{
    PhysicalInterface as PhysicalInterfaceEntity
};


class PhysicalInterfaceController extends Controller {

    /**
     * Delete a Physical Interface
     *
     * @param   int $id ID of the Physical Interface
     * @return  JsonResponse
     */
    public function delete( int $id ): JsonResponse{
        /** @var PhysicalInterfaceEntity $pi */
        if( !( $pi = D2EM::getRepository( PhysicalInterfaceEntity::class )->find( $id ) ) ) {
            return abort( '404' );
        }

        D2EM::remove( $pi );
        D2EM::flush();

        return response()->json( [ 'success' => true, 'message' => 'The Physical Interface has been deleted successfully.' ] );
    }

}
