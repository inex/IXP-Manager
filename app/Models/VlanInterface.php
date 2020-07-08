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

use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo, Relations\HasMany};


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
 * @property-read \IXP\Models\VirtualInterface|null $virtualInterface
 * @property-read \IXP\Models\Vlan|null $vlan
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereAs112client($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereBgpmd5secret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereBusyhost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereIpv4addressid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereIpv4bgpmd5secret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereIpv4canping($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereIpv4enabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereIpv4hostname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereIpv4monitorrcbgp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereIpv6addressid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereIpv6bgpmd5secret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereIpv6canping($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereIpv6enabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereIpv6hostname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereIpv6monitorrcbgp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereIrrdbfilter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereMaxbgpprefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereMcastenabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereRsclient($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereRsmorespecifics($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereVirtualinterfaceid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereVlanid($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\Layer2Address[] $layer2addresses
 * @property-read int|null $layer2addresses_count
 */
class VlanInterface extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vlaninterface';

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
     * See if a given protocol is enabled
     *
     * @param int $p
     *
     * @return bool
     */
    public function protocolEnabled( int $p ): bool
    {
        return $p === 4 ? $this->ipv4enabled : $this->ipv6enabled;
    }

}
