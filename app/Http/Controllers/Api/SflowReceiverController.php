<?php

namespace IXP\Http\Controllers\Api;

use Illuminate\Http\Request;

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
                ->view('api/sflow-receiver/pretagMap', ['map' => $map], 200)
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
                ->view('api/sflow-receiver/receiversLst', ['map' => $map], 200)
                ->header('Content-Type', 'text/html; charset=utf-8');
    }

}
