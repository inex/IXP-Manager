<?php

namespace IXP\Http\Controllers\Api\V4;

use Illuminate\Http\Request;

class DnsController extends Controller {

    /**
     * API call to generate DNS ARPA records in a given format
     *
     * @param int $vlanid Optional database id of a vlan to generate config for (vlan.id)
     * @return Response
     */
    public function arpa( Request $request, $vlanid, $protocol = 4 )
    {
        if( !( $vlan = d2r('Vlan')->find($vlanid) ) ) {
            abort( 404, "Unknown VLAN" );
        }
        
        if( !in_array($protocol,[4,6]) ) {
            abort( 404, "Unknown protocol" );
        }
        
        $arpa = array_map( function( $e ) use ($protocol) { $e['arpa'] = $this->convertIPtoArpa( $e['address'], $protocol ); return $e; },
            d2r('Vlan')->getArpaDetails( $vlan, $protocol )
        );

        if( $request->input('format') == 'json' ) {
            return response()->json($arpa);
        }

        return response()
                ->view('api/v4/dns/arpa', ['arpa' => $arpa, 'vlan' => $vlan], 200)
                ->header('Content-Type', 'text/plain; charset=utf-8');
    }
    
    private function convertIPtoArpa( $ip, $protocol ) {
        switch( $protocol ) {
            case 4:
                $parts = explode( '.', $ip );
                $arpa = sprintf( '%d.%d.%d.%d.in-addr.arpa', $parts[3], $parts[2], $parts[1], $parts[0] );
                break;
                
            case 6:
                $addr = inet_pton($ip);
                $unpack = unpack('H*hex', $addr);
                $hex = $unpack['hex'];
                $arpa = implode('.', array_reverse(str_split($hex))) . '.ip6.arpa';
                break;
        }
        
        return $arpa;
    }

}
