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

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use IXP\Models\Customer;
use IXP\Models\Switcher;
use IXP\Models\SwitchPort;

/**
 * IXP\Models\Aggregators\SwitcherAggregator
 *
 * @property int $id
 * @property int|null $cabinetid
 * @property int|null $vendorid
 * @property string|null $name
 * @property string|null $ipv4addr
 * @property string|null $ipv6addr
 * @property string|null $snmppasswd
 * @property int|null $infrastructure
 * @property string|null $model
 * @property int|null $active
 * @property string|null $notes
 * @property string|null $hostname
 * @property string|null $os
 * @property string|null $osDate
 * @property string|null $osVersion
 * @property string|null $serialNumber
 * @property string|null $lastPolled
 * @property int|null $mauSupported
 * @property int|null $asn
 * @property string|null $loopback_ip
 * @property string|null $loopback_name
 * @property string|null $mgmt_mac_address
 * @property int|null $snmp_engine_time
 * @property int|null $snmp_system_uptime
 * @property int|null $snmp_engine_boots
 * @property int $poll
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\Cabinet|null $cabinet
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\ConsoleServerConnection[] $consoleServerConnections
 * @property-read int|null $console_server_connections_count
 * @property-read \IXP\Models\Infrastructure|null $infrastructureModel
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\SwitchPort[] $switchPorts
 * @property-read int|null $switch_ports_count
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\SwitcherAggregator newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\SwitcherAggregator newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\SwitcherAggregator query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\SwitcherAggregator whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\SwitcherAggregator whereAsn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\SwitcherAggregator whereCabinetid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\SwitcherAggregator whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\SwitcherAggregator whereHostname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\SwitcherAggregator whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\SwitcherAggregator whereInfrastructure($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\SwitcherAggregator whereIpv4addr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\SwitcherAggregator whereIpv6addr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\SwitcherAggregator whereLastPolled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\SwitcherAggregator whereLoopbackIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\SwitcherAggregator whereLoopbackName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\SwitcherAggregator whereMauSupported($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\SwitcherAggregator whereMgmtMacAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\SwitcherAggregator whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\SwitcherAggregator whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\SwitcherAggregator whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\SwitcherAggregator whereOs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\SwitcherAggregator whereOsDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\SwitcherAggregator whereOsVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\SwitcherAggregator wherePoll($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\SwitcherAggregator whereSerialNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\SwitcherAggregator whereSnmpEngineBoots($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\SwitcherAggregator whereSnmpEngineTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\SwitcherAggregator whereSnmpSystemUptime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\SwitcherAggregator whereSnmppasswd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\SwitcherAggregator whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\SwitcherAggregator whereVendorid($value)
 * @mixin \Eloquent
 * @property-read \IXP\Models\Vendor|null $vendor
 */
class SwitcherAggregator extends Switcher
{
    /**
     * Return an array of all switch names where the array key is the switch id
     *
     * @param int|null      $infraid
     * @param int|null      $locationid
     * @param int|null      $speed

     * @return Collection
     */
    public static function getByLocationInfrastructureSpeed( int $infraid = null, int $locationid = null, int $speed = null ): Collection
    {
        return self::when( $locationid , function( Builder $q, $locationid ) {
            return $q->leftJoin( 'cabinet AS c', 'c.id', 'switch.cabinetid' )
                ->where( 'c.locationid', $locationid);
        })
            ->when( $infraid , function( Builder $q, $infraid ) {
                return $q->whereIn( 's.infrastructure', $infraid );
            })
            ->when( $speed , function( Builder $q, $speed ) {
                return $q->leftjoin( 'switchport AS sp', 'sp.switchid', 'switch.id' )
                    ->leftjoin( 'physicalinterface AS pi', 'pi.switchportid','sp.id' )
                    ->leftjoin( 'virtualinterface AS vi', 'vi.id','pi.virtualinterfaceid' )
                    ->leftjoin( 'vlaninterface AS vli', 'vli.virtualinterfaceid','vi.id' )
                    ->leftjoin( 'ipv4address AS ipv4', 'ipv4.id', '=', 'vli.ipv4addressid' )
                    ->leftjoin( 'ipv6address AS ipv6', 'ipv4.id', '=', 'vli.ipv6addressid' )
                    ->where( 'pi.speed', $speed );
            })
            ->where( 'active', true )
            ->orderBy( 'name', 'ASC' )
            ->get();
    }

