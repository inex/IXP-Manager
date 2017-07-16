<?php

namespace IXP\Http\Controllers\Api\V4;

/*
 * Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use D2EM;

use Entities\{
    Infrastructure    as InfrastructureEntity,
    PhysicalInterface as PhysicalInterfaceEntity,
    Router            as RouterEntity,
    Vlan              as VlanEntity,
    VlanInterface     as VlanInterfaceEntity
};

use Illuminate\Http\{Request,Response};
use Illuminate\Support\Facades\View as FacadeView;


class NagiosController extends Controller {


    public function customers( Request $request, int $vlanid, int $protocol, string $template = null ): Response {

        if( !in_array( $protocol, [ 4, 6 ] ) ) {
            return abort( 404, 'Unknown protocol' );
        }

        /** @var VlanEntity $v */
        if( !( $v =  D2EM::getRepository( VlanEntity::class )->find( $vlanid ) ) ){
            return abort( 404, 'Unknown VLAN' );
        }

        if( $template === null ) {
            $tmpl = 'api/v4/nagios/customers/default';
        } else {
            $tmpl = sprintf( 'api/v4/nagios/customers/%s', preg_replace( '/[^a-z0-9\-]/', '', strtolower( $template ) ) );
        }

        if( !FacadeView::exists( $tmpl ) ) {
            abort(404, 'Unknown template');
        }

//        if( $request->input( 'probe', false ) ) {
//            $probe = $request->input( 'probe' );
//        } else {
//            $probe = 'FPing' . ( $protocol == 4 ? '' : '6' );
//        }

        $vlis = D2EM::getRepository( VlanInterfaceEntity::class )->getForProto( $v->getId(), $protocol, false, PhysicalInterfaceEntity::STATUS_CONNECTED );

        return response()
            ->view( $tmpl, [
                'vlan'     => $v,
                'protocol' => $protocol,
                'vlis'     => $vlis,

                // optional POST/GET parameters
                'host_definition'               => $request->input( 'host_definition',        'ixp-manager-member-host'          ),
                'host_check_command'            => $request->input( 'host_check_command',     'check-host-alive'                 ),
                'max_check_attempts'            => $request->input( 'max_check_attempts',     '10'                               ),
                'check_period'                  => $request->input( 'check_period',           '24x7'                             ),
                'notification_interval'         => $request->input( 'notification_interval',  '120'                              ),
                'notification_period'           => $request->input( 'notification_period',    '24x7'                             ),
                'host_notification_options'     => $request->input( 'notification_options',   'd,u,r'                            ),
                'check_interval'                => $request->input( 'check_interval',         '5'                                ),
                'retry_check_interval'          => $request->input( 'retry_check_interval',   '1'                                ),
                'service_definition'            => $request->input( 'service_definition',     'ixp-manager-member-service'       ),
                'contact_groups'                => $request->input( 'contact_groups',         'admins'                           ),
                'ping_check_command'            => $request->input( 'ping_check_command',     'check_ping!250.0,20%!500.0,60%'   ),
                'pingbusy_check_command'        => $request->input( 'pingbusy_check_command', 'check_ping!1000.0,80%!2000.0,90%' ),

                'service_notification_options'  => $request->input( 'notification_options',   'w,u,c,r'                            ),

            ], 200 )
            ->header( 'Content-Type', 'text/plain; charset=utf-8' );
    }

    public function switches( Request $request, int $infraid, string $template = null ): Response {

        /** @var InfrastructureEntity $infra */
        if( !( $infra =  D2EM::getRepository( InfrastructureEntity::class )->find( $infraid ) ) ) {
            return abort( 404, 'Unknown infrastructure' );
        }

        if( $template === null ) {
            $tmpl = 'api/v4/nagios/switches/default';
        } else {
            $tmpl = sprintf( 'api/v4/nagios/switches/%s', preg_replace( '/[^a-z0-9\-]/', '', strtolower( $template ) ) );
        }

        if( !FacadeView::exists( $tmpl ) ) {
            abort(404, 'Unknown template');
        }

//        if( $request->input( 'probe', false ) ) {
//            $probe = $request->input( 'probe' );
//        } else {
//            $probe = 'FPing' . ( $protocol == 4 ? '' : '6' );
//        }


        $switches = $infra->getSwitchers();


        return response()
            ->view( $tmpl, [
                'infra'    => $infra,
                'switches' => $switches,
//                'probe'    => $probe,
//                'level'    => $request->input( 'level', '+++' ),
//                'protocol' => $protocol
            ], 200 )
            ->header( 'Content-Type', 'text/plain; charset=utf-8' );





    }

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
        $routers = D2EM::getRepository( RouterEntity::class )->filterForApiType( RouterEntity::API_TYPE_BIRDSEYE );

        if( $vlanid ) {
            $routers = D2EM::getRepository( RouterEntity::class )->filterCollectionOnVlanId( $routers, $vlanid );
        }

        if( !count($routers) ) {
            abort( 404, "No routers for the provided VLAN ID / Bird's Eye API type." );
        }

        return response()
                ->view('api/v4/nagios/birdseye-daemons', ['routers' => $routers, 'vlanid' => $vlanid ?? false], 200)
                ->header('Content-Type', 'text/plain; charset=utf-8');
    }


    public function birdseyeRsBgpSessions( $vlanid = null )
    {
        $routers = D2EM::getRepository( RouterEntity::class )->filterForApiType( RouterEntity::API_TYPE_BIRDSEYE );
        $routers = D2EM::getRepository( RouterEntity::class )->filterCollectionOnType( $routers, RouterEntity::TYPE_ROUTE_SERVER );

        if( $vlanid ) {
            $routers = D2EM::getRepository( RouterEntity::class )->filterCollectionOnVlanId( $routers, $vlanid );
        }
        
        if( !count( $routers ) ) {
            abort( 404, "No suitable definition(s) in config/routers.php found." );
        }
        
        $map   = [];
        $vlans = [];
        
        foreach( $routers as $h => $router ) {

            $h = $router->getHandle();
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
            ->header('Content-Type', 'text/plain; charset=utf-8');
    }

}
