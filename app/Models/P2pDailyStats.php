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


use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use IXP\Traits\Observable;

/**
 * @property int $id
 * @property int $cust_id
 * @property string $day
 * @property string $peer_id
 * @property int|null $ipv4_total_in
 * @property int|null $ipv4_total_out
 * @property int|null $ipv6_total_in
 * @property int|null $ipv6_total_out
 * @property int|null $ipv4_max_in
 * @property int|null $ipv4_max_out
 * @property int|null $ipv6_max_in
 * @property int|null $ipv6_max_out
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats query()
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats whereCustId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats whereDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats whereIpv4MaxIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats whereIpv4MaxOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats whereIpv4TotalIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats whereIpv4TotalOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats whereIpv6MaxIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats whereIpv6MaxOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats whereIpv6TotalIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats whereIpv6TotalOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats wherePeerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats whereUpdatedAt($value)
 * @property-read \IXP\Models\Customer|null $peer
 * @mixin \Eloquent
 */
class P2pDailyStats extends Model
{
    use Observable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'p2p_daily_stats';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cust_id',
        'day',
        'peer_id',
        'ipv4_total_in',
        'ipv4_total_out',
        'ipv4_max_in',
        'ipv4_max_out',
        'ipv6_total_in',
        'ipv6_total_out',
        'ipv6_max_in',
        'ipv6_max_out',
    ];

    /**
     * Get the logo for the customer
     *
     * @psalm-return HasOne<Customer>
     */
    public function peer(): HasOne
    {
        return $this->hasOne(Customer::class, 'id', 'peer_id' );
    }

    /**
     * Accessor for total traffic
     */
    public function total_traffic(): int
    {
        return $this->ipv4_total_out + $this->ipv4_total_in + $this->ipv6_total_out + $this->ipv6_total_in;
    }



    /**
     * Get the total traffic a customer exchanges with its peers for the latest day in the database.
     *
     * Only peers with entries on the the most recent day for this customer will be included.
     *
     * @param Customer $c
     * @return array [ peerid => total_traffic, ... ]
     */
    public static function latestTotalTraffic( Customer $c ): array
    {
        // latest day for which we have results
        if( !( $day = self::whereCustId( $c->id )->max('day') ) ) {
            return [];
        }

        return self::select( DB::raw('peer_id, ipv6_total_out + ipv4_total_out + ipv6_total_in + ipv4_total_in as total_traffic') )
            ->where( 'cust_id', $c->id )->where( 'day', $day)
            ->get()->pluck('total_traffic', 'peer_id')->toArray();
    }

    /**
     * Get the latest n P2pDailyStats for this customer.
     *
     * @param Customer $c
     */
    public static function latestN( Customer $c, int $n = 5 ): Collection
    {
        // latest day for which we have results
        if( !( $day = self::whereCustId( $c->id )->max('day') ) ) {
            return new Collection();
        }

        return self::select( DB::raw( '*, ipv6_total_out + ipv4_total_out + ipv6_total_in + ipv4_total_in as total_traffic' ) )
            ->where( 'cust_id', $c->id )->where( 'day', $day)
            ->limit( $n )->orderBy( 'total_traffic', 'desc' )->get();
    }


}
