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

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Http\{
    Request,
    Response
};

use Illuminate\Support\Facades\View as FacadeView;

use IXP\Models\{
    Aggregators\VlanInterfaceAggregator,
    Infrastructure,
    PhysicalInterface,
    Router,
    Vlan
};

/**
 * Nagios API Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   APIv4
 * @package    IXP\Http\Controllers\Api\V4
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class NagiosController extends Controller
{
    /**
     * An API call to generate customer reachability Nagios configuration for a given VLAN and protocol.
     *
     * @see http://docs.ixpmanager.org/features/nagios/
     *
     * @param Request           $r
     * @param Vlan              $vlan
     * @param int               $protocol
     * @param string|null       $template
     *
     * @return Response
     */
    public function customers( Request $r, Vlan $vlan, int $protocol, string $template = null ): Response
    {
        if( !in_array( $protocol, [ 4, 6 ] ) ) {
            abort( 404, 'Unknown protocol' );
        }

        if( $template === null ) {
            $tmpl = 'api/v4/nagios/customers/default';
        } else {
            $tmpl = sprintf( 'api/v4/nagios/customers/%s', preg_replace( '/[^a-z0-9\-]/', '', strtolower( $template ) ) );
        }

        if( !FacadeView::exists( $tmpl ) ) {
            abort(404, 'Unknown template');
        }

        return response()
            ->view( $tmpl, [
                'vlan'     => $vlan,
                'protocol' => $protocol,
                'vlis'     => VlanInterfaceAggregator::forProto( $vlan, $protocol, PhysicalInterface::STATUS_CONNECTED ),

                // optional POST/GET parameters
                'host_definition'               => $r->input( 'host_definition',              'ixp-manager-member-host'              ),
                'service_definition'            => $r->input( 'service_definition',           'ixp-manager-member-service'           ),
                'ping_service_definition'       => $r->input( 'ping_service_definition',      'ixp-manager-member-ping-service'      ),
                'ping_busy_service_definition'  => $r->input( 'ping_busy_service_definition', 'ixp-manager-member-ping-busy-service' ),

            ], 200 )
            ->header( 'Content-Type', 'text/plain; charset=utf-8' );
    }

    /**
     * An API call to generate production switch host and hostgroups for Nagios configuration for a given infrastructure.
     *
     * @see http://docs.ixpmanager.org/features/nagios/
     *
     * @param Request               $r
     * @param Infrastructure        $infra
     * @param string|null           $template
     *
     * @return Response
     */
    public function switches( Request $r, Infrastructure $infra, string $template = null ): Response
    {
        if( $template === null ) {
            $tmpl = 'api/v4/nagios/switches/default';
        } else {
            $tmpl = sprintf( 'api/v4/nagios/switches/%s', preg_replace( '/[^a-z0-9\-]/', '', strtolower( $template ) ) );
        }

        if( !FacadeView::exists( $tmpl ) ) {
            abort(404, 'Unknown template');
        }

        return response()
            ->view( $tmpl, [
                'infra'    => $infra,
                'switches' => $infra->switchers,

                // optional POST/GET parameters
                'host_definition' => $r->input( 'host_definition', 'ixp-manager-production-switch' ),
            ], 200 )
            ->header( 'Content-Type', 'text/plain; charset=utf-8' );
    }

    /**
     * An API call to generate Birdseye daemon checks for Nagios configuration for all or a given vlan.
     *
     * @see http://docs.ixpmanager.org/features/nagios/
     *
     * @param Request           $r
     * @param string|null       $template
     * @param Vlan|null         $vlan
     *
     * @return Response
     */
    public function birdseyeDaemons( Request $r, string $template = null, Vlan $vlan = null ): Response
    {
        $routers = Router::where( 'api_type', Router::API_TYPE_BIRDSEYE )
            ->when( $vlan, function( Builder $q, $vlan ) {
                return $q->where( 'vlan_id', $vlan->id );
            } )->orderBy( 'handle' )->get();

        if( !$routers->count() ) {
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
                    'routers'   => $routers,
                    'vlanid'    => $vlan->id ?? false,

                    // optional POST/GET parameters
                    'host_definition'    => $r->input( 'host_definition', 'ixp-manager-host-birdseye-daemon' ),
                    'service_definition' => $r->input( 'host_definition', 'ixp-manager-service-birdseye-daemon' ),

                ], 200)
                ->header('Content-Type', 'text/plain; charset=utf-8');
    }


    /**
     * An API call to generate customer BGP session checks for Nagios for a given router type, VLAN and protocol.
     *
     * @see http://docs.ixpmanager.org/features/nagios/
     *
     * @param Request           $r
     * @param Vlan              $vlan
     * @param int               $protocol
     * @param int               $type
     * @param string|null       $template
     *
     * @return Response
     */
    public function birdseyeBgpSessions( Request $r, Vlan $vlan, int $protocol, int $type, string $template = null ): Response
    {
        if( !in_array( $protocol, [ 4, 6 ] ) ) {
            abort( 404, 'Unknown protocol' );
        }

        if( !isset( Router::$TYPES[ $type ] ) ) {
            abort( 404, 'Unknown router type' );
        }

        if( $template === null ) {
            $tmpl = 'api/v4/nagios/birdseye-bgp-sessions/default';
        } else {
            $tmpl = sprintf( 'api/v4/nagios/birdseye-bgp-sessions/%s', preg_replace( '/[^a-z0-9\-]/', '', strtolower( $template ) ) );
        }

        if( !FacadeView::exists( $tmpl ) ) {
            abort(404, 'Unknown template');
        }

        $routers = Router::where( 'api_type', Router::API_TYPE_BIRDSEYE )
            ->where( 'type', $type )
            ->where( 'protocol', $protocol )
            ->where( 'vlan_id', $vlan->id )
            ->orderBy( 'handle' )->get();

        if( !$routers->count() ) {
            abort( 404, "No suitable router(s) found." );
        }

        return response()
            ->view( $tmpl, [
                'vlan'      => $vlan,
                'protocol'  => $protocol,
                'type'      => $type,
                'typeName'  => Router::$TYPES[ $type ],
                'typeShort' => strtolower( Router::$TYPES_SHORT[ $type ] ),
                'routers'   => $routers,
                'vlis'      => VlanInterfaceAggregator::forProto( $vlan, $protocol, PhysicalInterface::STATUS_CONNECTED ),

                // optional POST/GET parameters
                'service_definition'            => $r->input( 'service_definition',           'ixp-manager-member-bgp-session-service'           ),

            ], 200 )
            ->header( 'Content-Type', 'text/plain; charset=utf-8' );
    }
}