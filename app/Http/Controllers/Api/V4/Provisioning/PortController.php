<?php

namespace IXP\Http\Controllers\Api\V4\Provisioning;

/*
 * Copyright (C) 2026 KleyReX. All Rights Reserved.
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

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use IXP\Http\Controllers\Api\V4\Controller;

use IXP\Models\Aggregators\SwitchPortAggregator;
use IXP\Models\Infrastructure;
use IXP\Models\SwitchPort;
use IXP\Models\Switcher;

/**
 * Provisioning API - Port Controller
 *
 * Finds switch ports which are free to be assigned to a member.
 *
 * The web UI answers this question one switch at a time, because an operator has already
 * picked the switch from a dropdown. An ordering system has not: it knows the infrastructure
 * and the speed it sold, and needs to be told where that fits. This walks every active switch
 * and returns the candidates.
 *
 * Read-only, so a GET is correct here.
 *
 * @author     KleyReX
 * @category   APIv4
 * @package    IXP\Http\Controllers\Api\V4\Provisioning
 * @copyright  Copyright (C) 2026 KleyReX
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PortController extends Controller
{
    /**
     * List switch ports with no physical interface attached.
     *
     * A port counts as free when it has no PhysicalInterface and its type is either unset or
     * peering. Deliberately excluded are core, management, monitor, fanout and reseller ports:
     * handing one of those to an ordering system would let it reassign infrastructure.
     *
     * Optional query parameters:
     *   infrastructure  restrict to one infrastructure id
     *   switch          restrict to one switch id
     *   limit           cap the number of ports returned (default 100, max 1000)
     *
     * @param  Request  $r
     *
     * @return JsonResponse
     */
    public function free( Request $r ): JsonResponse
    {
        $validated = $r->validate( [
            'infrastructure' => 'nullable|integer|exists:infrastructure,id',
            'switch'         => 'nullable|integer|exists:switch,id',
            'limit'          => 'nullable|integer|min:1|max:1000',
        ] );

        $limit = (int)( $validated[ 'limit' ] ?? 100 );

        $switches = Switcher::where( 'active', true )
            ->when( isset( $validated[ 'infrastructure' ] ), function( $q ) use ( $validated ) {
                return $q->where( 'infrastructure', $validated[ 'infrastructure' ] );
            } )
            ->when( isset( $validated[ 'switch' ] ), function( $q ) use ( $validated ) {
                return $q->where( 'id', $validated[ 'switch' ] );
            } )
            ->orderBy( 'name' )
            ->get();

        $ports = [];

        foreach( $switches as $switch ) {
            $free = SwitchPortAggregator::getAllPortsForSwitch(
                $switch->id,
                [ SwitchPort::TYPE_UNSET, SwitchPort::TYPE_PEERING ],
                [],
                true
            );

            foreach( $free as $port ) {
                if( count( $ports ) >= $limit ) {
                    break 2;
                }

                $ports[] = [
                    'switchport'        => (int)$port[ 'id' ],
                    'name'              => $port[ 'name' ],
                    'type'              => (int)$port[ 'porttype' ],
                    'switch'            => $switch->id,
                    'switch_name'       => $switch->name,
                    'infrastructure'    => $switch->infrastructure,
                ];
            }
        }

        return response()->json( [
            'ports'     => $ports,
            'count'     => count( $ports ),
            'truncated' => count( $ports ) >= $limit,
        ] );
    }

    /**
     * List the active switches, so a caller can resolve names to ids without guessing.
     *
     * @return JsonResponse
     */
    public function switches(): JsonResponse
    {
        $switches = Switcher::where( 'active', true )->orderBy( 'name' )->get()
            ->map( fn( Switcher $s ) => [
                'id'                => $s->id,
                'name'              => $s->name,
                'infrastructure'    => $s->infrastructure,
                'hostname'          => $s->hostname,
            ] )->values()->all();

        return response()->json( [ 'switches' => $switches ] );
    }

    /**
     * List the infrastructures and their VLANs, which a caller needs before requesting a port.
     *
     * @return JsonResponse
     */
    public function infrastructures(): JsonResponse
    {
        $infras = [];

        foreach( Infrastructure::orderBy( 'name' )->get() as $infra ) {
            $vlans = [];

            foreach( $infra->vlans as $vlan ) {
                $vlans[] = [
                    'id'     => $vlan->id,
                    'name'   => $vlan->name,
                    'number' => $vlan->number,
                ];
            }

            $infras[] = [
                'id'        => $infra->id,
                'name'      => $infra->name,
                'shortname' => $infra->shortname,
                'isPrimary' => (bool)$infra->isPrimary,
                'vlans'     => $vlans,
            ];
        }

        return response()->json( [ 'infrastructures' => $infras ] );
    }
}
