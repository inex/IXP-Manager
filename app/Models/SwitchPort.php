<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\PatchPanelPort[] $patchPanelPorts
 * @property-read int|null $patch_panel_ports_count
 */
class SwitchPort extends Model
{

    const TYPE_UNSET          = 0;
    const TYPE_PEERING        = 1;
    const TYPE_MONITOR        = 2;
    const TYPE_CORE           = 3;
    const TYPE_OTHER          = 4;
    const TYPE_MANAGEMENT     = 5;

    /**
     * For resellers, we need to enforce the one port - one mac - one address rule
     * on the peering LAN. Depending on switch technology, this will be done using
     * a virtual ethernet port or a dedcaited fanout switch. A fanout port is a port
     * that sends a resold member's traffic to a peering port / switch.
     *
     * I.e. it is an output port to the exchange to connects to a standard peering
     * input port.
     *
     * @var int
     */
    const TYPE_FANOUT         = 6;

    /**
     * For resellers, we need an uplink port(s) through which they deliver reseller
     * connections.
     *
     * @var int
     */
    const TYPE_RESELLER       = 7;

    public static $TYPES = array(
        self::TYPE_UNSET      => 'Unset / Unknown',
        self::TYPE_PEERING    => 'Peering',
        self::TYPE_MONITOR    => 'Monitor',
        self::TYPE_CORE       => 'Core',
        self::TYPE_OTHER      => 'Other',
        self::TYPE_MANAGEMENT => 'Management',
        self::TYPE_FANOUT     => 'Fanout',
        self::TYPE_RESELLER   => 'Reseller'
    );

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

    /**
     * Get the patch panel ports for this switch port
     */
    public function patchPanelPorts(): HasMany
    {
        return $this->hasMany(PatchPanelPort::class, 'switch_port_id' );
    }

    /**
     * Returns all switch ports assigned to a physical interface for a switch.
     *
     * @param $switchid $id Switch ID - switch to query
     *
     * @return array
     */
    public static function getAllPortsAssignedToPIForSwitch( int $switchid ): array
    {
        return self::select( [
            'sp.id AS id', 'sp.name AS name', 'sp.type AS porttype',
            'pi.speed AS speed', 'pi.duplex AS duplex',
            'c.name AS custname'
        ] )
            ->from( 'switchport AS sp' )
            ->join( 'physicalinterface AS pi', 'pi.switchportid', 'sp.id' )
            ->join( 'virtualinterface AS vi', 'vi.id', 'pi.virtualinterfaceid' )
            ->join( 'cust AS c', 'c.id', 'vi.custid' )
            ->where( 'sp.switchid', $switchid )
            ->orderBy( 'id', 'ASC' )
            ->get()->keyBy( 'id' )->toArray();
    }

    /**
     * Returns all available switch ports for a switch.
     *
     * Restrict to only some types of switch port
     * Exclude switch port ids from the list
     *
     * Suitable for other generic use.
     *
     * @param int      $switchid        Switch ID - switch to query
     * @param array    $types           Switch port type restrict to some types only
     * @param array    $excludedSpid    Switch port IDs, if set, those ports are excluded from the results

     * @return array
     */
    public static function getAllPortsForSwitch( int $switchid, $types = [], $excludedSpid = [], bool $notAssignToPI = true ): array
    {
        return self::select( [
            'sp.id AS id', 'sp.name AS name', 'sp.type AS porttype'
        ] )
            ->from( 'switchport AS sp' )
            ->when( $notAssignToPI , function( Builder $q ) {
                return $q->leftJoin( 'physicalinterface AS pi', 'pi.switchportid', 'sp.id' )
                    ->where( 'pi.id', NULL);
            })
            ->when( count( $types ) > 0 , function( Builder $q ) use( $types ) {
                return $q->whereIn( 'sp.type', $types );
            })
            ->when( count( $excludedSpid ) > 0 , function( Builder $q ) use( $excludedSpid ) {
                return $q->whereNotIn( 'sp.id', $excludedSpid );
            })
            ->where( 'sp.switchid', $switchid )
            ->orderBy( 'id', 'ASC' )
            ->get()->keyBy( 'id' )->toArray();
    }
}
