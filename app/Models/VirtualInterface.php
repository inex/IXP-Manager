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
    Model
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
    public function customer()
    {
        return $this->belongsTo('IXP\Models\Customer', 'custid');
    }

    /**
     * Get the VLAN interfaces for the virtual interface
     */
    public function vlanInterfaces()
    {
        return $this->hasMany('IXP\Models\VlanInterface', 'virtualinterfaceid');
    }



}
