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
use Illuminate\Database\Eloquent\Model;

use IXP\Models\RsPrefix;


/**
 * IXP\Models\Aggregators\RsPrefixAggregator
 *
 * @property int $id
 * @property int|null $custid
 * @property string|null $timestamp
 * @property string|null $prefix
 * @property int|null $protocol
 * @property int|null $irrdb
 * @property int|null $rs_origin
 * @property-read \IXP\Models\Customer|null $customer
 * @method static Builder|RsPrefixAggregator newModelQuery()
 * @method static Builder|RsPrefixAggregator newQuery()
 * @method static Builder|RsPrefixAggregator query()
 * @method static Builder|RsPrefixAggregator whereCustid($value)
 * @method static Builder|RsPrefixAggregator whereId($value)
 * @method static Builder|RsPrefixAggregator whereIrrdb($value)
 * @method static Builder|RsPrefixAggregator wherePrefix($value)
 * @method static Builder|RsPrefixAggregator whereProtocol($value)
 * @method static Builder|RsPrefixAggregator whereRsOrigin($value)
 * @method static Builder|RsPrefixAggregator whereTimestamp($value)
 * @mixin \Eloquent
 */
class RsPrefixAggregator extends RsPrefix
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rs_prefixes';
    /**
     * Used to initialise customer array elements for @see getAggregateRouteSummaries()
     *
     * This isn't really necessary but it prevents a ton of isset() queries at the
     * presentation layer.
     *
     * @return array Initialised customer element array
     */
    private static function initialiseAggregateRouteSummariesArray(): array
    {
        $init = [
            'total' => 0
        ];

        foreach( RsPrefix::$SUMMARY_TYPES_FNS as $type => $fn )
        {
            $init[ $type ] = [
                4       => 0,
                6       => 0,
                'total' => 0
            ];
        }

        return $init;
    }

    /**
     * Return route acceptance counts for a specific customers as an aggregated array.
     *
     * A sample element of the array is (RS = Route Server):
     *
     *     [
     *         [total] => 10           // total routes of all types
     *         [adv_acc] => [          // routes advertised to the RS and accepted by the RS
     *             [4] => 6            // IPv4
     *             [6] => 2            // IPv6
     *             [total] => 8        // total
     *         ]
     *         [adv_nacc] => [         // routes advertised but not accepted (not in IRRDB)
     *             [4] => 0
     *             [6] => 1
     *             [total] => 1
     *         ]
     *         [nadv_acc] => [         // routes not advertised but that would be accepted
     *             [4] => 0
     *             [6] => 1
     *             [total] => 1
     *         ]
     *     ]
     *
     *
     * @return array Route acceptance counts for all customers as an aggregated array
     */
    public static function aggregateRouteSummariesForCustomer( $custid ): array
    {
        $summary = self::initialiseAggregateRouteSummariesArray();

        foreach( RsPrefix::$SUMMARY_TYPES_FNS as $type => $fn ) {
            foreach( [ 4, 6 ] as $protocol ) {
                if( $sum = self::$fn( $protocol, $custid ) ) {
                    $summary[ $type ][ $protocol ] = $sum[0]['prefixes'];
                    $summary[ $type ]['total'] += $sum[0]['prefixes'];
                    $summary[ 'total' ] += $sum[0]['prefixes'];
                }
            }
        }

        return $summary;
    }

    /**
     * Return route acceptance counts for all customers as an aggregated array.
     *
     * A sample element of the array is (RS = Route Server):
     *
     *     [64] => [                   // customer ID
     *         [total] => 10           // total routes of all types
     *         [adv_acc] => [          // routes advertised to the RS and accepted by the RS
     *             [4] => 6            // IPv4
     *             [6] => 2            // IPv6
     *             [total] => 8        // total
     *         ]
     *         [adv_nacc] => [         // routes advertised but not accepted (not in IRRDB)
     *             [4] => 0
     *             [6] => 1
     *             [total] => 1
     *         ]
     *         [nadv_acc] => [         // routes not advertised but that would be accepted
     *             [4] => 0
     *             [6] => 1
     *             [total] => 1
     *         ]
     *         [name] => Customer Name
     *     ]
     *
     *
     * @return array Route acceptance counts for all customers as an aggregated array
     */
    public static function aggregateRouteSummaries(): array
    {
        $summary = [];
        foreach( RsPrefix::$SUMMARY_TYPES_FNS as $type => $fn ) {
            foreach( [ 4, 6 ] as $protocol ) {
                foreach( self::$fn( $protocol ) as $route ) {
                    // initialise customer's summary array if necessary
                    if( !isset( $summary[ $route[ 'id' ] ] ) ) {
                        $summary[ $route['id'] ] = self::initialiseAggregateRouteSummariesArray();
                        $summary[ $route['id'] ][ 'name' ] = $route['name'];
                    }

                    $summary[ $route['id'] ][ $type ][ $protocol ] = $route['prefixes'];
                    $summary[ $route['id'] ][ $type ]['total']     += $route['prefixes'];
                    $summary[ $route['id'] ][ 'total' ]            += $route['prefixes'];
                }
            }
        }

        return $summary;
    }

    /**
     * Return categorised routes for a given customer as an aggregated array.
     *
     * A sample element of the array is (RS = Route Server):
     *
     *     [
     *         [adv_acc] => [                             // Routes advertised and accepted
     *             [0] => [
     *                 [id] => 64                         // Customer ID
     *                 [name] => ABC Limited              // Customer Name
     *                 [protocol] => 4                    // protocol (4,6)
     *                 [irrdb] => 1                       // 1 if the route is in IRRDB
     *                 [prefix] => 192.0.2.0/24           // prefix
     *                 [timestamp] => DateTime Object
     *                 [rsorigin] => 65500                // origin AS
     *             ]
     *             ...
     *         ]
     *         [adv_nacc] => [                            // Routes advertised but not accepted
     *             ...
     *         ]
     *         [nadv_acc] => [                            // Routes not advertised but acceptable
     *             ...
     *         ]
     *     ]
     *
     * @param int $cust The customer ID to return routes for
     * @param int|null protocol The (optional) protocol to limit results to (''4'', ''6'', ''NULL'')
     *
     * @return array Categorised routes for a given customer as an aggregated array.
     */
    public static function aggregateRoutes( int $cust, ?int $protocol = null ): array
    {
        $aggRoutes = [];

        foreach( RsPrefix::$ROUTES_TYPES_FNS as $type => $fn ){
            $aggRoutes[ $type ] = self::$fn( $protocol, $cust );
        }
        return $aggRoutes;
    }
    /**
     * Returns a count of all routes advertised to the route server and accepted by
     * it for all customers / a specific customer.
     *
     * @param int $protocol The protocol to count routes for (accepts ''4'' or ''6'')
     * @param int|null $cust The customer ID to limit the results for
     *
     * @return bool|array Count of all routes advertised to the route server and accepted by it
     */
    public static function summaryRoutesAdvertisedAndAccepted( $protocol, $cust = null )
    {
        return self::getSummaryRoutes( $protocol, 1, false, $cust );
    }

    /**
     * Returns a count of all routes not advertised to the route server but that would
     * be accepted by it for all customers / a specific customer.
     *
     * @param int $protocol The protocol to count routes for (accepts ''4'' or ''6'')
     * @param int|null $cust The customer ID to limit the results for
     *
     * @return bool|array Count of all routes not advertised to the route server but would be accepted by it
     */
    public static function summaryRoutesAdvertisedAndNotAccepted( int $protocol, int $cust = null )
    {
        return self::getSummaryRoutes( $protocol, 0, false, $cust );
    }

    /**
     * Returns a count of all routes advertised to the route server but not accepted by
     * it for all customers / a specific customer.
     *
     * @param int $protocol The protocol to count routes for (accepts ''4'' or ''6'')
     * @param int|null $cust The customer ID to limit the results for
     *
     * @return bool|array Count of all routes advertised to the route server but not accepted by it
     */
    public static function summaryRoutesNotAdvertisedButAcceptable( int $protocol, int $cust = null )
    {
        return self::getSummaryRoutes( $protocol, 1, true, $cust );
    }

    /**
     * Utility function used by the ''getSummaryRoutesXXX()'' function to query the database.
     *
     * The rules for routes are:
     *
     * * Advertised & Accepted: irrdb = 1 AND rs_origin IS NOT NULL
     * * Advertised & NOT Accepted: irrdb = 0 AND rs_origin IS NOT NULL
     * * Not Advertised & Acceptable: irrdb = 1 AND rs_origin IS NULL
     *
     * @param int $protocol The IP protocol to limit results to (accepts ''4'' or ''6'')
     * @param int $irrdb Limit results to ''irrdb = 1'' or ''irrdb = 0''
     * @param bool $rsOriginIsNull Limit results depending on whether the rs_origin is null or not
     * @param int|null $cust The customer ID to limit the results to
     *
     * @return bool|array The database query result (or false if none)
     */
    public static function getSummaryRoutes( int  $protocol, int $irrdb, bool $rsOriginIsNull, ?int $cust = null )
    {
        $result = self::selectRaw(
            'cust.id AS id, cust.name AS name,
            rs_prefixes.protocol AS protocol, rs_prefixes.irrdb AS irrdb,
            count( rs_prefixes.protocol ) AS prefixes'
        )
            ->from( 'rs_prefixes' )
            ->join( 'cust', 'cust.id', 'rs_prefixes.custid')
            ->where( 'rs_prefixes.rs_origin', $rsOriginIsNull ? '=' : '!=', null )
            ->where( 'rs_prefixes.irrdb', $irrdb )
            ->where( 'rs_prefixes.protocol', $protocol )
            ->when( $cust, function( Builder $q, $cust ) {
                return $q->where( 'cust.id', $cust );
            } )
        ->groupBy( 'cust.id' )
        ->orderByRaw( 'cust.name ASC, rs_prefixes.protocol ASC, rs_prefixes.irrdb ASC' )
        ->get();



        if( $cust !== null )
        {
            $result ? $result->first() : false;
        }

        return $result->isNotEmpty() ? $result->toArray() : false;
    }

    /**
     * Returns all routes advertised to the route server and accepted by
     * it for all customers / a specific customer.
     *
     * @param int|null $protocol The protocol to count routes for (accepts ''null'', ''4'' or ''6'')
     * @param int|null $cust The customer ID to limit the results for
     *
     * @return array All routes advertised to the route server and accepted by it
     */
    public static function routesAdvertisedAndAccepted( int $protocol = null, int $cust = null ): array
    {
        return self::getRoutes( 1, false, $protocol, $cust );
    }

    /**
     * Returns all routes not advertised to the route server but that would
     * be accepted by it for all customers / a specific customer.
     *
     * @param int|null $protocol The protocol to count routes for (accepts ''null'', ''4'' or ''6'')
     * @param int|null $cust The customer ID to limit the results for
     *
     * @return array All routes not advertised to the route server but would be accepted by it
     */
    public static function routesAdvertisedAndNotAccepted( $protocol = null, $cust = null ): array
    {
        return self::getRoutes( 0, false, $protocol, $cust );
    }

    /**
     * Returns all routes not advertised to the route server but that would
     * be accepted by it for all customers / a specific customer.
     *
     * @param int|null $protocol The protocol to count routes for (accepts ''null'', ''4'' or ''6'')
     * @param int|null $cust The customer ID to limit the results for
     *
     * @return array All routes not advertised to the route server but would be accepted by it
     */
    public static function routesNotAdvertisedButAcceptable( $protocol = null, $cust = null ): array
    {
        return self::getRoutes( 1, true, $protocol, $cust );
    }

    /**
     * Utility function used by the ''getRoutesXXX()'' function to query the database.
     *
     * The rules for routes are:
     *
     * * Advertised & Accepted: irrdb = 1 AND rs_origin IS NOT NULL
     * * Advertised & NOT Accepted: irrdb = 0 AND rs_origin IS NOT NULL
     * * Not Advertised & Acceptable: irrdb = 1 AND rs_origin IS NULL
     *
     * @param int       $irrdb Limit results to ''irrdb = 1'' or ''irrdb = 0''
     * @param bool      $rsOriginIsNull Limit results depending on whether the rs_origin is null or not
     * @param int|null  $protocol The IP protocol to limit results to (accepts ''null'', ''4'' or ''6'')
     * @param int|null  $cust The customer ID to limit the results to
     *
     * @return array The database query result
     */
    public static function getRoutes( int $irrdb, bool $rsOriginIsNull, int $protocol = null, int $cust = null ): array
    {
         return self::selectRaw(
            'cust.id AS id, cust.name AS name, rs_prefixes.protocol AS protocol,
                        rs_prefixes.irrdb AS irrdb, rs_prefixes.prefix AS prefix,
                        rs_prefixes.timestamp AS timestamp, rs_prefixes.rs_origin AS rsorigin'
        )
            ->leftJoin( 'cust', 'cust.id', 'rs_prefixes.custid')
            ->where( 'rs_prefixes.rs_origin', $rsOriginIsNull ? '=' : '!=', null )
            ->where( 'rs_prefixes.irrdb', $irrdb )
            ->when( $protocol, function( Builder $q, $protocol ) {
                return $q->where( 'rs_prefixes.protocol', $protocol );
            } )
            ->when( $cust, function( Builder $q, $cust ) {
                return $q->where( 'cust.id', $cust );
            } )
            ->orderByRaw( 'cust.name ASC, rs_prefixes.protocol ASC, rs_prefixes.irrdb ASC' )
            ->get()->toArray();
    }
}