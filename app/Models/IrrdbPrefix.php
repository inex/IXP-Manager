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

/**
 * IXP\Models\IrrdbPrefix
 *
 * @property int $id
 * @property int $customer_id
 * @property string $prefix
 * @property int $protocol
 * @property string|null $first_seen
 * @property string|null $last_seen
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\Customer $customer
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbPrefix newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbPrefix newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbPrefix query()
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbPrefix whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbPrefix whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbPrefix whereFirstSeen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbPrefix whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbPrefix whereLastSeen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbPrefix wherePrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbPrefix whereProtocol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbPrefix whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class IrrdbPrefix extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'irrdb_prefix';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'prefix',
        'protocol',
        'first_seen',
        'last_seen',
    ];

    /**
     * Get the customer for the irrdb prefix
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id' );
    }

}
