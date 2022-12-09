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

use App, Cache;

use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\View\View;

use IXP\Models\{Aggregators\VirtualInterfaceAggregator,
    Cabinet,
    Customer,
    Infrastructure,
    Location,
    PhysicalInterface,
    Vlan,
    VlanInterface};

use IXP\Services\Grapher\Graph;

use IXP\Services\Grapher;

/**
 * Admin Controller
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class AdminController extends Controller
{
    /**
     * Display the home page
     *
     * @param Request $r
     *
     * @return view
     */
    public function dashboard( Request $r ): View
    {
        return view( 'admin/dashboard' )->with([
            'stats'                 => $this->dashboardStats( $r ),
            'graphs'                => $this->publicPeeringGraphs( $r ),
            'graph_period'          => $r->query( 'graph_period', config( 'ixp_fe.admin_dashboard.default_graph_period' ) ),
            'graph_periods'         => Graph::PERIOD_DESCS,
        ]);
    }

    /**
     * Get type counts statistics
     *
     * @param Request $r
     *
     * @return array array of statistics
     */
    private function dashboardStats( Request $r ): array
    {
        // only do this once every 60 minutes
        if( $r->query( 'refresh_cache', 0 ) || !( $cTypes = Cache::get( 'admin_ctypes' ) ) ) {
            // Full / Associate / Probono / Internal
            $cTypes[ 'types' ] = Customer::selectRaw('type AS ctype, COUNT( type ) AS cnt')
                ->whereRaw(Customer::SQL_CUST_CURRENT)
                ->whereRaw(Customer::SQL_CUST_ACTIVE)
                ->groupBy('ctype')->get()->keyBy('ctype')->toArray();;

            // Searches for VirtualInterfaces where custtype us not internal.
            // Because it's Virtual Interfaces, it should only be current or unremoved customers, etc.
            $vis = VirtualInterfaceAggregator::getByLocation();

            $speeds = [];
            $byLocation = [];
            $byLan = [];
            $byIxp = [];
            $custsByLocation = [];
            $custsByInfra = [];
            $peeringCusts = [];

            // for rate limited ports:
            $rateLimitedPorts = [];
            $pispeeds = PhysicalInterface::$SPEED;
            krsort( $pispeeds, SORT_NUMERIC );


            foreach( $vis as $vi ) {
                $location = $vi[ 'locationname' ];
                $cabinet  = $vi[ 'cabinetname' ];
                $infrastructure = $vi[ 'infrastructure' ];
                $custid = $vi[ 'customerid' ];

                if ( !isset($custsByLocation[ $location ])) {
                    $custsByLocation[ $location ] = [
                        'count' => 1,
                        'id' => $vi[ 'locationid' ],
                        'name' => $location,
                        'custs' => [ $custid ],
                        'cabinets' => [],
                    ];
                } elseif( !in_array( $custid, $custsByLocation[ $location ][ 'custs' ], true ) ){
                    $custsByLocation[ $location ][ 'count' ]++;
                    $custsByLocation[ $location ][ 'custs' ][] = $custid;
                }

                if ( !isset( $custsByLocation[ $location ]['cabinets'][$cabinet] ) ) {
                    $custsByLocation[ $location ]['cabinets'][ $cabinet ] = [
                        'count' => 1,
                        'id' => $vi[ 'cabinetid' ],
                        'name' => $cabinet,
                        'custs' => [ $custid ]
                    ];
                } elseif( !in_array( $custid, $custsByLocation[ $location ]['cabinets'][$cabinet][ 'custs' ], true ) ){
                    $custsByLocation[ $location ]['cabinets'][$cabinet][ 'count' ]++;
                    $custsByLocation[ $location ]['cabinets'][$cabinet][ 'custs' ][] = $custid;
                }

                // Speeds have gotten more complex now that we've add rate limiters, sigh.
                // We're not going to go around the houses here to solve odd services - speeds
                // should be a multiple of physical speeds.
                $speed    = $vi[ 'speed' ];
                $numports = 1;

                if( $vi[ 'rlspeed' ] ) {
                    foreach( array_keys( $pispeeds ) as $kspeed ) {
                        if( $vi[ 'rlspeed' ] >= $kspeed ) {
                            $speed = $kspeed;
                            $numports = round( $vi[ 'rlspeed' ] / $kspeed );
                            $rateLimitedPorts[] = [ 'physint' => $vi['speed'], 'numports' => $numports, 'rlspeed' => $speed ];
                            break;
                        }
                    }
                }

                if ( !isset($speeds[ $speed ])) {
                    $speeds[ $speed ] = $numports;
                } else {
                    $speeds[ $speed ] += $numports;
                }

                if ( !isset($custsByInfra[ $infrastructure ])) {
                    $custsByInfra[ $infrastructure ] = [];
                }
                if ( !in_array($vi[ 'customerid' ], $custsByInfra[ $infrastructure ], true)) {
                    $custsByInfra[ $infrastructure ][] = $vi[ 'customerid' ];
                }

                if ( !in_array($vi[ 'customerid' ], $peeringCusts, true)) {
                    $peeringCusts[] = $vi[ 'customerid' ];
                }

                if ( !isset($byLocation[ $location ])) {
                    $byLocation[ $location ] = [
                        'id' => $vi[ 'locationid' ],
                        'cabinets' => [],
                    ];
                }
                if ( !isset($byLocation[ $location ]['cabinets'][ $cabinet ] )) {
                    $byLocation[ $location ]['cabinets'][ $cabinet ] = [ 'id' => $vi[ 'cabinetid' ]  ];
                }

                if ( !isset($byLocation[ $vi[ 'locationname' ] ][ $speed ])) {
                    $byLocation[ $location ][ $speed ] = $numports;
                } else {
                    $byLocation[ $location ][ $speed ] += $numports;
                }

                if ( !isset($byLocation[ $location ]['cabinets'][ $cabinet ][ $vi[ 'speed' ] ])) {
                    $byLocation[ $location ]['cabinets'][ $cabinet ][ $vi[ 'speed' ] ] = 1;
                } else {
                    $byLocation[ $location ]['cabinets'][ $cabinet ][ $vi[ 'speed' ] ]++;
                }

                if ( !isset( $byLan[ $infrastructure ] ) ) {
                    $byLan[ $infrastructure ] = [ 'id' => $vi[ 'infrastructureid' ] ];
                }

                if ( !isset( $byLan[ $infrastructure ][ $speed ] ) ) {
                    $byLan[ $infrastructure ][ $speed ] = $numports;
                } else {
                    $byLan[ $infrastructure ][ $speed ] += $numports;
                }
            }

            ksort($speeds, SORT_NUMERIC);

            usort($custsByLocation, function ($a, $b) {
                return $a[ 'count' ] <=> $b[ 'count' ];
            });

            $cTypes[ 'speeds' ]             = $speeds;
            $cTypes[ 'custsByLocation' ]    = $custsByLocation;
            $cTypes[ 'byLocation' ]         = $byLocation;
            $cTypes[ 'byLan' ]              = $byLan;
            $cTypes[ 'byIxp' ]              = $byIxp;
            $cTypes[ 'custsByInfra' ]       = $custsByInfra;
            $cTypes[ 'peeringCusts' ]       = $peeringCusts;
            $cTypes[ 'rateLimitedPorts' ]   = $rateLimitedPorts;

            // FROM of query is vlaninterface so should be current:
            $cTypes[ 'usage' ] = VlanInterface::selectRaw(
            'v.id AS vlanid,
                        v.name AS vlanname,
                        COUNT(vli.id) AS overall_count, 
                        SUM(vli.rsclient = 1) AS rsclient_count,
                        SUM(vli.ipv6enabled = 1) AS ipv6_count' )
                ->from('vlaninterface AS vli')
                ->Join('virtualinterface AS vi', 'vi.id', 'vli.virtualinterfaceid')
                ->Join('cust AS c', 'c.id', 'vi.custid')
                ->Join('vlan AS v', 'v.id', 'vli.vlanid')
                ->where('v.private', false)
                ->whereIn('c.type', [1, 4])
                ->groupBy('vlanname')->get()->toArray();

            // full/probono customers with connected interface by vlan
            $cTypes[ 'percentByVlan' ]  = VirtualInterfaceAggregator::getPercentageCustomersByVlan();
            $cTypes[ 'cached_at' ]      = Carbon::now();
            $cTypes[ 'infras' ]         = Infrastructure::orderBy('name' )->get()->toArray();
            $cTypes[ 'cabinets' ]       = Cabinet::orderBy('name' )->get()->toArray();
            $cTypes[ 'locations' ]      = Location::orderBy('name' )->get()->toArray();
            $cTypes[ 'vlans' ]          = Vlan::publicOnly()->orderBy('number')->get()->keyBy('id')->toArray();

            Cache::put('admin_ctypes', $cTypes, 300);
        }

        return $cTypes;
    }

    /**
     * Get public peering graphs
     *
     * @param Request $r
     *
     * @return array array of graphs
     *
     * @throws
     */
    private function publicPeeringGraphs( Request $r ): array
    {
        $grapher = App::make( Grapher::class );

        $period   = Graph::processParameterPeriod( $r->query( 'graph_period', config( 'ixp_fe.admin_dashboard.default_graph_period' ) ) );

        if( $r->query( 'refresh_cache', 0 ) || !( $graphs = Cache::get( 'admin_stats_'.$period ) ) ) {
            $graphs = [];

            $graphs['ixp'] = $grapher->ixp()
                ->setType(     Graph::TYPE_PNG )
                ->setProtocol( Graph::PROTOCOL_ALL )
                ->setPeriod(   $period )
                ->setCategory( Graph::CATEGORY_BITS );

            foreach( Infrastructure::all() as $inf ) {
                $graphs[ $inf->id ] = $grapher->infrastructure( $inf )
                    ->setType(     Graph::TYPE_PNG )
                    ->setProtocol( Graph::PROTOCOL_ALL )
                    ->setPeriod(   $period )
                    ->setCategory( Graph::CATEGORY_BITS );
            }

            Cache::put( 'admin_stats_'. $period, $graphs, 300 );
        }
        return $graphs;
    }
}