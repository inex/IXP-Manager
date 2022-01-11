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
    Builder,
    Model,
    Relations\HasMany
};

use IXP\Traits\Observable;

/**
 * IXP\Models\IrrdbConfig
 *
 * @property int $id
 * @property string|null $host
 * @property string|null $protocol
 * @property string|null $source
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\Customer[] $customers
 * @property-read int|null $customers_count
 * @method static Builder|IrrdbConfig newModelQuery()
 * @method static Builder|IrrdbConfig newQuery()
 * @method static Builder|IrrdbConfig query()
 * @method static Builder|IrrdbConfig whereCreatedAt($value)
 * @method static Builder|IrrdbConfig whereHost($value)
 * @method static Builder|IrrdbConfig whereId($value)
 * @method static Builder|IrrdbConfig whereNotes($value)
 * @method static Builder|IrrdbConfig whereProtocol($value)
 * @method static Builder|IrrdbConfig whereSource($value)
 * @method static Builder|IrrdbConfig whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class IrrdbConfig extends Model
{
    use Observable;

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
            "IRRDB Config [id:%d] '%s', '%s'",
            $model->id,
            $model->host,
            $model->source,
        );
    }
}