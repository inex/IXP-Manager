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
    Relations\HasMany
};

use IXP\Traits\Observable;

/**
 * IXP\Models\Infrastructure
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $shortname
 * @property bool $isPrimary
 * @property int|null $peeringdb_ix_id
 * @property int|null $ixf_ix_id
 * @property bool $exclude_from_ixf_export
 * @property string|null $country
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Collection<int, \IXP\Models\Switcher> $switchers
 * @property-read int|null $switchers_count
 * @property-read Collection<int, \IXP\Models\Vlan> $vlans
 * @property-read int|null $vlans_count
 * @method static Builder<static>|Infrastructure newModelQuery()
 * @method static Builder<static>|Infrastructure newQuery()
 * @method static Builder<static>|Infrastructure query()
 * @method static Builder<static>|Infrastructure whereCountry($value)
 * @method static Builder<static>|Infrastructure whereCreatedAt($value)
 * @method static Builder<static>|Infrastructure whereExcludeFromIxfExport($value)
 * @method static Builder<static>|Infrastructure whereId($value)
 * @method static Builder<static>|Infrastructure whereIsPrimary($value)
 * @method static Builder<static>|Infrastructure whereIxfIxId($value)
 * @method static Builder<static>|Infrastructure whereName($value)
 * @method static Builder<static>|Infrastructure whereNotes($value)
 * @method static Builder<static>|Infrastructure wherePeeringdbIxId($value)
 * @method static Builder<static>|Infrastructure whereShortname($value)
 * @method static Builder<static>|Infrastructure whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Infrastructure extends Model
{
    use Observable;

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
        'name',
        'shortname',
        'isPrimary',
        'exclude_from_ixf_export',
        'peeringdb_ix_id',
        'ixf_ix_id',
        'country',
        'notes',
    ];


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    #[\Override]
    protected function casts(): array
    {
        return [
            'isPrimary' => 'boolean',
            'exclude_from_ixf_export' => 'boolean',
        ];
    }


    /**
     * Get the vlans for the infrastructure
     *
     * @return HasMany<Vlan, Infrastructure>
     */
    public function vlans(): HasMany
    {
        return $this->hasMany(Vlan::class, 'infrastructureid' );
    }

    /**
     * Get the switchers for the infrastructure
     *
     * @return HasMany<Switcher, Infrastructure>
     */
    public function switchers(): HasMany
    {
        return $this->hasMany(Switcher::class, 'infrastructure' );
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
            "Infrastructure [id:%d] '%s'",
            $model->id,
            $model->name
        );
    }
}