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
     * @param   int $id The ID of the patch panel port to query
     * @return  JsonResponse JSON customer object
     */
    public function detail( int $id ): JsonResponse {

        if( !( $ppp = D2EM::getRepository( PatchPanelPort::class )->find( $id ) ) ) {
            if( !( $ppp = D2EM::getRepository( PatchPanelPortHistory::class )->findOneBy( [ 'patch_panel_port_id' => $id ] ) ) ) {
                abort( 404, 'No such patch panel port' );
            }
        }

        return response()->json( $ppp->jsonArray() );
    }


}
