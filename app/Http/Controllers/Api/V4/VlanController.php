<?php

namespace IXP\Http\Controllers\Api\V4;

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Validator;

use Illuminate\Http\{
    JsonResponse,
    Request
};

use IXP\Models\{
    Aggregators\VlanAggregator,
    Router,
    Vlan
};

/**
 * Vlan API Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   APIv4
 * @package    IXP\Http\Controllers\Api\V4
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
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
     * @param  Vlan    $v      Vlan
     *
     * @return  JsonResponse array of IP addresses
     *
     * @throws
     */
    public function ipAddresses( Vlan $v ) : JsonResponse
    {
        return response()->json([
            'ipv4' => VlanAggregator::ipAddresses( $v->id, Router::PROTOCOL_IPV4 ),
            'ipv6' => VlanAggregator::ipAddresses( $v->id, Router::PROTOCOL_IPV6 )
        ]);
    }

    /**
     * Determine is an IP address /really/ free by checking across all vlans
     *
     * Returns a array of objects where each object is the details of its usage (example below).
     * If not used, returns an empty array.
     *
     * @param Request $r
     *
     * @return  JsonResponse array of object
     *
     * @see VlanAggregator::usedAcrossVlans() for array structure.
     *
     */
    public function usedAcrossVlans( Request $r ) : JsonResponse
    {
        $validator = Validator::make( $r->all(), [
            'ip' => 'required|ip'
        ]);

        if( $validator->fails() ) {
            abort( 422, 'Invalid or no IP address - set POST "ip" parameter.' );
        }

        return response()->json(
            VlanAggregator::usedAcrossVlans( $r->ip )
        );
    }
}