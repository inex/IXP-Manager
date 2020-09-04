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
    Relations\HasMany
};

/**
 * IXP\Models\Infrastructure
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $shortname
 * @property int $isPrimary
 * @property int|null $peeringdb_ix_id
 * @property int|null $ixf_ix_id
 * @property string|null $country
 * @property-read Collection|\IXP\Models\Switcher[] $switchers
 * @property-read int|null $switchers_count
 * @property-read Collection|\IXP\Models\Vlan[] $vlans
 * @property-read int|null $vlans_count
 * @method static Builder|Infrastructure newModelQuery()
 * @method static Builder|Infrastructure newQuery()
 * @method static Builder|Infrastructure query()
 * @method static Builder|Infrastructure whereCountry($value)
 * @method static Builder|Infrastructure whereId($value)
 * @method static Builder|Infrastructure whereIsPrimary($value)
 * @method static Builder|Infrastructure whereIxfIxId($value)
 * @method static Builder|Infrastructure whereName($value)
 * @method static Builder|Infrastructure wherePeeringdbIxId($value)
 * @method static Builder|Infrastructure whereShortname($value)
 * @mixin Eloquent
 */
class Infrastructure extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'infrastructure';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ixp_id',
        'name',
        'shortname',
        'isPrimary',
        'peeringdb_ix_id',
        'ixf_ix_id',
        'country'
    ];

    /**
     * Get the vlans for the infrastructure
     */
    public function vlans(): HasMany
    {
        return $this->hasMany(Vlan::class, 'infrastructureid' );
    }

    /**
     * Get the switchers for the infrastructure
     */
    public function switchers(): HasMany
    {
        return $this->hasMany(Switcher::class, 'infrastructure' );
    }
}