<?php

namespace IXP\Http\Controllers\Api\V4;

use D2EM;

use Entities\{
    PatchPanelPort, PhysicalInterface
};

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    /**
     * Set the public and private notes of a patch panel
     *
     * @param   int $id    The ID of the patch panel port to query
     * @return  JsonResponse JSON customer object
     */
    public function setNotes( Request $request, int $id ) {

        if( !( $ppp = D2EM::getRepository( PatchPanelPort::class )->find( $id ) ) ) {
            abort( 404, 'No such patch panel port' );
        }

        $ppp->setNotes(        clean( $request->input('notes') ) );
        $ppp->setPrivateNotes( clean( $request->input('private_notes') ) );
        D2EM::flush();

        // we may also pass a new state for a physical interface with this request
        // (because we call this function from set connected / set ceased / etc)
        if( $request->input('pi_status') ) {
            if( $ppp->getSwitchPort() && ( $pi = $ppp->getSwitchPort()->getPhysicalInterface() ) ) {
                /** @var PhysicalInterface $pi */
                $pi->setStatus( $request->input( 'pi_status' ) );
            }
            D2EM::flush();
        }

        return response()->json( [ 'success' => true ] );
    }

}
