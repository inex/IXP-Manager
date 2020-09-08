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
    Model,
    Relations\BelongsTo
};

/**
 * IXP\Models\CoreLink
 *
 * @property int $id
 * @property int $core_interface_sidea_id
 * @property int $core_interface_sideb_id
 * @property int $core_bundle_id
 * @property int $bfd
 * @property string|null $ipv4_subnet
 * @property string|null $ipv6_subnet
 * @property int $enabled
 * @property-read \IXP\Models\CoreBundle $coreBundle
 * @property-read \IXP\Models\CoreInterface $coreInterfaceSideA
 * @property-read \IXP\Models\CoreInterface $coreInterfaceSideB
 * @method static \Illuminate\Database\Eloquent\Builder|CoreLink newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CoreLink newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CoreLink query()
 * @method static \Illuminate\Database\Eloquent\Builder|CoreLink whereBfd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CoreLink whereCoreBundleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CoreLink whereCoreInterfaceSideaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CoreLink whereCoreInterfaceSidebId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CoreLink whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CoreLink whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CoreLink whereIpv4Subnet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CoreLink whereIpv6Subnet($value)
 * @mixin \Eloquent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CoreLink whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CoreLink whereUpdatedAt($value)
 */
class CoreLink extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'corelinks';

    /**
     * Get the core interface side A  associated with the corelink.
     */
    public function coreInterfaceSideA(): BelongsTo
    {
        return $this->belongsTo(CoreInterface::class, 'core_interface_sidea_id' );
    }

    /**
     * Get the core interface side B  associated with the corelink.
     */
    public function coreInterfaceSideB(): BelongsTo
    {
        return $this->belongsTo(CoreInterface::class, 'core_interface_sideb_id' );
    }

    /**
     * Get the corebundle that own the corelink
     */
    public function coreBundle(): BelongsTo
    {
        return $this->belongsTo(CoreBundle::class, 'core_bundle_id' );
    }
}
