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

use DB;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo, Relations\HasMany, Relations\HasManyThrough};
use stdClass;

/**
 * IXP\Models\Vlan
 *
 * @property int $id
 * @property int $infrastructureid
 * @property string|null $name
 * @property int|null $number
 * @property int $private
 * @property string|null $notes
 * @property int $peering_matrix
 * @property int $peering_manager
 * @property string|null $config_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\Infrastructure $infrastructure
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\IPv4Address[] $ipv4Addresses
 * @property-read int|null $ipv4_addresses_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\IPv6Address[] $ipv6Addresses
 * @property-read int|null $ipv6_addresses_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\RouteServerFilter[] $routeServerFilters
 * @property-read int|null $route_server_filters_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\Router[] $routers
 * @property-read int|null $routers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\VlanInterface[] $vlanInterfaces
 * @property-read int|null $vlan_interfaces_count
 * @method static Builder|Vlan newModelQuery()
 * @method static Builder|Vlan newQuery()
 * @method static Builder|Vlan privateOnly()
 * @method static Builder|Vlan publicOnly()
 * @method static Builder|Vlan query()
 * @method static Builder|Vlan whereConfigName($value)
 * @method static Builder|Vlan whereCreatedAt($value)
 * @method static Builder|Vlan whereId($value)
 * @method static Builder|Vlan whereInfrastructureid($value)
 * @method static Builder|Vlan whereName($value)
 * @method static Builder|Vlan whereNotes($value)
 * @method static Builder|Vlan whereNumber($value)
 * @method static Builder|Vlan wherePeeringManager($value)
 * @method static Builder|Vlan wherePeeringMatrix($value)
 * @method static Builder|Vlan wherePrivate($value)
 * @method static Builder|Vlan whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Vlan extends Model
{
    // FIXME: @yannrobin can you remove this as private should just be a checkbox, not a dropdown list. i.e. boolean flag
    public const PRIVATE_NO  = 0;
    public const PRIVATE_YES = 1;

    public static $PRIVATE_YES_NO = array(
        self::PRIVATE_NO  => 'No',
        self::PRIVATE_YES => 'Yes'
    );

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vlan';

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
    public function ipv4Addresses(): HasMany
    {
        return $this->hasMany(IPv4Address::class, 'vlanid' );
    }

    /**
     * Get the ipv6addresses for the vlan
     */
    public function ipv6Addresses(): HasMany
    {
        return $this->hasMany(IPv6Address::class, 'vlanid' );
    }

    /**
     * Get the route server filters for the cabinet
     */
    public function routeServerFilters(): HasMany
    {
        return $this->hasMany(RouteServerFilter::class, 'vlan_id' );
    }

    /**
     * Get the infrastructure that own the vlan
     */
    public function infrastructure(): BelongsTo
    {
        return $this->belongsTo(Infrastructure::class, 'infrastructureid' );
    }

    /**
     * Scope a query to only include private VLANs
     */
    public function scopePrivateOnly( Builder $query ): Builder
    {
        return $query->where( 'private', 1 );
    }

    /**
     * Scope a query to only include public / non-private VLANs
     */
    public function scopePublicOnly( Builder $query ): Builder
    {
        return $query->where( 'private', 0 );
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
     * // FIXME @yannrobin can you think about moving this to the controller
     * // rather than here? Either private function or in the action itself.
     * // (customer over tab - and also possible user's dashboard)
     * // Actually: talk to @barryo tomorrow.
     *
     * @param Infrastructure|null $infra
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

    /**
     * Return an array of all public peering manager vlans names where the array key is the vlan id.
     *
     * @param int $custid
     *
     * @return array
     */
    public static function getPublicPeeringManager( int $custid ): array
    {
        return self::select( [ 'vlan.id AS id', 'vlan.name' ] )
            ->leftJoin( 'vlaninterface AS vli', 'vli.vlanid', 'vlan.id' )
            ->leftJoin( 'virtualinterface AS vi', 'vi.id', 'vli.virtualinterfaceid' )
            ->where( 'vi.custid',  $custid )
            ->where( 'vlan.private',  false )
            ->where( 'vlan.peering_manager',  true )
            ->where( 'vli.rsclient',  true )
            ->orderBy( 'vlan.name' )->get()->keyBy( 'id' )->toArray();
    }
}