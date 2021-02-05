<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * IXP\Models\BGPSession
 *
 * @property int $id
 * @property int $srcipaddressid
 * @property int $protocol
 * @property int $dstipaddressid
 * @property int $packetcount
 * @property string $last_seen
 * @property string|null $source
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSession query()
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSession whereDstipaddressid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSession whereLastSeen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSession wherePacketcount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSession whereProtocol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSession whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSession whereSrcipaddressid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSession whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BgpSession extends Model
{
    //
}
