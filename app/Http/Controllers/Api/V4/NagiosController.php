<?php

namespace IXP\Http\Controllers\Api\V4;

use Illuminate\Http\Request;

class NagiosController extends Controller {

    private function generateBirdseyeDaemonMap( $class, &$map ) {
        foreach( $class as $key => $details ) {
            if( $details['api_type'] !== 'birdseye' ) {
                continue;
            }

            $map[$key] = $details;
        }
    }

    /**
     *
     * @return Response
     */
    public function birdseyeDaemons( $vlanid = null )
    {
        $map = [];

        if( $vlanid ) {
            if( !config('lookingglass.'.$vlanid, false ) ) {
                abort( 404, "No definition in config/lookingglass.php for the provided VLAN id." );
            }

            foreach( config('lookingglass.'.$vlanid) as $class ) {
                $this->generateBirdseyeDaemonMap($class,$map);
            }
        } else {
            foreach( config('lookingglass') as $vlanid => $vlanDetails ) {
                foreach( $vlanDetails as $class ) {
                    $this->generateBirdseyeDaemonMap($class,$map);
                }
            }
        }

        return response()
                ->view('api2/nagios/birdseye-daemons', ['map' => $map, 'vlanid' => $vlanid], 200)
                ->header('Content-Type', 'text/html; charset=utf-8');
    }


    public function birdseyeRsBgpSessions( $vlanid = null )
    {
        if( $vlanid && !config('lookingglass.'.$vlanid, false ) ) {
            abort( 404, "No definition in config/lookingglass.php for the provided VLAN id." );
        }

        $map = [];

        // this is lazy but I'll swing back at it post v4
        foreach( d2r( 'VlanInterface' )->findAll() as $vli ) {
            if( $vlanid !== null && $vli->getVLAN()->getId() != $vlanid ) {
                continue;
            }

            if( $vli->getVlan()->getPrivate() ) {
                continue;
            }

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

                if( !config('lookingglass.'.($vli->getVLAN()->getId()).'.rs', false ) ) {
                    continue;
                }

                foreach( config('lookingglass.'.($vli->getVLAN()->getId()).'.rs') as $rs ) {
                    if( $rs['api_type'] !== 'birdseye' || $rs['protocol'] !== $proto ) {
                        continue;
                    }

                    $m['api']  = $rs['api'];
                    $m['name'] = 'BGP sesstion to ' . $rs['name'];

                    $map[] = $m;
                }
            }
        }

        return response()
                ->view('api2/nagios/birdseye-rs-bgp-daemons', ['map' => $map, 'vlanid' => $vlanid], 200)
                ->header('Content-Type', 'text/html; charset=utf-8');
    }

}
