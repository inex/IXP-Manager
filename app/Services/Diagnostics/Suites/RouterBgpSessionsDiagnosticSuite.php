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

    private Router|null $router;
    private LookingGlassContract|null $lookingGlass;

    /**
     * @param VlanInterface $vli
     * @param int $protocol
     */
    public function __construct(
        private readonly VlanInterface $vli,
        private readonly int $protocol,
    ) {
        $ipAddressObject = $vli->getIPAddress($this->protocol);
        $_address = '';
        if($ipAddressObject) {
            $_address = ' over ' . $ipAddressObject->address;
        }

        $this->name        = 'Router BGP Sessions for ' . $vli->vlan->name . $_address;
        $this->description = " ";
        $this->type        = 'INTERFACE';

        // route collector peerings are mandatory
        // this makes the protocol Builder type, not Router type, what is important for lookingGlass
        //$this->router = Router::notQuarantine()->routeCollector()->ipProtocol( $protocol );

        $protocolValidated = $protocol === 4 ? Router::PROTOCOL_IPV4 : Router::PROTOCOL_IPV6;
        $this->router = Router::where( 'protocol', $protocolValidated )
            ->where('quarantine', false)
            ->where('type', Router::TYPE_ROUTE_COLLECTOR)
            ->first();

        // now we get the looking glass:
        $this->lookingGlass = null;
        if($this->router) {
            $this->lookingGlass = App::make( LookingGlassService::class )->forRouter( $this->router );
        }

        parent::__construct();
    }

    /**
     * Run the diagnostics suite
     */
    public function run(): RouterBgpSessionsDiagnosticSuite
    {
        $this->results->add( $this->vlanRouterTest() );
        $this->results->add( $this->protocolStatusDiagnostics() );

        return $this;
    }

    /**
     * Examine the Vlan Router and provide information on it.
     *
     * @return DiagnosticResult
     */
    public function vlanRouterTest(): DiagnosticResult
    {
        $mainName = "Router existence diagnostics";

        if ( !$this->router || !$this->router->hasApi() ) {

            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_ERROR,
                narrative: "Router DEBUG or API not available",
            );

        } else {

            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_GOOD,
                narrative: "Router DEBUG and API available",
            );

        }

    }


    /**
     * Examine the Router Protocol Status and provide information on it.
     *
     * @return DiagnosticResult
     */
    public function protocolStatusDiagnostics(): DiagnosticResult
    {
        $mainName = "Protocol Status diagnostics";

        if ( $this->router && $this->router->hasApi() ) {
            // sample url for protocol status: http://rc1-ipv4.cork.inex.ie/api/protocol/pb_as112_vli249_ipv4
            // https://www.inex.ie/rc1-cork-ipv4/api/protocol/pb_as112_vli249_ipv4
            // test api use: https://www.inex.ie/rc1-cork-ipv4/api
            //$statusUrl = 'https://www.inex.ie/rc1-cork-ipv4/api/protocol/pb_as112_vli249_ipv4';

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

            // json content of interest:

            // state: up  -> okay, otherwise warn
            // interesting info if !up: state_changed
            // interesting info if up: route_limit_at vs import_limit => if within 80% -> warning

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