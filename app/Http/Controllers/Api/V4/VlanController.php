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
    Router        as RouterEntity,
    Vlan          as VlanEntity,
    VlanInterface as VlanInterfaceEntity
};

use Illuminate\Http\JsonResponse;
use Illuminate\Http\{Request,Response};
use Illuminate\Support\Facades\View as FacadeView;

/**
 * Vlan API Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class VlanController extends Controller
{

    /**
     * Get all IP addresses (v4 and v6) for a given VLAN.
     *
     * Returns a JSON object with two array elements: ipv4 and ipv6
     *
     * Each of these elements contain address objects of the form:
     *
     *     {
     *         id: "1040",                     // address ID from the IPv4/6 table
     *         address: "2001:7f8:18::20",     // address
     *         v_id: "2",                      // VLAN id
     *         vli_id: "16"                    // VlanInterface ID (or null if not assigned / in use)
     *     },

     *
     * @params  Request $request instance of the current HTTP request
     * @params  int $id Vlan id
     * @return  JsonResponse array of IP addresses
     */
    public function getIPAddresses( Request $request, int $id ) : JsonResponse {

        /** @var VlanEntity $vl */
        if( !( $v = D2EM::getRepository( VlanEntity::class )->find( $id ) ) ) {
            return abort( 404 );
        }

        return response()->json([
            'ipv4' => D2EM::getRepository( VlanEntity::class )->getIpAddresses( $v->getId(), RouterEntity::PROTOCOL_IPV4 ),
            'ipv6' => D2EM::getRepository( VlanEntity::class )->getIpAddresses( $v->getId(), RouterEntity::PROTOCOL_IPV6 )
        ]);
    }

    /**
     * Generate Smokeping configuration.
     *
     * @see http://docs.ixpmanager.org/features/smokeping/
     * @param Request $request
     * @param int $vlanid The ID of the VLAN
     * @param int $protocol Either 4 or 6
     * @param string $template Option template to use
     * @return Response
     */
    public function smokepingTargets( Request $request, int $vlanid, int $protocol, string $template = null ): Response {
        /** @var VlanEntity $v */
        if( !( $v =  D2EM::getRepository( VlanEntity::class )->find( $vlanid ) ) ){
            return abort( 404, 'Unknown VLAN' );
        }

        if( !in_array( $protocol, [ 4, 6 ] ) ) {
            return abort( 404, 'Unknown protocol' );
        }

        if( $template === null ) {
            $tmpl = 'api/v4/vlan/smokeping/default';
        } else {
            $tmpl = sprintf( 'api/v4/vlan/smokeping/%s', preg_replace( '/[^a-z0-9\-]/', '', strtolower( $template ) ) );
        }

        if( !FacadeView::exists( $tmpl ) ) {
            abort(404, 'Unknown template');
        }

        if( $request->input( 'probe', false ) ) {
            $probe = $request->input( 'probe' );
        } else {
            $probe = 'FPing' . ( $protocol == 4 ? '' : '6' );
        }

        // try and reorder the VLIs into alphabetical order of customer names
        $vlis = D2EM::getRepository( VlanInterfaceEntity::class )->getForProto( $v, $protocol, false );
        $orderedVlis = [];
        foreach( $vlis as $vli ) {
            $orderedVlis[ $vli['cname'] . '::' . $vli['vliid'] ] = $vli;
        }
        ksort( $orderedVlis, SORT_STRING | SORT_FLAG_CASE );

        return response()
            ->view( $tmpl, [
                    'vlan'     => $v,
                    'vlis'     => $orderedVlis,
                    'probe'    => $probe,
                    'level'    => $request->input( 'level', '+++' ),
                    'protocol' => $protocol
                ], 200 )
            ->header( 'Content-Type', 'text/plain; charset=utf-8' );
    }
}
