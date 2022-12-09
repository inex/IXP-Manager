<?php

namespace IXP\Models\Aggregators;

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

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use IXP\Models\Customer;
use IXP\Models\RouteServerFilter;
use IXP\Models\RouteServerFilterProd;

/**
 * IXP\Models\Aggregators\RouteServerFilterAggregator
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
 * @property-read Customer|null $customer
 * @property-read Customer|null $peer
 * @property-read \IXP\Models\Vlan|null $vlan
 * @method static Builder|RouteServerFilterAggregator newModelQuery()
 * @method static Builder|RouteServerFilterAggregator newQuery()
 * @method static Builder|RouteServerFilterAggregator query()
 * @method static Builder|RouteServerFilterAggregator whereActionAdvertise($value)
 * @method static Builder|RouteServerFilterAggregator whereActionReceive($value)
 * @method static Builder|RouteServerFilterAggregator whereAdvertisedPrefix($value)
 * @method static Builder|RouteServerFilterAggregator whereCreatedAt($value)
 * @method static Builder|RouteServerFilterAggregator whereCustomerId($value)
 * @method static Builder|RouteServerFilterAggregator whereEnabled($value)
 * @method static Builder|RouteServerFilterAggregator whereId($value)
 * @method static Builder|RouteServerFilterAggregator whereLive($value)
 * @method static Builder|RouteServerFilterAggregator whereOrderBy($value)
 * @method static Builder|RouteServerFilterAggregator wherePeerId($value)
 * @method static Builder|RouteServerFilterAggregator whereProtocol($value)
 * @method static Builder|RouteServerFilterAggregator whereReceivedPrefix($value)
 * @method static Builder|RouteServerFilterAggregator whereUpdatedAt($value)
 * @method static Builder|RouteServerFilterAggregator whereVlanId($value)
 * @mixin \Eloquent
 */
class RouteServerFilterAggregator extends RouteServerFilter
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'route_server_filters';


    /**
     * Most functions will need the set of user and production filters
     *
     * @return Collection[] [ ufilters, pfilters ]
     */
    private static function filters( Customer $c ): array
    {
        return [
            RouteServerFilter::whereCustomerId($c->id)->orderBy('order_by')->get(),
            RouteServerFilterProd::whereCustomerId($c->id)->orderBy('order_by')->get()
        ];
    }

    /**
     * Check if the production filters are in sync with the user's editable filters
     * @param Customer $c
     * @return bool
     */
    public static function inSync( Customer $c ): bool
    {
        [ $ufilters, $pfilters ] = self::filters($c);

        // both empty -> not in sync
        if( !$ufilters->isEmpty() && $pfilters->isEmpty() ) {
            return false;
        }

        // unequal number of rules -> not in sync
        if( $ufilters->count() !== $pfilters->count() ) {
            return false;
        }

        // we're sorting by order so rules should be equal on a rule by rule basis:
        foreach( $ufilters as $id => $uf ) {
            foreach( $uf->getAttributes() as $a => $v ) {
                if( in_array( $a, [ 'id', 'created_at', 'updated_at', 'live' ] ) ) {
                    continue; // skip these attributes
                }

                if( $pfilters[$id]->$a !== $v ) {
                    return false;
                }
            }
        }

        return true;
    }


    /**
     * Revert - reverse sync where we move production to user's rules
     */
    public static function revert( Customer $c ): void
    {
        [ , $pfilters ] = self::filters($c);

        DB::transaction(function () use($pfilters, $c) {
            // start by clearing out the user's filters
            RouteServerFilter::whereCustomerId( $c->id )->delete();

            // pfilters empty -> all done
            if( !$pfilters->isEmpty() ) {
                // unequal number of rules -> copy pfilters to ufilters
                foreach( $pfilters as $pf ) {
                    RouteServerFilter::forceCreate( array_merge(
                        $pf->getAttributes(),
                        [ 'id' => null ]
                    ) );
                }
            }
        });
    }


    /**
     * Commit - sync staged / user's rules to production
     */
    public static function commit( Customer $c ): void
    {
        [ $ufilters ,  ] = self::filters($c);

        DB::transaction(function () use($ufilters, $c) {
            // start by clearing out the production
            RouteServerFilterProd::whereCustomerId( $c->id )->delete();

            // ufilters empty -> all done
            if( !$ufilters->isEmpty() ) {
                // unequal number of rules -> copy ufilters to pfilters
                foreach( $ufilters as $pf ) {
                    RouteServerFilterProd::forceCreate( array_merge(
                        $pf->getAttributes(),
                        [ 'id' => null ]
                    ) );
                }
            }
        });
    }


}