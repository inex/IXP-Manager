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
 * IXP\Models\RsPrefix
 *
 * @property int $id
 * @property int|null $custid
 * @property string|null $timestamp
 * @property string|null $prefix
 * @property int|null $protocol
 * @property int|null $irrdb
 * @property int|null $rs_origin
 * @property-read \IXP\Models\Customer|null $customer
 * @method static \Illuminate\Database\Eloquent\Builder|RsPrefix newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RsPrefix newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RsPrefix query()
 * @method static \Illuminate\Database\Eloquent\Builder|RsPrefix whereCustid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsPrefix whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsPrefix whereIrrdb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsPrefix wherePrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsPrefix whereProtocol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsPrefix whereRsOrigin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsPrefix whereTimestamp($value)
 * @mixin \Eloquent
 */
class RsPrefix extends Model
{
    /**
     * Map prefix acceptance types to summary functions
     * @var array Map prefix acceptance types to summary functions
     */
    public static $SUMMARY_TYPES_FNS = [
        'adv_acc'  => 'summaryRoutesAdvertisedAndAccepted',
        'adv_nacc' => 'summaryRoutesAdvertisedAndNotAccepted',
        'nadv_acc' => 'summaryRoutesNotAdvertisedButAcceptable'
    ];

    /**
     * Map prefix acceptance types to lookup functions
     * @var array Map prefix acceptance types to lookup functions
     */
    public static $ROUTES_TYPES_FNS = [
        'adv_acc'  => 'routesAdvertisedAndAccepted',
        'adv_nacc' => 'routesAdvertisedAndNotAccepted',
        'nadv_acc' => 'routesNotAdvertisedButAcceptable'
    ];

    /**
     * Get the the customer that own the rs prefix
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'custid' );
    }
}