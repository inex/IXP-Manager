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
use Illuminate\Support\Facades\Process;
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
 * @category   IXP
 * @package    IXP\Services\Diagnostics
 * @copyright  Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */

class VlanInterfaceL3DiagnosticSuite extends DiagnosticSuite
{

    /**
     * @param VlanInterface $vli
     * @param int $protocol
     */
    public function __construct(
        private readonly VlanInterface $vli,
        private readonly int $protocol,
    ) {

        $this->name        = 'Vlan Interface / L3 diagnostics on ' . $vli->vlan->name . ' via ' . $vli->getIPAddress($this->protocol)->address;
        $this->description = " ";
        $this->type        = 'VLAN_INTERFACE';

        parent::__construct();
    }

    /**
     * Run the diagnostics suite
     * @throws BindingResolutionException
     */
    public function run(): VlanInterfaceL3DiagnosticSuite
    {

        $this->results->add( $this->ping(   $this->vli, $this->protocol ) );

        return $this;
    }


    /**
     *
     * @return DiagnosticResult
     */
    public function ping( VlanInterface $vli, int $protocol ): DiagnosticResult
    {
        $mainName = $vli->getIPAddress($protocol)->address . ' responds to pings';

        try {
            $result = Process::run( sprintf( config( "ixp.exec.ping{$protocol}" ),  $vli->getIPAddress($protocol)->address ) );

            if( $result->successful() ) {

                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_GOOD,
                    narrativeHtml: "<pre>{$result->output()}</pre>",
                );

            } else {

                return new DiagnosticResult(
                    name: $mainName . ' - no, see detail for more information',
                    result: DiagnosticResult::TYPE_ERROR,
                    narrativeHtml: "<pre>{$result->output()}</pre>",
                );

            }

        } catch ( \Exception $e ) {

            return new DiagnosticResult(
                name: $mainName . ' - diagnostic failed to run',
                result: DiagnosticResult::TYPE_UNKNOWN,
                narrativeHtml: $e->getMessage(),
            );

        }
    }

}