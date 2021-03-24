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
    Model,
    Relations\BelongsTo,
    Relations\HasOne
};

/**
 * IXP\Models\IPv6Address
 *
 * @property int $id
 * @property int|null $vlanid
 * @property string|null $address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\Vlan|null $vlan
 * @property-read \IXP\Models\VlanInterface|null $vlanInterface
 * @method static \Illuminate\Database\Eloquent\Builder|IPv6Address newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IPv6Address newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IPv6Address query()
 * @method static \Illuminate\Database\Eloquent\Builder|IPv6Address whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IPv6Address whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IPv6Address whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IPv6Address whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IPv6Address whereVlanid($value)
 * @mixin \Eloquent
 */
class IPv6Address extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ipv6address';

    /**
     * Get the vlan that own the ipv6address
     */
    public function vlan(): BelongsTo
    {
        return $this->belongsTo(Vlan::class, 'vlanid' );
    }

    /**
     * Get the vlan interface associated with the ipv6.
     */
    public function vlanInterface(): HasOne
    {
        return $this->hasOne(VlanInterface::class, 'ipv6addressid' );
    }

}
