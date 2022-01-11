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

use Illuminate\Database\Eloquent\{
    Builder,
};
use Illuminate\Support\Collection;

use IXP\Models\{Customer, PhysicalInterface, Router, Vlan, VlanInterface};

/**
 * IXP\Models\Aggregators\VlanInterfaceAggregator
 *
 * @property int $id
 * @property int|null $ipv4addressid
 * @property int|null $ipv6addressid
 * @property int|null $virtualinterfaceid
 * @property int|null $vlanid
 * @property int|null $ipv4enabled
 * @property string|null $ipv4hostname
 * @property int|null $ipv6enabled
 * @property string|null $ipv6hostname
 * @property int|null $mcastenabled
 * @property int|null $irrdbfilter
 * @property string|null $bgpmd5secret
 * @property string|null $ipv4bgpmd5secret
 * @property string|null $ipv6bgpmd5secret
 * @property int|null $maxbgpprefix
 * @property int|null $rsclient
 * @property int|null $ipv4canping
 * @property int|null $ipv6canping
 * @property int|null $ipv4monitorrcbgp
 * @property int|null $ipv6monitorrcbgp
 * @property int|null $as112client
 * @property int|null $busyhost
 * @property string|null $notes
 * @property int $rsmorespecifics
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\IPv4Address|null $ipv4address
 * @property-read \IXP\Models\IPv6Address|null $ipv6address
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\Layer2Address[] $layer2addresses
 * @property-read int|null $layer2addresses_count
 * @property-read \IXP\Models\VirtualInterface|null $virtualInterface
 * @property-read Vlan|null $vlan
 * @method static Builder|VlanInterfaceAggregator newModelQuery()
 * @method static Builder|VlanInterfaceAggregator newQuery()
 * @method static Builder|VlanInterfaceAggregator query()
 * @method static Builder|VlanInterfaceAggregator whereAs112client($value)
 * @method static Builder|VlanInterfaceAggregator whereBgpmd5secret($value)
 * @method static Builder|VlanInterfaceAggregator whereBusyhost($value)
 * @method static Builder|VlanInterfaceAggregator whereCreatedAt($value)
 * @method static Builder|VlanInterfaceAggregator whereId($value)
 * @method static Builder|VlanInterfaceAggregator whereIpv4addressid($value)
 * @method static Builder|VlanInterfaceAggregator whereIpv4bgpmd5secret($value)
 * @method static Builder|VlanInterfaceAggregator whereIpv4canping($value)
 * @method static Builder|VlanInterfaceAggregator whereIpv4enabled($value)
 * @method static Builder|VlanInterfaceAggregator whereIpv4hostname($value)
 * @method static Builder|VlanInterfaceAggregator whereIpv4monitorrcbgp($value)
 * @method static Builder|VlanInterfaceAggregator whereIpv6addressid($value)
 * @method static Builder|VlanInterfaceAggregator whereIpv6bgpmd5secret($value)
 * @method static Builder|VlanInterfaceAggregator whereIpv6canping($value)
 * @method static Builder|VlanInterfaceAggregator whereIpv6enabled($value)
 * @method static Builder|VlanInterfaceAggregator whereIpv6hostname($value)
 * @method static Builder|VlanInterfaceAggregator whereIpv6monitorrcbgp($value)
 * @method static Builder|VlanInterfaceAggregator whereIrrdbfilter($value)
 * @method static Builder|VlanInterfaceAggregator whereMaxbgpprefix($value)
 * @method static Builder|VlanInterfaceAggregator whereMcastenabled($value)
 * @method static Builder|VlanInterfaceAggregator whereNotes($value)
 * @method static Builder|VlanInterfaceAggregator whereRsclient($value)
 * @method static Builder|VlanInterfaceAggregator whereRsmorespecifics($value)
 * @method static Builder|VlanInterfaceAggregator whereUpdatedAt($value)
 * @method static Builder|VlanInterfaceAggregator whereVirtualinterfaceid($value)
 * @method static Builder|VlanInterfaceAggregator whereVlanid($value)
 * @mixin \Eloquent
 */
class VlanInterfaceAggregator extends VlanInterface
{

