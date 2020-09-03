<?php

namespace IXP\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * IXP\Models\TrafficDaily
 *
 * @property int $id
 * @property int $cust_id
 * @property string|null $day
 * @property string|null $category
 * @property int|null $day_avg_in
 * @property int|null $day_avg_out
 * @property int|null $day_max_in
 * @property int|null $day_max_out
 * @property int|null $day_tot_in
 * @property int|null $day_tot_out
 * @property int|null $week_avg_in
 * @property int|null $week_avg_out
 * @property int|null $week_max_in
 * @property int|null $week_max_out
 * @property int|null $week_tot_in
 * @property int|null $week_tot_out
 * @property int|null $month_avg_in
 * @property int|null $month_avg_out
 * @property int|null $month_max_in
 * @property int|null $month_max_out
 * @property int|null $month_tot_in
 * @property int|null $month_tot_out
 * @property int|null $year_avg_in
 * @property int|null $year_avg_out
 * @property int|null $year_max_in
 * @property int|null $year_max_out
 * @property int|null $year_tot_in
 * @property int|null $year_tot_out
 * @property-read \IXP\Models\Customer $customer
 * @method static Builder|TrafficDaily newModelQuery()
 * @method static Builder|TrafficDaily newQuery()
 * @method static Builder|TrafficDaily query()
 * @method static Builder|TrafficDaily whereCategory($value)
 * @method static Builder|TrafficDaily whereCustId($value)
 * @method static Builder|TrafficDaily whereDay($value)
 * @method static Builder|TrafficDaily whereDayAvgIn($value)
 * @method static Builder|TrafficDaily whereDayAvgOut($value)
 * @method static Builder|TrafficDaily whereDayMaxIn($value)
 * @method static Builder|TrafficDaily whereDayMaxOut($value)
 * @method static Builder|TrafficDaily whereDayTotIn($value)
 * @method static Builder|TrafficDaily whereDayTotOut($value)
 * @method static Builder|TrafficDaily whereId($value)
 * @method static Builder|TrafficDaily whereMonthAvgIn($value)
 * @method static Builder|TrafficDaily whereMonthAvgOut($value)
 * @method static Builder|TrafficDaily whereMonthMaxIn($value)
 * @method static Builder|TrafficDaily whereMonthMaxOut($value)
 * @method static Builder|TrafficDaily whereMonthTotIn($value)
 * @method static Builder|TrafficDaily whereMonthTotOut($value)
 * @method static Builder|TrafficDaily whereWeekAvgIn($value)
 * @method static Builder|TrafficDaily whereWeekAvgOut($value)
 * @method static Builder|TrafficDaily whereWeekMaxIn($value)
 * @method static Builder|TrafficDaily whereWeekMaxOut($value)
 * @method static Builder|TrafficDaily whereWeekTotIn($value)
 * @method static Builder|TrafficDaily whereWeekTotOut($value)
 * @method static Builder|TrafficDaily whereYearAvgIn($value)
 * @method static Builder|TrafficDaily whereYearAvgOut($value)
 * @method static Builder|TrafficDaily whereYearMaxIn($value)
 * @method static Builder|TrafficDaily whereYearMaxOut($value)
 * @method static Builder|TrafficDaily whereYearTotIn($value)
 * @method static Builder|TrafficDaily whereYearTotOut($value)
 * @mixin \Eloquent
 */
class TrafficDaily extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'traffic_daily';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the customer that own the traffic daily
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'cust_id');
    }

    /**
     * Delete all entries for a given day
     *
     * @param Carbon $day The day to delete all entries for
     *
     * @return void
     */
    public static function deleteForDay( Carbon $day  )
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
    public static function deleteBefore( Carbon $day  )
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
     *        [0] => array(28) {
     *          ["day"] => object(DateTime)#286 (3) {
     *              ....
     *          }
     *          ["category"] => string(4) "bits"
     *          ["day_avg_in"] => string(8) "32732583"
     *          ...
     *          ["year_tot_out"] => string(16) "1430530473953106"
     *          ["id"] => string(6) "156931"
     *          ["Customer"] => array(31) {
     *            ["name"] => string(10) "A Name"
     *            ["type"] => int(1)
     *            ...
     *            ["id"] => int(4)
     *          }
     *        }
     *        [1] => array(28) {
     *          ["day"] => object(DateTime)#292 (3) {
     *              ...
     *          }
     *          ["category"] => string(4) "bits"
     *          ...
     *        }
     *      }
     *
     * @see \IXP_Mrtg::$CATEGORIES
     *
     * @param Carbon $day The day to load records for
     * @param string $category The category of records to load (one of \IXP_Mrtg::$CATEGORIES)
     *
     * @return array An array of all switch objects
     */
    public static function loadTraffic( Carbon $day, string $category )
    {
        return self::select( [ 'td.*', 'c.*'] )
            ->from( 'traffic_daily AS td' )
            ->leftJoin( 'cust AS c', 'c.id', 'td.cust_id')
            ->where( 'td.day', $day->format( 'Y-m-d' ) )
            ->where( 'td.category', $category )
            ->get()->toArray();
    }
}
