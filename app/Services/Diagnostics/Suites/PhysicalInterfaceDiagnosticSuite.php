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
use IXP\Models\VirtualInterface;
use IXP\Models\PhysicalInterface;
use IXP\Services\Diagnostics\DiagnosticResult;
use IXP\Services\Diagnostics\Suite;

/**
 * Diagnostics Service - Physical Interfaces Suite
 *
 * @author     Barry O'Donovan  <barry@opensolutions.ie>
 * @author     Laszlo Kiss      <laszlo@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Services\Diagnostics
 * @copyright  Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */

class PhysicalInterfaceDiagnosticSuite extends Suite
{
    public const string DIAGNOSTIC_SUITE_NAME = 'Physical Interfaces Overview';

    public const string DIAGNOSTIC_SUITE_DESCRIPTION = "Physical Interfaces overview diagnostics.";

    public const string DIAGNOSTIC_SUITE_TYPE = 'PHYSICAL_INTERFACE';

    private \Illuminate\Database\Eloquent\Collection $physicalInterfaces;

    public function __construct(
        private VirtualInterface $virtualInterface,
    ) {
        $this->physicalInterfaces = $this->virtualInterface->physicalInterfaces;

//        $this->name        = ;
//        $this->description = ;
//        $this->type        = ;
    }

    /**
     * Run the diagnostics suite
     */
    public function run(): PhysicalInterfaceDiagnosticSuite
    {
        $this->results = [];

        //info("all PI\n".var_export($this->physicalInterfaces->toArray(), true));

        // ordering here will determine order on view
        foreach ($this->physicalInterfaces as $physicalInterface) {
            $this->results[$physicalInterface["id"]][] = $this->physicalInterfaceLastPoll($physicalInterface);

            if(!is_null($this->virtualInterface->mtu) && $this->virtualInterface->mtu > 0) {
                $this->results[$physicalInterface["id"]][] = $this->physicalInterfaceMTU($physicalInterface);
            }


        }


        return $this;
    }


    /**
     * Examine the physical interface last poll and provide information on it.
     *
     * @param PhysicalInterface $physicalInterface
     * @return DiagnosticResult
     */
    private function physicalInterfaceLastPoll(PhysicalInterface $physicalInterface): DiagnosticResult
    {
        $mainName = "Diagnostic tests";

        $lastPoll = $physicalInterface->switchPort->lastSnmpPoll;
        $diff = Carbon::now()->diffInDays(Carbon::parse($lastPoll));

        if ($diff >= 1 || is_null($physicalInterface->switchPort->lastSnmpPoll)) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_WARN,
                narrative: "Some diagnostic tests are not real time",
            );
        }

        return new DiagnosticResult(
            name: $mainName,
            result: DiagnosticResult::TYPE_GOOD,
            narrative: "Diagnostic tests are in real time",
        );
    }


    /**
     * Examine the physical interface mtu and provide information on it.
     *
     * @param PhysicalInterface $physicalInterface
     * @return DiagnosticResult
     */
    private function physicalInterfaceMTU(PhysicalInterface $physicalInterface): DiagnosticResult
    {
        $mainName = "MTU diagnostic";

        $viMtu = $this->virtualInterface->mtu;

        if ($physicalInterface->switchPort->ifMtu === $viMtu) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_GOOD,
                narrative: "MTU values matching",
            );
        } else if ($physicalInterface->switchPort->ifMtu < $viMtu) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_ERROR,
                narrative: "Error: MTU value too low",
            );
        } else {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_WARN,
                narrative: "MTU values NOT matching",
            );
        }

    }




}