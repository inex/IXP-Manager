<?php

namespace IXP\Http\Controllers;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use App, Cache, D2EM;

use Carbon\Carbon;

use IXP\Services\Grapher\Graph as Graph;

use Entities\{
    Customer            as CustomerEntity,
    Infrastructure      as InfrastructureEntity,
    IXP                 as IXPEntity,
    Location            as LocationEntity,
    VirtualInterface    as VirtualInterfaceEntity,
    Vlan                as VlanEntity,
    VlanInterface       as VlanInterfaceEntity
};

use Illuminate\Http\Request;
use Illuminate\View\View;


/**
 * Admin Controller
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 *
 * @category   Admin
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class AdminController extends Controller
{
    /**
     * Display the home page
     *
     * @param Request $request
     *
     * @return view
     */
    public function dashboard( Request $request ): View
    {
        return view( 'admin/dashboard' )->with([
            'stats'                 => $this->dashboardStats( $request ),
            'graphs'                => $this->publicPeeringGraphs( $request ),
            'graph_period'          => $request->query( 'graph_period', config( 'ixp_fe.admin_dashboard.default_graph_period' ) ),
            'graph_periods'         => Graph::PERIOD_DESCS,
        ]);
    }


    /**
     * Get type counts statistics
     *
     * @param Request $request
     *
     * @return array array of statistics
     */
    private function dashboardStats( Request $request )
    {
        // only do this once every 60 minutes
        if( $request->query( 'refresh_cache', 0 ) || !( $cTypes = Cache::get( 'admin_ctypes' ) ) ) {

            // Full / Associate / Probono / Internal
            $cTypes['types'] = D2EM::getRepository( CustomerEntity::class )->getTypeCounts();

            // Searches for VirtualInterfaces where custtype us not internal.
            // Because it's Virtual Interfaces, it should only be current or unremoved customers, etc.
            $vis = D2EM::getRepository( VirtualInterfaceEntity::class )->getByLocation();

            $speeds          = [];
            $byLocation      = [];
            $byLan           = [];
            $byIxp           = [];
            $custsByLocation = [];
            $custsByInfra    = [];
            $peeringCusts    = [];

            foreach( $vis as $vi ) {

                $location       = $vi['locationname'];
                $infrastructure = $vi['infrastructure'];

                // ----

                if( !isset( $custsByLocation[ $location ] ) ) {
                    $custsByLocation[ $location ] = 1;
                } else {
                    $custsByLocation[ $location ]++;
                }

                // ----

                if( !isset( $speeds[ $vi['speed'] ] ) ) {
                    $speeds[ $vi[ 'speed' ] ] = 1;
                } else {
                    $speeds[ $vi[ 'speed' ] ]++;
                }

                // ----

                if( !isset( $custsByInfra[ $infrastructure ] ) ) {
                    $custsByInfra[ $infrastructure ] = [];
                }
                if( !in_array( $vi['customerid'], $custsByInfra[ $infrastructure ] ) ) {
                    $custsByInfra[ $infrastructure ][] = $vi[ 'customerid' ];
                }

                if( !in_array( $vi['customerid'], $peeringCusts ) ) {
                    $peeringCusts[] = $vi[ 'customerid' ];
                }

                // ----

                if( !isset( $byLocation[ $location ] ) ) {
                    $byLocation[ $location ] = [];
                }
                if( !isset( $byLocation[ $vi['locationname'] ][ $vi['speed'] ] ) ) {
                    $byLocation[ $location ][ $vi[ 'speed' ] ] = 1;
                } else {
                    $byLocation[ $location ][ $vi[ 'speed' ] ]++;
                }

                // ----

                if( !isset( $byLan[ $infrastructure ] ) ) {
                    $byLan[ $infrastructure ] = [];
                }

                if( !isset( $byLan[ $infrastructure ][ $vi['speed'] ] ) ) {
                    $byLan[ $infrastructure ][ $vi[ 'speed' ] ] = 1;
                } else {
                    $byLan[ $infrastructure ][ $vi[ 'speed' ] ]++;
                }
            }

            ksort( $speeds, SORT_NUMERIC );
            arsort( $custsByLocation, SORT_NUMERIC );

            $cTypes['speeds']           = $speeds;
            $cTypes['custsByLocation']  = $custsByLocation;
            $cTypes['byLocation']       = $byLocation;
            $cTypes['byLan']            = $byLan;
            $cTypes['byIxp']            = $byIxp;
            $cTypes['custsByInfra']     = $custsByInfra;
            $cTypes['peeringCusts']     = $peeringCusts;

            // FROM of query is vlaninterface so should be current:
            $cTypes['rsUsage']          = D2EM::getRepository( VlanInterfaceEntity::class )->getRsClientUsagePerVlan();

            // FROM of query is vlaninterface so should be current:
            $cTypes['ipv6Usage']        = D2EM::getRepository( VlanInterfaceEntity::class )->getIPv6UsagePerVlan();

            // full/probono customers with connected interface by vlan
            $cTypes['percentByVlan']    = D2EM::getRepository( VirtualInterfaceEntity::class )->getPercentageCustomersByVlan();

            $cTypes['cached_at']        = Carbon::now();

            $cTypes['infras']           = D2EM::getRepository( InfrastructureEntity::class )->getAllAsArray();
            $cTypes['locations']        = D2EM::getRepository( LocationEntity::class )->getNames();
            $cTypes['vlans']            = D2EM::getRepository( VlanEntity::class )->getNames();

            Cache::put( 'admin_ctypes', $cTypes, 300 );
        }

        return $cTypes;
    }

    /**
     * Get public peering graphs
     *
     * @param Request $request
     * @return array array of graphs
     *
     * @throws
     */
    private function publicPeeringGraphs( Request $request )
    {
        $grapher = App::make('IXP\Services\Grapher');

        $period   = Graph::processParameterPeriod( $request->query( 'graph_period', config( 'ixp_fe.admin_dashboard.default_graph_period' ) ) );

        if( $request->query( 'refresh_cache', 0 ) || !( $graphs = Cache::get( 'admin_stats_'.$period ) ) ) {
            $graphs = [];

            $graphs['ixp'] = $grapher->ixp( D2EM::getRepository(IXPEntity::class )->getDefault() )
                ->setType(     Graph::TYPE_PNG )
                ->setProtocol( Graph::PROTOCOL_ALL )
                ->setPeriod(   $period )
                ->setCategory( Graph::CATEGORY_BITS );

            foreach( D2EM::getRepository(IXPEntity::class )->getDefault()->getInfrastructures() as $inf ) {
                $graphs[ $inf->getId()] = $grapher->infrastructure( $inf )
                    ->setType(     Graph::TYPE_PNG )
                    ->setProtocol( Graph::PROTOCOL_ALL )
                    ->setPeriod(   $period )
                    ->setCategory( Graph::CATEGORY_BITS );
            }

            Cache::put( 'admin_stats_'.$period, $graphs, 300 );
        }

        return $graphs;
    }

}