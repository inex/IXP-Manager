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
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDaily newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDaily newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDaily query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDaily whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDaily whereCustId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDaily whereDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDaily whereDayAvgIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDaily whereDayAvgOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDaily whereDayMaxIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDaily whereDayMaxOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDaily whereDayTotIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDaily whereDayTotOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDaily whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDaily whereMonthAvgIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDaily whereMonthAvgOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDaily whereMonthMaxIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDaily whereMonthMaxOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDaily whereMonthTotIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDaily whereMonthTotOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDaily whereWeekAvgIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDaily whereWeekAvgOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDaily whereWeekMaxIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDaily whereWeekMaxOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDaily whereWeekTotIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDaily whereWeekTotOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDaily whereYearAvgIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDaily whereYearAvgOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDaily whereYearMaxIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDaily whereYearMaxOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDaily whereYearTotIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDaily whereYearTotOut($value)
 * @mixin \Eloquent
 * @property int $ixp_id
 * @property-read \IXP\Models\Customer $customer
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\TrafficDaily whereIxpId($value)
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
    public static function loadTraffic( $day, $category )
    {
        return self::select( [ 'td.*', 'c.*'] )
            ->from( 'traffic_daily AS td' )
            ->leftJoin( 'cust AS c', 'c.id', 'td.cust_id')
            ->where( 'td.day', $day->format( 'Y-m-d' ) )
            ->where( 'td.category', $category )
            ->get()->toArray();
    }
}
