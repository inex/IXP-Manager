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
 * IXP\Models\PeeringManager
 *
 * @property int $id
 * @property int|null $custid
 * @property int|null $peerid
 * @property string|null $email_last_sent
 * @property int|null $emails_sent
 * @property int|null $peered
 * @property int|null $rejected
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\Customer|null $customer
 * @property-read \IXP\Models\Customer|null $peer
 * @method static \Illuminate\Database\Eloquent\Builder|PeeringManager newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PeeringManager newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PeeringManager query()
 * @method static \Illuminate\Database\Eloquent\Builder|PeeringManager whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PeeringManager whereCustid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PeeringManager whereEmailLastSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PeeringManager whereEmailsSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PeeringManager whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PeeringManager whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PeeringManager wherePeered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PeeringManager wherePeerid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PeeringManager whereRejected($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PeeringManager whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PeeringManager extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'peering_manager';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email_last_sent',
        'emails_sent',
        'peered',
        'rejected',
        'notes',
    ];

    /**
     * Get the customer that owns the peering manager
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'custid');
    }

    /**
     * Get the peer that owns the peering manager
     */
    public function peer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'peerid');
    }
}
