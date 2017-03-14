<?php

namespace IXP\Http\Controllers\Api\V4;

use D2EM;

use Entities\{
    PatchPanelPort, PatchPanelPortHistory
};

use Illuminate\Http\JsonResponse;

class PatchPanelPortController extends Controller {


    /**
     * Get the details of a patch panel port
     *
     * @param   int $id    The ID of the patch panel port to query
     * @param   bool $deep Return a deep array by including associated objects
     * @return  JsonResponse JSON customer object
     */
    public function detail( int $id, bool $deep = false ): JsonResponse {

        if( !( $ppp = D2EM::getRepository( PatchPanelPort::class )->find( $id ) ) ) {
            abort( 404, 'No such patch panel port' );
        }

        return response()->json( $ppp->jsonArray($deep) );
    }


}
