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

use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\BelongsTo, Relations\HasMany};

/**
 * IXP\Models\VirtualInterface
 *
 * @property int $id
 * @property int|null $custid
 * @property string|null $name
 * @property string|null $description
 * @property int|null $mtu
 * @property int|null $trunk
 * @property int|null $channelgroup
 * @property int $lag_framing
 * @property int $fastlacp
 * @property-read \IXP\Models\Customer|null $customer
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\PhysicalInterface[] $physicalInterfaces
 * @property-read int|null $physical_interfaces_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\VlanInterface[] $vlanInterfaces
 * @property-read int|null $vlan_interfaces_count
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VirtualInterface newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VirtualInterface newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VirtualInterface query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VirtualInterface whereChannelgroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VirtualInterface whereCustid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VirtualInterface whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VirtualInterface whereFastlacp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VirtualInterface whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VirtualInterface whereLagFraming($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VirtualInterface whereMtu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VirtualInterface whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VirtualInterface whereTrunk($value)
 * @mixin \Eloquent
 */
class VirtualInterface extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'virtualinterface';

    /**
     * Get the customer that owns the virtual interfaces.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'custid' );
    }

    /**
     * Get the VLAN interfaces for the virtual interface
     */
    public function vlanInterfaces(): HasMany
    {
        return $this->hasMany(VlanInterface::class, 'virtualinterfaceid');
    }

    /**
     * Get the physical interfaces for the virtual interface
     */
    public function physicalInterfaces(): HasMany
    {
        return $this->hasMany(PhysicalInterface::class, 'virtualinterfaceid');
    }

    /**
     * Get the speed of the LAG
     *
     * @param bool $connectedOnly Only consider physical interfaces with 'CONNECTED' state
     *
     * @return int
     */
    public function speed( $connectedOnly = true ): int
    {
        $speed = 0;
        foreach( $this->physicalInterfaces as $pi ) {
            if( $connectedOnly && !$pi->statusIsConnected() ) {
                continue;
            }
            $speed += $pi->speed;
        }

        return $speed;
    }

    /**
     * Utility function to provide an array of all virtual interface objects on a given
     * infrastructure
     *
     * @param Infrastructure    $infra The infrastructure to gather VirtualInterfaces for
     * @param int|bool          $proto Either 4 or 6 to limit the results to interface with IPv4 / IPv6
     * @param bool              $externalOnly If true (default) then only external (non-internal) interfaces will be returned
     *
     * @return \Illuminate\Support\Collection
     *
     * @throws
     */
    public static function getForInfrastructure( Infrastructure $infra, $proto = false, $externalOnly = true ): \Illuminate\Support\Collection
    {
        return self::select( [ 'vi.*' ] )
            ->from( 'virtualinterface AS vi' )
            ->Join( 'cust AS c', 'c.id', 'vi.custid' )
            ->Join( 'vlaninterface AS vli', 'vli.virtualinterfaceid', 'vi.id' )
            ->Join( 'physicalinterface AS pi', 'pi.virtualinterfaceid', 'vi.id' )
            ->Join( 'switchport AS sp', 'sp.id', 'pi.switchportid' )
            ->Join( 'switch AS s', 's.id', 'sp.switchid' )
            ->Join( 'infrastructure AS i', 'i.id', 's.infrastructure' )
            ->where( 'i.id', $infra->id )
            ->whereRaw( Customer::SQL_CUST_ACTIVE )
            ->whereRaw( Customer::SQL_CUST_CURRENT )
            ->whereRaw( Customer::SQL_CUST_TRAFFICING )
            ->where( 'pi.status', PhysicalInterface::STATUS_CONNECTED )
            ->when( $proto , function( Builder $q, $proto ) {
                $p = in_array( $proto, [ 4, 6 ], true ) ? $proto : 4;
                return $q->whereRaw( "vli.ipv{$p}enabled = 1" );
            })
            ->when( $externalOnly , function( Builder $q ) {
                return $q->whereRaw( Customer::SQL_CUST_EXTERNAL );
            })->orderBy( 'c.name' )->get()->keyBy( 'id' );
    }

    /**
     * Is this LAG graphable?
     *
     * @return bool
     */
    public function isGraphable(): bool
    {
        foreach( $this->physicalInterfaces as $pi ) {
            if( $pi->isGraphable() ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return the core bundle associated to the virtual interface or false
     *
     * @return CoreBundle|bool
     */
    public function getCoreBundle()
    {
        foreach( $this->physicalInterfaces as $pi ) {
            if( $pi->coreinterface()->exists() ) {
                $ci = $pi->coreinterface;
                /** @var $ci CoreInterface */
                return $ci->getCoreLink()->coreBundle;
            }
        }
        return false;
    }
}
