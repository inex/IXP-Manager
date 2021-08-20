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
    Relations\BelongsTo
};

use IXP\Traits\Observable;

/**
 * IXP\Models\Logo
 *
 * @property int $id
 * @property int|null $customer_id
 * @property string $type
 * @property string $original_name
 * @property string $stored_name
 * @property string $uploaded_by
 * @property int $width
 * @property int $height
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\Customer|null $customer
 * @method static \Illuminate\Database\Eloquent\Builder|Logo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Logo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Logo query()
 * @method static \Illuminate\Database\Eloquent\Builder|Logo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Logo whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Logo whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Logo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Logo whereOriginalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Logo whereStoredName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Logo whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Logo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Logo whereUploadedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Logo whereWidth($value)
 * @mixin \Eloquent
 */
class Logo extends Model
{
    use Observable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'original_name',
        'stored_name',
        'uploaded_by',
        'width',
        'height',
    ];

    /**
     * Get the customer that own the logo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Creates a hierarchy directory structure to shard image storage
     *
     * @return string the/sharded/path/filename
     */
    public function shardedPath(): string
    {
        return $this->stored_name[ 0 ] . '/' . $this->stored_name[ 1 ] . '/' . $this->stored_name;
    }

    /**
     * Get the full path of the a logo
     *
     * @return string the/full/path/filename
     */
    public function fullPath(): string
    {
        return public_path() . '/logos/' . $this->shardedPath();
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
            "Logo [id:%d] belonging to %s [id:%d] '%s'",
            $model->id,
            ucfirst( config( 'ixp_fe.lang.customer.one' ) ),
            $model->customer_id,
            $model->customer->name,
        );
    }
}
