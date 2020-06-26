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

use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\HasMany};
use stdClass;

/**
 * IXP\Models\Infrastructure
 *
 * @property int $id
 * @property int $ixp_id
 * @property string|null $name
 * @property string|null $shortname
 * @property int $isPrimary
 * @property int|null $peeringdb_ix_id
 * @property int|null $ixf_ix_id
 * @property string|null $country
 * @method static Builder|Infrastructure newModelQuery()
 * @method static Builder|Infrastructure newQuery()
 * @method static Builder|Infrastructure query()
 * @method static Builder|Infrastructure whereCountry($value)
 * @method static Builder|Infrastructure whereId($value)
 * @method static Builder|Infrastructure whereIsPrimary($value)
 * @method static Builder|Infrastructure whereIxfIxId($value)
 * @method static Builder|Infrastructure whereIxpId($value)
 * @method static Builder|Infrastructure whereName($value)
 * @method static Builder|Infrastructure wherePeeringdbIxId($value)
 * @method static Builder|Infrastructure whereShortname($value)
 * @mixin Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\Vlan[] $Vlans
 * @property-read int|null $vlans_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\Switcher[] $Switchers
 * @property-read int|null $switchers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\Switcher[] $switchers
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\Vlan[] $vlans
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
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

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
     * The cache key for the primary infrastructure
     * @var string The cache key for the primary infrastructure
     */
    const CACHE_KEY_PRIMARY = 'infrastructure_primary';

    /**
     * The cache key for the all infrastructures
     * @var string The cache key for all infrastructures
     */
    const CACHE_KEY_ALL = 'infrastructure_all';

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

    /**
     * Gets a listing of mailing list or a single one if an ID is provided
     *
     * @param stdClass $feParams
     * @param int|null $id
     *
     * @return array
     */
    public static function getFeList( stdClass $feParams, int $id = null ): array
    {
        return self::when( $id , function( Builder $q, $id ) {
            return $q->where('id', $id );
        } )->when( $feParams->listOrderBy , function( Builder $q, $orderby ) use ( $feParams )  {
            return $q->orderBy( $orderby, $feParams->listOrderByDir ?? 'ASC');
        })->get()->toArray();
    }

    /**
     * Gets a listing of infrastructures as array
     *
     * @return array
     */
    public static function getListAsArray(): array
    {
        return self::orderBy( 'name', 'asc' )->get()->toArray();
    }
}
