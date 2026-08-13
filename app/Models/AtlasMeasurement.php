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
    Model,
    Relations\BelongsTo,
    Relations\HasOne
};

use Illuminate\Support\Carbon;

/**
 * IXP\Models\AtlasMeasurement
 *
 * @property int $id
 * @property int $run_id
 * @property int|null $cust_source
 * @property int|null $cust_dest
 * @property int|null $atlas_id
 * @property string|null $atlas_create
 * @property string|null $atlas_start
 * @property string|null $atlas_stop
 * @property string|null $atlas_data
 * @property string|null $atlas_request
 * @property string|null $atlas_state
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \IXP\Models\AtlasResult|null $atlasResult
 * @property-read \IXP\Models\AtlasRun $atlasRun
 * @property-read \IXP\Models\Customer|null $custDest
 * @property-read \IXP\Models\Customer|null $custSource
 * @method static Builder<static>|AtlasMeasurement newModelQuery()
 * @method static Builder<static>|AtlasMeasurement newQuery()
 * @method static Builder<static>|AtlasMeasurement query()
 * @method static Builder<static>|AtlasMeasurement whereAtlasCreate($value)
 * @method static Builder<static>|AtlasMeasurement whereAtlasData($value)
 * @method static Builder<static>|AtlasMeasurement whereAtlasId($value)
 * @method static Builder<static>|AtlasMeasurement whereAtlasRequest($value)
 * @method static Builder<static>|AtlasMeasurement whereAtlasStart($value)
 * @method static Builder<static>|AtlasMeasurement whereAtlasState($value)
 * @method static Builder<static>|AtlasMeasurement whereAtlasStop($value)
 * @method static Builder<static>|AtlasMeasurement whereCreatedAt($value)
 * @method static Builder<static>|AtlasMeasurement whereCustDest($value)
 * @method static Builder<static>|AtlasMeasurement whereCustSource($value)
 * @method static Builder<static>|AtlasMeasurement whereId($value)
 * @method static Builder<static>|AtlasMeasurement whereRunId($value)
 * @method static Builder<static>|AtlasMeasurement whereUpdatedAt($value)
 * @mixin Eloquent
 */

class AtlasMeasurement extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'atlas_measurements';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'run_id',
        'cust_source',
        'cust_dest',
        'atlas_id',
        'atlas_create',
        'atlas_request',
        'atlas_start',
        'atlas_stop',
        'atlas_data',
        'atlas_state',
    ];

    /**
     * Get the atlas run
     *
     * @return BelongsTo<AtlasRun,AtlasMeasurement>
     */
    public function atlasRun(): BelongsTo
    {
        return $this->belongsTo( AtlasRun::class, 'run_id');
    }

    /**
     * Get the customer source
     *
     * @return BelongsTo<Customer,AtlasMeasurement>
     */
    public function custSource(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'cust_source');
    }

    /**
     * Get the customer destination
     *
     * @return BelongsTo<Customer,AtlasMeasurement>
     */
    public function custDest(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'cust_dest');
    }

    /**
     * Get the atlas result for a measurement
     *
     * @return HasOne<AtlasResult,AtlasMeasurement>
     */
    public function atlasResult(): HasOne
    {
        return $this->hasOne( AtlasResult::class, 'measurement_id');
    }
}