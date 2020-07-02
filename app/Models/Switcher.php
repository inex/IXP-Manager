<?php

namespace IXP\Models;

use Cache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Session;
use stdClass;

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
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\ConsoleServerConnection[] $consoleServerConnections
 * @property-read int|null $console_server_connections_count
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
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cabinetid',
        'vendorid',
        'name',
        'ipv4addr',
        'ipv6addr',
        'snmppasswd',
        'infrastructure',
        'model',
        'active',
        'notes',
        'hostname',
        'os',
        'osDate',
        'osVersion',
        'serialNumber',
        'lastPolled',
        'mauSupported',
        'asn',
        'loopback_ip',
        'loopback_name',
        'mgmt_mac_address',
        'snmp_engine_time',
        'snmp_system_uptime',
        'snmp_engine_boots',
    ];

    /**
     * The cache key for all switch objects
     *
     * @var string The cache key for all switch objects
     */
    const ALL_CACHE_KEY = 'ixp_switches';

    /**
     * Constants for the list mode dropdown in the swtiches list
     */
    const VIEW_MODE_DEFAULT     = 'view_mode_default';
    const VIEW_MODE_OS          = 'view_mode_os';
    const VIEW_MODE_L3          = 'view_mode_l3';

    /**
     * @var array Textual representations of the list mode
     */
    public static $VIEW_MODES = [
        self::VIEW_MODE_DEFAULT     => 'Default',
        self::VIEW_MODE_OS          => 'OS View',
        self::VIEW_MODE_L3          => 'L3 View',
    ];

    /**
     * Get the infrastructure that own the switcher
     */
    public function infrastructure(): BelongsTo
    {
        return $this->belongsTo(Infrastructure::class, 'infrastructure' );
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

    /**
     * Get the console server connections for the switcher
     */
    public function consoleServerConnections(): HasMany
    {
        return $this->hasMany(ConsoleServerConnection::class, 'switchid');
    }

    /**
     * Gets a listing of switches or a single one if an ID is provided
     *
     * @param stdClass $feParams
     * @param int|null $id
     * @param null $params
     *
     * @return array
     */
    public static function getFeList( stdClass $feParams, int $id = null, $params = null ): array
    {
        return self::select( [
            'switch.*',
            'i.name AS infrastructure',
            'v.id AS vendorid', 'v.name AS vendor',
            'c.id AS cabinetid', 'c.name AS cabinet'
        ] )
            ->leftJoin( 'infrastructure AS i', 'i.id', 'switch.infrastructure')
            ->leftJoin( 'cabinet AS c', 'c.id', 'switch.cabinetid')
            ->leftJoin( 'vendor AS v', 'v.id', 'switch.vendorid')
        ->when( $id , function( Builder $q, $id ) {
            return $q->where('switch.id', $id );
        } )->when( isset( $params[ 'params' ][ 'activeOnly' ] ) && $params[ 'params' ][ 'activeOnly' ] , function( Builder $q ) {
            return $q->where('switch.active', true );
        } )->when( $feParams->listOrderBy , function( Builder $q, $orderby ) use ( $feParams )  {
            return $q->orderBy( $orderby, $feParams->listOrderByDir ?? 'ASC');
        })->get()->toArray();
    }

    /**
     * Gets a listing of patch panel ports for a switch
     *
     * @return Collection
     */
    public function getPatchPanelPorts(): Collection
    {
        return self::select( 'ppp.*' )
            ->leftJoin( 'switchport AS sp', 'sp.switchid', 'switch.id' )
            ->leftJoin( 'patch_panel_port AS ppp', 'ppp.switch_port_id', 'sp.id' )
            ->where( 'switch.id', $this->id )
            ->where( 'ppp.id', '!=', NULL )
            ->groupBy( 'ppp.id' )
            ->get();
    }

    /**
     * Gets a listing of physical interfaces for a switch
     *
     * @return Collection
     */
    public function getPhysicalInterfaces(): Collection
    {
        return self::select( 'pi.*' )
            ->leftJoin( 'switchport AS sp', 'sp.switchid', 'switch.id' )
            ->leftJoin( 'physicalinterface AS pi', 'pi.switchportid', 'sp.id' )
            ->where( 'switch.id', $this->id )
            ->where( 'pi.id', '!=', NULL )
            ->get();
    }

    /**
     * Generate a deterministic caching key for given parameters
     *
     * @param bool $active If `true`, return only active switches
     *
     * @return string The generate caching key
     */
    public static function genCacheKey( $active ): string
    {
        $key = $active ? '-active' : '-all';
        return self::ALL_CACHE_KEY . $key;
    }

    /**
     * Return an array of all switch objects from the database with caching
     *
     * @param bool $active If `true`, return only active switches
     *
     * @return Collection
     */
    public static function getAndCache( bool $active = false ): Collection
    {
        return Cache::remember( self::genCacheKey( $active ) , 3600, function () use( $active )  {
            return self::when( $active , function( Builder $q ) {
                return $q->where( 'active', 1 );
            })->orderBy( 'name', 'ASC' )->get();
        });
    }

    /**
     * Clear the cache of all result sets
     *
     * @return void
     */
    public static function clearCacheAll(): void
    {
        foreach( [ true, false ] as $active ) {
            Session::remove( self::genCacheKey( $active ) );
        }
    }

    /**
     * Gets a listing of switcher as array
     *
     * @param bool $active
     *
     * @return array
     */
    public static function getListAsArray( bool $active = false ): array
    {
        return self::getAndCache( $active )->toArray();
    }

    /**
     * Return an array of all switch names where the array key is the switch id
     *
     * @param int|null      $infraid
     * @param int|null      $locationid
     * @param int|null      $speed

     * @return Collection
     */
    public static function getByLocationInfrastructureSpeed( int $infraid = null, int $locationid = null, int $speed = null ): Collection
    {
        return self::when( $locationid , function( Builder $q, $locationid ) {
                return $q->leftJoin( 'cabinet AS c', 'c.id', 'switch.cabinetid' )
                    ->where( 'c.locationid', $locationid);
            })
            ->when( $infraid , function( Builder $q, $infraid ) {
                return $q->whereIn( 's.infrastructure', $infraid );
            })
            ->when( $speed , function( Builder $q, $speed ) {
                return $q->leftjoin( 'switchport AS sp', 'sp.switchid', 'switch.id' )
                    ->leftjoin( 'physicalinterface AS pi', 'pi.switchportid','sp.id' )
                    ->leftjoin( 'virtualinterface AS vi', 'vi.id','pi.virtualinterfaceid' )
                    ->leftjoin( 'vlaninterface AS vli', 'vli.virtualinterfaceid','vi.id' )
                    ->leftjoin( 'ipv4address AS ipv4', 'ipv4.id', '=', 'vli.ipv4addressid' )
                    ->leftjoin( 'ipv6address AS ipv6', 'ipv4.id', '=', 'vli.ipv6addressid' )
                    ->where( 'pi.speed', $speed );
            })
            ->where( 'active', true )
            ->orderBy( 'name', 'ASC' )
            ->get();
    }

    /**
     * Return an array of configurations
     *
     * @param int   $switchid       Switcher id for filtering results
     * @param int   $infraid        Infrastructure id for filtering results
     * @param int   $facilityid     Facility id for filtering results
     * @param  $speed          Speed filtering results
     * @param int   $vlanid         Vlan id for filtering results
     * @param bool  $rsclient
     * @param bool  $ipv6enabled
     *
     * @return array
     */
    public static function getConfiguration( int $switchid = null, int $infraid = null, int $facilityid = null, $speed = null, int $vlanid = null, bool $rsclient = false, bool $ipv6enabled = false ): array
    {
        return self::selectRaw(
    's.name AS switchname, 
                s.id AS switchid,
                GROUP_CONCAT( sp.ifName ) AS ifName,
                GROUP_CONCAT( pi.speed )  AS speed,
                GROUP_CONCAT( pi.status ) AS portstatus,
                c.name AS customer, 
                c.id AS custid, 
                c.autsys AS asn,
                MAX( vli.rsclient    ) AS rsclient,
                MAX( vli.ipv4enabled ) AS ipv4enabled, 
                MAX( vli.ipv6enabled ) AS ipv6enabled, 
                v.name AS vlan,
                GROUP_CONCAT( DISTINCT ipv4.address ) AS ipv4address, 
                GROUP_CONCAT( DISTINCT ipv6.address ) AS ipv6address'
        )
            ->from( 'vlaninterface AS vli' )
            ->leftjoin( 'ipv4address AS ipv4', 'ipv4.id', 'vli.ipv4addressid' )
            ->leftjoin( 'ipv6address AS ipv6', 'ipv6.id', 'vli.ipv6addressid' )
            ->leftjoin( 'vlan AS v', 'v.id', '=', 'vli.vlanid' )
            ->leftjoin( 'virtualinterface AS vi', 'vi.id','vli.virtualinterfaceid' )
            ->leftjoin( 'cust AS c', 'c.id','vi.custid' )
            ->leftjoin( 'physicalinterface AS pi', 'pi.virtualinterfaceid','vi.id' )
            ->leftjoin( 'switchport AS sp', 'sp.id', 'pi.switchportid' )
            ->leftjoin( 'switch AS s', 's.id', 'sp.switchid' )
            ->leftjoin( 'cabinet AS cab', 'cab.id', 's.cabinetid' )
            ->whereRaw( Customer::SQL_CUST_CURRENT )
            ->when( $switchid , function( Builder $q, $switchid ) {
                return $q->where( 's.id', $switchid);
            })
            ->when( $infraid , function( Builder $q, $infraid ) {
                return $q->where( 's.infrastructure', $infraid );
            })
            ->when( $facilityid , function( Builder $q, $facilityid ) {
                return $q->where( 'cab.locationid', $facilityid );
            })
            ->when( $speed , function( Builder $q, $speed ) {
                return $q->where( 'pi.speed', $speed );
            })
            ->when( $vlanid , function( Builder $q, $vlanid ) {
                return $q->where( 'vli.vlanid', $vlanid );
            }, function ( $query) {
                return $query->where( 'v.private', false );
            })
            ->when( $rsclient , function( Builder $q ) {
                return $q->where( 'vli.rsclient', true );
            })
            ->when( $ipv6enabled , function( Builder $q ) {
                return $q->where( 'vli.ipv6enabled', true );
            })
            ->groupBy( 'customer', 'custid', 'asn', 'switchname', 'switchid', 'vlan' )
            ->orderBy( 'customer', 'ASC' )
            ->get()->toArray();
    }

}
