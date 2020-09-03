<?php

namespace IXP\Models;

use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use stdClass;

/**
 * IXP\Models\MacAddress
 *
 * @property int $id
 * @property int|null $virtualinterfaceid
 * @property string|null $firstseen
 * @property string|null $lastseen
 * @property string|null $mac
 * @method static Builder|MacAddress newModelQuery()
 * @method static Builder|MacAddress newQuery()
 * @method static Builder|MacAddress query()
 * @method static Builder|MacAddress whereFirstseen($value)
 * @method static Builder|MacAddress whereId($value)
 * @method static Builder|MacAddress whereLastseen($value)
 * @method static Builder|MacAddress whereMac($value)
 * @method static Builder|MacAddress whereVirtualinterfaceid($value)
 * @mixin \Eloquent
 */
class MacAddress extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'macaddress';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Gets a listing of mac addresses or a single one if an ID is provided
     *
     * @param stdClass $feParams
     * @param int|null $id
     *
     * @return array
     */
    public static function getFeList( stdClass $feParams, int $id = null ): array
    {
        return self::selectRaw( "m.*,
            vi.id AS viid,
            c.id AS customerid, c.abbreviatedName AS customer,
            s.name AS switchname, 
            GROUP_CONCAT( sp.name ) AS switchport,
            GROUP_CONCAT( DISTINCT ipv4.address ) AS ip4,
            GROUP_CONCAT( DISTINCT ipv6.address ) AS ip6,
            COALESCE( o.organisation, 'Unknown' ) AS organisation"
        )
        ->from( 'macaddress AS m' )
        ->join( 'virtualinterface AS vi', 'vi.id', 'm.virtualinterfaceid' )
        ->join( 'vlaninterface AS vli', 'vli.virtualinterfaceid', 'vi.id' )
        ->leftjoin( 'ipv4address AS ipv4', 'ipv4.id', 'vli.ipv4addressid' )
        ->leftjoin( 'ipv6address AS ipv6', 'ipv4.id', 'vli.ipv6addressid' )
        ->join( 'cust AS c', 'c.id', 'vi.custid' )
        ->leftjoin( 'physicalinterface AS pi', 'pi.virtualinterfaceid', 'vi.id' )
        ->leftjoin( 'switchport AS sp', 'sp.id', 'pi.switchportid' )
        ->leftjoin( 'switch AS s', 's.id', 'sp.switchid' )
        ->leftjoin( 'oui AS o', 'o.oui', '=', DB::raw("SUBSTRING( m.mac, 1, 6 )") )
        ->when( $id , function( Builder $q, $id ) {
            return $q->where('id', $id );
        } )->groupBy( 'm.mac', 'vi.id', 'm.id', 'm.firstseen', 'm.lastseen',
                'c.id', 'c.abbreviatedName', 's.name', 'o.organisation'
            )
        ->when( $feParams->listOrderBy , function( Builder $q, $orderby ) use ( $feParams )  {
            return $q->orderBy( $orderby, $feParams->listOrderByDir ?? 'ASC');
        })->get()->toArray();
    }
}