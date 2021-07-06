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
    Model,
    Relations\BelongsTo,
    Relations\HasMany
};

use IXP\Traits\Observable;

/**
 * IXP\Models\VlanInterface
 *
 * @property int $id
 * @property int|null $ipv4addressid
 * @property int|null $ipv6addressid
 * @property int|null $virtualinterfaceid
 * @property int|null $vlanid
 * @property int|null $ipv4enabled
 * @property string|null $ipv4hostname
 * @property int|null $ipv6enabled
 * @property string|null $ipv6hostname
 * @property int|null $mcastenabled
 * @property int|null $irrdbfilter
 * @property string|null $bgpmd5secret
 * @property string|null $ipv4bgpmd5secret
 * @property string|null $ipv6bgpmd5secret
 * @property int|null $maxbgpprefix
 * @property int|null $rsclient
 * @property int|null $ipv4canping
 * @property int|null $ipv6canping
 * @property int|null $ipv4monitorrcbgp
 * @property int|null $ipv6monitorrcbgp
 * @property int|null $as112client
 * @property int|null $busyhost
 * @property string|null $notes
 * @property int $rsmorespecifics
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\IPv4Address|null $ipv4address
 * @property-read \IXP\Models\IPv6Address|null $ipv6address
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\Layer2Address[] $layer2addresses
 * @property-read int|null $layer2addresses_count
 * @property-read \IXP\Models\VirtualInterface|null $virtualInterface
 * @property-read \IXP\Models\Vlan|null $vlan
 * @method static Builder|VlanInterface newModelQuery()
 * @method static Builder|VlanInterface newQuery()
 * @method static Builder|VlanInterface query()
 * @method static Builder|VlanInterface whereAs112client($value)
 * @method static Builder|VlanInterface whereBgpmd5secret($value)
 * @method static Builder|VlanInterface whereBusyhost($value)
 * @method static Builder|VlanInterface whereCreatedAt($value)
 * @method static Builder|VlanInterface whereId($value)
 * @method static Builder|VlanInterface whereIpv4addressid($value)
 * @method static Builder|VlanInterface whereIpv4bgpmd5secret($value)
 * @method static Builder|VlanInterface whereIpv4canping($value)
 * @method static Builder|VlanInterface whereIpv4enabled($value)
 * @method static Builder|VlanInterface whereIpv4hostname($value)
 * @method static Builder|VlanInterface whereIpv4monitorrcbgp($value)
 * @method static Builder|VlanInterface whereIpv6addressid($value)
 * @method static Builder|VlanInterface whereIpv6bgpmd5secret($value)
 * @method static Builder|VlanInterface whereIpv6canping($value)
 * @method static Builder|VlanInterface whereIpv6enabled($value)
 * @method static Builder|VlanInterface whereIpv6hostname($value)
 * @method static Builder|VlanInterface whereIpv6monitorrcbgp($value)
 * @method static Builder|VlanInterface whereIrrdbfilter($value)
 * @method static Builder|VlanInterface whereMaxbgpprefix($value)
 * @method static Builder|VlanInterface whereMcastenabled($value)
 * @method static Builder|VlanInterface whereNotes($value)
 * @method static Builder|VlanInterface whereRsclient($value)
 * @method static Builder|VlanInterface whereRsmorespecifics($value)
 * @method static Builder|VlanInterface whereUpdatedAt($value)
 * @method static Builder|VlanInterface whereVirtualinterfaceid($value)
 * @method static Builder|VlanInterface whereVlanid($value)
 * @mixin Eloquent
 */
class VlanInterface extends Model
{
    use Observable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vlaninterface';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'virtualinterfaceid',
        'vlanid',
        'irrdbfilter',
        'rsmorespecifics',
        'mcastenabled',
        'maxbgpprefix',
        'rsclient',
        'as112client',
        'busyhost',
    ];

    /**
     * Get the customer that owns the virtual interfaces.
     */
    public function virtualInterface(): BelongsTo
    {
        return $this->belongsTo(VirtualInterface::class, 'virtualinterfaceid');
    }

    /**
     * Get the vlan that holds the vlan interface.
     */
    public function vlan(): BelongsTo
    {
        return $this->belongsTo(Vlan::class, 'vlanid');
    }

    /**
     * Get the layer2addresses for the vlan interface
     */
    public function layer2addresses(): HasMany
    {
        return $this->hasMany(Layer2Address::class, 'vlan_interface_id' );
    }

    /**
     * Get the ipv4address associated with the vlaninterface.
     */
    public function ipv4address(): BelongsTo
    {
        return $this->belongsTo(IPv4Address::class, 'ipv4addressid' );
    }

    /**
     * Get the ipv6address associated with the vlaninterface.
     */
    public function ipv6address(): BelongsTo
    {
        return $this->belongsTo(IPv6Address::class, 'ipv6addressid' );
    }

    /**
     * See if a given protocol is enabled
     *
     * @param int|string $proto
     *
     * @return bool
     */
    public function ipvxEnabled( $proto ): bool
    {
        switch( $proto ) {
            case 4:
            case 'ipv4':
                return (bool)$this->ipv4enabled;
                break;
            case 6:
            case 'ipv6':
                return (bool)$this->ipv6enabled;
                break;
            default:
                return false;
        }
    }

    /**
     * Is this VLAN interface graphable?
     *
     * @return bool
     */
    public function isGraphable(): bool
    {
        return $this->virtualInterface->isGraphable();
    }

    /**
     * Convenience function to see if we can graph a VLI for latency for a given protocol
     *
     * @param string $proto Either ipv4 / ipv6 (as defined in Grapher)
     *
     * @return bool
     *
     * @throws
     */
    public function canGraphForLatency( string $proto ): bool
    {
        switch( $proto ) {
            case 'ipv4':
                return !$this->vlan->private
                    && $this->ipv4enabled
                    && $this->ipv4canping
                    && $this->ipv4address;
                break;
            case 'ipv6':
                return !$this->vlan->private
                    && $this->ipv6enabled
                    && $this->ipv6canping
                    && $this->ipv6address;
                break;
            default:
                return false;
        }
    }

    /**
     * Convenience function to get an IP address based on a given protocol
     *
     * @param string $proto Either ipv4 / ipv6 (as defined in Grapher)
     *
     * @return null|IPv4Address|IPv6Address
     *
     * @throws
     */
    public function getIPAddress( string $proto )
    {
        switch( strtolower( $proto ) ) {
            case 'ipv4':
                return $this->ipv4address;
                break;
            case 'ipv6':
                return $this->ipv6address;
                break;
            default:
                return null;
        }
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
            "Vlan Interface [id:%d] belonging to Virtual Interface [id:%d]",
            $model->id,
            $model->virtualinterfaceid,
        );
    }
}