<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $cust_id
 * @property string $day
 * @property string $peer_id
 * @property int|null $ipv4_total_in
 * @property int|null $ipv4_total_out
 * @property int|null $ipv6_total_in
 * @property int|null $ipv6_total_out
 * @property int|null $ipv4_max_in
 * @property int|null $ipv4_max_out
 * @property int|null $ipv6_max_in
 * @property int|null $ipv6_max_out
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats query()
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats whereCustId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats whereDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats whereIpv4MaxIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats whereIpv4MaxOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats whereIpv4TotalIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats whereIpv4TotalOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats whereIpv6MaxIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats whereIpv6MaxOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats whereIpv6TotalIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats whereIpv6TotalOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats wherePeerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|P2pDailyStats whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class P2pDailyStats extends Model
{
    use HasFactory;
}