    /**
     * Return an array of configurations
     *
     * @param int|null $switchid Switcher id for filtering results
     * @param int|null $infraid Infrastructure id for filtering results
     * @param int|null $facilityid Facility id for filtering results
     * @param int|null $speed Speed filtering results
     * @param int|null $vlanid Vlan id for filtering results
     * @param bool $rsclient
     * @param bool $ipv6enabled
     *
     * @return array
     */
    public static function getConfiguration( int $switchid = null, int $infraid = null, int $facilityid = null, int $speed = null, int $vlanid = null, bool $rsclient = false, bool $ipv6enabled = false ): array
    {
        return self::selectRaw(
            's.name AS switchname, 
                s.id AS switchid,
                GROUP_CONCAT( sp.ifName ) AS ifName,
                GROUP_CONCAT( pi.speed )  AS speed,
                GROUP_CONCAT( pi.status ) AS portstatus,
                cust.name AS customer, 
                cust.id AS custid, 
                cust.autsys AS asn,
                MAX( vli.rsclient    ) AS rsclient,
                MAX( vli.ipv4enabled ) AS ipv4enabled, 
                MAX( vli.ipv6enabled ) AS ipv6enabled, 
                v.name AS vlan,
                GROUP_CONCAT( DISTINCT ipv4.address ) AS ipv4address, 
                GROUP_CONCAT( DISTINCT ipv6.address ) AS ipv6address'
        )
            ->from( 'vlaninterface AS vli' )
            ->leftjoin( 'ipv4address AS ipv4', 'ipv4.id', 'vli.ipv4addressid' )
            ->leftjoin( 'ipv6address AS ipv6', 'ipv6.id', 'vli.ipv6addressid' )
            ->leftjoin( 'vlan AS v', 'v.id', '=', 'vli.vlanid' )
            ->leftjoin( 'virtualinterface AS vi', 'vi.id','vli.virtualinterfaceid' )
            ->leftjoin( 'cust', 'cust.id','vi.custid' )
            ->leftjoin( 'physicalinterface AS pi', 'pi.virtualinterfaceid','vi.id' )
            ->leftjoin( 'switchport AS sp', 'sp.id', 'pi.switchportid' )
            ->leftjoin( 'switch AS s', 's.id', 'sp.switchid' )
            ->leftjoin( 'cabinet AS cab', 'cab.id', 's.cabinetid' )
            ->whereRaw( Customer::SQL_CUST_CURRENT )
            ->when( $switchid , function( Builder $q, $switchid ) {
                return $q->where( 's.id', $switchid);
            })
            ->when( $infraid , function( Builder $q, $infraid ) {
                return $q->where( 's.infrastructure', $infraid );
            })
            ->when( $facilityid , function( Builder $q, $facilityid ) {
                return $q->where( 'cab.locationid', $facilityid );
            })
            ->when( $speed , function( Builder $q, $speed ) {
                return $q->where( 'pi.speed', $speed );
            })
            ->when( $vlanid , function( Builder $q, $vlanid ) {
                return $q->where( 'vli.vlanid', $vlanid );
            }, function ( $query) {
                return $query->where( 'v.private', false );
            })
            ->when( $rsclient , function( Builder $q ) {
                return $q->where( 'vli.rsclient', true );
            })
            ->when( $ipv6enabled , function( Builder $q ) {
                return $q->where( 'vli.ipv6enabled', true );
            })
            ->groupBy( 'customer', 'custid', 'asn', 'switchname', 'switchid', 'vlan' )
            ->orderBy( 'customer', 'ASC' )
            ->get()->toArray();
    }


    /**
     * Returns all available switch ports for a switch.
     *
     * Restrict to only some types of switch port
     * Exclude switch port ids from the list
     *
     * Suitable for other generic use.
     *
     * @param int   $id Switch ID - switch to query
     * @param array $types Switch port type restrict to some types only
     * @param array $spid Switch port IDs, if set, those ports are excluded from the results
     * @param bool  $notAssignToPI
     * @param bool  $piNull
     * @return array
     */
    public static function allPorts( int $id, $types = [], $spid = [], bool $notAssignToPI = true, bool $piNull = true ): array
    {
        $ports = self::select( [ 'sp.name AS name', 'sp.type AS porttype', 'sp.id AS id' ] )
            ->leftJoin( 'switchport AS sp', 'sp.switchid', 'switch.id' )
            ->when( $notAssignToPI, function( Builder $q ) use( $piNull ) {
                return $q->addSelect( [ 'pi.id AS pi_id' ] )
                    ->leftJoin( 'physicalinterface as pi', 'pi.switchportid', 'sp.id' )
                    ->when( $piNull , function( Builder $q ) {
                        return $q->whereNull( 'pi.id' );
                    } );
            } )->where( 'switch.id', $id )
            ->when( count( $types ) > 0, function( Builder $q ) use( $types ) {
                return $q->whereIn( 'sp.type', $types );
            } )
            ->when( $spid !== null && count( $spid ) > 0, function( Builder $q ) use( $spid ) {
                return $q->whereNotIn( 'sp.id', $spid );
            } )
            ->orderBy( 'sp.id' )
            ->get()->keyBy( 'id' )->toArray();

        foreach( $ports as $index => $port ){
            $ports[ $index ][ 'type' ]             = SwitchPort::$TYPES[ $port[ 'porttype' ] ];
            $ports[ $index ][ 'spname-sptype' ]    = $port[ "name" ] . ' (' . SwitchPort::$TYPES[ $port[ 'porttype' ] ] . ')';
        }

        return $ports;
    }
}