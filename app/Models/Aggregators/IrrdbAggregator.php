<?php

namespace IXP\Models\Aggregators;

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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use IXP\Models\Customer;
use IXP\Models\IPv4Address;
use IXP\Models\IPv6Address;
use IXP\Models\IrrdbAsn;
use IXP\Models\IrrdbPrefix;
use IXP\Models\Vlan;

class IrrdbAggregator
{
    /**
     * Utility function to get the prefixes/ASN a customer has for a given protocol
     *
     * Returns an array of associative arrays containing keys 'id' (database ID) and 'prefix'.
     *
     * If `$flatten` is true, returns a flat array or prefixes indexed by database ID.
     *
     * @param int           $custid     The customer entity
     * @param int           $protocol   The IP protocol (4/6)
     * @param string        $type
     * @param boolean       $flatten    Return a flat array indexed by database ID with value being the prefix string
     *
     * @return array The number of prefixes found
     */
    public static function forCustomerAndProtocol( int $custid, int $protocol, string $type, bool $flatten = false ): array
    {
        if( $type === 'asn' ) {
            $model = IrrdbAsn::class; /** @var IrrdbAsn  $model */
            $field = 'asn';
            $orderby = $field . ' ASC, id ASC';
        } else {
            $model = IrrdbPrefix::class; /** @var IrrdbPrefix  $model */
            $field = 'prefix';
            $orderby = 'INET' . ( $protocol === 6 ? '6' : '' ) . "_ATON( {$field} ) ASC, id ASC";
        }

        $results = $model::select( [ 'id', $field , 'first_seen', 'last_seen' ] )
            ->where( 'customer_id', $custid )
            ->where( 'protocol', $protocol )
            ->orderByRaw( $orderby )
            ->get();

        if( !$flatten ) {
            return $results->toArray();
        }

        return $results->keyBy( 'id' )->pluck( $field, 'id'  )->toArray();
    }

    /**
     * Utility function to get the prefixes/ASN a customer has for a given protocol
     * for the purpose of generating router configuration
     *
     * Returns an array of prefixes.
     *
     * @param int|Customer  $cust       The customer entity | id
     * @param int           $protocol   The IP protocol (4/6)
     * @param bool          $resetCache If true, delete and reseed the cache
     *
     * @return array The prefixes found
     */
    public static function prefixesForRouterConfiguration( int|Customer $cust, int $protocol, bool $resetCache = false ): array
    {
        if( is_int( $cust ) ) {
            $cust = Customer::find( $cust );
        }

        if( $resetCache ) {
            Cache::store('file')->forget( 'irrdb:prefix:ipv' . $protocol . ':' . $cust->asMacro( $protocol ) );
        }

        // Pull these out of the cache if possible, otherwise the database.
        return Cache::store('file')->rememberForever( 'irrdb:prefix:ipv' . $protocol . ':' . $cust->asMacro( $protocol ), function() use ($cust,$protocol) {
            return IrrdbPrefix::select('prefix')
                ->where( 'customer_id', $cust->id )
                ->where('protocol', $protocol )
                ->orderByRaw( 'INET' . ( $protocol === 6 ? '6' : '' ) . '_ATON( prefix ) ASC' )
                ->orderBy( 'id', 'ASC' )
                ->pluck('prefix')
                ->toArray();
        });
    }


    /**
     * Utility function to get the prefixes/ASN a customer has for a given protocol
     * for the purpose of generating router configuration
     *
     * Returns an array of prefixes.
     *
     * @param int|Customer  $cust       The customer entity | id
     * @param int           $protocol   The IP protocol (4/6)
     * @param bool          $resetCache If true, delete and reseed the cache
     *
     * @return array The prefixes found
     */
    public static function asnsForRouterConfiguration( int|Customer $cust, int $protocol, bool $resetCache = false ): array
    {
        if( is_int( $cust ) ) {
            $cust = Customer::find( $cust );
        }

        if( $resetCache ) {
            Cache::store('file')->forget( 'irrdb:asn:ipv' . $protocol . ':' . $cust->asMacro( $protocol ) );
        }

        // Pull these out of the cache if possible, otherwise the database.
        return Cache::store('file')->rememberForever( 'irrdb:asn:ipv' . $protocol . ':' . $cust->asMacro( $protocol ), function() use ($cust,$protocol) {
            return IrrdbAsn::select('asn')
                ->where( 'customer_id', $cust->id )
                ->where('protocol', $protocol )
                ->orderBy( 'asn', 'ASC' )
                ->orderBy( 'id', 'ASC' )
                ->pluck('asn')
                ->toArray();
        });
    }
}