<?php

namespace IXP\Models;

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
use Illuminate\Database\Eloquent\{
    Builder,
    Model,
    Relations\BelongsTo
};

use IXP\Traits\Observable;

/**
 * IXP\Models\Router
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
 * @property \Illuminate\Support\Carbon|null $last_updated
 * @property int $pause_updates
 * @property bool $rpki
 * @property string|null $software_version
 * @property string|null $operating_system
 * @property string|null $operating_system_version
 * @property int $rfc1997_passthru
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Router|null $pair
 * @property-read \IXP\Models\Vlan $vlan
 * @method static Builder|Router hasApi()
 * @method static Builder|Router ipv4()
 * @method static Builder|Router ipv6()
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
 * @method static Builder|Router whereCreatedAt($value)
 * @method static Builder|Router whereHandle($value)
 * @method static Builder|Router whereId($value)
 * @method static Builder|Router whereLastUpdateStarted($value)
 * @method static Builder|Router whereLastUpdated($value)
 * @method static Builder|Router whereLgAccess($value)
 * @method static Builder|Router whereMgmtHost($value)
 * @method static Builder|Router whereName($value)
 * @method static Builder|Router whereOperatingSystem($value)
 * @method static Builder|Router whereOperatingSystemVersion($value)
 * @method static Builder|Router wherePairId($value)
 * @method static Builder|Router wherePauseUpdates($value)
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
 * @method static Builder|Router whereUpdatedAt($value)
 * @method static Builder|Router whereVlanId($value)
 * @method static Builder|Router routeCollector()
 * @method static Builder|Router ipvX(int $protocol)
 * @method static Builder|Router ipProtocol(int $protocol)
 * @property int|null $rpki_min_version
 * @property int|null $rpki_max_version
 * @method static Builder<static>|Router whereRpkiMaxVersion($value)
 * @method static Builder<static>|Router whereRpkiMinVersion($value)
 * @mixin \Eloquent
 */
