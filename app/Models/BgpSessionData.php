<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * IXP\Models\BGPSessionData
 *
 * @property int $id
 * @property int|null $srcipaddressid
 * @property int|null $dstipaddressid
 * @property int|null $protocol
 * @property int|null $vlan
 * @property int|null $packetcount
 * @property string|null $timestamp
 * @property string|null $source
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSessionData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSessionData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSessionData query()
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSessionData whereDstipaddressid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSessionData whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSessionData wherePacketcount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSessionData whereProtocol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSessionData whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSessionData whereSrcipaddressid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSessionData whereTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSessionData whereVlan($value)
 * @mixin \Eloquent
 */
class BgpSessionData extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bgpsessiondata';
}
