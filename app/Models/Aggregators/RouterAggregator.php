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
 * @property int|null $pair_id
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
 * @property \Illuminate\Support\Carbon|null $last_update_started
 * @property bool $rpki
 * @property string|null $software_version
 * @property string|null $operating_system
 * @property string|null $operating_system_version
 * @property int $rfc1997_passthru
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $last_updated
 * @property int $pause_updates
 * @property-read Router|null $pair
 * @property-read \IXP\Models\Vlan $vlan
 * @method static Builder<static>|RouterAggregator hasApi()
 * @method static Builder<static>|RouterAggregator ipProtocol(int $protocol)
 * @method static Builder<static>|RouterAggregator ipv4()
 * @method static Builder<static>|RouterAggregator ipv6()
 * @method static Builder<static>|RouterAggregator largeCommunities()
 * @method static Builder<static>|RouterAggregator newModelQuery()
 * @method static Builder<static>|RouterAggregator newQuery()
 * @method static Builder<static>|RouterAggregator notQuarantine()
 * @method static Builder<static>|RouterAggregator query()
 * @method static Builder<static>|RouterAggregator routeCollector()
 * @method static Builder<static>|RouterAggregator routeServer()
 * @method static Builder<static>|RouterAggregator rpki()
 * @method static Builder<static>|RouterAggregator whereApi($value)
 * @method static Builder<static>|RouterAggregator whereApiType($value)
 * @method static Builder<static>|RouterAggregator whereAsn($value)
 * @method static Builder<static>|RouterAggregator whereBgpLc($value)
 * @method static Builder<static>|RouterAggregator whereCreatedAt($value)
 * @method static Builder<static>|RouterAggregator whereHandle($value)
 * @method static Builder<static>|RouterAggregator whereId($value)
 * @method static Builder<static>|RouterAggregator whereLastUpdateStarted($value)
 * @method static Builder<static>|RouterAggregator whereLastUpdated($value)
 * @method static Builder<static>|RouterAggregator whereLgAccess($value)
 * @method static Builder<static>|RouterAggregator whereMgmtHost($value)
 * @method static Builder<static>|RouterAggregator whereName($value)
 * @method static Builder<static>|RouterAggregator whereOperatingSystem($value)
 * @method static Builder<static>|RouterAggregator whereOperatingSystemVersion($value)
 * @method static Builder<static>|RouterAggregator wherePairId($value)
 * @method static Builder<static>|RouterAggregator wherePauseUpdates($value)
 * @method static Builder<static>|RouterAggregator wherePeeringIp($value)
 * @method static Builder<static>|RouterAggregator whereProtocol($value)
 * @method static Builder<static>|RouterAggregator whereQuarantine($value)
 * @method static Builder<static>|RouterAggregator whereRfc1997Passthru($value)
 * @method static Builder<static>|RouterAggregator whereRouterId($value)
 * @method static Builder<static>|RouterAggregator whereRpki($value)
 * @method static Builder<static>|RouterAggregator whereShortname($value)
 * @method static Builder<static>|RouterAggregator whereSkipMd5($value)
 * @method static Builder<static>|RouterAggregator whereSoftware($value)
 * @method static Builder<static>|RouterAggregator whereSoftwareVersion($value)
 * @method static Builder<static>|RouterAggregator whereTemplate($value)
 * @method static Builder<static>|RouterAggregator whereType($value)
 * @method static Builder<static>|RouterAggregator whereUpdatedAt($value)
 * @method static Builder<static>|RouterAggregator whereVlanId($value)
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
     * @return array[]
     *
     * @psalm-return array<array>
     */
    public static function forDropdown( ?Customer $cust = null, ?User $user = null ): array
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
     * @return array[][]
     *
     * @psalm-return array<array<non-empty-list<mixed>>>
     */
    public static function forTab( ?Customer $cust = null, ?User $user = null ): array
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