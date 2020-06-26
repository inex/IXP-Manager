<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * IXP\Models\Switcher
 *
 * @property int $id
 * @property int|null $cabinetid
 * @property int|null $vendorid
 * @property string|null $name
 * @property string|null $ipv4addr
 * @property string|null $ipv6addr
 * @property string|null $snmppasswd
 * @property int|null $infrastructure
 * @property string|null $model
 * @property int|null $active
 * @property string|null $notes
 * @property string|null $hostname
 * @property string|null $os
 * @property string|null $osDate
 * @property string|null $osVersion
 * @property string|null $serialNumber
 * @property string|null $lastPolled
 * @property int|null $mauSupported
 * @property int|null $asn
 * @property string|null $loopback_ip
 * @property string|null $loopback_name
 * @property string|null $mgmt_mac_address
 * @property int|null $snmp_engine_time
 * @property int|null $snmp_system_uptime
 * @property int|null $snmp_engine_boots
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Switcher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Switcher newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Switcher query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Switcher whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Switcher whereAsn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Switcher whereCabinetid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Switcher whereHostname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Switcher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Switcher whereInfrastructure($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Switcher whereIpv4addr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Switcher whereIpv6addr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Switcher whereLastPolled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Switcher whereLoopbackIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Switcher whereLoopbackName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Switcher whereMauSupported($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Switcher whereMgmtMacAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Switcher whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Switcher whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Switcher whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Switcher whereOs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Switcher whereOsDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Switcher whereOsVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Switcher whereSerialNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Switcher whereSnmpEngineBoots($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Switcher whereSnmpEngineTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Switcher whereSnmpSystemUptime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Switcher whereSnmppasswd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Switcher whereVendorid($value)
 * @mixin \Eloquent
 * @property-read \IXP\Models\Cabinet $cabinet
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\SwitchPort[] $switchPorts
 * @property-read int|null $switch_ports_count
 */
class Switcher extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'switch';

    /**
     * Get the infrastructure that own the switcher
     */
    public function infrastructure(): BelongsTo
    {
        return $this->belongsTo(Infrastructure::class );
    }

    /**
     * Get the cabinet that own the switcher
     */
    public function cabinet(): BelongsTo
    {
        return $this->belongsTo(Cabinet::class, 'cabinetid' );
    }

    /**
     * Get the switch ports for the switcher
     */
    public function switchPorts(): HasMany
    {
        return $this->hasMany(SwitchPort::class, 'switchid');
    }
}
