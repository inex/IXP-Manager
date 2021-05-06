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

use DB, Eloquent, stdClass;

use Illuminate\Support\{
    Collection
};

use Illuminate\Database\Eloquent\{
    Builder,
    Model,
    Relations\BelongsTo,
    Relations\HasMany
};

/**
 * IXP\Models\AtlasRun
 *
 * @property int $id
 * @property int|null $vlan_id
 * @property int|null $protocol
 * @property string|null $scheduled_at
 * @property string|null $started_at
 * @property string|null $completed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\AtlasMeasurement[] $atlasMeasurements
 * @property-read int|null $atlas_measurements_count
 * @property-read \IXP\Models\Vlan|null $vlan
 * @method static Builder|AtlasRun newModelQuery()
 * @method static Builder|AtlasRun newQuery()
 * @method static Builder|AtlasRun query()
 * @method static Builder|AtlasRun whereCompletedAt($value)
 * @method static Builder|AtlasRun whereCreatedAt($value)
 * @method static Builder|AtlasRun whereId($value)
 * @method static Builder|AtlasRun whereProtocol($value)
 * @method static Builder|AtlasRun whereScheduledAt($value)
 * @method static Builder|AtlasRun whereStartedAt($value)
 * @method static Builder|AtlasRun whereUpdatedAt($value)
 * @method static Builder|AtlasRun whereVlanId($value)
 * @mixin Eloquent
 */
class AtlasRun extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'atlas_runs';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'protocol',
        'started_at',
        'scheduled_at',
        'created_at',
        'completed_at',
        'vlan_id'
    ];

    /**
     * CONST SCHEDULED
     */
    public const SCHEDULED_AT_NOW         = 1;
    public const SCHEDULED_AT_DATETIME    = 2;

    /**
     * @var array Email ids to classes
     */
    public static $SCHEDULED_AT = [
        self::SCHEDULED_AT_NOW         =>      'Run immediately',
        self::SCHEDULED_AT_DATETIME    =>      'Run scheduled at'
    ];

    /**
     * Get the atlas measurements
     */
    public function atlasMeasurements(): HasMany
    {
        return $this->hasMany( AtlasMeasurement::class, 'run_id');
    }

    /**
     * Get the vlan
     */
    public function vlan(): BelongsTo
    {
        return $this->belongsTo(Vlan::class, 'vlan_id');
    }
}