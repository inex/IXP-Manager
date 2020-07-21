<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * IXP\Models\IPv6Address
 *
 * @property int $id
 * @property int|null $vlanid
 * @property string|null $address
 * @property-read \IXP\Models\Vlan|null $vlan
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\IPv6Address newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\IPv6Address newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\IPv6Address query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\IPv6Address whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\IPv6Address whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\IPv6Address whereVlanid($value)
 * @mixin \Eloquent
 */
class IPv6Address extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ipv6address';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the vlan that own the ipv6address
     */
    public function vlan(): BelongsTo
    {
        return $this->belongsTo(Vlan::class, 'vlanid' );
    }
}
