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
    Relations\BelongsTo
};

/**
 * IXP\Models\AtlasResult
 *
 * @property int $id
 * @property int|null $measurement_id
 * @property string|null $routing
 * @property string|null $path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\AtlasMeasurement $atlasMeasurement
 * @method static Builder|AtlasResult newModelQuery()
 * @method static Builder|AtlasResult newQuery()
 * @method static Builder|AtlasResult query()
 * @method static Builder|AtlasResult whereCreatedAt($value)
 * @method static Builder|AtlasResult whereId($value)
 * @method static Builder|AtlasResult whereMeasurementId($value)
 * @method static Builder|AtlasResult wherePath($value)
 * @method static Builder|AtlasResult whereRouting($value)
 * @method static Builder|AtlasResult whereUpdatedAt($value)
 * @mixin Eloquent
 */

class AtlasResult extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'atlas_results';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'measurement_id',
        'routing',
        'path',
    ];

    /**
     * Get the atlas measurement
     */
    public function atlasMeasurement(): BelongsTo
    {
        return $this->belongsTo(AtlasMeasurement::class );
    }
}