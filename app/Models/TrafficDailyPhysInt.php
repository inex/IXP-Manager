<?php

namespace IXP\Models;

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

use Illuminate\Database\Eloquent\{
    Builder,
    Model,
    Relations\BelongsTo
};

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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\PhysicalInterface $physicalInterface
 * @method static Builder|TrafficDailyPhysInt newModelQuery()
 * @method static Builder|TrafficDailyPhysInt newQuery()
 * @method static Builder|TrafficDailyPhysInt query()
 * @method static Builder|TrafficDailyPhysInt whereCategory($value)
 * @method static Builder|TrafficDailyPhysInt whereCreatedAt($value)
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
 * @method static Builder|TrafficDailyPhysInt whereUpdatedAt($value)
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
     * Get the physical interface that own the traffic daily phys int
     */
    public function physicalInterface(): BelongsTo
    {
        return $this->belongsTo(PhysicalInterface::class, 'physicalinterface_id');
    }
}
