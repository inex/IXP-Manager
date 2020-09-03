<?php

namespace IXP\Models;

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

use Eloquent;

use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo, Relations\HasMany, Relations\HasOne};
use Illuminate\Support\Collection;
use IXP\Exceptions\Services\Grapher\ParameterException as GrapherParameterException;
use IXP\Services\Grapher\Graph;


/**
 * IXP\Models\VlanInterface
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
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\Layer2Address[] $layer2addresses
 * @property-read int|null $layer2addresses_count
 * @property-read \IXP\Models\VirtualInterface|null $virtualInterface
 * @property-read \IXP\Models\Vlan|null $vlan
 */
class VlanInterface extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vlaninterface';

    /**
     * Get the customer that owns the virtual interfaces.
     */
    public function virtualInterface(): BelongsTo
    {
        return $this->belongsTo(VirtualInterface::class, 'virtualinterfaceid');
    }

    /**
     * Get the vlan that holds the vlan interface.
     */
    public function vlan(): BelongsTo
    {
        return $this->belongsTo(Vlan::class, 'vlanid');
    }

    /**
     * Get the layer2addresses for the vlan interface
     */
    public function layer2addresses(): HasMany
    {
        return $this->hasMany(Layer2Address::class, 'vlan_interface_id' );
    }

    /**
     * Get the ipv4address associated with the vlaninterface.
     */
    public function ipv4address(): BelongsTo
    {
        return $this->belongsTo(IPv4Address::class, 'ipv4addressid' );
    }

    /**
     * Get the ipv6address associated with the vlaninterface.
     */
    public function ipv6address(): BelongsTo
    {
        return $this->belongsTo(IPv6Address::class, 'ipv6addressid' );
    }

    /**
     * See if a given protocol is enabled
     *
     * @param int $p
     *
     * @return bool
     */
    public function protocolEnabled( int $p ): bool
    {
        return $p === 4 ? $this->ipv4enabled : $this->ipv6enabled;
    }



    /**
     * Is this VLAN interface graphable?
     *
     * @return bool
     */
    public function isGraphable(): bool
    {
        return $this->virtualInterface->isGraphable();
    }

    /**
     * Convenience function to see if we can graph a VLI for latency for a given protocol
     *
     * @param string $protocol Either ipv4 / ipv6 (as defined in Grapher)
     *
     * @return bool
     *
     * @throws
     */
    public function canGraphForLatency( string $protocol ): bool
    {
        if( !isset( Graph::PROTOCOLS_REAL[ $protocol ] ) ) {
            throw new GrapherParameterException( 'Unknown protocol: ' . $protocol );
        }

        $fnAddress = strtolower( $protocol ) . 'addressid';
        $fnCanping = strtolower( $protocol ) . 'canping';
        $fnEnabled = strtolower( $protocol ) . 'enabled';

        return !$this->vlan->private
            && $this->$fnEnabled
            && $this->$fnCanping
            && $this->$fnAddress;
    }

    /**
     * Convenience function to get an IP address based on a given protocol
     *
     * @param string $protocol Either ipv4 / ipv6 (as defined in Grapher)
     *
     * @return null|IPv4Address|IPv6Address
     *
     * @throws
     */
    public function getIPAddress( string $protocol )
    {
        if( !isset( Graph::PROTOCOLS_REAL[ $protocol ] ) ) {
            $protocol = 'ipv4';
        }

        $fnAddress = ucfirst( $protocol ) . 'address';

        return $this->$fnAddress;
    }

    /**
     * Convenience function to see if an IP protocol is enabled
     *
     * @param string $protocol Either ipv4 / ipv6 (as defined in Grapher)
     *
     * @return bool
     *
     * @throws
     */
    public function isIPEnabled( string $protocol ): bool
    {
        if( !isset( Graph::PROTOCOLS_REAL[$protocol] ) ) {
            throw new GrapherParameterException( 'Unknown protocol: ' . $protocol );
        }

        $fnEnabled = strtolower( $protocol ) . 'enabled';

        return $this->$fnEnabled;
    }

    /**
     * Utility function to provide an array of VLAN interface objects on a given VLAN.
     *
     * @param Vlan $vlan The VLAN to gather VlanInterfaces for
     * @param bool $protocol Either 4 or 6 to limit the results to interface with IPv4 / IPv6
     *
     * @return Collection
     *
     */
    public static function getForVlan( Vlan $vlan, $protocol = false )
    {
        return self::select( [ 'vli.*' ] )
            ->from( 'vlaninterface AS vli' )
            ->Join( 'vlan AS v', 'v.id', 'vli.vlanid' )
            ->Join( 'virtualinterface AS vi', 'vi.id', 'vli.virtualinterfaceid' )
            ->Join( 'physicalinterface AS pi', 'pi.virtualinterfaceid', 'vi.id' )
            ->Join( 'cust AS c', 'c.id', 'vi.custid' )
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
            ->orderBy( 'c.name' )->get()->keyBy( 'id' );
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
    public static function getForProto( Vlan $vlan, int $proto, int $pistatus = PhysicalInterface::STATUS_CONNECTED ) : array
    {
        if( !in_array( $proto, [ 4, 6 ] ) ){
            $proto = 4;
        }

        return self::select( [
            'c.id AS cid', 'c.name AS cname',
            'c.abbreviatedName AS abrevcname',
            'c.shortname AS cshortname',
            'c.autsys AS autsys', 'c.maxprefixes AS gmaxprefixes',
            'c.peeringmacro AS peeringmacro', 'c.peeringmacrov6  AS peeringmacrov6',

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
            ->leftJoin( 'cust AS c', 'c.id', 'vi.custid' )
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
            ->groupByRaw( "vli.id, c.id, c.name, c.abbreviatedName, c.shortname, c.autsys,
                        c.maxprefixes, c.peeringmacro, c.peeringmacrov6,
                        vli.ipv{$proto}enabled, addr.address, vli.ipv{$proto}bgpmd5secret, vli.maxbgpprefix,
                        vli.ipv{$proto}hostname, vli.ipv{$proto}monitorrcbgp, vli.busyhost,
                        vli.as112client, vli.rsclient, vli.irrdbfilter, vli.ipv{$proto}canping,
                        s.id, s.name,
                        cab.id, cab.name,
                        l.name, l.shortname, l.tag" )
            ->orderByRaw( 'c.autsys ASC, vli.id ASC' )->get()->toArray();
    }

    /**
     * Utility function to provide an array of all VLAN interface objects for a given
     * customer at an optionally given IXP.
     *
     * @param Customer $c
     *
     * @return Collection
     *
     */
    public static function getForCustomer( Customer $c ) : Collection
    {
        return self::select( [ 'vli.*' ] )
            ->from( 'vlaninterface AS vli' )
            ->Join( 'virtualinterface AS vi', 'vi.id', 'vli.virtualinterfaceid' )
            ->Join( 'cust AS c', 'c.id', 'vi.custid' )
            ->Join( 'vlan AS v', 'v.id', 'vli.vlanid' )
            ->where( 'c.id', $c->id )
            ->orderBy( 'v.number' )->get()->keyBy( 'id' );
    }

    /**
     * Get statistics of RS clients / total on a per VLAN basis
     *
     * Returns an array of objects such as:
     *
     *     [
     *         {
     *             +"vlanname": "Peering VLAN #1",
     *             ++"overall_count": 60,
     *             ++"rsclient_count": "54",
     *         }
     *     ]
     *
     * @return array
     */
    public static function getRsClientUsagePerVlan(): array
    {
        return self::selectRaw( 'v.name AS vlanname,
         COUNT(vli.id) AS overall_count, 
         SUM(vli.rsclient = 1) AS rsclient_count' )
            ->from( 'vlaninterface AS vli' )
            ->Join( 'virtualinterface AS vi', 'vi.id', 'vli.virtualinterfaceid' )
            ->Join( 'cust AS c', 'c.id', 'vi.custid' )
            ->Join( 'vlan AS v', 'v.id', 'vli.vlanid' )
            ->where( 'v.private', false )
            ->whereIn( 'c.type', [1,4] )
            ->groupBy( 'vlanname' )->get()->toArray();
    }

    /**
     * Get statistics of ipv6 enabled / total on a per VLAN basis
     *
     * Returns an array of objects such as:
     *
     *     [
     *         {
     *             +"vlanname": "Peering VLAN #1",
     *             ++"overall_count": 60,
     *             ++"ipv6_count": "54",
     *         }
     *     ]
     *
     * @return array
     */
    public static function getIPv6UsagePerVlan(): array
    {
        return self::selectRaw( 'v.name AS vlanname, 
        COUNT(vli.id) AS overall_count, 
        SUM(vli.ipv6enabled = 1) AS ipv6_count' )
            ->from( 'vlaninterface AS vli' )
            ->Join( 'virtualinterface AS vi', 'vi.id', 'vli.virtualinterfaceid' )
            ->Join( 'cust AS c', 'c.id', 'vi.custid' )
            ->Join( 'vlan AS v', 'v.id', 'vli.vlanid' )
            ->where( 'v.private', false )
            ->whereIn( 'c.type', [1,4] )
            ->groupBy( 'vlanname' )->get()->toArray();
    }
}
