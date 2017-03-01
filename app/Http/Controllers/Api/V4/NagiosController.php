<?php

namespace IXP\Http\Controllers\Api\V4;

use Illuminate\Http\Request;
use IXP\Utils\Routers;

class NagiosController extends Controller {

    /**
     * API call to create Nagios configuration to monitor Bird's Eye looking glasses and - thus -
     * Bird BGP daemons.
     *
     * Takes router / Bird instances from config/routers.php.
     *
     * @param int $vlanid Optional database id of a vlan to generate config for (vlan.id)
     * @return Response
     */
    public function birdseyeDaemons( $vlanid = null )
    {
        $map     = [];
        $routers = ( new Routers )->filterForApiType('birdseye');

        if( $vlanid ) {
            $routers->filterForVlanId($vlanid);
        }

        if( $routers->isEmpty() ) {
            abort( 404, "No definition(s) in config/routers.php for the provided VLAN ID / Bird's Eye API type." );
        }

        return response()
                ->view('api/v4/nagios/birdseye-daemons', ['routers' => $routers->getObjects(), 'vlanid' => $vlanid ?? false], 200)
                ->header('Content-Type', 'text/html; charset=utf-8');
    }


    public function birdseyeRsBgpSessions( $vlanid = null )
    {
        $routers = ( new Routers )->filterForApiType('birdseye');

        $routers->filterForType('RS');
        
        if( $vlanid ) {
            $routers->filterForVlanId($vlanid);
        }
        
        if( $routers->isEmpty() ) {
            abort( 404, "No suitable definition(s) in config/routers.php found." );
        }
        
        $map   = [];
        $vlans = [];
        
        foreach( $routers->getObjects() as $h => $router ) {
        
            if( !$router->hasApi() ) {
                continue;
            }
            
            if( !isset( $vlans[ $router->vlanId() ] ) ) {
                if( !( $vlans[$router->vlanId()] = d2r('Vlan')->find( $router->vlanId() ) ) ) {
                    // non-existent VLAN
                    continue;
                }
            }

            foreach( $vlans[$router->vlanId()]->getVlanInterfaces() as $vli ) {
                if( !$vli->getRsclient() ) {
                    continue;
                }

                $connected = false;
                foreach( $vli->getVirtualInterface()->getPhysicalInterfaces() as $pi ) {
                    if( $pi->statusIsConnected() ) {
                        $connected = true;
                        break;
                    }
                }

                if( !$connected ) {
                    continue;
                }

                if( !( $vli->getVirtualInterface()->getCustomer()->isTypeFull() || $vli->getVirtualInterface()->getCustomer()->isTypeProBono() ) ) {
                    continue;
                }

                foreach( [4,6] as $proto ) {
                    if( !$vli->{'getIpv'.$proto.'Enabled'}() || !$vli->{'getIpv'.$proto.'monitorrcbgp'}() || !$vli->{"getIpv{$proto}canping"}() || !$vli->{"getIpv{$proto}Address"}() ) {
                        continue;
                    }

                    // FIXME we generate these (protocol name and Nagios cust hostname) is >=2 locations now -> centralise
                    $m = [];
                    $m['pname'] = sprintf( "pb_%04d_as%d", $vli->getId(), $vli->getVirtualInterface()->getCustomer()->getAutsys() );
                    $m['hname'] = sprintf( "%s-ipv%d-vlan%d-%d",
                        $vli->getVirtualInterface()->getCustomer()->getShortname(),
                        $proto, $vli->getVLAN()->getNumber(), $vli->getId()
                    );

                    if( $router->protocol() != $proto ) {
                        continue;
                    }

                    $m['api']  = $router->api();
                    $m['name'] = 'BGP sesstion to ' . $router->name();

                    $map[] = $m;
                }
            }
        }
        
        return response()
            ->view('api/v4/nagios/birdseye-rs-bgp-daemons', ['map' => $map, 'vlanid' => $vlanid ?? false], 200)
            ->header('Content-Type', 'text/html; charset=utf-8');
    }

}
