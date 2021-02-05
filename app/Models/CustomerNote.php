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

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * IXP\Models\CustomerNote
 *
 * @property int $id
 * @property int $customer_id
 * @property int $private
 * @property string $title
 * @property string $note
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \IXP\Models\Customer $customer
 * @method static Builder|CustomerNote newModelQuery()
 * @method static Builder|CustomerNote newQuery()
 * @method static Builder|CustomerNote privateOnly()
 * @method static Builder|CustomerNote publicOnly()
 * @method static Builder|CustomerNote query()
 * @method static Builder|CustomerNote whereCreatedAt($value)
 * @method static Builder|CustomerNote whereCustomerId($value)
 * @method static Builder|CustomerNote whereId($value)
 * @method static Builder|CustomerNote whereNote($value)
 * @method static Builder|CustomerNote wherePrivate($value)
 * @method static Builder|CustomerNote whereTitle($value)
 * @method static Builder|CustomerNote whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CustomerNote extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cust_notes';

    /**
     * Get the customer that own the customer note
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id' );
    }

    /**
     * Scope a query to only include private notes
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopePrivateOnly( Builder $query ): Builder
    {
        return $query->where( 'private', 1 );
    }

    /**
     * Scope a query to only public notes
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopePublicOnly( Builder $query ): Builder
    {
        return $query->where( 'private', 0 );
    }
}
