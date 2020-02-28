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
 */
class Vlan extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vlan';

    /**
     * Get the vlan interfaces that are in this vlan
     */
    public function vlanInterfaces()
    {
        return $this->hasMany('IXP\Models\VlanInterface', 'vlanid');
    }

    /**
     * Get the vlan interfaces that are in this vlan
     */
    public function routers()
    {
        return $this->hasMany('IXP\Models\Router' );
    }

}
