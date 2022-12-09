<?php

namespace IXP\Http\Controllers;

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */
use Auth, Cache;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\{
    RedirectResponse
};

use Illuminate\View\View;

use Illuminate\Http\{
    Request
};

use IXP\Models\{
    BgpSession,
    Customer,
    Vlan
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * PeeringMatrixController Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PeeringMatrixController extends Controller
{
    /**
     * Display dashboard
     *
     * @param   Request $r
     *
     * @return  View|RedirectResponse
     *
     * @throws
     */
    public function index( Request $r ): View|RedirectResponse
    {
        if( config( 'ixp_fe.frontend.disabled.peering-matrix', false ) ) {
            AlertContainer::push( 'The peering matrix has been disabled.', Alert::DANGER );
            return redirect('');
        }

        if( !ixp_min_auth( config( 'ixp.peering-matrix.min-auth' ) ) ) {
            AlertContainer::push( 'You do not have the required privileges to access the peering matrix', Alert::DANGER );
            return redirect('');
        }

        $protos = [
            4 => 'IPv4',
            6 => 'IPv6'
        ];

        if( $r->vlan  !== null ) {
            if(  $vlan = Vlan::find( $r->vlan ) ) {
                $vl = $vlan->id;
                $r->session()->put( "peering-matrix-vlan", $vl );
            } else {
                $r->session()->remove( "peering-matrix-vlan" );
                $vl = false;
            }
        } else if( $r->session()->exists( "peering-matrix-vlan" ) ) {
            $vl = $r->session()->get( "peering-matrix-vlan" );
        } else {
            $vl = config( "identity.vlans.default" );
        }

        if( $r->proto  !== null ) {
            if( array_key_exists( $r->proto , $protos ) ) {
                $proto = $r->proto;
                $r->session()->put( "peering-matrix-proto", $proto );
            } else {
                $r->session()->remove( "peering-matrix-proto" );
                $proto = 4;
            }
        } else if( $r->session()->exists( "peering-matrix-proto" ) ) {
            $proto = $r->session()->get( "peering-matrix-proto" );
        } else {
            $proto = 4;
        }

        // Find all VLANs marked for inclusion in the peering matrices.
        $vlans = Vlan::select( [ 'id', 'name' ] )
            ->where( 'peering_matrix', 1 )
            ->orderBy( 'number' )->get()
            ->keyBy( 'id' )->toArray();

        if( !count( $vlans ) ) {
            AlertContainer::push( 'No VLANs have been enabled for the peering matrix. Please see <a href="'
                . 'https://github.com/inex/IXP-Manager/wiki/Peering-Matrix">these instructions</a>'
                . ' / contact our support team.', Alert::DANGER );
            return redirect( '');
        }


        if( !isset( $vlans[ $vl ] ) ){
            $vl = config( "identity.vlans.default", null );

            if( !isset( $vlans[ $vl ] ) ) {
                AlertContainer::push( 'There is no default VLAN set for the peering matrix. Please '
                    . 'set <code>IDENTITY_DEFAULT_VLAN</code> in your <code>.env</code> file to a valid DB ID '
                    . 'of the VLAN you would like the peering matrix to show by default.', Alert::DANGER );

                return redirect( '');
            }
        }

        $restrictActivePeeringMatrix = true;
        if( Auth::check() && Auth::getUser()->isSuperUser() ){
            $restrictActivePeeringMatrix = false;
        }

        //Return all active, trafficking and external customers on a given VLAN for a given protocol
        //(indexed by ASN)
        $cust = Vlan::select( [ 'cust.autsys', 'cust.name', 'cust.shortname', 'vli.rsclient', 'cust.activepeeringmatrix', 'cust.id' ] )
            ->leftJoin( 'vlaninterface AS vli', 'vli.vlanid', 'vlan.id' )
            ->leftJoin( 'virtualinterface AS vi', 'vi.id', 'vli.virtualinterfaceid' )
            ->leftJoin( 'cust', 'cust.id', 'vi.custid' )
            ->whereRaw( Customer::SQL_CUST_CURRENT )->whereRaw( Customer::SQL_CUST_TRAFFICING )
            ->whereRaw( Customer::SQL_CUST_EXTERNAL )->where( 'vlan.id', $vl )
            ->where( "vli.ipv{$proto}enabled", 1 )->orderBy( 'cust.autsys' )
            ->when( $restrictActivePeeringMatrix, function( Builder $q ) {
                return $q->where( 'cust.activepeeringmatrix', 1 );
            } )
            ->get()->keyBy( 'autsys' )->toArray();

        $asns = array_keys( $cust );

        return view( 'peering-matrix/index' )->with([
            'sessions'                      => $this->getPeers( $vl, $proto, $restrictActivePeeringMatrix ),
            'custs'                         => $cust,
            'vlans'                         => $vlans,
            'protos'                        => $protos,
            'proto'                         => $proto,
            'vl'                            => $vl,
            'asnStringFormat'               => count($asns) > 0 ? "% " . strlen( $asns[ count( $asns ) - 1 ] ) . "s" : "% 0s",
        ]);
    }

    /**
     * Get all the BGP peers of all peers
     *
     * This function is for generating the peering matrix based on data contained in the
     * `bgpsession` table which is updated based on detected BGP sessions between
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
     * @param  int      $vlan  The VLAN ID of the peering LAN to query
     * @param  int      $protocol  The IP protocol to query (4 or 6)
     * @param  bool     $restrictActivePeeringMatrix
     *
     * @return array Array of peerings (as described above)
     *
     */
    private function getPeers( int $vlan, int $protocol = 6, bool $restrictActivePeeringMatrix = true ): array
    {
        $key = "pm_sessions_{$vlan}_{$protocol}";

        if( $apeers = Cache::get( $key ) ) {
            return $apeers;
        }

        // the number of days back we look is not a perfect science. generally, the bigger the interface / more
        // traffic, the less likely we'll find bgp sessions recently via sflow sampling.
        // 28 days seems good in practice but his can be changed in config/ixp.php
        $lookback_days = (int)config( 'ixp.peering-matrix.lookback_days' );
        if( !is_int( $lookback_days ) || $lookback_days < 1 ) {
            $lookback_days = 30;
        }

        // we've added "bs.timestamp >= NOW() - INTERVAL 7 DAY" below as we don't
        // dump old date (yet) and the time to run the query is O(n) on number
        // of rows...
        $peers = BgpSession::selectRaw(
            'cs.shortname AS csshortname, 
                        cs.name AS csname, 
                        cs.autsys AS csautsys,
                        cs.activepeeringmatrix AS csactivepeeringmatrix,
                        cd.autsys AS cdautsys'
            )->from( 'bgp_sessions AS bs' )
            ->leftJoin( "ipv{$protocol}address AS srcip", 'srcip.id', 'bs.srcipaddressid' )
            ->leftJoin( "ipv{$protocol}address AS dstip", 'dstip.id', 'bs.dstipaddressid' )
            ->leftJoin( 'vlaninterface AS vlis', "vlis.ipv{$protocol}addressid", 'srcip.id' )
            ->leftJoin( 'vlaninterface AS vlid', "vlid.ipv{$protocol}addressid", 'dstip.id' )
            ->leftJoin( 'virtualinterface AS vis', 'vis.id', 'vlis.virtualinterfaceid' )
            ->leftJoin( 'virtualinterface AS vid', 'vid.id', 'vlid.virtualinterfaceid' )
            ->leftJoin( 'cust AS cs', 'cs.id', 'vis.custid' )
            ->leftJoin( 'cust AS cd', 'cd.id', 'vid.custid' )
            ->leftJoin( 'vlan', 'vlan.id', 'srcip.vlanid' )
            ->whereRaw( 'bs.last_seen >= NOW() - INTERVAL ' . $lookback_days . ' DAY' )
            ->where( 'bs.protocol', $protocol )
            ->where( 'bs.packetcount', '>=', 1 )
            ->where( 'vlan.id', $vlan )
            ->when( $restrictActivePeeringMatrix, function( Builder $q ) {
                return $q->where( 'cs.activepeeringmatrix', 1 )
                        ->where( 'cd.activepeeringmatrix', 1);
            } )
            ->groupByRaw( 'bs.srcipaddressid, bs.dstipaddressid, bs.id, vlis.virtualinterfaceid, vlid.virtualinterfaceid' )
            ->orderBy( 'csautsys' )->get()->toArray();

        $apeers = [];

        foreach( $peers as $p ) {
            if( !isset( $apeers[ $p['csautsys'] ] ) ) {
                $apeers[ $p['csautsys'] ] = [];
                $apeers[ $p['csautsys'] ]['shortname']           = $p['csshortname'];
                $apeers[ $p['csautsys'] ]['name']                = $p['csname'];
                $apeers[ $p['csautsys'] ]['activepeeringmatrix'] = $p['csactivepeeringmatrix'];
                $apeers[ $p['csautsys'] ]['peers']               = [];
            }
            $apeers[ $p['csautsys'] ]['peers'][ $p['cdautsys'] ] = $p['cdautsys'];
        }

        Cache::put( $key, $apeers, 3600 );
        return $apeers;
    }
}