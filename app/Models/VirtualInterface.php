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
    Collection,
    Model,
    Relations\BelongsTo,
    Relations\HasMany
};

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
 * @property-read Collection|\IXP\Models\PhysicalInterface[] $physicalInterfaces
 * @property-read int|null $physical_interfaces_count
 * @property-read Collection|\IXP\Models\VlanInterface[] $vlanInterfaces
 * @property-read int|null $vlan_interfaces_count
 * @method static Builder|VirtualInterface newModelQuery()
 * @method static Builder|VirtualInterface newQuery()
 * @method static Builder|VirtualInterface query()
 * @method static Builder|VirtualInterface whereChannelgroup($value)
 * @method static Builder|VirtualInterface whereCustid($value)
 * @method static Builder|VirtualInterface whereDescription($value)
 * @method static Builder|VirtualInterface whereFastlacp($value)
 * @method static Builder|VirtualInterface whereId($value)
 * @method static Builder|VirtualInterface whereLagFraming($value)
 * @method static Builder|VirtualInterface whereMtu($value)
 * @method static Builder|VirtualInterface whereName($value)
 * @method static Builder|VirtualInterface whereTrunk($value)
 * @mixin Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\PhysicalInterface[] $physicalInterfacesConnected
 * @property-read int|null $physical_interfaces_connected_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VirtualInterface whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VirtualInterface whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VirtualInterface connected()
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
    public function speed( bool $connectedOnly = true ): int
    {
        if( $connectedOnly ) {
            return $this->physicalInterfaces()->connected()->sum('speed');
        }

        return $this->physicalInterfaces()->sum('speed');
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