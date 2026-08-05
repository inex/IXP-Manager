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

use IXP\Models\IPv4Address;
use IXP\Models\IPv6Address;
use IXP\Models\Vlan;

/**
 * Provisioning API - IP Controller
 *
 * Reports which addresses in a VLAN's pool are free.
 *
 * Note there is deliberately no "reserve an address" endpoint. An address held by nothing but
 * a promise is a leak waiting to happen: if the caller then fails, the address stays out of
 * circulation with no record of why. Allocation happens as part of creating a connection,
 * inside the same transaction, where it either sticks or is rolled back with everything else.
 * A caller that simply wants the next free address asks for `"auto"` there.
 *
 * @author     KleyReX
 * @category   APIv4
 * @package    IXP\Http\Controllers\Api\V4\Provisioning
 * @copyright  Copyright (C) 2026 KleyReX
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IpController extends Controller
{
    /**
     * List the addresses of a VLAN, marking each as free or in use.
     *
     * Query parameters:
     *   protocol  4, 6 or both (default both)
     *   free      1 to list only unassigned addresses
     *   limit     cap per protocol (default 200, max 2000)
     *
     * @param  Request  $r
     * @param  Vlan     $vlan
     *
     * @return JsonResponse
     */
    public function addresses( Request $r, Vlan $vlan ): JsonResponse
    {
        $validated = $r->validate( [
            'protocol' => 'nullable|integer|in:4,6',
            'free'     => 'nullable|boolean',
            'limit'    => 'nullable|integer|min:1|max:2000',
        ] );

        $limit    = (int)( $validated[ 'limit' ] ?? 200 );
        $freeOnly = (bool)( $validated[ 'free' ] ?? false );
        $protocol = isset( $validated[ 'protocol' ] ) ? (int)$validated[ 'protocol' ] : null;

        $out = [
            'vlan' => [ 'id' => $vlan->id, 'name' => $vlan->name, 'number' => $vlan->number ],
        ];

        if( $protocol !== 6 ) {
            $out[ 'ipv4' ] = $this->listFor( IPv4Address::class, 'INET_ATON', $vlan, $freeOnly, $limit );
        }

        if( $protocol !== 4 ) {
            $out[ 'ipv6' ] = $this->listFor( IPv6Address::class, 'INET6_ATON', $vlan, $freeOnly, $limit );
        }

        return response()->json( $out );
    }

    /**
     * Build the address list for one protocol.
     *
     * @param  class-string<IPv4Address|IPv6Address>  $model
     * @param  string                                 $orderFn
     * @param  Vlan                                   $vlan
     * @param  bool                                   $freeOnly
     * @param  int                                    $limit
     *
     * @return array
     */
    private function listFor( string $model, string $orderFn, Vlan $vlan, bool $freeOnly, int $limit ): array
    {
        $query = $model::where( 'vlanid', $vlan->id )
            ->when( $freeOnly, fn( $q ) => $q->whereDoesntHave( 'vlanInterface' ) )
            ->with( 'vlanInterface' )
            ->orderByRaw( "{$orderFn}(address) ASC" );

        $total = ( clone $query )->count();

        $addresses = $query->limit( $limit )->get()->map( fn( $ip ) => [
            'id'      => $ip->id,
            'address' => $ip->address,
            'free'    => $ip->vlanInterface === null,
        ] )->values()->all();

        return [
            'addresses' => $addresses,
            'total'     => $total,
            'truncated' => $total > count( $addresses ),
        ];
    }
}
