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
use IXP\Models\SwitchPort;
use IXP\Models\VirtualInterface;
use IXP\Services\Diagnostics\DiagnosticResult;
use IXP\Services\Diagnostics\DiagnosticSuite;
use IXP\Models\VlanInterface;

use IXP\Services\LookingGlass as LookingGlassService;
use App;

/**
 * Diagnostics Service - Virtual Interfaces Suite
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
    /**
     * @param VlanInterface $vli
     * @param int $protocol
     */
    public function __construct(
        private readonly VlanInterface $vli,
        private readonly int $protocol,
    ) {
        $this->name        = 'Router BGP Sessions for ' . $vli->vlan->name . ' over ' . $vli->getIPAddress($this->protocol);
        $this->description = ".";
        $this->type        = 'INTERFACE';

        parent::__construct();
    }

    /**
     * Run the diagnostics suite
     */
    public function run(): RouterBgpSessionsDiagnosticSuite
    {
        // route collector peerings are mandatory

        $router = Router::notQuarantine()->routeCollector()->ipProtocol( $this->protocol );

        // test: !$router || !$router->api()  -> if not, DEBUG not API available

        // now we get the looking glass:
        $lg = App::make( LookingGlassService::class )->forRouter( $router );

        // sample url for protocol status: http://rc1-ipv4.cork.inex.ie/api/protocol/pb_as112_vli249_ipv4
        // https://www.inex.ie/rc1-cork-ipv4/api/protocol/pb_as112_vli249_ipv4

        // actually use: https://www.inex.ie/rc1-cork-ipv4/

        // protocol bgp pb_as<?= $int['autsys'] ? >_vli<?= $int[ 'vliid' ] ? >_ipv<?= $int[ 'protocol' ] ?? 4 ? > {

        // "pb_as{$vli->virtualinterface->customer->autsys}_vli{$vli->id}_ipv{$protocol}

        // json content of interest:

        // state: up  -> okay, otherwise warn
        // interesting info if !up: state_changed
        // interesting info if up: route_limit_at vs import_limit => if within 80% -> warning



        return $this;
    }



}