<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use IXP\Services\Grapher\Graph;

/**
 * IXP\Models\TrafficDailyPhysInt
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
 * @property-read \IXP\Models\PhysicalInterface $physicalInterface
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereDayAvgIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereDayAvgOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereDayMaxIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereDayMaxInAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereDayMaxOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereDayMaxOutAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereDayTotIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereDayTotOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereMonthAvgIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereMonthAvgOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereMonthMaxIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereMonthMaxInAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereMonthMaxOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereMonthMaxOutAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereMonthTotIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereMonthTotOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt wherePhysicalinterfaceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereWeekAvgIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereWeekAvgOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereWeekMaxIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereWeekMaxInAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereWeekMaxOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereWeekMaxOutAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereWeekTotIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereWeekTotOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereYearAvgIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereYearAvgOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereYearMaxIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereYearMaxInAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereYearMaxOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereYearMaxOutAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereYearTotIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDailyPhysInt whereYearTotOut($value)
 * @mixin \Eloquent
 */
class TrafficDailyPhysInt extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'traffic_daily_phys_ints';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the physical interface that own the traffic daily phys int
     */
    public function physicalInterface(): BelongsTo
    {
        return $this->belongsTo(PhysicalInterface::class, 'physicalinterface_id');
    }

    /**
     * Get days for which stats are available
     *
     * @return array Array of strings of format yyyy-mm-dd
     */
    public static function availableForDays(): array
    {
        return Arr::flatten( self::select( [ 'day' ] )
            ->distinct( 'day' )
            ->orderBy( 'day')->get()->toArray() );
    }

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
                        SUM( pi.speed ) AS vi_speed,
                        ROUND( GREATEST( (MAX( tdpi.{$period}_max_in )/1000000/MAX( pi.speed ))*100, (MAX( tdpi.{$period}_max_out )/1000000/MAX( pi.speed ))*100 ), 2) AS util"
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
