<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * IXP\Models\SwitchPort
 *
 * @property int $id
 * @property int|null $switchid
 * @property int|null $type
 * @property string|null $name
 * @property string|null $ifName
 * @property string|null $ifAlias
 * @property int|null $ifHighSpeed
 * @property int|null $ifMtu
 * @property string|null $ifPhysAddress
 * @property int|null $ifAdminStatus
 * @property int|null $ifOperStatus
 * @property int|null $ifLastChange
 * @property string|null $lastSnmpPoll
 * @property int|null $ifIndex
 * @property int $active
 * @property int|null $lagIfIndex
 * @property string|null $mauType
 * @property string|null $mauState
 * @property string|null $mauAvailability
 * @property string|null $mauJacktype
 * @property int|null $mauAutoNegSupported
 * @property int|null $mauAutoNegAdminState
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\SwitchPort newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\SwitchPort newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\SwitchPort query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\SwitchPort whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\SwitchPort whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\SwitchPort whereIfAdminStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\SwitchPort whereIfAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\SwitchPort whereIfHighSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\SwitchPort whereIfIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\SwitchPort whereIfLastChange($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\SwitchPort whereIfMtu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\SwitchPort whereIfName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\SwitchPort whereIfOperStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\SwitchPort whereIfPhysAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\SwitchPort whereLagIfIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\SwitchPort whereLastSnmpPoll($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\SwitchPort whereMauAutoNegAdminState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\SwitchPort whereMauAutoNegSupported($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\SwitchPort whereMauAvailability($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\SwitchPort whereMauJacktype($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\SwitchPort whereMauState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\SwitchPort whereMauType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\SwitchPort whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\SwitchPort whereSwitchid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\SwitchPort whereType($value)
 * @mixin \Eloquent
 * @property-read \IXP\Models\Switcher|null $switcher
 */
class SwitchPort extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'switchport';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the switcher that own the switch port
     */
    public function switcher(): BelongsTo
    {
        return $this->belongsTo(Switcher::class, 'switchid' );
    }
}
