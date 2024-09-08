<?php

namespace IXP\Services\Diagnostics\Suites;

/*
 * Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Carbon\Carbon;
use IXP\Models\Router;
use IXP\Services\Diagnostics\DiagnosticResult;
use IXP\Services\Diagnostics\DiagnosticSuite;
use IXP\Models\VlanInterface;

use IXP\Services\LookingGlass as LookingGlassService;
use IXP\Contracts\LookingGlass as LookingGlassContract;
use App;

/**
 * Diagnostics Service - Router BGP Suite
 *
 * @author     Barry O'Donovan  <barry@opensolutions.ie>
 * @author     Laszlo Kiss      <laszlo@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Services\Diagnostics
 * @copyright  Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */

class RouterBgpSessionsDiagnosticSuite extends DiagnosticSuite
{
    /** @var Router[]  */
    private $routers = [];

    /** @var LookingGlassContract[] */
    private array $lg = [];

    /**
     * @param VlanInterface $vli
     * @param int $protocol
     */
    public function __construct(
        private readonly VlanInterface $vli,
        private readonly int $protocol,
    ) {

        $this->name        = 'BGP Sessions over ' . $vli->vlan->name . ' via ' . $vli->getIPAddress($this->protocol)->address;
        $this->description = " ";
        $this->type        = 'VLAN_INTERFACE';

        $this->routers = Router::where( 'protocol', $protocol === 4 ? Router::PROTOCOL_IPV4 : Router::PROTOCOL_IPV6 )
            ->where( 'vlan_id', $vli->vlan->id )
//            ->where('quarantine', false)
//            ->where('type', Router::TYPE_ROUTE_COLLECTOR)
            ->get();

        parent::__construct();
    }

    /**
     * Run the diagnostics suite
     */
    public function run(): RouterBgpSessionsDiagnosticSuite
    {
        foreach( $this->routers as $router ) {
            $this->lg[$router->handle] = App::make( LookingGlassService::class )->forRouter( $router );

            if( $router->hasApi() ) {

                if( $status = json_decode( $this->lg[ $router->handle ]->status() ) ) {
                    $this->results->add( new DiagnosticResult(
                        name: "Router {$router->handle} up, last reconfig " .
                        Carbon::parse( $status->status->last_reconfig )->diffForHumans(),
                        result: DiagnosticResult::TYPE_TRACE,
                    ) );
                } else {
                    $this->results->add( new DiagnosticResult(
                        name: "Router {$router->handle} not up or looking glass failure",
                        result: DiagnosticResult::TYPE_ERROR,
                    ) );
                    continue;
                }
            } else {

                $this->results->add( new DiagnosticResult(
                    name: "Router {$router->handle} does not have an API, skipping tests",
                    result: DiagnosticResult::TYPE_DEBUG
                ) );
                continue;
            }

            $this->results->add( $this->protocolStatus( $this->vli, $router, $this->lg[ $router->handle ] ) );

        }

        return $this;
    }


    /**
     * Examine the Router Protocol Status and provide information on it.
     *
     * @return DiagnosticResult
     */
    public function protocolStatus( VlanInterface $vli, Router $r, LookingGlassContract $lg )
    {
        $mainName = "Protocol Status diagnostics";

        if ( $this->router && $this->router->hasApi() ) {

            $statusUrl = $this->router->api . '/protocol/pb_as' . $this->vli->virtualinterface->customer->autsys . "_vli" . $this->vli->id . "_ipv" . $this->protocol;

            try {
                $fileContent = file_get_contents( $statusUrl );
            } catch(\Exception $e ) {
                info("ERROR: Status content inaccessible.\n".$e->getMessage());
                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_FATAL,
                    narrative: "ERROR: Status content inaccessible. More info in log",
                );

            }

            $protocolStatus = json_decode( $fileContent );

            if( $protocolStatus->protocol->state !== 'up' ) {

                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_WARN,
                    narrative: "Router Protocol Status is " . $protocolStatus->protocol->state,
                );

            } else {

                $importPercent = $protocolStatus->protocol->routes->imported / $protocolStatus->protocol->route_limit_at;
                if( $importPercent < .8 ) {

                    return new DiagnosticResult(
                        name: $mainName,
                        result: DiagnosticResult::TYPE_WARN,
                        narrative: "Router Protocol Status is up, but import rate is low",
                    );

                } else {

                    return new DiagnosticResult(
                        name: $mainName,
                        result: DiagnosticResult::TYPE_GOOD,
                        narrative: "Router Protocol Status is up, and import rate is good",
                    );

                }

            }
        } else {

            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_ERROR,
                narrative: "Router DEBUG or API not available",
            );

        }

    }


}