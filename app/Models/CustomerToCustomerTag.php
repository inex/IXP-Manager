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

use Illuminate\Database\Eloquent\Model;

/**
 * IXP\Models\CustomerToCustomerTag
 *
 * @property int $customer_tag_id
 * @property int $customer_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerToCustomerTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerToCustomerTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerToCustomerTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerToCustomerTag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerToCustomerTag whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerToCustomerTag whereCustomerTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerToCustomerTag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CustomerToCustomerTag extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cust_to_cust_tag';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_tag_id',
        'customer_id',
    ];
}
