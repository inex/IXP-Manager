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
use Illuminate\Contracts\Container\BindingResolutionException;
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
            ->get();

        parent::__construct();
    }

    /**
     * Run the diagnostics suite
     * @throws BindingResolutionException
     */
    public function run(): RouterBgpSessionsDiagnosticSuite
    {
        foreach( $this->routers as $router ) {

            if( $router->hasApi() ) {

                ## TRYCATCH
                $this->lg[$router->handle] = App::make( LookingGlassService::class )->forRouter( $router );

                if( $status = json_decode( $this->lg[ $router->handle ]->status() ) ) {

                    $this->results->add( new DiagnosticResult(
                        name: "Router {$router->handle} up, last reconfig " .
                        Carbon::parse( $status->status->last_reconfig )->diffForHumans(),
                        result: DiagnosticResult::TYPE_TRACE,
                    ) );

                } else {

                    $this->results->add( new DiagnosticResult(
                        name: "Router {$router->handle} not up or looking glass failure",
                        result: DiagnosticResult::TYPE_FATAL,
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

            $this->results->add( $this->protocolStatus( $this->vli, $this->protocol, $router, $this->lg[ $router->handle ] ) );

        }

        return $this;
    }


    /**
     * Examine the Router Protocol Status and provide information on it.
     *
     * @return DiagnosticResult
     */
    public function protocolStatus( VlanInterface $vli, int $protocol, Router $r, LookingGlassContract $lg )
    {
        $mainName = "BGP status for {$r->handle} - ";

        // we have inconsistent protocol naming which needs to be corrected
        if( $r->isType( Router::TYPE_ROUTE_SERVER ) ) {
            $pb = "pb_" . sprintf( "%04d", $vli->id ) . "_as" . $vli->virtualInterface->customer->autsys;
        } else {
            $pb = "pb_as" . $vli->virtualInterface->customer->autsys . "_vli{$vli->id}_ipv{$protocol}";
        }

        ## TRYCATCH - update to new unknown result
        try {
            if( !( $bgpsum = json_decode( $lg->bgpNeighbourSummary( $pb ) ) ) ) {

                return new DiagnosticResult(
                    name: $mainName . 'could not query looking glass',
                    result: DiagnosticResult::TYPE_FATAL,
                    narrative: "API call to looking glass failed.",
                );

            }
        } catch( \Exception $e ) {

            return new DiagnosticResult(
                name: $mainName . 'exception thrown when querying looking glass',
                result: DiagnosticResult::TYPE_FATAL,
                narrative: $e->getMessage(),
            );

        }

        $bgpsum = $bgpsum->protocol; // narrow focus to what interests us

        if( !isset($bgpsum->import_limit) ) {
            $bgpsum->import_limit = 0;
            $max_prefixes = false;
        } else {
            $max_prefixes = true;
            $max_prefixes_percent = (int) ($bgpsum->route_limit_at / $bgpsum->import_limit) * 100;
        }

        $narrative = <<<ENDNARR
        <b>State:</b> {$bgpsum->state}<br>
        <b>Changed:</b> {$bgpsum->state_changed}<br>
        <b>Connection:</b> {$bgpsum->connection}<br>
        <b>Hold timer (now):</b> {$bgpsum->hold_timer} ({$bgpsum->hold_timer_now})<br>
        <b>Keepalive (now):</b> {$bgpsum->keepalive} ({$bgpsum->keepalive_now})<br>
        <b>Max prefixes:</b> {$bgpsum->import_limit}<br>
        <b># Routes:</b> {$bgpsum->route_limit_at}<br>
        ENDNARR;

        if( $bgpsum->state !== 'up' ) {

            return new DiagnosticResult(
                name: $mainName . 'session state ' . $bgpsum->state,
                result: DiagnosticResult::TYPE_ERROR,
                narrativeHtml: $narrative,
            );

        }


        if( $max_prefixes && $max_prefixes_percent > 80 ) {

            return new DiagnosticResult(
                name: $mainName . "session up but max prefixes at {$max_prefixes_percent}% ({$bgpsum->route_limit_at}/{$bgpsum->import_limit})",
                result: DiagnosticResult::TYPE_WARN,
                narrativeHtml: $narrative,
            );

        }

        return new DiagnosticResult(
            name: $mainName . "session up " . ( $max_prefixes ? "({$bgpsum->route_limit_at}/{$bgpsum->import_limit} prefixes) " : "(no max prefixes) " )
                . "(last keepalive " . ($bgpsum->keepalive-$bgpsum->keepalive_now) . "/{$bgpsum->keepalive})",
            result: DiagnosticResult::TYPE_GOOD,
            narrativeHtml: $narrative,
        );
    }


}