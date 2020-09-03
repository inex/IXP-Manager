<?php

namespace IXP\Models;

use Carbon\Carbon;
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
 * @method static Builder|TrafficDailyPhysInt newModelQuery()
 * @method static Builder|TrafficDailyPhysInt newQuery()
 * @method static Builder|TrafficDailyPhysInt query()
 * @method static Builder|TrafficDailyPhysInt whereCategory($value)
 * @method static Builder|TrafficDailyPhysInt whereDay($value)
 * @method static Builder|TrafficDailyPhysInt whereDayAvgIn($value)
 * @method static Builder|TrafficDailyPhysInt whereDayAvgOut($value)
 * @method static Builder|TrafficDailyPhysInt whereDayMaxIn($value)
 * @method static Builder|TrafficDailyPhysInt whereDayMaxInAt($value)
 * @method static Builder|TrafficDailyPhysInt whereDayMaxOut($value)
 * @method static Builder|TrafficDailyPhysInt whereDayMaxOutAt($value)
 * @method static Builder|TrafficDailyPhysInt whereDayTotIn($value)
 * @method static Builder|TrafficDailyPhysInt whereDayTotOut($value)
 * @method static Builder|TrafficDailyPhysInt whereId($value)
 * @method static Builder|TrafficDailyPhysInt whereMonthAvgIn($value)
 * @method static Builder|TrafficDailyPhysInt whereMonthAvgOut($value)
 * @method static Builder|TrafficDailyPhysInt whereMonthMaxIn($value)
 * @method static Builder|TrafficDailyPhysInt whereMonthMaxInAt($value)
 * @method static Builder|TrafficDailyPhysInt whereMonthMaxOut($value)
 * @method static Builder|TrafficDailyPhysInt whereMonthMaxOutAt($value)
 * @method static Builder|TrafficDailyPhysInt whereMonthTotIn($value)
 * @method static Builder|TrafficDailyPhysInt whereMonthTotOut($value)
 * @method static Builder|TrafficDailyPhysInt wherePhysicalinterfaceId($value)
 * @method static Builder|TrafficDailyPhysInt whereWeekAvgIn($value)
 * @method static Builder|TrafficDailyPhysInt whereWeekAvgOut($value)
 * @method static Builder|TrafficDailyPhysInt whereWeekMaxIn($value)
 * @method static Builder|TrafficDailyPhysInt whereWeekMaxInAt($value)
 * @method static Builder|TrafficDailyPhysInt whereWeekMaxOut($value)
 * @method static Builder|TrafficDailyPhysInt whereWeekMaxOutAt($value)
 * @method static Builder|TrafficDailyPhysInt whereWeekTotIn($value)
 * @method static Builder|TrafficDailyPhysInt whereWeekTotOut($value)
 * @method static Builder|TrafficDailyPhysInt whereYearAvgIn($value)
 * @method static Builder|TrafficDailyPhysInt whereYearAvgOut($value)
 * @method static Builder|TrafficDailyPhysInt whereYearMaxIn($value)
 * @method static Builder|TrafficDailyPhysInt whereYearMaxInAt($value)
 * @method static Builder|TrafficDailyPhysInt whereYearMaxOut($value)
 * @method static Builder|TrafficDailyPhysInt whereYearMaxOutAt($value)
 * @method static Builder|TrafficDailyPhysInt whereYearTotIn($value)
 * @method static Builder|TrafficDailyPhysInt whereYearTotOut($value)
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
     * Delete all entries for a given day
     *
     * @param Carbon $day The day to delete all entries for
     *
     * @return void
     */
    public static function deleteForDay( Carbon $day )
    {
        return self::where( 'day', $day->format('Y-m-d') )->delete();
    }

    /**
     * Delete all entries before a given day
     *
     * @param Carbon $day The day to delete all entries before
     *
     * @return void
     */
    public static function deleteBefore( Carbon $day )
    {
        return self::where( 'day', '<', $day->format('Y-m-d') )->delete();
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
