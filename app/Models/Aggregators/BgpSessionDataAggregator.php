<?php

namespace IXP\Models\Aggregators;

use Cache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use IXP\Models\BgpSessionData;
use IXP\Models\Vlan;
use IXP_Exception;

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
 * @method static Builder|BgpSessionDataAggregator newModelQuery()
 * @method static Builder|BgpSessionDataAggregator newQuery()
 * @method static Builder|BgpSessionDataAggregator query()
 * @method static Builder|BgpSessionDataAggregator whereDstipaddressid($value)
 * @method static Builder|BgpSessionDataAggregator whereId($value)
 * @method static Builder|BgpSessionDataAggregator wherePacketcount($value)
 * @method static Builder|BgpSessionDataAggregator whereProtocol($value)
 * @method static Builder|BgpSessionDataAggregator whereSource($value)
 * @method static Builder|BgpSessionDataAggregator whereSrcipaddressid($value)
 * @method static Builder|BgpSessionDataAggregator whereTimestamp($value)
 * @method static Builder|BgpSessionDataAggregator whereVlan($value)
 * @mixin \Eloquent
 */
class BgpSessionDataAggregator extends BgpSessionData
{

    /**
     * Get all the BGP peers of all peers
     *
     * This function is for generating the peering matrix based on data contained in the
     * `bgpsessiondata` table which is updated based on detected BGP sessions between
     * routers on the peering LAN(s) from sflow data.
     *
     * It returns an array of all BGP peers show their peers, such as:
     *
     *     array(57) {
     *         [42] => array(3) {
     *             ["shortname"] => string(10) "pchanycast"
     *             ["name"] => string(25) "Packet Clearing House DNS"
     *             ["peers"] => array(17) {
     *                   [2110] => string(4) "2110"
     *                   [2128] => string(4) "2128"
     *                   ...
     *             }
     *         }
     *         [112] => array(3) {
     *             ["shortname"] => string(5) "as112"
     *             ["name"] => string(17) "AS112 Reverse DNS"
     *             ["peers"] => array(20) {
     *                   [1213] => string(4) "1213"
     *                   [2110] => string(4) "2110"
     *                   ...
     *             }
     *         }
     *         ...
     *     }
     *
     * It also caches the results on a per VLAN, per protocol basis.
     *
     * @param int|null  $vlan           The VLAN ID of the peering LAN to query
     * @param int       $protocol       The IP protocol to query (4 or 6)
     * @param int|null  $asn            Optional ASN to limit the query to
     * @param bool      $forceDb        Set to true to ignore the cache and force the query to the database
     *
     * @return array            Array of peerings (as described above)
     *
     * @throws
     */
    public static function getPeers( ?int $vlan = null, int $protocol = 6, ?int $asn = null, bool $forceDb = false ): array
    {
        $key = "pm_sessions_{$vlan}_{$protocol}";

        if( !$forceDb && ( $apeers = Cache::get( $key ) ) ){
            return $apeers;
        }

        if( !in_array( $protocol, [ 4, 6 ] ) ){
            throw new IXP_Exception( 'Invalid protocol' );
        }

        if( $vlan !== null && !( $evlan = Vlan::find( $vlan ) ) )
            throw new IXP_Exception( 'Invalid VLAN' );

        // we've added "bs.timestamp >= NOW() - INTERVAL 7 DAY" below as we don't
        // dump old date (yet) and the time to run the query is O(n) on number
        // of rows...
        // also: CREATE INDEX idx_timestamp ON bgpsessiondata (timestamp)

        // need to construct a raw SQL here due to the schema design by NH
        $peers = self::selectRaw(
            'bs.*, srcip.*, dstip.*,
            vlis.virtualinterfaceid as visid, vlid.virtualinterfaceid as vidid,
            cs.shortname AS csshortname, cs.name AS csname, cs.autsys AS csautsys,
            cd.shortname AS cdshortname, cd.name AS cdname, cd.autsys AS cdautsys,
            vlan.id AS vlanid, vlan.name AS vlanname, vlan.number AS vlantag,
            COUNT( bs.packetcount ) AS packetcount'
        )->from( 'bgpsessiondata AS bs' )
            ->leftJoin( "ipv{$protocol}address AS srcip", 'srcip.id', 'bs.srcipaddressid'  )
            ->leftJoin( "ipv{$protocol}address AS dstip", 'dstip.id', 'bs.dstipaddressid' )
            ->leftJoin( 'vlaninterface AS vlis', "vlis.ipv{$protocol}addressid", 'srcip.id' )
            ->leftJoin( 'vlaninterface AS vlid', "vlid.ipv{$protocol}addressid", 'dstip.id' )
            ->leftJoin( 'virtualinterface AS vis', 'vis.id', 'vlis.virtualinterfaceid' )
            ->leftJoin( 'virtualinterface AS vid', 'vid.id', 'vlid.virtualinterfaceid' )
            ->leftJoin( 'cust AS cs', 'cs.id', 'vis.custid' )
            ->leftJoin( 'cust AS cd', 'cd.id', 'vid.custid' )
            ->leftJoin( 'vlan AS vlan', 'vlan.number', 'bs.vlan' )
            ->whereRaw( 'bs.timestamp >= NOW() - INTERVAL 7 DAY' )
            ->where( 'bs.protocol', $protocol )
            ->where( 'packetcount', '>=', 1 )
            ->when( $vlan !== null && $evlan, function( Builder $q ) use ( $evlan ) {
                return $q->where( 'vlan.id', $evlan->id );
            } )
            ->when( $asn !== null, function( Builder $q, $asn ) {
                return $q->where( 'cs.autsys', (int)$asn );
            } )
            ->groupBy( [ 'bs.srcipaddressid', 'bs.dstipaddressid', 'bs.id', 'vlis.virtualinterfaceid', 'vlid.virtualinterfaceid' ] )
        ->get()->toArray();


        $apeers = [];

        foreach( $peers as $p ) {
            if( !isset( $apeers[ $p['csautsys'] ] ) ) {
                $apeers[ $p[ 'csautsys' ] ] = [];
                $apeers[ $p[ 'csautsys' ] ][ 'shortname' ] = $p[ 'csshortname' ];
                $apeers[ $p[ 'csautsys' ] ][ 'name' ]      = $p[ 'csname' ];
                $apeers[ $p[ 'csautsys' ] ][ 'peers' ]     = [];
            }

            $apeers[ $p[ 'csautsys' ] ][ 'peers' ][ $p[ 'cdautsys' ] ] = $p[ 'cdautsys' ];
        }

        ksort( $apeers, SORT_NUMERIC );

        foreach( $apeers as $asn => $p ) {
            ksort( $apeers[ $asn ][ 'peers' ], SORT_NUMERIC );
        }

        Cache::put( $key, $apeers, 3600 );

        return $apeers;
    }
}
