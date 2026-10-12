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

use Illuminate\Database\Eloquent\{
    Builder,
};

use IXP\Services\Grapher\Graph;

use \IXP\Models\TrafficDailyPhysInt;

/**
 * IXP\Models\Aggregators\TrafficDailyPhysIntAggregator
 *
 * @property int $id
 * @property int $physicalinterface_id
 * @property string|null $day
 * @property string|null $category
 * @property int|null $day_avg_in
 * @property int|null $day_avg_out
 * @property int|null $day_max_in
 * @property int|null $day_max_out
 * @property string|null $day_max_in_at
 * @property string|null $day_max_out_at
 * @property int|null $day_tot_in
 * @property int|null $day_tot_out
 * @property int|null $week_avg_in
 * @property int|null $week_avg_out
 * @property int|null $week_max_in
 * @property int|null $week_max_out
 * @property string|null $week_max_in_at
 * @property string|null $week_max_out_at
 * @property int|null $week_tot_in
 * @property int|null $week_tot_out
 * @property int|null $month_avg_in
 * @property int|null $month_avg_out
 * @property int|null $month_max_in
 * @property int|null $month_max_out
 * @property string|null $month_max_in_at
 * @property string|null $month_max_out_at
 * @property int|null $month_tot_in
 * @property int|null $month_tot_out
 * @property int|null $year_avg_in
 * @property int|null $year_avg_out
 * @property int|null $year_max_in
 * @property int|null $year_max_out
 * @property string|null $year_max_in_at
 * @property string|null $year_max_out_at
 * @property int|null $year_tot_in
 * @property int|null $year_tot_out
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\PhysicalInterface $physicalInterface
 * @method static Builder<static>|TrafficDailyPhysIntAggregator newModelQuery()
 * @method static Builder<static>|TrafficDailyPhysIntAggregator newQuery()
 * @method static Builder<static>|TrafficDailyPhysIntAggregator query()
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereCategory($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereCreatedAt($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereDay($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereDayAvgIn($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereDayAvgOut($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereDayMaxIn($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereDayMaxInAt($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereDayMaxOut($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereDayMaxOutAt($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereDayTotIn($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereDayTotOut($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereId($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereMonthAvgIn($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereMonthAvgOut($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereMonthMaxIn($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereMonthMaxInAt($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereMonthMaxOut($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereMonthMaxOutAt($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereMonthTotIn($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereMonthTotOut($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator wherePhysicalinterfaceId($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereUpdatedAt($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereWeekAvgIn($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereWeekAvgOut($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereWeekMaxIn($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereWeekMaxInAt($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereWeekMaxOut($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereWeekMaxOutAt($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereWeekTotIn($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereWeekTotOut($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereYearAvgIn($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereYearAvgOut($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereYearMaxIn($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereYearMaxInAt($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereYearMaxOut($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereYearMaxOutAt($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereYearTotIn($value)
 * @method static Builder<static>|TrafficDailyPhysIntAggregator whereYearTotOut($value)
 * @mixin \Eloquent
 */
class TrafficDailyPhysIntAggregator extends TrafficDailyPhysInt
{
    /**
     * Return an array of traffic data (joined with the customer record) for
     * a given day and category.
     *
     * For example:
     *
     *     array(55) {
     *          0 => array:8 [
     *              "cid" => 1,
     *              "cname" => "ABC Ltd"
     *              "vname" => "INEX LAN1"
     *              "viid" => 1,
     *              "switch" => "swi1-kcp1-1"
     *              "in" => "9929169056"
     *              "out" => "348408392"
     *              "num_ports_in_lag" => "5"
     *              "vi_speed" => "50000"
     *              "util" => "99.29"
     *              ],
     *          ...
     *      }
     *
     * @see \IXP_Mrtg::$CATEGORIES
     * @param string    $day        The day to load records for
     * @param string    $category   The category of records to load (one of \IXP_Mrtg::$CATEGORIES)
     * @param string    $period
     * @param int       $vid
     *
     * @return array An array of all switch objects
     */
    public static function loadTraffic( string $day, string $category, string $period, int $vid ): array
    {
        $period = Graph::processParameterPeriod( $period );

        return self::selectRaw(
            "c.id AS cid, c.abbreviatedName AS cname, ANY_VALUE( s.name ) as switch,
                        vi.id AS viid,
                        SUM( tdpi.{$period}_max_in ) AS max_in,
                        SUM( tdpi.{$period}_max_out ) AS max_out,
                        COUNT( pi.id ) AS num_ports_in_lag,
                        SUM( COALESCE( pi.rate_limit, pi.speed ) ) AS vi_speed,
                        ROUND( GREATEST( (MAX( tdpi.{$period}_max_in )/1000000/MAX( COALESCE( pi.rate_limit, pi.speed ) ))*100, (MAX( tdpi.{$period}_max_out )/1000000/MAX( COALESCE( pi.rate_limit, pi.speed ) ))*100 ), 2) AS util"
        )
            ->from( 'traffic_daily_phys_ints AS tdpi' )
            ->leftJoin( 'physicalinterface AS pi', 'pi.id', 'tdpi.physicalinterface_id' )
            ->leftJoin( 'virtualinterface AS vi', 'vi.id', 'pi.virtualinterfaceid' )
            ->leftJoin( 'cust AS c', 'c.id', 'vi.custid' )
            ->leftJoin( 'switchport AS sp', 'sp.id', 'pi.switchportid' )
            ->leftJoin( 'switch AS s', 's.id', 'sp.switchid' )
            ->when( $vid , function( Builder $q, $vid ) {
                return $q->leftJoin( 'vlaninterface AS vli', 'vli.virtualinterfaceid', 'vi.id' )
                    ->leftJoin( 'vlan AS v', 'v.id', 'vli.vlanid' )
                    ->where( 'v.id', $vid );
            } )
            ->where( 'tdpi.day', $day )
            ->where( 'tdpi.category', $category )
            ->groupBy( 'vi.id' )->get()->toArray();
    }
}
