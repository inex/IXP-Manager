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
    Model
};

/**
 * IXP\Models\NetworkInfo
 *
 * @property int $id
 * @property int|null $vlanid
 * @property int|null $protocol
 * @property string|null $network
 * @property int|null $masklen
 * @property string|null $rs1address
 * @property string|null $rs2address
 * @property string|null $dnsfile
 * @method static Builder|NetworkInfo newModelQuery()
 * @method static Builder|NetworkInfo newQuery()
 * @method static Builder|NetworkInfo query()
 * @method static Builder|NetworkInfo whereDnsfile($value)
 * @method static Builder|NetworkInfo whereId($value)
 * @method static Builder|NetworkInfo whereMasklen($value)
 * @method static Builder|NetworkInfo whereNetwork($value)
 * @method static Builder|NetworkInfo whereProtocol($value)
 * @method static Builder|NetworkInfo whereRs1address($value)
 * @method static Builder|NetworkInfo whereRs2address($value)
 * @method static Builder|NetworkInfo whereVlanid($value)
 * @mixin \Eloquent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\NetworkInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\NetworkInfo whereUpdatedAt($value)
 */
class NetworkInfo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'networkinfo';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'vlanid',
        'protocol',
        'network',
        'masklen',
    ];
}