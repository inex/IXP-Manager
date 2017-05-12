<?php

namespace IXP\Http\Controllers\Api\V4;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use D2EM;

use Entities\{
    SflowReceiver as SflowReceiverEntity
};




class SflowReceiverController extends Controller {

    /**
     *
     * @return Response
     */
    public function pretagMap( Request $request )
    {
        $map = [];

        foreach( d2r('SflowReceiver')->findAll() as $sr ) {
            foreach( $sr->getVirtualInterface()->getMACAddresses() as $mac ) {

                // looks like there's some crud in the MAC table so filter that:
                if( strlen( $mac->getMac() ) != 12 ) {
                    continue;
                }

                $m['virtualinterfaceid'] = $sr->getVirtualInterface()->getId();
                $m['mac']             = $mac->getMacFormattedWithColons();
                $map[] = $m;
            }
        }

        return response()
                ->view('api/v4/sflow-receiver/pretagMap', ['map' => $map], 200)
                ->header('Content-Type', 'text/html; charset=utf-8');
    }

    /**
     *
     * @return Response
     */
    public function receiversLst( Request $request )
    {
        $map = [];

        foreach( d2r('SflowReceiver')->findAll() as $sr ) {
            $m['virtualinterfaceid'] = $sr->getVirtualInterface()->getId();
            $m['dst_ip']          = $sr->getDstIp();
            $m['dst_port']        = $sr->getDstPort();
            $map[] = $m;
        }

        return response()
                ->view('api/v4/sflow-receiver/receiversLst', ['map' => $map], 200)
                ->header('Content-Type', 'text/html; charset=utf-8');
    }


    /**
     * Delete a Sflow receiver
     *
     * @param   int $id ID of the SflowReceiver
     * @return  JsonResponse
     */
    public function delete( int $id ): JsonResponse{
        /** @var SflowReceiverEntity $sflr */
        if( !( $sflr = D2EM::getRepository( SflowReceiverEntity::class )->find( $id ) ) ) {
            return abort( '404' );
        }

        D2EM::remove( $sflr );
        D2EM::flush();

        return response()->json( [ 'success' => true, 'message' => 'The Sflow Receiver has been deleted successfully.' ] );
    }

}
