<?php

namespace IXP\Jobs;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Cache;

use Carbon\Carbon;

use Illuminate\Bus\Queueable;

use Illuminate\Queue\{
    SerializesModels,
    InteractsWithQueue
};

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use IXP\Exceptions\GeneralException;

use IXP\Models\{
    Customer,
    Router,
    VlanInterface
};

use IXP\Services\LookingGlass;

/**
 * FetchFilteredPrefixesForCustomer
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Jobs
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class FetchFilteredPrefixesForCustomer extends Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Customer
     */
    protected $customer;

    /**
     * @var array
     */
    protected $filteredPrefixes = [];

    /**
     * Create a new job instance.
     *
     * @param Customer $customer
     *
     * @return void
     */
    public function __construct( Customer $customer )
    {
        $this->customer = $customer;
    }

    /**
     * Execute the job.
     *
     * @param LookingGlass $lg
     *
     * @return void
     *
     * @throws GeneralException
     */
    public function handle( LookingGlass $lg ): void
    {
        if( !$this->havePersistentCache() ) {
            throw new GeneralException('A persistent cache is required to fetch filtered prefixes' );
        }


        // so, find all vlan interfaces where this customer is configured for route server client
        // them, for each router, get a list of filtered prefixes and record reason(s) and routers

        /** @var VlanInterface[] $vlis */
        $vlis = VlanInterface::select('vli.*')
            ->from( 'vlaninterface AS vli' )
            ->leftJoin( 'virtualinterface AS vi', 'vi.id', 'vli.virtualinterfaceid' )
            ->leftJoin( 'cust AS c', 'c.id', 'vi.custid' )
            ->leftJoin( 'vlan AS v', 'v.id', 'vli.vlanid' )
            ->where( 'vli.rsclient', true )
            ->where( 'v.private', false )
            ->where( 'c.id', $this->customer->id )
            ->get();

        foreach( $vlis as $vli ) {
            // query routers for this VLAN
            foreach( array_keys( Router::$PROTOCOLS ) as $ipproto ) {
                if( $vli->ipvxEnabled( $ipproto ) ) {
                    $this->queryRouteServer( $lg, $vli, $ipproto );
                }
            }
        }

        // and jam the results into the cache
        Cache::put('filtered-prefixes-' . $this->customer->id, $this->filteredPrefixes, 900);
    }

    /**
     * Query the various route servers for filtered prefixes and add them to the $this->filteredPrefixes array
     *
     * @param LookingGlass      $lg
     * @param VlanInterface     $vli
     * @param int               $ipproto
     *
     * @return void
     *
     * @throws
     */
    private function queryRouteServer( LookingGlass $lg, VlanInterface $vli, int $ipproto ): void
    {
        $bird_protocol = sprintf( "pb_%04d_as%d", $vli->id, $vli->virtualInterface->customer->autsys );

        /** @var Router[] $routers */
        $routers = Router::where( 'vlan_id', $vli->vlan->id )
            ->where( 'protocol', $ipproto )
            ->notQuarantine()
            ->hasApi()
            ->routeServer()
            ->largeCommunities()
            ->get();

        foreach( $routers as $router ) {
            if( !( $resp = \json_decode( $lg->forRouter( $router )->routesProtocolLargeCommunityWildXYRoutes( $bird_protocol, $router->asn, 1101 ) ) ) || !isset( $resp->routes ) ) {
                continue;
            }

            foreach( $resp->routes as $route ) {
                if( !isset( $this->filteredPrefixes[ $route->network ] ) ) {
                    $this->filteredPrefixes[ $route->network ]              = [];
                    $this->filteredPrefixes[ $route->network ]['found_at']  = now();
                    $this->filteredPrefixes[ $route->network ]['reasons']   = [];
                }

                $this->filteredPrefixes[ $route->network ]['routers'][      $router->handle ] = $bird_protocol;
                $this->filteredPrefixes[ $route->network ]['age'][          $router->handle ] = Carbon::parse( $route->age );
                $this->filteredPrefixes[ $route->network ]['primary'][      $router->handle ] = $route->primary;
                $this->filteredPrefixes[ $route->network ]['as_path'][      $router->handle ] = $route->bgp->as_path;
                $this->filteredPrefixes[ $route->network ]['gateway'][      $router->handle ] = $route->gateway;
                $this->filteredPrefixes[ $route->network ]['learnt_from'][  $router->handle ] = $route->learnt_from;
                $this->filteredPrefixes[ $route->network ]['communities'][  $router->handle ] = $route->bgp->communities ?? [];
                $this->filteredPrefixes[ $route->network ]['lcommunities'][ $router->handle ] = $route->bgp->large_communities ?? [];

                foreach( $route->bgp->large_communities as $lc ) {
                    if( $lc[0] !== $router->asn || $lc[1] !== 1101 ) {
                        continue;
                    }

                    $lc = implode( ':', $lc );
                    if( !in_array( $lc, $this->filteredPrefixes[ $route->network ]['reasons'] ) ) {
                        $this->filteredPrefixes[ $route->network ]['reasons'][] = $lc;
                    }
                }
            }
        }
    }
}