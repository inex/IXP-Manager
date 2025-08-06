<?php

namespace IXP\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbUpdateLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbUpdateLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbUpdateLog query()
 * @property int $id
 * @property int $cust_id
 * @property \Illuminate\Support\Carbon|null $prefix_v4
 * @property \Illuminate\Support\Carbon|null $prefix_v6
 * @property \Illuminate\Support\Carbon|null $asn_v4
 * @property \Illuminate\Support\Carbon|null $asn_v6
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbUpdateLog whereAsnV4($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbUpdateLog whereAsnV6($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbUpdateLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbUpdateLog whereCustId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbUpdateLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbUpdateLog wherePrefixV4($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbUpdateLog wherePrefixV6($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbUpdateLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class IrrdbUpdateLog extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cust_id',
        'prefix_v4',
        'prefix_v6',
        'asn_v4',
        'asn_v6',
    ];


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'prefix_v4' => 'datetime',
            'prefix_v6' => 'datetime',
            'asn_v4'    => 'datetime',
            'asn_v6'    => 'datetime',
        ];
    }

    /**
     * Find the /oldest/ /relevant/ last update:
     *
     * - oldest: will be null if never updated
     * - relevant: if customer is not v6 enabled, ignore these fields
     *
     * @param Customer $c
     * @return Carbon
     */
    public static function lastUpdatedMax( Customer $c ): ?Carbon
    {
        /** @var IrrdbUpdateLog $log */
        $log = self::firstWhere('cust_id', $c->id);

        if( !$log || !$c->irrdbFiltered() ) {
            return null;
        }

        $oldest = null;

        if( $c->isIPvXEnabled(4) ) {
            if( !$oldest && $log->prefix_v4 ) {
                $oldest = $log->prefix_v4;
            } else if( $oldest && $oldest > $log->prefix_v4 ) {
                $oldest = $log->prefix_v4;
            }

            if( !$oldest && $log->asn_v4 ) {
                $oldest = $log->asn_v4;
            } else if( $oldest && $oldest > $log->asn_v4 ) {
                $oldest = $log->asn_v4;
            }
        }

        if( $c->isIPvXEnabled(6) ) {
            if( !$oldest && $log->prefix_v6 ) {
                $oldest = $log->prefix_v6;
            } else if( $oldest && $oldest > $log->prefix_v6 ) {
                $oldest = $log->prefix_v6;
            }

            if( !$oldest && $log->asn_v6 ) {
                $oldest = $log->asn_v6;
            } else if( $oldest && $oldest > $log->asn_v6 ) {
                $oldest = $log->asn_v6;
            }
        }

        return $oldest;
    }

}
