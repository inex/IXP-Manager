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
 * @method static \Illuminate\Database\Eloquent\Builder|PhysicalInterface newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PhysicalInterface newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PhysicalInterface query()
 * @method static \Illuminate\Database\Eloquent\Builder|PhysicalInterface whereAutoneg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhysicalInterface whereDuplex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhysicalInterface whereFanoutPhysicalInterfaceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhysicalInterface whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhysicalInterface whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhysicalInterface whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhysicalInterface whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhysicalInterface whereSwitchportid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhysicalInterface whereVirtualinterfaceid($value)
 * @mixin Eloquent
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
     * Get the customer that owns the virtual interfaces.
     */
    public function virtualInterface()
    {
        return $this->belongsTo('IXP\Models\VirtualInterface', 'virtualinterfaceid');
    }
    
}
