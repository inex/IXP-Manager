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

use Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * IXP\Models\PhysicalInterface
 *
 * @property int $id
 * @property int|null $switchportid
 * @property int|null $virtualinterfaceid
 * @property int|null $status
 * @property int|null $speed
 * @property string|null $duplex
 * @property string|null $notes
 * @property int|null $fanout_physical_interface_id
 * @property int $autoneg
 * @property-read \IXP\Models\VirtualInterface|null $virtualInterface
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PhysicalInterface newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PhysicalInterface newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PhysicalInterface query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PhysicalInterface whereAutoneg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PhysicalInterface whereDuplex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PhysicalInterface whereFanoutPhysicalInterfaceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PhysicalInterface whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PhysicalInterface whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PhysicalInterface whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PhysicalInterface whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PhysicalInterface whereSwitchportid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PhysicalInterface whereVirtualinterfaceid($value)
 * @mixin \Eloquent
 * @property-read \IXP\Models\SwitchPort|null $switchPort
 */
class PhysicalInterface extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'physicalinterface';

    /**
     * Get the virtual interface that owns the physical interface.
     */
    public function virtualInterface(): BelongsTo
    {
        return $this->belongsTo(VirtualInterface::class, 'virtualinterfaceid' );
    }

    /**
     * Get the switch port that owns the physical interface.
     */
    public function switchPort(): HasOne
    {
        return $this->hasOne(SwitchPort::class, 'switchportid');
    }


    /**
     * Provide array of all the speeds
     *
     * @return array
     */
    public static function getAllSpeed(): array
    {
        return self::selectRaw( 'DISTINCT physicalinterface.speed AS speed' )
            ->orderBy( 'speed', 'ASC' )
            ->get()->toArray();
    }
}
