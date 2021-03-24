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

use Illuminate\Database\Eloquent\Model;

/**
 * IXP\Models\IrrdbAsn
 *
 * @property int $id
 * @property int $customer_id
 * @property int $asn
 * @property int $protocol
 * @property string|null $first_seen
 * @property string|null $last_seen
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbAsn newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbAsn newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbAsn query()
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbAsn whereAsn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbAsn whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbAsn whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbAsn whereFirstSeen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbAsn whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbAsn whereLastSeen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbAsn whereProtocol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbAsn whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class IrrdbAsn extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'irrdb_asn';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'asn',
        'protocol',
        'first_seen',
        'last_seen',
    ];

}
