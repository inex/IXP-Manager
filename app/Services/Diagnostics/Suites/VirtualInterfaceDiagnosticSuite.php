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
use IXP\Exceptions\GeneralException;
use IXP\Models\Customer;
use IXP\Models\VirtualInterface;
use IXP\Services\Diagnostics\DiagnosticResult;
use IXP\Services\Diagnostics\Suite;
use phpDocumentor\Reflection\Types\Collection;

/**
 * Diagnostics Service - Customer Suite
 *
 * @author     Barry O'Donovan  <barry@opensolutions.ie>
 * @author     Laszlo Kiss      <laszlo@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */

class VirtualInterfaceDiagnosticSuite extends Suite
{
    public const string DIAGNOSTIC_SUITE_NAME = 'Member Overview';

    public const string DIAGNOSTIC_SUITE_DESCRIPTION = "General member overview diagnostics.";

    public const string DIAGNOSTIC_SUITE_TYPE = 'CUSTOMER';

    private \Illuminate\Database\Eloquent\Collection $virtualInterfaces;

    public function __construct(
        private Customer $customer
    ) {
        $this->virtualInterfaces = $this->customer->virtualInterfaces;
    }

    /**
     * Run the diagnostics suite
     * @throws GeneralException
     */
    public function run(): VirtualInterfaceDiagnosticSuite
    {
        // ordering here will determine order on view
        foreach ($this->virtualInterfaces as $virtualInterface) {
            $this->results = [
                $virtualInterface["id"] => [
                    $this->virtualInterfaceType($virtualInterface),
                    // here comes other details, physical interfaces, switchport, switches, vlaninterfaces as a tree structure
                    // it needs a creative frontend solution to show all the child branches and its diagnostic data
                ]
            ];
        }

        return $this;
    }


    /**
     * Examine the customer status and provide information on it.
     *
     * @param VirtualInterface $virtualInterface
     * @return DiagnosticResult
     */
    private function virtualInterfaceType(VirtualInterface $virtualInterface): DiagnosticResult {
        $mainName = 'Virtual Interface Type';

        if($virtualInterface->type()) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_OKAY,
                narrative: "The Virtual interface type is ".VirtualInterface::$VI_TYPES_TEXT[$virtualInterface->type()].".",
            );
        }

        return new DiagnosticResult(
            name: $mainName,
            result: DiagnosticResult::TYPE_ERROR,
            narrative: "There are no physical interfaces for this virtual interface.",
        );
    }





}