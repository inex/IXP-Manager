<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * IXP\Models\IPv4Address
 *
 * @property int $id
 * @property int|null $vlanid
 * @property string|null $address
 * @property-read \IXP\Models\Vlan|null $vlan
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\IPv4Address newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\IPv4Address newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\IPv4Address query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\IPv4Address whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\IPv4Address whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\IPv4Address whereVlanid($value)
 * @mixin \Eloquent
 */
class IPv4Address extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ipv4address';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the vlan that own the ipv4address
     */
    public function vlan(): BelongsTo
    {
        return $this->belongsTo(Vlan::class, 'vlanid' );
    }
}
