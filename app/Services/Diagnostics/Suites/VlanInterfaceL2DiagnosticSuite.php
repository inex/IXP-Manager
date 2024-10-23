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

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Process;
use IXP\Services\Diagnostics\DiagnosticResult;
use IXP\Services\Diagnostics\DiagnosticSuite;
use IXP\Models\VlanInterface;

/**
 * Diagnostics Service - Router BGP Suite
 *
 * @author     Barry O'Donovan  <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP\Services\Diagnostics
 * @copyright  Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */

class VlanInterfaceL2DiagnosticSuite extends DiagnosticSuite
{

    /**
     * @param VlanInterface $vli
     */
    public function __construct(
        private readonly VlanInterface $vli,
    ) {

        $this->name        = 'Vlan Interface / L2 diagnostics on ' . $vli->vlan->name;
        $this->description = " ";
        $this->type        = 'VLAN_INTERFACE';

        parent::__construct();
    }

    /**
     * Run the diagnostics suite
     * @throws BindingResolutionException
     */
    public function run(): VlanInterfaceL2DiagnosticSuite
    {

        $this->results->add( $this->arping( $this->vli ) );

        return $this;
    }



    /**
     *
     * @return DiagnosticResult[]
     */
    public function arping( VlanInterface $vli ): array
    {
        $results = [];

        foreach( $vli->layer2addresses as $l2a ) {

            $mainName = $l2a->macFormatted(':') . ' responds to arp pings';

            ## TRYCATCH
            $result = Process::run( sprintf( config( "ixp.exec.arping.{$vli->vlanid}" ), $l2a->macFormatted(':') ) );

            if( $result->successful() ) {

                $results[] = new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_GOOD,
                    narrativeHtml: "<pre>{$result->output()}</pre>",
                );

            }

            $results[] = new DiagnosticResult(
                name: $mainName . ' - no, see detail for more information',
                result: DiagnosticResult::TYPE_ERROR,
                narrativeHtml: "<pre>{$result->output()}</pre>",
            );

        }

        return $results;
    }

}