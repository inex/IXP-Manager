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
 * @property mixed|null $atlas_data
 * @property mixed|null $atlas_request
 * @property string|null $atlas_state
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \IXP\Models\AtlasResult|null $atlasResult
 * @property-read \IXP\Models\AtlasRun $atlasRun
 * @property-read \IXP\Models\Customer|null $custDest
 * @property-read \IXP\Models\Customer|null $custSource
 * @method static Builder|AtlasMeasurement newModelQuery()
 * @method static Builder|AtlasMeasurement newQuery()
 * @method static Builder|AtlasMeasurement query()
 * @method static Builder|AtlasMeasurement whereAtlasCreate($value)
 * @method static Builder|AtlasMeasurement whereAtlasData($value)
 * @method static Builder|AtlasMeasurement whereAtlasId($value)
 * @method static Builder|AtlasMeasurement whereAtlasRequest($value)
 * @method static Builder|AtlasMeasurement whereAtlasStart($value)
 * @method static Builder|AtlasMeasurement whereAtlasState($value)
 * @method static Builder|AtlasMeasurement whereAtlasStop($value)
 * @method static Builder|AtlasMeasurement whereCreatedAt($value)
 * @method static Builder|AtlasMeasurement whereCustDest($value)
 * @method static Builder|AtlasMeasurement whereCustSource($value)
 * @method static Builder|AtlasMeasurement whereId($value)
 * @method static Builder|AtlasMeasurement whereRunId($value)
 * @method static Builder|AtlasMeasurement whereUpdatedAt($value)
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
     */
    public function atlasRun(): BelongsTo
    {
        return $this->belongsTo( AtlasRun::class, 'run_id');
    }

    /**
     * Get the customer source
     */
    public function custSource(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'cust_source');
    }

    /**
     * Get the customer destination
     */
    public function custDest(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'cust_dest');
    }

    /**
     * Get the atlas result for a measurement
     */
    public function atlasResult(): HasOne
    {
        return $this->hasOne( AtlasResult::class, 'measurement_id');
    }
}