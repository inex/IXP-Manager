<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbUpdateLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbUpdateLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbUpdateLog query()
 * @property int $id
 * @property int $cust_id
 * @property string|null $prefix_v4
 * @property string|null $prefix_v6
 * @property string|null $asn_v4
 * @property string|null $asn_v6
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

}