    /**
     * Utility function to provide an array of VLAN interface objects on a given VLAN.
     *
     * @param Vlan $vlan The VLAN to gather VlanInterfaces for
     * @param bool $protocol Either 4 or 6 to limit the results to interface with IPv4 / IPv6
     *
     * @return Collection
     *
     */
    public static function forVlan( Vlan $vlan, $protocol = false )
    {
        return self::select( [ 'vli.*' ] )
            ->from( 'vlaninterface AS vli' )
            ->Join( 'vlan AS v', 'v.id', 'vli.vlanid' )
            ->Join( 'virtualinterface AS vi', 'vi.id', 'vli.virtualinterfaceid' )
            ->Join( 'physicalinterface AS pi', 'pi.virtualinterfaceid', 'vi.id' )
            ->Join( 'cust', 'cust.id', 'vi.custid' )
            ->where( 'v.id', $vlan->id )
            ->whereRaw( Customer::SQL_CUST_ACTIVE )
            ->whereRaw( Customer::SQL_CUST_CURRENT )
            ->whereRaw( Customer::SQL_CUST_TRAFFICING )
            ->whereRaw( Customer::SQL_CUST_EXTERNAL )
            ->where( 'pi.status', PhysicalInterface::STATUS_CONNECTED )
            ->when( $protocol , function( Builder $q, $proto ) {
                $p = in_array( $proto, [ 4, 6 ], true ) ? $proto : 4;
                return $q->whereRaw( "vli.ipv{$p}enabled = 1" );
            })
            ->orderBy( 'cust.name' )->get()->keyBy( 'id' );
    }

