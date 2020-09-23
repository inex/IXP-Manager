<?php

namespace IXP\Models\Aggregators;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use IXP\Models\RsPrefix;


/**
 * IXP\Models\Aggregators\RsPrefixAggregator
 *
 * @method static Builder|RsPrefixAggregator newModelQuery()
 * @method static Builder|RsPrefixAggregator newQuery()
 * @method static Builder|RsPrefixAggregator query()
 * @mixin \Eloquent
 */
class RsPrefixAggregator extends RsPrefix
{
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

        foreach( RsPrefix::$SUMMARY_TYPES_FNS as $type => $fn )
        {
            foreach( [ 4, 6 ] as $protocol )
            {
                if( $sum = self::$fn( $protocol, $custid ) )
                {
                    $summary[ $type ][ $protocol ] = $sum['prefixes'];
                    $summary[ $type ]['total'] += $sum['prefixes'];
                    $summary[ 'total' ] += $sum['prefixes'];

                }
            }
        }
        return $summary;
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
        ->orderBy( 'cust.id' )
        ->groupByRaw( 'cust.name ASC, rs_prefixes.protocol ASC, rs_prefixes.irrdb ASC' )
        ->get()->first();

        return $result ? $result->toArray() : false;
    }
}