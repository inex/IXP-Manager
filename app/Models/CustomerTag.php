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
    Relations\BelongsToMany
};

/**
 * IXP\Models\CustomerTag
 *
 * @property int $id
 * @property string $tag
 * @property string $display_as
 * @property string|null $description
 * @property int $internal_only
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\Customer[] $customers
 * @property-read int|null $customers_count
 * @method static Builder|CustomerTag newModelQuery()
 * @method static Builder|CustomerTag newQuery()
 * @method static Builder|CustomerTag query()
 * @method static Builder|CustomerTag whereCreatedAt($value)
 * @method static Builder|CustomerTag whereDescription($value)
 * @method static Builder|CustomerTag whereDisplayAs($value)
 * @method static Builder|CustomerTag whereId($value)
 * @method static Builder|CustomerTag whereInternalOnly($value)
 * @method static Builder|CustomerTag whereTag($value)
 * @method static Builder|CustomerTag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CustomerTag extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cust_tag';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tag',
        'display_as',
        'description',
        'internal_only',
    ];

    /**
     * Get all the customers for the tag
     */
    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'cust_to_cust_tag', 'customer_tag_id' );
    }
}