    /**
     * Utility function to provide an array of all VLAN interfaces on a given
     * VLAN for a given protocol.
     *
     * Returns an array of elements such as:
     *
     *     [
     *         [cid] => 999
     *         [cname] => Customer Name
     *         [abrevcname] => Abbreviated Customer Name
     *         [cshortname] => shortname
     *         [autsys] => 65500
     *         [gmaxprefixes] => 20        // from cust table (global)
     *         [peeringmacro] => ABC
     *         [peeringmacrov6] => ABC
     *         [vid]        => 2
     *         [vtag]       => 10,
     *         [vname]      => "Peering LAN #1
     *         [viid] => 120
     *         [vliid] => 159
     *         [canping] => 1
     *         [enabled] => 1              // VLAN interface enabled for requested protocol?
     *         [address] => 192.0.2.123    // assigned address for requested protocol?
     *         [monitorrcbgp] => 1
     *         [bgpmd5secret] => qwertyui  // MD5 for requested protocol
     *         [hostname] => hostname      // Hostname
     *         [maxbgpprefix] => 20        // VLAN interface max prefixes
     *         [as112client] => 1          // if the member is an as112 client or not
     *         [rsclient] => 1             // if the member is a route server client or not
     *         [rsmorespecifics] => 0/1    // if IRRDB filtering should allow more specifics
     *         [busyhost]
     *         [sid]
     *         [sname]
     *         [cabid]
     *         [cabname]
     *         [location_name]
     *         [location_tag]
     *         [location_shortname]
     *     ]
     *
     * @param Vlan  $vlan       The VLAN
     * @param int   $proto      Either 4 or 6
     * @param int   $pistatus   The status of the physical interface
     *
     * @return array
     *
     * @throws
     */
    public static function forProto( Vlan $vlan, int $proto, int $pistatus = PhysicalInterface::STATUS_CONNECTED ) : array
    {
        if( !in_array( $proto, [ 4, 6 ] ) ){
            $proto = 4;
        }

        return self::select( [
            'cust.id AS cid', 'cust.name AS cname',
            'cust.abbreviatedName AS abrevcname',
            'cust.shortname AS cshortname',
            'cust.autsys AS autsys', 'cust.maxprefixes AS gmaxprefixes',
            'cust.peeringmacro AS peeringmacro', 'cust.peeringmacrov6  AS peeringmacrov6',

            'v.id AS vid', 'v.number AS vtag', 'v.name AS vname', 'vi.id AS viid',

            'vli.id AS vliid',

            "vli.ipv{$proto}enabled      AS enabled" ,
            "vli.ipv{$proto}hostname     AS hostname" ,
            "vli.ipv{$proto}monitorrcbgp AS monitorrcbgp" ,
            "vli.ipv{$proto}bgpmd5secret AS bgpmd5secret" ,
            'vli.maxbgpprefix            AS maxbgpprefix' ,
            'vli.as112client             AS as112client' ,
            'vli.rsclient                AS rsclient' ,
            'vli.busyhost                AS busyhost' ,
            'vli.irrdbfilter             AS irrdbfilter' ,
            'vli.rsmorespecifics         AS rsmorespecifics' ,
            "vli.ipv{$proto}canping      AS canping" ,

            'addr.address AS address',

            's.id   AS sid' ,
            's.name AS sname' ,

            'cab.id   AS cabid' ,
            'cab.name AS cabname' ,

            'l.id        AS location_id' ,
            'l.name      AS location_name' ,
            'l.shortname AS location_shortname' ,
            'l.tag       AS location_tag'
        ] )
            ->from( 'vlaninterface AS vli' )
            ->leftJoin( 'virtualinterface AS vi', 'vi.id', 'vli.virtualinterfaceid' )
            ->leftJoin( "ipv{$proto}address AS addr", 'addr.id', "vli.ipv{$proto}addressid" )
            ->leftJoin( 'cust', 'cust.id', 'vi.custid' )
            ->leftJoin( 'physicalinterface AS pi', 'pi.virtualinterfaceid', 'vi.id' )
            ->leftjoin( 'switchport AS sp', 'sp.id', 'pi.switchportid' )
            ->leftJoin( 'switch AS s', 's.id', 'sp.switchid')
            ->leftJoin( 'cabinet AS cab', 'cab.id', 's.cabinetid')
            ->leftJoin( 'location AS l', 'l.id', 'cab.locationid')
            ->leftJoin( 'vlan AS v', 'v.id', 'vli.vlanid' )
            ->where( 'v.id', $vlan->id )
            ->whereRaw( Customer::SQL_CUST_ACTIVE )
            ->whereRaw( Customer::SQL_CUST_CURRENT )
            ->whereRaw( Customer::SQL_CUST_TRAFFICING )
            ->where( 'pi.status', $pistatus )
            ->groupByRaw( "vli.id, cust.id, cust.name, cust.abbreviatedName, cust.shortname, cust.autsys,
                        cust.maxprefixes, cust.peeringmacro, cust.peeringmacrov6,
                        vli.ipv{$proto}enabled, addr.address, vli.ipv{$proto}bgpmd5secret, vli.maxbgpprefix,
                        vli.ipv{$proto}hostname, vli.ipv{$proto}monitorrcbgp, vli.busyhost,
                        vli.as112client, vli.rsclient, vli.irrdbfilter, vli.ipv{$proto}canping,
                        s.id, s.name,
                        cab.id, cab.name,
                        l.name, l.shortname, l.tag" )
            ->orderByRaw( 'cust.autsys ASC, vli.id ASC' )->get()->toArray();
    }



    /**
     * Find all IP addresses on a given VLAN for a given ASN and protocol.
     *
     * This is used (for example) when generating router configuration
     * which prevents next-hop hijacking but allows the same ASN to
     * set its other IPs as the next hop.
     *
     * @param Vlan $v
     * @param int $asn
     * @param int $proto
     *
     * @return array Array of IP addresses [ '192.0.2.2', '192.0.2.23', ]
     *
     * @throws
     */
    public static function getAllIPsForASN( Vlan $v, int $asn, int $proto ): array
    {
        if( !in_array( $proto, [ 4,6 ] , true ) ) {
            throw new \Exception( 'Invalid inet protocol' );
        }

        $ips = Vlan::select( [ 'ip.address' ] )
            ->from( 'vlaninterface AS vli' )
            ->leftJoin( 'vlan AS v', 'v.id', 'vli.vlanid' )
            ->leftJoin( "ipv{$proto}address AS ip", 'ip.id', "vli.ipv{$proto}addressid" )
            ->leftJoin( 'virtualinterface AS vi', 'vi.id', 'vli.virtualinterfaceid')
            ->leftJoin( 'cust', 'cust.id', 'vi.custid')
            ->where( 'cust.autsys', $asn )->where( 'v.id', $v->id )
            ->get()->pluck( 'address' )->toArray();

        $vips = [];
        foreach( $ips as $ip ) {
            if( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
                $vips[] = $ip;
            }
        }

        return $vips;
    }

    /**
     * Utility function to get and return active VLAN interfaces on the requested protocol
     * suitable for route collector / server configuration.
     *
     * Sample return:
     *
     *     [
     *         [cid] => 999
     *         [cname] => Customer Name
     *         [cshortname] => shortname
     *         [autsys] => 65000
     *         [peeringmacro] => QWE              // or AS65500 if not defined
     *         [vliid] => 159
     *         [fvliid] => 00159                  // formatted %05d
     *         [address] => 192.0.2.123
     *         [bgpmd5secret] => qwertyui         // or false
     *         [as112client] => 1                 // if the member is an as112 client or not
     *         [rsclient] => 1                    // if the member is a route server client or not
     *         [maxprefixes] => 20
     *         [irrdbfilter] => 0/1               // if IRRDB filtering should be applied
     *         [rsmorespecifics] => 0/1           // if IRRDB filtering should allow more specifics
     *         [location_name] => Interxion DUB1
     *         [location_shortname] => IX-DUB1
     *         [location_tag] => ix1
     *     ]
     *
     * @param Vlan  $vlan
     * @param int   $protocol
     * @param int   $target
     * @param bool  $quarantine
     *
     * @return array As defined above
     *
     */
    public static function sanitiseVlanInterfaces( Vlan $vlan, int $protocol = 4, int $target = Router::TYPE_ROUTE_SERVER, bool $quarantine = false ): array
    {
        $ints = self::forProto( $vlan, $protocol, $quarantine  ? PhysicalInterface::STATUS_QUARANTINE : PhysicalInterface::STATUS_CONNECTED );

        $newints = [];

        foreach( $ints as $index => $int ) {

            if( !$int['enabled'] ) {
                continue;
            }

            $int['protocol'] = $protocol;

            // don't need this anymore:
            unset( $int['enabled'] );

            if( $target === Router::TYPE_ROUTE_SERVER && !$int['rsclient'] ) {
                continue;
            }

            if( $target === Router::TYPE_AS112 && !$int['as112client'] ) {
                continue;
            }

            $int['fvliid'] = sprintf( '%04d', $int['vliid'] );

            if( $int['maxbgpprefix'] && $int['maxbgpprefix'] > $int['gmaxprefixes'] ) {
                $int['maxprefixes'] = $int['maxbgpprefix'];
            } else {
                $int['maxprefixes'] = $int['gmaxprefixes'];
            }

            if( !$int['maxprefixes'] ) {
                $int['maxprefixes'] = 250;
            }

            unset( $int['gmaxprefixes'] );
            unset( $int['maxbgpprefix'] );

            if( $protocol === 6 && $int['peeringmacrov6'] ) {
                $int['peeringmacro'] = $int['peeringmacrov6'];
            }

            if( !$int['peeringmacro'] ) {
                $int['peeringmacro'] = 'AS' . $int['autsys'];
            }

            unset( $int['peeringmacrov6'] );

            if( !$int['bgpmd5secret'] ) {
                $int['bgpmd5secret'] = false;
            }

            $int['allpeeringips'] = self::getAllIPsForASN( $vlan, $int['autsys'], $protocol );

            // 2021-09 We now load these dynamically on a per neighbour basis in the configuration templates.
            // if( $int['irrdbfilter'] ) {
            //     $int['irrdbfilter_prefixes'] = IrrdbAggregator::prefixesForRouterConfiguration( $int[ 'cid' ], $protocol );
            //     $int['irrdbfilter_asns'    ] = IrrdbAggregator::asnsForRouterConfiguration( $int[ 'cid' ], $protocol );
            // }

            $newints[ $int['address'] ] = $int;
        }

        return $newints;
    }
}