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

use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};

/**
 * IXP\Models\MacAddress
 *
 * @property int $id
 * @property int|null $virtualinterfaceid
 * @property string|null $firstseen
 * @property string|null $lastseen
 * @property string|null $mac
 * @method static Builder|MacAddress newModelQuery()
 * @method static Builder|MacAddress newQuery()
 * @method static Builder|MacAddress query()
 * @method static Builder|MacAddress whereFirstseen($value)
 * @method static Builder|MacAddress whereId($value)
 * @method static Builder|MacAddress whereLastseen($value)
 * @method static Builder|MacAddress whereMac($value)
 * @method static Builder|MacAddress whereVirtualinterfaceid($value)
 * @mixin \Eloquent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\MacAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\MacAddress whereUpdatedAt($value)
 * @property-read \IXP\Models\VirtualInterface|null $virtualInterface
 */
class MacAddress extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'macaddress';

    /**
     * Get the virtual interface for the mac addresses for
     */
    public function virtualInterface(): BelongsTo
    {
        return $this->belongsTo(VirtualInterface::class, 'virtualinterfaceid');
    }
}