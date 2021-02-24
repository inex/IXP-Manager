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
 * @method static Builder|VlanInterface newModelQuery()
 * @method static Builder|VlanInterface newQuery()
 * @method static Builder|VlanInterface query()
 * @method static Builder|VlanInterface whereAs112client($value)
 * @method static Builder|VlanInterface whereBgpmd5secret($value)
 * @method static Builder|VlanInterface whereBusyhost($value)
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
 * @method static Builder|VlanInterface whereVirtualinterfaceid($value)
 * @method static Builder|VlanInterface whereVlanid($value)
 * @mixin Eloquent
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
    public function virtualInterface()
    {
        return $this->belongsTo('IXP\Models\VirtualInterface', 'virtualinterfaceid');
    }

    /**
     * Get the vlan that holds the vlan interface.
     */
    public function vlan()
    {
        return $this->belongsTo('IXP\Models\Vlan', 'vlanid');
    }


    /**
     * See if a given protocol is enabled
     */
    public function protocolEnabled( int $p ): bool {
        return $p === 4 ? $this->ipv4enabled : $this->ipv6enabled;
    }

}
