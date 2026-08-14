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
use Eloquent;

use Illuminate\Database\Eloquent\{
    Builder,
    Model,
    Relations\BelongsTo
};

use Illuminate\Support\{
    Carbon,
};

/**
 * IXP\Models\AtlasProbe
 *
 * @property int $id
 * @property int $cust_id
 * @property string|null $address_v4
 * @property string|null $address_v6
 * @property int|null $v4_enabled
 * @property int|null $v6_enabled
 * @property int|null $asn
 * @property int $atlas_id
 * @property int $is_anchor
 * @property int $is_public
 * @property string|null $last_connected
 * @property string|null $status
 * @property string|null $api_data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \IXP\Models\Customer $customer
 * @method static Builder<static>|AtlasProbe forActiveProtocol(int $protocol)
 * @method static Builder<static>|AtlasProbe forAtlas(int $atlasid)
 * @method static Builder<static>|AtlasProbe forCustomer(int $custid)
 * @method static Builder<static>|AtlasProbe newModelQuery()
 * @method static Builder<static>|AtlasProbe newQuery()
 * @method static Builder<static>|AtlasProbe query()
 * @method static Builder<static>|AtlasProbe whereAddressV4($value)
 * @method static Builder<static>|AtlasProbe whereAddressV6($value)
 * @method static Builder<static>|AtlasProbe whereApiData($value)
 * @method static Builder<static>|AtlasProbe whereAsn($value)
 * @method static Builder<static>|AtlasProbe whereAtlasId($value)
 * @method static Builder<static>|AtlasProbe whereCreatedAt($value)
 * @method static Builder<static>|AtlasProbe whereCustId($value)
 * @method static Builder<static>|AtlasProbe whereId($value)
 * @method static Builder<static>|AtlasProbe whereIsAnchor($value)
 * @method static Builder<static>|AtlasProbe whereIsPublic($value)
 * @method static Builder<static>|AtlasProbe whereLastConnected($value)
 * @method static Builder<static>|AtlasProbe whereStatus($value)
 * @method static Builder<static>|AtlasProbe whereUpdatedAt($value)
 * @method static Builder<static>|AtlasProbe whereV4Enabled($value)
 * @method static Builder<static>|AtlasProbe whereV6Enabled($value)
 * @mixin Eloquent
 */
class AtlasProbe extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'atlas_probes';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cust_id',
        'atlas_id',
        'v4_enabled',
        'v6_enabled',
        'address_v4',
        'address_v6',
        'is_anchor',
        'is_public',
        'status',
        'api_data',
        'asn',
        'last_connected',
    ];

    /**
     * Get the customer that owns the Atlas probe
     *
     * @return BelongsTo<Customer, AtlasProbe>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'cust_id');
    }

    /**
     * Scope a query to only include atlas probes for a given customer.
     *
     * @param Builder $query
     * @param  int  $custid
     *
     * @return Builder
     */
    public function scopeForCustomer( Builder $query, int $custid ): Builder
    {
        return $query->where('cust_id', $custid );
    }

    /**
     * Scope a query to only include atlas probes for a given atlas id.
     *
     * @param   Builder $query
     * @param   int     $atlasid
     *
     * @return Builder
     */
    public function scopeForAtlas( Builder $query, int $atlasid ): Builder
    {
        return $query->where('atlas_id', $atlasid );
    }

    /**
     * Scope a query to only include atlas probes for a given protocol.
     *
     * @param   Builder $query
     * @param   int     $protocol
     *
     * @return Builder
     */
    public function scopeForActiveProtocol( Builder $query, int $protocol ): Builder
    {
        return $query->where( 'v' . $protocol . '_enabled', 1 );
    }
}