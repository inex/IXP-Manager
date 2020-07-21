<?php

namespace IXP\Models;

use DB;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use stdClass;

/**
 * IXP\Models\Layer2Address
 *
 * @property int $id
 * @property int $vlan_interface_id
 * @property string|null $mac
 * @property string|null $firstseen
 * @property string|null $lastseen
 * @property string|null $created
 * @property-read \IXP\Models\VlanInterface $vlanInterface
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Layer2Address newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Layer2Address newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Layer2Address query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Layer2Address whereCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Layer2Address whereFirstseen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Layer2Address whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Layer2Address whereLastseen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Layer2Address whereMac($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Layer2Address whereVlanInterfaceId($value)
 * @mixin \Eloquent
 */
class Layer2Address extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'l2address';

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
        'vlan_interface_id',
        'mac',
        'firstseen',
        'lastseen',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating( function ( $model ) {
            $model->created = $model->freshTimestamp();
        });
    }

    /**
     * Get the vlan interface that holds the layer2address
     */
    public function vlanInterface(): BelongsTo
    {
        return $this->belongsTo(VlanInterface::class, 'vlan_interface_id');
    }

    /**
     * Get mac formatted with comma (xx:xx:xx:xx:xx:xx)
     *
     * @return string
     */
    public function getMacFormattedWithColons(): string
    {
        return wordwrap( $this->mac, 2, ':',true);
    }

    /**
     * Gets a listing of layer2addresses or a single one if an ID is provided
     *
     * @param stdClass $feParams
     * @param int|null $id
     *
     * @return array
     */
    public static function getFeList( stdClass $feParams, int $id = null ): array
    {
        return self::selectRaw( "l.*,
            vi.id AS viid,
            c.id AS customerid, c.abbreviatedName AS customer,
            s.name AS switchname,
            vl.name as vlan, vl.id as vlanid, 
            vli.id as vliid,
            GROUP_CONCAT( sp.name ) AS switchport,
            GROUP_CONCAT( DISTINCT ipv4.address ) AS ip4,
            GROUP_CONCAT( DISTINCT ipv6.address ) AS ip6,
            COALESCE( o.organisation, 'Unknown' ) AS organisation"
        )
            ->from( 'l2address AS l' )
            ->join( 'vlaninterface AS vli', 'vli.id', 'l.vlan_interface_id' )
            ->join( 'vlan AS vl', 'vl.id', 'vli.vlanid' )
            ->leftjoin( 'ipv4address AS ipv4', 'ipv4.id', 'vli.ipv4addressid' )
            ->leftjoin( 'ipv6address AS ipv6', 'ipv6.id', 'vli.ipv6addressid' )
            ->join( 'virtualinterface AS vi', 'vi.id', 'vli.virtualinterfaceid' )
            ->join( 'cust AS c', 'c.id', 'vi.custid' )
            ->leftjoin( 'physicalinterface AS pi', 'pi.virtualinterfaceid', 'vi.id' )
            ->leftjoin( 'switchport AS sp', 'sp.id', 'pi.switchportid' )
            ->leftjoin( 'switch AS s', 's.id', 'sp.switchid' )
            ->leftjoin( 'oui AS o', 'o.oui', '=', DB::raw("SUBSTRING( l.mac, 1, 6 )") )
            ->when( $id , function( Builder $q, $id ) {
                return $q->where('l.id', $id );
            } )->groupBy( 'l.mac', 'vi.id', 'l.id', 'l.firstseen',
                'l.lastseen', 'c.id', 'c.abbreviatedName', 's.name',
                'vl.name', 'vl.id', 'vli.id', 'o.organisation'
            )
            ->when( $feParams->listOrderBy , function( Builder $q, $orderby ) use ( $feParams )  {
                return $q->orderBy( $orderby, $feParams->listOrderByDir ?? 'ASC');
            })->get()->toArray();
    }

    /**
     * Get layer2address for a given vlan
     *
     * @param  string $mac The MAC address to search for
     * @param  int $vlanid The ID of the VLAN to search
     *
     * @return Collection
     */
    public static function getForVlan( string $mac, int $vlanid ): Collection
    {
        return self::from( 'l2address AS l' )
            ->join( 'vlaninterface AS vli', 'vli.id',  'l.vlan_interface_id' )
            ->join( 'vlan AS v', 'v.id', 'vli.vlanid' )
            ->where( 'mac' , $mac )
            ->where( 'v.id', $vlanid )
            ->get();
    }
}