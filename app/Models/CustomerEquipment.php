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
    Builder,
    Model,
    Relations\BelongsTo
};

/**
 * IXP\Models\CustomerEquipment
 *
 * @property int $id
 * @property int|null $custid
 * @property int|null $cabinetid
 * @property string|null $name
 * @property string|null $descr
 * @property-read \IXP\Models\Cabinet|null $cabinet
 * @method static Builder|CustomerEquipment newModelQuery()
 * @method static Builder|CustomerEquipment newQuery()
 * @method static Builder|CustomerEquipment query()
 * @method static Builder|CustomerEquipment whereCabinetid($value)
 * @method static Builder|CustomerEquipment whereCustid($value)
 * @method static Builder|CustomerEquipment whereDescr($value)
 * @method static Builder|CustomerEquipment whereId($value)
 * @method static Builder|CustomerEquipment whereName($value)
 * @mixin \Eloquent
 */
class CustomerEquipment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'custkit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'custid',
        'cabinetid',
        'name',
        'descr',
    ];

    /**
     * Get the cabinet that own the customer equipment
     */
    public function cabinet(): BelongsTo
    {
        return $this->belongsTo(Cabinet::class, 'cabinetid' );
    }
}