<?php

namespace IXP\Models;

/*
 * Copyright (C) 2026 KleyReX. All Rights Reserved.
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

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Links an external system's reference to what it created here.
 *
 * @property int         $id
 * @property string      $reference
 * @property string      $model_type
 * @property int         $model_id
 * @property int|null    $created_by
 *
 * @author     KleyReX
 * @category   IXP
 * @package    IXP\Models
 * @copyright  Copyright (C) 2026 KleyReX
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ProvisioningReference extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'provisioning_references';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reference',
        'model_type',
        'model_id',
        'created_by',
    ];

    /**
     * The thing this reference produced.
     *
     * @return MorphTo
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The customer a previous call with this reference produced, if any.
     *
     * @param  string|null  $reference
     *
     * @return Customer|null
     */
    public static function customerFor( ?string $reference ): ?Customer
    {
        if( $reference === null || $reference === '' ) {
            return null;
        }

        $ref = self::where( 'reference', $reference )
            ->where( 'model_type', Customer::class )
            ->first();

        return $ref ? Customer::find( $ref->model_id ) : null;
    }
}
