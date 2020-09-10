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

use Illuminate\Database\Eloquent\{
    Builder,
    Model,
    Relations\HasMany
};

/**
 * IXP\Models\IrrdbConfig
 *
 * @property int $id
 * @property string|null $host
 * @property string|null $protocol
 * @property string|null $source
 * @property string|null $notes
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\Customer[] $customers
 * @property-read int|null $customers_count
 * @method static Builder|IrrdbConfig newModelQuery()
 * @method static Builder|IrrdbConfig newQuery()
 * @method static Builder|IrrdbConfig query()
 * @method static Builder|IrrdbConfig whereHost($value)
 * @method static Builder|IrrdbConfig whereId($value)
 * @method static Builder|IrrdbConfig whereNotes($value)
 * @method static Builder|IrrdbConfig whereProtocol($value)
 * @method static Builder|IrrdbConfig whereSource($value)
 * @mixin \Eloquent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\IrrdbConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\IrrdbConfig whereUpdatedAt($value)
 */
class IrrdbConfig extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'irrdbconfig';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'host',
        'protocol',
        'source',
        'notes',
    ];

    /**
     * Get the customers for the Irrdb Config
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'irrdb');
    }
}