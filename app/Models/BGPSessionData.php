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
 * @method static \Illuminate\Database\Eloquent\Builder|BGPSessionData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BGPSessionData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BGPSessionData query()
 * @method static \Illuminate\Database\Eloquent\Builder|BGPSessionData whereDstipaddressid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BGPSessionData whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BGPSessionData wherePacketcount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BGPSessionData whereProtocol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BGPSessionData whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BGPSessionData whereSrcipaddressid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BGPSessionData whereTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BGPSessionData whereVlan($value)
 * @mixin \Eloquent
 */
class BGPSessionData extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bgpsessiondata';
}
