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

use Illuminate\Database\Eloquent\{
    Builder,
    Model
};

/**
 * IXP\Models\Router
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
 * @property bool|null $lg_access
 * @property bool $quarantine
 * @property bool $bgp_lc
 * @property string $template
 * @property bool $skip_md5
 * @property string|null $last_updated
 * @property bool $rpki
 * @property string|null $software_version
 * @property string|null $operating_system
 * @property string|null $operating_system_version
 * @property int $rfc1997_passthru
 * @property-read \IXP\Models\Vlan $vlan
 * @method static Builder|Router hasApi()
 * @method static Builder|Router iPv4()
 * @method static Builder|Router iPv6()
 * @method static Builder|Router largeCommunities()
 * @method static Builder|Router newModelQuery()
 * @method static Builder|Router newQuery()
 * @method static Builder|Router notQuarantine()
 * @method static Builder|Router query()
 * @method static Builder|Router routeServer()
 * @method static Builder|Router rpki()
 * @method static Builder|Router whereApi($value)
 * @method static Builder|Router whereApiType($value)
 * @method static Builder|Router whereAsn($value)
 * @method static Builder|Router whereBgpLc($value)
 * @method static Builder|Router whereHandle($value)
 * @method static Builder|Router whereId($value)
 * @method static Builder|Router whereLastUpdated($value)
 * @method static Builder|Router whereLgAccess($value)
 * @method static Builder|Router whereMgmtHost($value)
 * @method static Builder|Router whereName($value)
 * @method static Builder|Router whereOperatingSystem($value)
 * @method static Builder|Router whereOperatingSystemVersion($value)
 * @method static Builder|Router wherePeeringIp($value)
 * @method static Builder|Router whereProtocol($value)
 * @method static Builder|Router whereQuarantine($value)
 * @method static Builder|Router whereRfc1997Passthru($value)
 * @method static Builder|Router whereRouterId($value)
 * @method static Builder|Router whereRpki($value)
 * @method static Builder|Router whereShortname($value)
 * @method static Builder|Router whereSkipMd5($value)
 * @method static Builder|Router whereSoftware($value)
 * @method static Builder|Router whereSoftwareVersion($value)
 * @method static Builder|Router whereTemplate($value)
 * @method static Builder|Router whereType($value)
 * @method static Builder|Router whereVlanId($value)
 * @mixin Eloquent
 */
class Router extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'routers';


    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'asn'        => 'integer',
        'lg_access'  => 'boolean',
        'quarantine' => 'boolean',
        'bgp_lc'     => 'boolean',
        'skip_md5'   => 'boolean',
        'rpki'       => 'boolean',
    ];

    /**
     * CONST PROTOCOL
     */
    const PROTOCOL_IPV4                 = 4;
    const PROTOCOL_IPV6                 = 6;

    /**
     * @var array Email ids to classes
     */
    public static $PROTOCOLS = [
        self::PROTOCOL_IPV4     =>      'IPv4',
        self::PROTOCOL_IPV6     =>      'IPv6'
    ];

    /**
     * CONST TYPES
     */
    const TYPE_ROUTE_SERVER                 = 1;
    const TYPE_ROUTE_COLLECTOR              = 2;
    const TYPE_AS112                        = 3;
    const TYPE_OTHER                        = 99;

    /**
     * @var array Email ids to classes
     */
    public static $TYPES = [
        self::TYPE_ROUTE_SERVER             => 'Route Server',
        self::TYPE_ROUTE_COLLECTOR          => 'Route Collector',
        self::TYPE_AS112                    => 'AS112',
        self::TYPE_OTHER                    => 'Other'
    ];

    /**
     * @var array Email ids to classes
     */
    public static $TYPES_SHORT = [
        self::TYPE_ROUTE_SERVER             => 'RS',
        self::TYPE_ROUTE_COLLECTOR          => 'RC',
        self::TYPE_AS112                    => 'AS112',
        self::TYPE_OTHER                    => 'Other'
    ];

    /**
     * CONST SOFTWARES
     */
    const SOFTWARE_BIRD                     = 1;
    const SOFTWARE_BIRD2                    = 6;
    const SOFTWARE_QUAGGA                   = 2;
    const SOFTWARE_FRROUTING                = 3;
    const SOFTWARE_OPENBGPD                 = 4;
    const SOFTWARE_CISCO                    = 5;
    const SOFTWARE_OTHER                    = 99;

    /**
     * @var array Email ids to classes
     */
    public static $SOFTWARES = [
        self::SOFTWARE_BIRD                 => 'Bird v1',
        self::SOFTWARE_BIRD2                => 'Bird v2',
        self::SOFTWARE_QUAGGA               => 'Quagga',
        self::SOFTWARE_FRROUTING            => 'FRRouting',
        self::SOFTWARE_OPENBGPD             => 'OpenBGPd',
        self::SOFTWARE_CISCO                => 'Cisco',
        self::SOFTWARE_OTHER                => 'Other'
    ];

    /**
     * CONST SOFTWARES
     */
    const API_TYPE_NONE                     = 0;
    const API_TYPE_BIRDSEYE                 = 1;
    const API_TYPE_OTHER                    = 99;



    /**
     * @var array Email ids to classes
     */
    public static $API_TYPES = [
        self::API_TYPE_NONE                 => 'None',
        self::API_TYPE_BIRDSEYE             => 'Birdseye',
        self::API_TYPE_OTHER                => 'Other'
    ];



    /**
     * Get the vlan that holds the router
     */
    public function vlan()
    {
        return $this->belongsTo('IXP\Models\Vlan' );
    }

    /**
     * Get the API
     *
     * Alias to allow Entities\Router and Models\Router to work interchangably
     */
    public function api()
    {
        return $this->api;
    }

    /**
     * Get the API type
     *
     * Alias to allow Entities\Router and Models\Router to work interchangably
     */
    public function apiType(): int {
        return $this->api_type;
    }


    /**
     * Scope a query to only include servers with an API
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeHasApi($query)
    {
        return $query->where('api_type', '>', 0);
    }

    /**
     * Scope a query to only include route servers
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeRouteServer($query)
    {
        return $query->where('type', self::TYPE_ROUTE_SERVER);
    }

    /**
     * Scope a query to match IPv4 routers only
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeIPv4($query)
    {
        return $query->where('protocol', self::PROTOCOL_IPV4);
    }

    /**
     * Scope a query to match IPv6 routers only
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeIPv6($query)
    {
        return $query->where('protocol', self::PROTOCOL_IPV6);
    }

    /**
     * Scope a query to match BGP Large Communities enabled
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeLargeCommunities($query)
    {
        return $query->where('bgp_lc', true);
    }

    /**
     * Scope a query to match against quarantine routers
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeNotQuarantine($query)
    {
        return $query->where('quarantine', false);
    }

    /**
     * Scope a query to match RPKI enabled
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeRpki($query)
    {
        return $query->where('rpki', true);
    }

}
