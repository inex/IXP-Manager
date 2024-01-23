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

use IXP\Traits\Observable;

/**
 * IXP\Models\RouteServerFilterProd
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
 * @method static \Illuminate\Database\Eloquent\Builder|RouteServerFilterProd newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RouteServerFilterProd newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RouteServerFilterProd query()
 * @method static \Illuminate\Database\Eloquent\Builder|RouteServerFilterProd whereActionAdvertise($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RouteServerFilterProd whereActionReceive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RouteServerFilterProd whereAdvertisedPrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RouteServerFilterProd whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RouteServerFilterProd whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RouteServerFilterProd whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RouteServerFilterProd whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RouteServerFilterProd whereLive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RouteServerFilterProd whereOrderBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RouteServerFilterProd wherePeerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RouteServerFilterProd whereProtocol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RouteServerFilterProd whereReceivedPrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RouteServerFilterProd whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RouteServerFilterProd whereVlanId($value)
 * @mixin \Eloquent
 */
class RouteServerFilterProd extends RouteServerFilter
{
    ////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////
    ///
    /// SHADOW CLASS -> cosider making changes to RouteServerFilter first!
    ///
    ////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'route_server_filters_prod';

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
            "Route Server Filter (Production) [id:%d] belonging to %s [id:%d] '%s' and Peer [id:%d] '%s'",
            $model->id,
            ucfirst( config( 'ixp_fe.lang.customer.one' ) ),
            $model->customer_id,
            $model->customer->name ?? Customer::find( $model->customer_id )?->name,
            $model->peer_id,
            $model->peer?->name,
        );
    }
}
