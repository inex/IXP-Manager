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
    Builder,
    Model,
    Relations\BelongsTo
};

use IXP\Traits\Observable;

/**
 * IXP\Models\CustomerEquipment
 *
 * @property int $id
 * @property int|null $custid
 * @property int|null $cabinetid
 * @property string|null $name
 * @property string|null $descr
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\Cabinet|null $cabinet
 * @method static Builder|CustomerEquipment newModelQuery()
 * @method static Builder|CustomerEquipment newQuery()
 * @method static Builder|CustomerEquipment query()
 * @method static Builder|CustomerEquipment whereCabinetid($value)
 * @method static Builder|CustomerEquipment whereCreatedAt($value)
 * @method static Builder|CustomerEquipment whereCustid($value)
 * @method static Builder|CustomerEquipment whereDescr($value)
 * @method static Builder|CustomerEquipment whereId($value)
 * @method static Builder|CustomerEquipment whereName($value)
 * @method static Builder|CustomerEquipment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CustomerEquipment extends Model
{
    use Observable;

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

    /**
     * String to describe the model being updated / deleted / created
     *
     * @param Model $model
     *
     * @return string
     */
    public static function logSubject( Model $model ): string
    {
        return sprintf(
            "Colocated Equipment (Customer Equipment) [id:%d] belonging to Rack (Cabinet) [id:%d] '%s'",
            $model->id,
            $model->cabinetid,
            $model->cabinet->name
        );
    }
}