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

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use IXP\Traits\Observable;
/**
 * IXP\Models\CustomerToCustomerTag
 *
 * @property int $customer_tag_id
 * @property int $customer_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\Customer $customer
 * @property-read \IXP\Models\CustomerTag $tag
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
    use Observable;

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

    /**
     * Get the customer that own the customer to contact
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id' );
    }

    /**
     * Get the tag that own the customer to contact
     */
    public function tag(): BelongsTo
    {
        return $this->belongsTo(CustomerTag::class, 'customer_tag_id' );
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
            "%s Tag [id:%d] '%s' associated to %s [id:%d] '%s'",
            ucfirst( config( 'ixp_fe.lang.customer.one' ) ),
            $model->customer_tag_id,
            $model->tag->tag,
            config( 'ixp_fe.lang.customer.one' ),
            $model->customer_id,
            $model->customer->name
        );
    }
}
