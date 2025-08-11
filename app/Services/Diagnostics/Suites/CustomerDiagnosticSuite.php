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
use IXP\Services\Diagnostics\DiagnosticSuite;

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

class CustomerDiagnosticSuite extends DiagnosticSuite
{

    public function __construct(
        private readonly Customer $customer
    ) {
        $this->name        = 'Member Overview';
        $this->description = 'Diagnostics for the overall member\'s set-up.';
        $this->type        = 'CUSTOMER';

        parent::__construct();
    }

    /**
     * Run the diagnostics suite
     */
    public function run(): CustomerDiagnosticSuite
    {
        // ordering here will determine order on view
        $this->results->add( $this->customerType( $this->customer ) );
        $this->results->add( $this->customerStatus( $this->customer ) );
        $this->results->add( $this->customerHasLeft( $this->customer ) );
        $this->results->add( $this->customerRouteServerClient( $this->customer ) );

        return $this;
    }


    /**
     * Examine the customer type and provide information on it.
     *
     * @param Customer $customer
     * @return DiagnosticResult
     */
    public function customerType( Customer $customer ): DiagnosticResult {
        $mainName = 'Member Type: ';

        return match ( $customer->type ) {

            Customer::TYPE_FULL => new DiagnosticResult(
                name: $mainName . $customer->type(),
                result: DiagnosticResult::TYPE_DEBUG,
                narrative: "The member is a standard full member",
            ),

            Customer::TYPE_PROBONO => new DiagnosticResult(
                name: $mainName . $customer->type(),
                result: DiagnosticResult::TYPE_INFO,
                narrative: "The member is a pro bono member",
            ),

            Customer::TYPE_INTERNAL => new DiagnosticResult(
                name: $mainName . $customer->type(),
                result: DiagnosticResult::TYPE_WARN,
                narrative: "The member is an internal member used for IXP infrastructure. Do not assume normal member interfaces and behaviors.",
            ),

            Customer::TYPE_ASSOCIATE => new DiagnosticResult(
                name: $mainName .$customer->type(),
                result: DiagnosticResult::TYPE_WARN,
                narrative: "The member is an associate member and should not have any connections or other services.",
            ),

            default => new DiagnosticResult(
                name: $mainName . 'UNKNOWN',
                result: DiagnosticResult::TYPE_FATAL,
                narrative: "The member type {$this->customer->type()} is an unknown type to the diagnostic logic.",
            ),
        };

    }


    /**
     * Examine the customer status and provide information on it.
     *
     * @param Customer $customer
     * @return DiagnosticResult
     */
    public function customerStatus( Customer $customer ): DiagnosticResult {
        $mainName = 'Member Status: ';

        return match ($customer->status ) {

            Customer::STATUS_NOTCONNECTED, Customer::STATUS_SUSPENDED => new DiagnosticResult(
                name: $mainName .$customer->status(),
                result: DiagnosticResult::TYPE_WARN,
                narrative: "The member's status is " .$customer->status(),
            ),

            Customer::STATUS_NORMAL => new DiagnosticResult(
                name: $mainName .$customer->status(),
                result: DiagnosticResult::TYPE_DEBUG,
                narrative: "The member's status is " .$customer->status(),
            ),

            default => new DiagnosticResult(
                name: $mainName . 'UNKNOWN',
                result: DiagnosticResult::TYPE_FATAL,
                narrative: "The member's status {$this->customer->status()} is an unknown status to the diagnostic logic.",
            ),
        };

    }


    /**
     * Examine the customer left the IXP and provide information on it.
     *
     */
    public function customerHasLeft( Customer $customer ): DiagnosticResult {

        if($customer->hasLeft() ) {
             return new DiagnosticResult(
                    name: "This member left the IXP " . Carbon::parse($this->customer->dateleave)->diffForHumans(),
                    result: DiagnosticResult::TYPE_ERROR,
                    narrative: "The member left the IXP on " . Carbon::parse($this->customer->dateleave)->format('Y-m-d'),
            );
        }

        return new DiagnosticResult(
            name: "This member has not left the IXP",
            result: DiagnosticResult::TYPE_TRACE,
            narrative: "The member has not left the IXP",
        );
    }


    /**
     * Examine the customer Route Server Client status and provide information on it.
     *
     * @param Customer $customer
     * @return DiagnosticResult
     * @throws GeneralException
     */
    public function customerRouteServerClient( Customer $customer ): DiagnosticResult {
        $mainName = 'Route Server Client: ';

        if($customer->routeServerClient() ) {
            return new DiagnosticResult(
                name: $mainName . 'Yes',
                result: DiagnosticResult::TYPE_DEBUG,
                narrative: "The member is a route server client",
            );
        }

        return new DiagnosticResult(
            name: $mainName . 'No',
            result: DiagnosticResult::TYPE_DEBUG,
            narrative: "The member is not a route server client",
        );

    }


}