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

use Eloquent;

use Illuminate\Database\Eloquent\{
    Builder,
    Collection,
    Model,
    Relations\BelongsTo,
    Relations\HasMany
};

use IXP\Traits\Observable;

/**
 * IXP\Models\VirtualInterface
 *
 * @property int $id
 * @property int|null $custid
 * @property string|null $name
 * @property string|null $description
 * @property int|null $mtu
 * @property bool|null $trunk
 * @property int|null $channelgroup
 * @property bool $lag_framing
 * @property bool $fastlacp
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\Customer|null $customer
 * @property-read Collection|\IXP\Models\MacAddress[] $macAddresses
 * @property-read int|null $mac_addresses_count
 * @property-read Collection|\IXP\Models\PhysicalInterface[] $physicalInterfaces
 * @property-read int|null $physical_interfaces_count
 * @property-read Collection|\IXP\Models\SflowReceiver[] $sflowReceivers
 * @property-read int|null $sflow_receivers_count
 * @property-read Collection|\IXP\Models\VlanInterface[] $vlanInterfaces
 * @property-read int|null $vlan_interfaces_count
 * @method static Builder|VirtualInterface newModelQuery()
 * @method static Builder|VirtualInterface newQuery()
 * @method static Builder|VirtualInterface query()
 * @method static Builder|VirtualInterface whereChannelgroup($value)
 * @method static Builder|VirtualInterface whereCreatedAt($value)
 * @method static Builder|VirtualInterface whereCustid($value)
 * @method static Builder|VirtualInterface whereDescription($value)
 * @method static Builder|VirtualInterface whereFastlacp($value)
 * @method static Builder|VirtualInterface whereId($value)
 * @method static Builder|VirtualInterface whereLagFraming($value)
 * @method static Builder|VirtualInterface whereMtu($value)
 * @method static Builder|VirtualInterface whereName($value)
 * @method static Builder|VirtualInterface whereTrunk($value)
 * @method static Builder|VirtualInterface whereUpdatedAt($value)
 * @mixin Eloquent
 */
class VirtualInterface extends Model
{
    use Observable;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'virtualinterface';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'custid',
        'name',
        'description',
        'mtu',
        'trunk',
        'channelgroup',
        'lag_framing',
        'fastlacp',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'trunk'         => 'boolean',
        'lag_framing'   => 'boolean',
        'fastlacp'      => 'boolean',
    ];

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
     * Get the mac addresses for the virtual interface
     */
    public function macAddresses(): HasMany
    {
        return $this->hasMany(MacAddress::class, 'virtualinterfaceid');
    }

    /**
     * Get the sflow receivers for the virtual interface
     */
    public function sflowReceivers(): HasMany
    {
        return $this->hasMany(SflowReceiver::class, 'virtual_interface_id');
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
        if( $this->physicalInterfaces()->graphable()->count() ) {
            return true;
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
                return $ci->coreLink()->coreBundle;
            }
        }
        return false;
    }

    /**
     * Get peerring PhysicalInterfaces
     *

     */
    public function peeringPhysicalInterface(): array
    {
        $ppis = [];
        foreach( $this->physicalInterfaces as $ppi ) {
            if( $ppi->peeringPhysicalInterface ){
                $ppis[] = $ppi->peeringPhysicalInterface;
            }
        }
        return $ppis;
    }

    /**
     * Get fanout PhysicalInterfaces
     *
     * @return array
     */
    public function fanoutPhysicalInterface(): array
    {
        $ppis = [];
        foreach( $this->physicalInterfaces as $ppi){
            if( $ppi->fanoutPhysicalInterface ) {
                $ppis[] = $ppi->peeringPhysicalInterface;
            }
        }
        return $ppis;
    }

    /**
     * Get a Switch Port of a virtual interface.
     *
     * @return SwitchPort|bool The switch port or false if no switch port.
     */
    public function switchPort()
    {
        if( $this->physicalInterfaces()->count() ){
            return $this->physicalInterfaces()->first()->switchPort;
        }
        return false;
    }

    /**
     * Get the *type* of virtual interface based on the switchport type.
     *
     * Actually returns type of the first physical interface's switchport. All
     * switchports in a virtual interface should be the same type so just
     * examining the first is sufficient to determine the *virtual interface type*.
     *
     * @see SwitchPortt::$TYPES
     *
     * @return string|bool The virtual interface type (`\Models\SwitchPort::TYPE_XXX`) or false if no physical interfaces.
     */
    public function type()
    {
        if( $this->physicalInterfaces->isNotEmpty() ) {
            return $this->physicalInterfaces[ 0 ]->switchPort->type;
        }
        return false;
    }

    /**
     * Turn the database integer representation of the type into text as
     * defined in the SwitchPort::$TYPES array (or 'Unknown')
     * @return string
     */
    public function resolveType(): string
    {
        return SwitchPort::$TYPES[ $this->type() ] ?? 'Unknown';
    }

    /**
     * Is the type SwitchPort::TYPE_PEERING?
     *
     * @return bool
     */
    public function typePeering(): bool
    {
        return $this->type() === SwitchPort::TYPE_PEERING;
    }

    /**
     * Is the type SwitchPort::TYPE_FANOUT?
     *
     * @return bool
     */
    public function typeFanout(): bool
    {
        return $this->type() === SwitchPort::TYPE_FANOUT;
    }

    /**
     * Is the type SwitchPort::TYPE_RESELLER?
     *
     * @return bool
     */
    public function typeReseller(): bool
    {
        return $this->type() === SwitchPort::TYPE_RESELLER;
    }

    /**
     * Is the type SwitchPort::TYPE_CORE?
     *
     * @return bool
     */
    public function typeCore(): bool
    {
        return $this->type() === SwitchPort::TYPE_CORE;
    }

    /**
     * Get the bundle name if name and channel group are set. Otherwise an empty string.
     *
     * @return string
     */
    public function bundleName(): string
    {
        return $this->name && $this->channelgroup ? $this->name . $this->channelgroup : '';
    }

    /**
     * Check if the switch is the same for the physical interfaces of the virtual interface
     *
     * @return bool
     */
    public function sameSwitchForEachPI(): bool
    {
         return self::select( 'sp.switchid AS switchid' )
                 ->from( 'virtualinterface AS vi' )
                 ->leftJoin( 'physicalinterface AS pi', 'pi.virtualinterfaceid', 'vi.id' )
                 ->leftJoin( 'switchport AS sp', 'sp.id', 'pi.switchportid' )
                 ->where( 'vi.id', $this->id )->distinct()->get()->pluck( 'switchid' )->count() === 1;
    }

    /**
     * Number of non-private VLANs
     *
     * Usually just one but we use this for labeling on the frontend if >1
     *
     * @return int
     */
    public function numberPublicVlans(): int
    {
        return self::from( 'virtualinterface AS vi' )
            ->leftJoin( 'vlaninterface AS vli', 'vli.virtualinterfaceid', 'vi.id' )
            ->leftJoin( 'vlan AS v', 'v.id', 'vli.vlanid' )
            ->where( 'vi.id', $this->id )
            ->where( 'private', false )->count();
    }

    /**
     * String to describe the model being updated / deleted / created
     *
     * @param Model $model
     *
     * @return string
     */
    public static function logSubject( Model $model ): string
    {
        return sprintf(
            "Virtual Interface [id:%d] belonging to %s [id:%d] '%s'",
            $model->id,
            ucfirst( config( 'ixp_fe.lang.customer.one' ) ),
            $model->custid,
            $model->customer->name,
        );
    }
}