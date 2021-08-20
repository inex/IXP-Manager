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
    Model,
    Relations\BelongsTo,
    Relations\HasOne
};

/**
 * IXP\Models\CoreInterface
 *
 * @property int $id
 * @property int|null $physical_interface_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\CoreLink|null $coreLinkSideA
 * @property-read \IXP\Models\CoreLink|null $coreLinkSideB
 * @property-read \IXP\Models\PhysicalInterface|null $physicalInterface
 * @method static \Illuminate\Database\Eloquent\Builder|CoreInterface newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CoreInterface newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CoreInterface query()
 * @method static \Illuminate\Database\Eloquent\Builder|CoreInterface whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CoreInterface whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CoreInterface wherePhysicalInterfaceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CoreInterface whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CoreInterface extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'coreinterfaces';

    /**
     * Get the physical interface associated with the core interface.
     */
    public function physicalInterface(): BelongsTo
    {
        return $this->belongsTo(PhysicalInterface::class, 'physical_interface_id' );
    }

    /**
     * Get the corelink associated with the core interface side A.
     */
    public function coreLinkSideA(): HasOne
    {
        return $this->hasOne(CoreLink::class, 'core_interface_sidea_id' );
    }

    /**
     * Get the corelink associated with the core interface side B.
     */
    public function coreLinkSideB(): HasOne
    {
        return $this->hasOne(CoreLink::class, 'core_interface_sideb_id' );
    }

    /**
     * Check which side has a core link linked
     *
     * @return CoreLink
     */
    public function coreLink(): CoreLink
    {
        if( $this->coreLinkSideA()->exists() ) {
            return $this->coreLinkSideA;
        }
        return $this->coreLinkSideB;
    }
}