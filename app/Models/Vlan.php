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

use Cache;
use DB;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo, Relations\HasMany, Relations\HasManyThrough};
use stdClass;

/**
 * IXP\Models\Vlan
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $number
 * @property string|null $notes
 * @property int $private
 * @property int $infrastructureid
 * @property int $peering_matrix
 * @property int $peering_manager
 * @property string|null $config_name
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\Router[] $routers
 * @property-read int|null $routers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\VlanInterface[] $vlanInterfaces
 * @property-read int|null $vlan_interfaces_count
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Vlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Vlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Vlan query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Vlan whereConfigName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Vlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Vlan whereInfrastructureid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Vlan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Vlan whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Vlan whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Vlan wherePeeringManager($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Vlan wherePeeringMatrix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Vlan wherePrivate($value)
 * @mixin \Eloquent
 * @property-read \IXP\Models\Infrastructure $infrastructure
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\IPv4Address[] $ipv4addresses
 * @property-read int|null $ipv4addresses_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\IPv6Address[] $ipv6addresses
 * @property-read int|null $ipv6addresses_count
 */
class Vlan extends Model
{
    const PRIVATE_NO  = 0;
    const PRIVATE_YES = 1;

    public static $PRIVATE_YES_NO = array(
        self::PRIVATE_NO  => 'No',
        self::PRIVATE_YES => 'Yes'
    );

    /**
     * The cache key for all VLAN objects
     *
     * @var string The cache key for all VLAN objects
     */
    const ALL_CACHE_KEY = 'inex_vlans';

    /**
     * Constant to represent normal and private VLANs
     *
     * @var int Constant to represent normal and private VLANs
     */
    const TYPE_ALL     = 0;

    /**
     * Constant to represent normal VLANs only
     *
     * @var int Constant to represent normal VLANs ony
     */
    const TYPE_NORMAL  = 1;

    /**
     * Constant to represent private VLANs only
     *
     * @var int Constant to represent private VLANs ony
     */
    const TYPE_PRIVATE = 2;


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vlan';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'number',
        'notes',
        'private',
        'infrastructureid',
        'peering_matrix',
        'peering_manager',
        'config_name',
    ];

    /**
     * Get the vlan interfaces that are in this vlan
     */
    public function vlanInterfaces(): HasMany
    {
        return $this->hasMany(VlanInterface::class, 'vlanid');
    }

    /**
     * Get the vlan interfaces that are in this vlan
     */
    public function routers(): HasMany
    {
        return $this->hasMany(Router::class );
    }

    /**
     * Get the ipv4addresses for the vlan
     */
    public function ipv4addresses(): HasMany
    {
        return $this->hasMany(IPv4Address::class, 'vlanid' );
    }

    /**
     * Get the ipv6addresses for the vlan
     */
    public function ipv6addresses(): HasMany
    {
        return $this->hasMany(IPv6Address::class, 'vlanid' );
    }

    /**
     * Get the infrastructure that own the vlan
     */
    public function infrastructure(): BelongsTo
    {
        return $this->belongsTo(Infrastructure::class, 'infrastructureid' );
    }

    /**
     * Gets a listing of vlans for dropdown
     *
     * @return Collection
     */
    public static function getListForDropdown(): Collection
    {
        return self::orderBy( 'name', 'asc' )->get();
    }

    /**
     * Return an array of all VLAN objects from the database with caching
     * (and with the option to specify types - returns normal (non-private)
     * VLANs by default.
     *
     * @param $type int The VLAN types to return (see TYPE_ constants).
     * @param $orderBy string Typical values: number, name
     * @param $cache bool Whether to use the cache or not
     *
     * @return Collection
     */
    public static function getAndCache( int $type = self::TYPE_NORMAL, string $orderBy = "number", bool $cache = true ): Collection
    {
        $type = $type !== self::TYPE_ALL && $type !== self::TYPE_PRIVATE ? self::TYPE_NORMAL : $type ;

        return Cache::remember(self::ALL_CACHE_KEY . "_{$type}_{$orderBy}", 3600, function () use( $type, $orderBy )  {
            return self::when( $type === self::TYPE_PRIVATE , function( Builder $q ) {
                return $q->where( 'private', 1 );
            })
            ->when( $type === self::TYPE_NORMAL , function( Builder $q ) {
                return $q->where( 'private', 0 );
            })
            ->orderBy( $orderBy, 'ASC' )->get();
        });
    }

    /**
     * Gets a listing of vlan as array
     *
     * @param int $type The VLAN types to return (see TYPE_ constants).
     *
     * @return array
     */
    public static function getListAsArray( $type = self::TYPE_NORMAL ): array
    {
        return self::getAndCache( $type )->toArray();
    }

    /**
     * Gets a listing of vlans or a single one if an ID is provided
     *
     * @param stdClass $feParams
     * @param int|null $id
     *
     * @return array
     */
    public static function getFeList( stdClass $feParams, int $id = null ): array
    {
        $query = self::select( [ 'vlan.*', 'i.shortname AS infrastructure' ] )
            ->leftJoin( 'infrastructure AS i', 'i.id', 'vlan.infrastructureid' )
            ->when( $id , function( Builder $q, $id ) {
                return $q->where('vlan.id', $id );
            } );

        if( isset( $feParams->privateList ) && $feParams->privateList ){
            $query->where( 'private', 1);
        } else if( isset( $feParams->publicOnly ) && $feParams->publicOnly === true ) {
            $query->where( 'private', '!=',1);
        }

        if( isset( $feParams->infra) && $feParams->infra ){
            $query->where( 'i.id', $feParams->infra->id );
        }

        return $query->when( $feParams->listOrderBy , function( Builder $q, $orderby ) use ( $feParams )  {
                    return $q->orderBy( $orderby, $feParams->listOrderByDir ?? 'ASC');
                })->get()->toArray();
    }

    /**
     * Returns an array of private VLANs with their details and membership.
     *
     * @param Infrastructure|null $infra
     *
     * @return array
     */
    public static function getPrivateVlanDetails( Infrastructure $infra = null ): array
    {
        $vlans =  self::where( 'private', 1 )
            ->when( $infra , function( Builder $q, $infra ) {
                return $q->where('infrastructureid', $infra->id );
            } )
            ->groupBy( 'id' )->get();

        $result = [];
        foreach( $vlans as $v ) {
            $result[ $v->id ]['vlanid']         = $v->id;
            $result[ $v->id ]['name']           = $v->name;
            $result[ $v->id ]['number']         = $v->number;
            $result[ $v->id ]['infrastructure'] = $v->infrastructure->name;

            $members   = [];
            $locations = [];
            $switches  = [];

            foreach( $v->vlanInterfaces as $vli ){
                $cust = $vli->virtualInterface->customer;

                $members[ $cust->id ][ 'id' ]   = $cust->id;
                $members[ $cust->id ][ 'name' ] = $cust->name;
                $members[ $cust->id ][ 'viid' ] = $vli->virtualInterface->id;

                foreach( $vli->virtualInterface->physicalInterfaces as $pi ) {
                    $switcher = $pi->switchPort->switcher;
                    $location = $switcher->cabinet->location;

                    $locations[ $location->id ] = $location->name;
                    $switches[ $switcher->id ] = $switcher->name;
                }
            }

            $result[ $v->id ][ 'members' ]      = $members;
            $result[ $v->id ][ 'locations' ]    = $locations;
            $result[ $v->id ][ 'switches' ]     = $switches;
        }
        return $result;
    }
}