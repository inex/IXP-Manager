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
use IXP\Services\Diagnostics\DiagnosticResult;
use IXP\Services\Diagnostics\Suite;

/**
 * Diagnostics Service - Customer Suite
 *
 * @author     Barry O'Donovan  <barry@opensolutions.ie>
 * @author     Laszlo Kiss      <laszlo@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Services\Diagnostics
 * @copyright  Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */

class CustomerDiagnosticSuite extends Suite
{
    public const string DIAGNOSTIC_SUITE_NAME = 'Member Overview';

    public const string DIAGNOSTIC_SUITE_DESCRIPTION = "Diagnostics for the overall member's set-up.";

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
        $this->results[] = $this->customerStatus();
        $this->results[] = $this->customerHasLeft();
        $this->results[] = $this->customerRouteServerClient();
//        $this->results[] = $this->customerIrrdbFiltered();
//        $this->results[] = $this->customerIrrdbAsnsPresent();

        return $this;
    }


    /**
     * Examine the customer type and provide information on it.
     *
     * @return DiagnosticResult
     */
    private function customerType(): DiagnosticResult {
        $mainName = 'Member Type';
        
        switch( $this->customer->type ) {

            case Customer::TYPE_FULL:
                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_DEBUG,
                    narrative: "The member is a standard 'full' member",
                );
                break;

            case Customer::TYPE_PROBONO:
                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_INFO,
                    narrative: "The member is a <b>pro bono</b> member",
                );
                break;

            case Customer::TYPE_INTERNAL:
                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_WARN,
                    narrative: "The member is an internal member used for IXP infrastructure. Do not assume normal member interfaces and behaviors.",
                );
                break;

            case Customer::TYPE_ASSOCIATE:
                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_WARN,
                    narrative: "The member is an associate member and should not have any connections or other services.",
                );
                break;
        }

        return new DiagnosticResult(
            name: $mainName,
            result: DiagnosticResult::TYPE_FATAL,
            narrative: "The member type {$this->customer->type()} is an unknown type to the diagnostic logic.",
        );
    }


    /**
     * Examine the customer status and provide information on it.
     *
     * @return DiagnosticResult
     */
    private function customerStatus(): DiagnosticResult {
        $mainName = 'Member Status';

        switch( $this->customer->status ) {

            case Customer::STATUS_NOTCONNECTED:
            case Customer::STATUS_SUSPENDED:
                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_WARN,
                    narrative: "The member's status is " . $this->customer->status(),
                );
                break;

            case Customer::STATUS_NORMAL:
                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_DEBUG,
                    narrative: "The member's status is " . $this->customer->status(),
                );
                break;
        }

        return new DiagnosticResult(
            name: $mainName,
            result: DiagnosticResult::TYPE_FATAL,
            narrative: "The member's status {$this->customer->status()} is an unknown status to the diagnostic logic.",
        );
    }


    /**
     * Examine the customer left the IXP and provide information on it.
     *
     */
    private function customerHasLeft(): DiagnosticResult {
        $mainName = 'Member Left';

        if( $this->customer->hasLeft() ) {
             return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_ERROR,
                    narrative: "The member left the IXP on " . Carbon::parse($this->customer->dateleave)->format('Y-m-d'),
            );
        }

        return new DiagnosticResult(
            name: $mainName,
            result: DiagnosticResult::TYPE_TRACE,
            narrative: "The member not left the IXP",
        );
    }


    /**
     * Examine the customer Route Server Client status and provide information on it.
     *
     * @return DiagnosticResult
     */
    private function customerRouteServerClient(): DiagnosticResult {
        $mainName = 'Route Server Client';

        if( $this->customer->routeServerClient() ) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_INFO,
                narrative: "The member is a route server client",
            );
        }

        return new DiagnosticResult(
            name: $mainName,
            result: DiagnosticResult::TYPE_INFO,
            narrative: "The member <strong>is not</strong> a route server client",
        );

    }


    /**
     * Examine the customer IRRDB filtering status and provide information on it.
     *
     * @return DiagnosticResult
     */
    private function customerIrrdbFiltered(): DiagnosticResult {
        $mainName = "IRRDB Filtered Status";

        if( !$this->customer->routeServerClient() ) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_TRACE,
                narrative: "The member is not a route server client so IRRDB filtering not considered",
            );
        }

        if( $this->customer->irrdbFiltered() ) {

            if($this->customer->irrdbMoreSpecificsAllowed()) {
                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_WARN,
                    narrative: "The member is IRRDB filtered but note that more specific prefixes are allowed",
                );
            }

            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_GOOD,
                narrative: "The member is IRRDB filtered",
            );

        }

        return new DiagnosticResult(
            name: $mainName,
            result: DiagnosticResult::TYPE_ERROR,
            narrative: "The member is not IRRDB filtered",
        );
    }


    /**
     * Examine the customer IRRDB filtering status and provide information on it.
     *
     * @return DiagnosticResult
     */
    private function customerIrrdbAsnsPresent(): DiagnosticResult {
        $mainName = "IRRDB Filtered Status";

        if( !$this->customer->routeServerClient() ) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_TRACE,
                narrative: "The member is not a route server client so IRRDB filtering not considered",
            );
        }

        if( $this->customer->irrdbFiltered() ) {

            if($this->customer->irrdbMoreSpecificsAllowed()) {
                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_WARN,
                    narrative: "The member is IRRDB filtered but note that more specific prefixes are allowed",
                );
            }

            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_GOOD,
                narrative: "The member is IRRDB filtered",
            );

        }

        return new DiagnosticResult(
            name: $mainName,
            result: DiagnosticResult::TYPE_ERROR,
            narrative: "The member is not IRRDB filtered",
        );
    }



}