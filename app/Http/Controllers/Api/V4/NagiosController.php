<?php

namespace IXP\Http\Controllers\Api\V4;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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


    /**
     * An API call to generate customer reachability Nagios configuration for a given VLAN and protocol.
     *
     * @see http://docs.ixpmanager.org/features/nagios/
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $vlanid
     * @param int                      $protocol
     * @param string|null              $template
     * @return \Illuminate\Http\Response
     */
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

        $vlis = D2EM::getRepository( VlanInterfaceEntity::class )->getForProto( $v->getId(), $protocol, PhysicalInterfaceEntity::STATUS_CONNECTED );

        return response()
            ->view( $tmpl, [
                'vlan'     => $v,
                'protocol' => $protocol,
                'vlis'     => $vlis,

                // optional POST/GET parameters
                'host_definition'               => $request->input( 'host_definition',              'ixp-manager-member-host'              ),
                'service_definition'            => $request->input( 'service_definition',           'ixp-manager-member-service'           ),
                'ping_service_definition'       => $request->input( 'ping_service_definition',      'ixp-manager-member-ping-service'      ),
                'ping_busy_service_definition'  => $request->input( 'ping_busy_service_definition', 'ixp-manager-member-ping-busy-service' ),

            ], 200 )
            ->header( 'Content-Type', 'text/plain; charset=utf-8' );
    }


    /**
     * An API call to generate production switch host and hostgroups for Nagios configuration for a given infrastructure.
     *
     * @see http://docs.ixpmanager.org/features/nagios/
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $infraid
     * @param string|null              $template
     * @return \Illuminate\Http\Response
     */
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

        $switches = $infra->getSwitchers();


        return response()
            ->view( $tmpl, [
                'infra'    => $infra,
                'switches' => $switches,

                // optional POST/GET parameters
                'host_definition' => $request->input( 'host_definition', 'ixp-manager-production-switch' ),
            ], 200 )
            ->header( 'Content-Type', 'text/plain; charset=utf-8' );
    }

    /**
     * An API call to generate Birdseye daemon checks for Nagios configuration for all or a given vlan.
     *
     * @see http://docs.ixpmanager.org/features/nagios/
     *
     * @param \Illuminate\Http\Request $request
     * @param string|null              $template
     * @param int                      $vlanid
     * @return \Illuminate\Http\Response
     */
    public function birdseyeDaemons( Request $request, string $template = null, int $vlanid = null )
    {
        $routers = D2EM::getRepository( RouterEntity::class )->filterForApiType( RouterEntity::API_TYPE_BIRDSEYE );

        if( $vlanid ) {
            $routers = D2EM::getRepository( RouterEntity::class )->filterCollectionOnVlanId( $routers, $vlanid );
        }

        if( !count($routers) ) {
            abort( 404, "No routers for the provided VLAN ID / Bird's Eye API type." );
        }

        if( $template === null ) {
            $tmpl = 'api/v4/nagios/birdseye-daemons/default';
        } else {
            $tmpl = sprintf( 'api/v4/nagios/birdseye-daemons/%s', preg_replace( '/[^a-z0-9\-]/', '', strtolower( $template ) ) );
        }

        if( !FacadeView::exists( $tmpl ) ) {
            abort(404, 'Unknown template');
        }

        return response()
                ->view( $tmpl, [
                    'routers' => $routers,
                    'vlanid' => $vlanid ?? false,

                    // optional POST/GET parameters
                    'host_definition'    => $request->input( 'host_definition', 'ixp-manager-host-birdseye-daemon' ),
                    'service_definition' => $request->input( 'host_definition', 'ixp-manager-service-birdseye-daemon' ),

                ], 200)
                ->header('Content-Type', 'text/plain; charset=utf-8');
    }


    /**
     * An API call to generate customer BGP session checks for Nagios for a given router type, VLAN and protocol.
     *
     * @see http://docs.ixpmanager.org/features/nagios/
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $vlanid
     * @param int                      $protocol
     * @param int                      $type
     * @param string|null              $template
     * @return \Illuminate\Http\Response
     */
    public function birdseyeBgpSessions( Request $request, int $vlanid, int $protocol, int $type, string $template = null ): Response {

        if( !in_array( $protocol, [ 4, 6 ] ) ) {
            return abort( 404, 'Unknown protocol' );
        }

        if( !isset( RouterEntity::$TYPES[$type] ) ) {
            return abort( 404, 'Unknown router type' );
        }

        /** @var VlanEntity $v */
        if( !( $v =  D2EM::getRepository( VlanEntity::class )->find( $vlanid ) ) ){
            return abort( 404, 'Unknown VLAN' );
        }

        if( $template === null ) {
            $tmpl = 'api/v4/nagios/birdseye-bgp-sessions/default';
        } else {
            $tmpl = sprintf( 'api/v4/nagios/birdseye-bgp-sessions/%s', preg_replace( '/[^a-z0-9\-]/', '', strtolower( $template ) ) );
        }

        if( !FacadeView::exists( $tmpl ) ) {
            abort(404, 'Unknown template');
        }

        // this is pretty inefficient...
        $routers = D2EM::getRepository( RouterEntity::class )->filterForApiType( RouterEntity::API_TYPE_BIRDSEYE );
        $routers = D2EM::getRepository( RouterEntity::class )->filterCollectionOnType( $routers, $type );
        $routers = D2EM::getRepository( RouterEntity::class )->filterCollectionOnProtocol( $routers, $protocol );
        $routers = D2EM::getRepository( RouterEntity::class )->filterCollectionOnVlanId( $routers, $v->getId() );

        if( !count( $routers ) ) {
            abort( 404, "No suitable router(s) found." );
        }

        $vlis = D2EM::getRepository( VlanInterfaceEntity::class )->getForProto( $v->getId(), $protocol, PhysicalInterfaceEntity::STATUS_CONNECTED );

        return response()
            ->view( $tmpl, [
                'vlan'      => $v,
                'protocol'  => $protocol,
                'type'      => $type,
                'typeName'  => RouterEntity::$TYPES[$type],
                'typeShort' => strtolower( RouterEntity::$TYPES_SHORT[$type] ),
                'routers'   => $routers,
                'vlis'      => $vlis,

                // optional POST/GET parameters
                'service_definition'            => $request->input( 'service_definition',           'ixp-manager-member-bgp-session-service'           ),

            ], 200 )
            ->header( 'Content-Type', 'text/plain; charset=utf-8' );
    }

}
