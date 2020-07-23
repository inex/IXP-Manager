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
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * IXP\Models\RouteServerFilter
 *
 * @property int $id
 * @property int|null $customer_id
 * @property int|null $peer_id
 * @property int|null $vlan_id
 * @property string|null $prefix
 * @property int|null $protocol
 * @property string|null $action_advertise
 * @property string|null $action_receive
 * @property int $enabled
 * @property int $order_by
 * @property string $live
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter whereActionAdvertise($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter whereActionReceive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter whereLive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter whereOrderBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter wherePeerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter wherePrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter whereProtocol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\RouteServerFilter whereVlanId($value)
 * @mixin \Eloquent
 */
class RouteServerFilter extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'route_server_filters';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'peer_id',
        'vlan_id',
        'prefix',
        'protocol',
        'action_advertise',
        'action_receive',
        'enabled',
        'order_by',
        'live',
    ];

    const AS_IN             = null;
    const NO_ADVERTISE      = "NO_ADVERTISE";
    const PREPEND_ONCE      = "PREPEND_ONCE";
    const PREPEND_TWICE     = "PREPEND_TWICE";
    const PREPEND_THRICE    = "PREPEND_THRICE";

    public static $ADVERTISE_ACTION_TEXT = [
        self::AS_IN             => 'Advertise As Is',
        self::NO_ADVERTISE      => 'Do Not Advertise To Peer',
        self::PREPEND_ONCE      => 'Prepend My ASN Once To Peer',
        self::PREPEND_TWICE     => 'Prepend My ASN Twice To Peer',
        self::PREPEND_THRICE    => 'Prepend My ASN Thrice To Peer',
    ];

    public static $RECEIVE_ACTION_TEXT = [
        self::AS_IN             => "Receive As Is",
        self::NO_ADVERTISE      => "Do Not Receive To Peer",
        self::PREPEND_ONCE      => "Prepend Peer's ASN Once",
        self::PREPEND_TWICE     => "Prepend Peer's ASN Twice",
        self::PREPEND_THRICE    => "Prepend Peer's ASN Thrice",
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
     * @return string
     */
    public function resolveActionAdvertise(): string
    {
        return self::$ADVERTISE_ACTION_TEXT[ $this->action_advertise ] ?? 'Unknown';
    }

    /**
     * Turn the database integer representation of the action receive into text as
     * defined in the self::$RECEIVE_ACTION_TEXT array (or 'Unknown')
     * @return string
     */
    public function resolveActionReceive(): string
    {
        return self::$RECEIVE_ACTION_TEXT[ $this->action_receive ] ?? 'Unknown';
    }

    /**
     * Turn the database integer representation of the protocol into text as
     * defined in the RouterEntity::$PROTOCOLS array (or 'Unknown')
     * @return string
     */
    public function resolveProtocol(): string
    {
        return Router::$PROTOCOLS[ $this->protocol ] ?? 'Both';
    }
}