class Router extends Model
{
    use Observable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pair_id',
        'vlan_id',
        'handle',
        'protocol',
        'type',
        'name',
        'shortname',
        'router_id',
        'peering_ip',
        'asn',
        'software',
        'mgmt_host',
        'api',
        'api_type',
        'lg_access',
        'quarantine',
        'bgp_lc',
        'template',
        'skip_md5',
        'rpki',
        'software_version',
        'operating_system',
        'operating_system_version',
        'rfc1997_passthru',
        'last_updated',
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'asn'                 => 'integer',
        'quarantine'          => 'boolean',
        'bgp_lc'              => 'boolean',
        'skip_md5'            => 'boolean',
        'rpki'                => 'boolean',
        'last_updated'        => 'datetime',
        'last_update_started' => 'datetime',
    ];


    /**
     * CONST PROTOCOL
     */
    public const string PROTOCOL_IPV4                 = '4';
    public const string PROTOCOL_IPV6                 = '6';

    /**
     * @var array Router Protocols
     */
    public static array $PROTOCOLS = [
        self::PROTOCOL_IPV4     =>      'IPv4',
        self::PROTOCOL_IPV6     =>      'IPv6'
    ];

    /**
     * CONST TYPES
     */
    public const int TYPE_ROUTE_SERVER                 = 1;
    public const int TYPE_ROUTE_COLLECTOR              = 2;
    public const int TYPE_AS112                        = 3;
    public const int TYPE_OTHER                        = 99;

    /**
     * @var array Router types textual description
     */
    public static array $TYPES = [
        self::TYPE_ROUTE_SERVER             => 'Route Server',
        self::TYPE_ROUTE_COLLECTOR          => 'Route Collector',
        self::TYPE_AS112                    => 'AS112',
        self::TYPE_OTHER                    => 'Other'
    ];

    /**
     * @var array Router types short description
     */
    public static array $TYPES_SHORT = [
        self::TYPE_ROUTE_SERVER             => 'RS',
        self::TYPE_ROUTE_COLLECTOR          => 'RC',
        self::TYPE_AS112                    => 'AS112',
        self::TYPE_OTHER                    => 'Other'
    ];

    /**
     * CONST SOFTWARES
     */
    public const int SOFTWARE_BIRD                     = 1;
    public const int SOFTWARE_BIRD2                    = 6;
    public const int SOFTWARE_BIRD3                    = 7;
    public const int SOFTWARE_QUAGGA                   = 2;
    public const int SOFTWARE_FRROUTING                = 3;
    public const int SOFTWARE_OPENBGPD                 = 4;
    public const int SOFTWARE_CISCO                    = 5;
    public const int SOFTWARE_OTHER                    = 99;

    /**
     * @var array Router softwares textual description
     */
    public static array $SOFTWARES = [
        self::SOFTWARE_BIRD                 => 'Bird v1',
        self::SOFTWARE_BIRD2                => 'Bird v2',
        self::SOFTWARE_BIRD3                => 'Bird v3',        
        self::SOFTWARE_QUAGGA               => 'Quagga',
        self::SOFTWARE_FRROUTING            => 'FRRouting',
        self::SOFTWARE_OPENBGPD             => 'OpenBGPd',
        self::SOFTWARE_CISCO                => 'Cisco',
        self::SOFTWARE_OTHER                => 'Other'
    ];

    /**
     * CONST SOFTWARES
     */
    public const int API_TYPE_NONE                     = 0;
    public const int API_TYPE_BIRDSEYE                 = 1;
    public const int API_TYPE_OTHER                    = 99;

    /**
     * @var array Router API types
     */
    public static array $API_TYPES = [
        self::API_TYPE_NONE                 => 'None',
        self::API_TYPE_BIRDSEYE             => 'Birdseye',
        self::API_TYPE_OTHER                => 'Other'
    ];

    /**
     * Get the vlan that own the router
     *
     * @psalm-return BelongsTo<Vlan>
     */
    public function vlan(): BelongsTo
    {
        return $this->belongsTo(Vlan::class, 'vlan_id' );
    }

    /**
     * Get the router's configuration / isolation pair
     *
     * @psalm-return BelongsTo<self>
     */
    public function pair(): BelongsTo
    {
        return $this->belongsTo(Router::class, 'pair_id' );
    }

    /**
     * Get the API type
     *
     * Alias to allow Entities\Router and Models\Router to work interchangeably
     */
    public function apiType(): int
    {
        return $this->api_type;
    }

    /**
     * Scope a query to only include servers with an API
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeHasApi( Builder $query ): Builder
    {
        return $query->where('api_type', '>', 0);
    }

    /**
     * Scope a query to only include route collectors
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeRouteCollector( Builder $query ): Builder
    {
        return $query->where('type', self::TYPE_ROUTE_COLLECTOR);
    }

    /**
     * Scope a query to only include route servers
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeRouteServer( Builder $query ): Builder
    {
        return $query->where('type', self::TYPE_ROUTE_SERVER);
    }

    /**
     * Scope a query to match IPv4 routers only
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeIpv4( Builder $query ): Builder
    {
        return $query->where('protocol', self::PROTOCOL_IPV4);
    }

    /**
     * Scope a query to match IPv6 routers only
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeIpv6( Builder $query ): Builder
    {
        return $query->where('protocol', self::PROTOCOL_IPV6);
    }
    
    /**
     * Scope a query to match IPvX routers only
     *
     * @param Builder $query
     * @param int $protocol
     * @return Builder
     */
    public function scopeIpProtocol( Builder $query, int $protocol ): Builder
    {
        return $query->where('protocol', $protocol === 4 ? self::PROTOCOL_IPV4 : self::PROTOCOL_IPV6 );
    }


    /**
     * Scope a query to match BGP Large Communities enabled
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeLargeCommunities( Builder $query ): Builder
    {
        return $query->where('bgp_lc', true);
    }

    /**
     * Scope a query to match against quarantine routers
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeNotQuarantine( Builder $query ): Builder
    {
        return $query->where('quarantine', false);
    }

    /**
     * Scope a query to match RPKI enabled
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeRpki( Builder $query ): Builder
    {
        return $query->where('rpki', true);
    }


    /**
     * Turn the database integer representation of the software into text as
     * defined in the self::$SOFTWARES array (or 'Unknown')
     *
     * @return string
     */
    public function software(): string
    {
        return self::$SOFTWARES[ $this->software ] ?? 'Unknown';
    }

    /**
     * Turn the database integer representation of the api type into text as
     * defined in the self::$SOFTWARES array (or 'Unknown')
     * @return string
     */
    public function resolveApiType(): string
    {
        return self::$API_TYPES[ $this->api_type ] ?? 'Unknown';
    }

    /**
     * Turn the database integer representation of the protocol into text as
     * defined in the self::$PROTOCOLS array (or 'Unknown')
     *
     * @return string
     */
    public function protocol(): string
    {
        return self::$PROTOCOLS[ $this->protocol ] ?? 'Unknown';
    }

    /**
     * Turn the database integer representation of the type into text as
     * defined in the self::$TYPES_SHORT array (or 'Unknown')
     * @return string
     */
    public function typeShortName(): string
    {
        return self::$TYPES_SHORT[ $this->type ] ?? 'Unknown';
    }

    /**
     * Turn the database integer representation of the type into text as
     * defined in the self::$TYPES array (or 'Unknown')
     * @return string
     */
    public function type(): string
    {
        return self::$TYPES[ $this->type ] ?? 'Unknown';
    }

    /**
     * Check if the given type matches the routers's type.
     *
     * @param int $type The type to check against.
     * @return bool True if the given type matches, false otherwise.
     */
    public function isType( int $type ): bool
    {
        return $this->type === $type;
    }


    /**
     * Turn the database integer representation of the lg access into text as
     * defined in the User::$PRIVILEGES_ALL array (or 'Unknown')
     *
     * @return string
     */
    public function lgAccess(): string
    {
        return User::$PRIVILEGES_ALL[ $this->lg_access ] ?? 'Unknown';
    }

    /**
     * Does the router have an API?
     *
     * In other words, is 'api' and 'api_type' set?
     *
     * @return bool
     */
    public function api(): bool
    {
        return $this->api && $this->api_type;
    }

    /**
     * This function controls access to a router for a looking glass
     *
     * @param int $privs User's privileges (see \Models\User)
     *
     * @return bool
     */
    public function authorise( int $privs ): bool
    {
        return $privs >= $this->lg_access;
    }

    /**
     * This function check is the last updated time is greater than the given number of seconds
     *
     * @param int $threshold
     *
     * @return ?bool
     */
    public function lastUpdatedGreaterThanSeconds( int $threshold ): ?bool
    {
        // TESTS: covered

        if( !$this->last_updated ) {
            // if null, then, as far as we know, it has never been updated....
            return null;
        }

        return $this->last_updated->diffInSeconds() > $threshold;
    }


    /**
     * Is the router being updated?
     *
     * If null, then we can't determine this as the appropriate columns
     * have not been set.
     *
     * @return bool|null
     */
    public function isUpdating(): ?bool
    {
        // TESTS: covered

        if( !$this->last_updated && !$this->last_update_started ) {
            return null; // don't know
        }

        if( !$this->last_updated && $this->last_update_started ) {
            return true; // looks like it, first time though.
        }

        if( $this->last_updated >= $this->last_update_started ) {
            return false; // nope, updates done
        }

        return true;
    }

    /**
     * This function check is the last updated time is greater than the given number of seconds
     *
     * If null, then we can't determine this as the appropriate columns
     * have not been set or it is not mid-update.
     *
     * Best to use this with isUpdating() first.
     *
     * @param int $threshold
     *
     * @return bool|null
     */
    public function isUpdateTakingLongerThanSeconds( int $threshold ): ?bool
    {
        // TESTS: covered

        if( $this->isUpdating() !== true ) {
            return null;
        }

        return $this->last_update_started->diffInSeconds() > $threshold;
    }


    /**
     * This function checks to see if this router can be updated.
     *
     * This uses database read/write locks to ensure it is transaction / concurrency safe.
     *
     * It can be updated if:
     *
     * 1. Fresh router - last update start and finish times are null.
     * 2. It doesn't have a pair and it itself is not mid-update.
     * 3. It does have a pair and neither it nor itself are mid-update.
     *
     *  For testing, we are not including database read/write locks here.
     *  This function should be run in the following way:
     *
     *  try {
     *      DB::unprepared( 'LOCK TABLES routers WRITE' );
     *
     *      if( !( $r = Router::whereHandle( $handle )->first() ) ) {
     *          return response( 'Unknown router handle', 404 );
     *      }
     *
     *      if( $r->canUpdate( true ) ) {
     *          return response()->json( $this->lastUpdatedArray( $r ) );
     *      }
     *  } finally {
     *      if( $r->getDirty() ) {
     *          $r->save();
     *      }
     *      DB::unprepared( 'UNLOCK TABLES' );
     *  }
 *
     * The lock parameter should be set to true to take an update lock (i.e. to
     * set last_update_started).
     *
     * @param bool $lock As well as querying the update status, we should lock it also.
     * @return bool
     */
    public function canUpdate( bool $lock = false ): bool
    {
        // if we're paused, we don't need to check anything else
        if( $this->pause_updates ) {
            return false;
        }
        
        // check for pairs first
        if( $this->pair && $this->pair->isUpdating() ) {
            return false;
        }
        
        $canUpdate = false;

        // Got to assume yes here as we're asking the question.
        if( !$this->last_updated && !$this->last_update_started ) {
            $canUpdate = true;
        }

        else if( !$this->isUpdating() ) {
            $canUpdate = true;
        }

        if( $canUpdate && $lock ) {

            // need to avoid a same-second race condition
            if( $this->last_updated ) {
                while( $this->last_updated->format('U') >= now()->format('U') ) {
                    sleep(1);
                }
            }

            $this->last_update_started = now();
        }

        return $canUpdate;
    }
    
    
    /**
     * This function releases an update lock.
     *
     * For testing, we are not including database read/write locks here.
     * This function should be run in the following way:
     *
     * try {
     *     DB::unprepared( 'LOCK TABLES routers WRITE' );
     *
     *     if( !( $r = Router::whereHandle( $handle )->first() ) ) {
     *         return response( 'Unknown router handle', 404 );
     *     }
     *
     *     $r->releaseUpdateLock();
     * } finally {
     *     if( $r->getDirty() ) {
     *         $r->save();
     *     }
     *     DB::unprepared( 'UNLOCK TABLES' );
     * }
     *
     * It always returns true, including if there is no current lock.
     *
     * @return true
     */
    public function releaseUpdateLock(): true
    {
        if( !$this->isUpdating() ) {
            return true;
        }
        
        if( !$this->last_updated && !$this->last_update_started ) {
            return true;
        }
        
        if( !$this->last_updated && $this->last_update_started ) {
            $this->last_update_started = null;
        } else {
            $this->last_update_started = $this->last_updated;
        }
        
        return true;
    }
    
    
    /**
     * String to describe the model being updated / deleted / created
     *
     * @param Router $model
     *
     * @return string
     */
    public static function logSubject( Router $model ): string
    {
        return sprintf(
            "Router [id:%d] '%s' belonging to Vlan [id:%d] '%s'",
            $model->id,
            $model->handle,
            $model->vlan_id,
            $model->vlan->name,
        );
    }


    /**
     * We don't want to log router config updates via the API
     */
    public function observerSkipUpdateLogging( array $changes ): bool {

        $interesting = array_filter( array_keys($changes), function( $v ) {
            return !in_array( $v, [ 'last_updated', 'updated_at', 'last_update_started' ] );
        } );

        return count( $interesting ) === 0;
    }


}