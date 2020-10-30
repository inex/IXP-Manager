<?php

namespace IXP\Models\Aggregators;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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
use IXP\Models\{
    Router,
    Vlan
};


class VlanAggregator extends Vlan
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vlan';

    /**
     * Get the IPv4 or IPv6 list for a vlan as an array.
     *
     * Returns a array sorted by IP address with elements:
     *
     *     {
     *         id: "1040",                     // address ID from the IPv4/6 table
     *         address: "2001:7f8:18::20",     // address
     *         v_id: "2",                      // VLAN id
     *         vli_id: "16"                    // VlanInterface ID (or null if not assigned / in use)
     *     },
     *
     * @param  int $vid     The ID of the VLAN to query
     * @param  int $proto   The IP protocol to get addresses for (one of RouterEntity::PROTOCOL_IPV4/6)
     *
     * @return array Array of addresses as defined above.
     *
     * @throws
     */
    public static function ipAddresses( int $vid, int $proto ) : array
    {
        if( $proto === (int)Router::PROTOCOL_IPV6 ) {
            $orderBy = 'INET6_ATON'; $table = 'ipv6address';
        } else if( $proto === (int)Router::PROTOCOL_IPV4 ) {
            $orderBy = 'INET_ATON'; $table = 'ipv4address';
        } else {
            throw new \Exception('Invalid protocol' );
        }

        return Vlan::select( [
                "${table}.id", "${table}.address",
                'vlan.id AS vid', 'vli.id as vliid'
            ] )
            ->leftJoin( $table, "${table}.vlanid", 'vlan.id' )
            ->leftJoin( 'vlaninterface as vli', "vli.${table}id", "${table}.id" )
            ->where( 'vlan.id', $vid )
            ->orderByRaw( "${orderBy}(address) ASC" )
            ->get()->toArray();

    }

    /**
     * Determine is an IP address /really/ free by checking across all vlans
     *
     * Returns a array of objects as follows (or empty array if not user):
     *
     * [
     *      [
     *          customer: {
     *              id: x,
     *              name: "",
     *              autsys: x,
     *              abbreviated_name: ""
     *          },
     *          virtualinterface: {
     *              id: x
     *          },
     *          vlaninterface: {
     *              id: x
     *          },
     *          vlan: {
     *              id: x,
     *              name: "",
     *              number: x
     *          }
     *      },
     *      {
     *
     *      ]
     * ]
     *
     * @param  string $ip The IPv6/4 address to check
     * @return array Array of object
     */
    public static function usedAcrossVlans( string $ip ) : array
    {
        $table = strpos( $ip, ':' ) !== false ? 'ipv6address' : 'ipv4address' ;

        return Vlan::select( [
                'c.id AS cid', 'c.name AS cname', 'c.autsys AS cautsys', 'c.abbreviatedName AS cabbreviatedname',
                'vi.id AS viid', 'vli.id AS vliid',
                'v.id AS vid', 'v.name AS vname', 'v.number AS vnumber'
            ] )
            ->from( 'vlaninterface AS vli' )
            ->leftJoin( $table, "${table}.id", "vli.${table}id" )
            ->leftJoin( 'virtualinterface AS vi', 'vi.id', 'vli.virtualinterfaceid')
            ->leftJoin( 'cust AS c', 'c.id', 'vi.custid' )
            ->leftJoin( 'vlan AS v', 'v.id', 'vli.vlanid')
            ->where( "${table}.address", $ip )->get()->toArray();
    }
}