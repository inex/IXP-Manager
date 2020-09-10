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

use IXP\Models\Customer;

/**
 * IXP\Models\Aggregators\CustomerAggregator
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $type
 * @property string|null $shortname
 * @property int|null $autsys
 * @property int|null $maxprefixes
 * @property string|null $peeringemail
 * @property string|null $nocphone
 * @property string|null $noc24hphone
 * @property string|null $nocfax
 * @property string|null $nocemail
 * @property string|null $nochours
 * @property string|null $nocwww
 * @property int|null $irrdb
 * @property string|null $peeringmacro
 * @property string|null $peeringpolicy
 * @property string|null $corpwww
 * @property \Illuminate\Support\Carbon|null $datejoin
 * @property \Illuminate\Support\Carbon|null $dateleave
 * @property int|null $status
 * @property int|null $activepeeringmatrix
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $lastupdatedby
 * @property string|null $creator
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property int|null $company_registered_detail_id
 * @property int|null $company_billing_details_id
 * @property string|null $peeringmacrov6
 * @property string|null $abbreviatedName
 * @property string|null $MD5Support
 * @property int|null $reseller
 * @property int $isReseller
 * @property int $in_manrs
 * @property int $in_peeringdb
 * @property int $peeringdb_oauth
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\ConsoleServerConnection[] $consoleServerConnections
 * @property-read int|null $console_server_connections_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\Contact[] $contacts
 * @property-read int|null $contacts_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\DocstoreCustomerDirectory[] $docstoreCustomerDirectories
 * @property-read int|null $docstore_customer_directories_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\DocstoreCustomerFile[] $docstoreCustomerFiles
 * @property-read int|null $docstore_customer_files_count
 * @property-read \IXP\Models\IrrdbConfig|null $irrdbConfig
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\IrrdbPrefix[] $irrdbPrefixes
 * @property-read int|null $irrdb_prefixes_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\PatchPanelPort[] $patchPanelPorts
 * @property-read int|null $patch_panel_ports_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\RouteServerFilter[] $peerRouteServerFilters
 * @property-read int|null $peer_route_server_filters_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\RouteServerFilter[] $routeServerFilters
 * @property-read int|null $route_server_filters_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\TrafficDaily[] $trafficDailies
 * @property-read int|null $traffic_dailies_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\User[] $users
 * @property-read int|null $users_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\VirtualInterface[] $virtualInterfaces
 * @property-read int|null $virtual_interfaces_count
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer current()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer currentActive($trafficing = false, $externalOnly = false)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer trafficking()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator whereAbbreviatedName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator whereActivepeeringmatrix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator whereAutsys($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator whereCompanyBillingDetailsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator whereCompanyRegisteredDetailId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator whereCorpwww($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator whereCreator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator whereDatejoin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator whereDateleave($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator whereInManrs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator whereInPeeringdb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator whereIrrdb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator whereIsReseller($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator whereLastupdatedby($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator whereMD5Support($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator whereMaxprefixes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator whereNoc24hphone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator whereNocemail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator whereNocfax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator whereNochours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator whereNocphone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator whereNocwww($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator wherePeeringdbOauth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator wherePeeringemail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator wherePeeringmacro($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator wherePeeringmacrov6($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator wherePeeringpolicy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator whereReseller($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator whereShortname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\CustomerAggregator whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\PatchPanelPortHistory[] $patchPanelPortHistories
 * @property-read int|null $patch_panel_port_histories_count
 */
class CustomerAggregator extends Customer
{

    /**
     * Get All customer by vlan and protocol
     *
     * @param int|null $vlanid
     * @param int|null $protocol
     *
     * @return array
     */
    public static function getByVlanAndProtocol( int $vlanid = null, int $protocol = null ): array
    {
        return self::select( [ 'c.id', 'c.name' ] )
            ->from( 'cust AS c' )
            ->leftJoin( 'virtualinterface AS vi', 'vi.custid', 'c.id' )
            ->leftJoin( 'vlaninterface AS vli', 'vli.virtualinterfaceid', 'vi.id' )
            ->leftJoin( 'vlan AS v', 'v.id', 'vli.vlanid' )
            ->leftJoin( 'routers AS r', 'r.vlan_id', 'v.id' )
            ->where( 'vli.rsclient', true )
            ->when( $protocol, function( Builder $q, $protocol ) {
                return $q->where( 'r.protocol', $protocol )
                    ->where( "vli.ipv{$protocol}enabled", true );
            }, function( $query ) {
                return $query->where( function( $q ) {
                    $q->where( 'r.protocol', 4 )
                        ->orWhere( 'r.protocol', 6 );
                } )->where( function( $q ) {
                    $q->where( 'vli.ipv4enabled', true )
                        ->orWhere( 'vli.ipv6enabled', true );
                } );
            } )->when( $vlanid, function( Builder $q, $vlanid ) {
                return $q->where( 'v.id', $vlanid );
            } )->distinct( 'c.id' )->orderBy( 'c.name', 'asc' )->get()->toArray();
    }
}