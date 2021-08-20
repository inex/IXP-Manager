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
use IXP\Models\Customer;
use IXP\Models\Router;
use IXP\Models\User;

/**
 * IXP\Models\Aggregators\RouterAggregator
 *
 * @property int $id
 * @property int $vlan_id
 * @property string $handle
 * @property int $protocol
 * @property int $type
 * @property string $name
 * @property string $shortname
 * @property string $router_id
 * @property string $peering_ip
 * @property int $asn
 * @property string $software
 * @property string $mgmt_host
 * @property string|null $api
 * @property int $api_type
 * @property int|null $lg_access
 * @property bool $quarantine
 * @property bool $bgp_lc
 * @property string $template
 * @property bool $skip_md5
 * @property \Illuminate\Support\Carbon|null $last_updated
 * @property bool $rpki
 * @property string|null $software_version
 * @property string|null $operating_system
 * @property string|null $operating_system_version
 * @property int $rfc1997_passthru
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\Vlan $vlan
 * @method static Builder|Router hasApi()
 * @method static Builder|Router ipv4()
 * @method static Builder|Router ipv6()
 * @method static Builder|Router largeCommunities()
 * @method static Builder|RouterAggregator newModelQuery()
 * @method static Builder|RouterAggregator newQuery()
 * @method static Builder|Router notQuarantine()
 * @method static Builder|RouterAggregator query()
 * @method static Builder|Router routeServer()
 * @method static Builder|Router rpki()
 * @method static Builder|RouterAggregator whereApi($value)
 * @method static Builder|RouterAggregator whereApiType($value)
 * @method static Builder|RouterAggregator whereAsn($value)
 * @method static Builder|RouterAggregator whereBgpLc($value)
 * @method static Builder|RouterAggregator whereCreatedAt($value)
 * @method static Builder|RouterAggregator whereHandle($value)
 * @method static Builder|RouterAggregator whereId($value)
 * @method static Builder|RouterAggregator whereLastUpdated($value)
 * @method static Builder|RouterAggregator whereLgAccess($value)
 * @method static Builder|RouterAggregator whereMgmtHost($value)
 * @method static Builder|RouterAggregator whereName($value)
 * @method static Builder|RouterAggregator whereOperatingSystem($value)
 * @method static Builder|RouterAggregator whereOperatingSystemVersion($value)
 * @method static Builder|RouterAggregator wherePeeringIp($value)
 * @method static Builder|RouterAggregator whereProtocol($value)
 * @method static Builder|RouterAggregator whereQuarantine($value)
 * @method static Builder|RouterAggregator whereRfc1997Passthru($value)
 * @method static Builder|RouterAggregator whereRouterId($value)
 * @method static Builder|RouterAggregator whereRpki($value)
 * @method static Builder|RouterAggregator whereShortname($value)
 * @method static Builder|RouterAggregator whereSkipMd5($value)
 * @method static Builder|RouterAggregator whereSoftware($value)
 * @method static Builder|RouterAggregator whereSoftwareVersion($value)
 * @method static Builder|RouterAggregator whereTemplate($value)
 * @method static Builder|RouterAggregator whereType($value)
 * @method static Builder|RouterAggregator whereUpdatedAt($value)
 * @method static Builder|RouterAggregator whereVlanId($value)
 * @mixin \Eloquent
 */
class RouterAggregator extends Router
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'routers';

    /**
     * Gather the data for looking glass dropdowns
     *
     * This is the dropdown on the top right of the IXP Manager looking glass interface.
     *
     * @param Customer|null $cust
     * @param User|null     $user
     *
     * @return array
     */
    public static function forDropdown( Customer $cust = null, User $user = null ): array
    {
        $privs = $user ? $user->privs() : User::AUTH_PUBLIC;
        $routers = self::whereNotNull( 'api' )
            ->where( 'api_type', 1 )
            ->where( 'lg_access', '<=', $privs )
            ->when( !$user, function( Builder $q ) {
                return $q->where( 'quarantine', false );
            } )
            ->orderBy( 'handle' )
            ->get()->keyBy( 'handle' );

        $result = [];
        foreach( $routers as $key => $r ) {
            if( $r->quarantine && $privs !== User::AUTH_SUPERUSER && !$cust->hasInterfacesInQuarantine() ) {
                continue;
            }
            $result[ $r->type() ][ $key ] = $r->name;
        }

        return $result;
    }

    /**
     * Gather the data for looking glass dropdowns
     *
     * This is the dropdown on the top right of the IXP Manager looking glass interface.
     *
     * @param Customer|null $cust
     * @param User|null     $user
     *
     * @return array
     */
    public static function forTab( Customer $cust = null, User $user = null )
    {
        $privs = $user ? $user->privs() : User::AUTH_PUBLIC;
        $routers = self::
//        select( [
//            'routers.handle', 'routers.name', 'routers.updated_at'
//        ] )
//            leftJoin( 'vlan as v', 'v.id', 'routers.vlan_id' )
//            ->leftJoin( 'infrastructure as i', 'i.id', 'v.infrastructureid' )
            whereNotNull( 'api' )
            ->where( 'api_type', 1 )
            ->where( 'lg_access', '<=', $privs )
            ->when( !$user, function( Builder $q ) {
                return $q->where( 'quarantine', false );
            } )
            ->get();

        $result = [];
        foreach( $routers as $key => $r ) {
            if( $r->quarantine && $privs !== User::AUTH_SUPERUSER && !$cust->hasInterfacesInQuarantine() ) {
                continue;
            }
            $result[ $r->vlan->infrastructure->name ][ $r->protocol ][] = $r;
        }

        return $result;
    }
}