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

use IXP\Models\Customer;
use IXP\Services\Diagnostics\DiagnosticResult;
use IXP\Services\Diagnostics\Suite;

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

class CustomerDiagnosticSuite extends Suite
{
    public const string DIAGNOSTIC_SUITE_NAME = 'Member Overview';

    public const string DIAGNOSTIC_SUITE_DESCRIPTION = "General member overview diagnostics.";

    public const string DIAGNOSTIC_SUITE_TYPE = 'CUSTOMER';


    public function __construct(
        private Customer $customer
    ) {}

    /**
     * Run the diagnostics suite
     */
    public function run(): CustomerDiagnosticSuite
    {
        // ordering here will determine order on view
        $this->results[] = $this->customerType();

        return $this;
    }


    /**
     * Examine the customer type and provide information on it.
     *
     * @return DiagnosticResult
     */
    private function customerType(): DiagnosticResult {

        switch( $this->customer->type ) {

            case Customer::TYPE_FULL:
                return new DiagnosticResult(
                    name: 'Member Type',
                    result: DiagnosticResult::TYPE_OKAY,
                    narrative: "The member is a standard 'full' member",
                );
                break;

            case Customer::TYPE_PROBONO:
                return new DiagnosticResult(
                    name: 'Member Type',
                    result: DiagnosticResult::TYPE_INFO,
                    narrative: "The member is a <b>pro bono</b> member",
                );
                break;

            case Customer::TYPE_INTERNAL:
                return new DiagnosticResult(
                    name: 'Member Type',
                    result: DiagnosticResult::TYPE_WARNING,
                    narrative: "The member is an internal member used for IXP infrastructure. Do not assume normal member interfaces and behaviors.",
                );
                break;

            case Customer::TYPE_ASSOCIATE:
                return new DiagnosticResult(
                    name: 'Member Type',
                    result: DiagnosticResult::TYPE_ERROR,
                    narrative: "The member is an associate member and should not have any connections or other services.",
                );
                break;
        }

        return new DiagnosticResult(
            name: 'Member Type',
            result: DiagnosticResult::TYPE_UNKNOWN,
            narrative: "The member is an unknown type to the diagnostic logic.",
        );
    }




}