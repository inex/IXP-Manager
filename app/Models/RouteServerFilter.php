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
    Model,
    Relations\BelongsTo
};


/**
 * IXP\Models\RouteServerFilter
 *
 * @property int $id
 * @property int|null $customer_id
 * @property int|null $peer_id
 * @property int|null $vlan_id
 * @property string|null $received_prefix
 * @property string|null $advertised_prefix
 * @property int|null $protocol
 * @property string|null $action_advertise
 * @property string|null $action_receive
 * @property int $enabled
 * @property int $order_by
 * @property string $live
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\Customer|null $customer
 * @property-read \IXP\Models\Customer|null $peer
 * @property-read \IXP\Models\Vlan|null $vlan
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter whereActionAdvertise($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter whereActionReceive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter whereAdvertisedPrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter whereLive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter whereOrderBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter wherePeerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter whereProtocol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter whereReceivedPrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter whereVlanId($value)
 * @mixin \Eloquent
 */
class RouteServerFilter extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'peer_id',
        'vlan_id',
        'received_prefix',
        'advertised_prefix',
        'protocol',
        'action_advertise',
        'action_receive',
        'enabled',
        'order_by',
        'live',
    ];

    public const NO_ACTION         = "NO_ACTION";
    public const AS_IS             = "AS_IS";
    public const NO_ADVERTISE      = "NO_ADVERTISE";
    public const PREPEND_ONCE      = "PREPEND_ONCE";
    public const PREPEND_TWICE     = "PREPEND_TWICE";
    public const PREPEND_THRICE    = "PREPEND_THRICE";

    public static $ADVERTISE_ACTION_TEXT = [
        self::NO_ACTION         => 'No Action',
        self::AS_IS             => 'Advertise As Is',
        self::NO_ADVERTISE      => 'Do Not Advertise',
        self::PREPEND_ONCE      => 'Prepend My ASN x1',
        self::PREPEND_TWICE     => 'Prepend My ASN x2',
        self::PREPEND_THRICE    => 'Prepend My ASN x3',
    ];

    public static $RECEIVE_ACTION_TEXT = [
        self::NO_ACTION         => 'No Action',
        self::AS_IS             => 'Receive As Is',
        self::NO_ADVERTISE      => "Do Not Receive (Drop)",
        self::PREPEND_ONCE      => "Prepend Peer's ASN x1",
        self::PREPEND_TWICE     => "Prepend Peer's ASN x2",
        self::PREPEND_THRICE    => "Prepend Peer's ASN x3",
    ];

    /**
     * Get the customer that owns the route server filter.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Get the peer that owns the route server filter.
     */
    public function peer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'peer_id');
    }

    /**
     * Get the vlan that owns the route server filter.
     */
    public function vlan(): BelongsTo
    {
        return $this->belongsTo(Vlan::class, 'vlan_id');
    }

    /**
     * Turn the database integer representation of the action advertise into text as
     * defined in the self::$ADVERTISE_ACTION_TEXT array (or 'Unknown')
     *
     * @return string
     */
    public function resolveActionAdvertise(): string
    {
        return self::$ADVERTISE_ACTION_TEXT[ $this->action_advertise ] ?? 'Unknown';
    }

    /**
     * Turn the database integer representation of the action receive into text as
     * defined in the self::$RECEIVE_ACTION_TEXT array (or 'Unknown')
     *
     * @return string
     */
    public function resolveActionReceive(): string
    {
        return self::$RECEIVE_ACTION_TEXT[ $this->action_receive ] ?? 'Unknown';
    }

    /**
     * Turn the database integer representation of the protocol into text as
     * defined in the RouterEntity::$PROTOCOLS array (or 'Unknown')
     *
     * @return string
     */
    public function resolveProtocol(): string
    {
        return Router::$PROTOCOLS[ $this->protocol ] ?? 'Both';
    }
}