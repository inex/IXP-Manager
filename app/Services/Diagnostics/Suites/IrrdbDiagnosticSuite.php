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

use Exception;
use IXP\Exceptions\GeneralException;
use IXP\IXP;
use IXP\Models\Customer;
use IXP\Models\IrrdbAsn;
use IXP\Models\IrrdbPrefix;
use IXP\Models\IrrdbUpdateLog;
use IXP\Services\Diagnostics\DiagnosticResult;
use IXP\Services\Diagnostics\Suite;

/**
 * Diagnostics Service - Customer IRRDB Suite
 *
 * @author     Barry O'Donovan  <barry@opensolutions.ie>
 * @author     Laszlo Kiss      <laszlo@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Services\Diagnostics
 * @copyright  Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */

class IrrdbDiagnosticSuite extends Suite
{

    public function __construct(
        private readonly Customer $customer
    ) {
        $this->name        = 'IRRDB Filtering';
        $this->description = "Diagnostics related to IRRDB filtering.";
        $this->type        = 'CUSTOMER';
    }

    /**
     * Run the diagnostics suite
     * @throws GeneralException
     */
    public function run(): IrrdbDiagnosticSuite
    {
        // ordering here will determine order on view
        $this->results[] = $this->customerIrrdbFiltered();

        if( $this->customer->routeServerClient() && $this->customer->irrdbFiltered() ) {

            $this->results[] = $this->customerIrrdbAsnsPresent( IXP::IPv4 );
            $this->results[] = $this->customerIrrdbAsnsPresent( IXP::IPv6 );
            $this->results[] = $this->customerIrrdbPrefixesPresent( IXP::IPv4 );
            $this->results[] = $this->customerIrrdbPrefixesPresent( IXP::IPv6 );
        }

        return $this;
    }


    /**
     * Examine the customer IRRDB filtering status and provide information on it.
     *
     * @return DiagnosticResult
     * @throws GeneralException
     */
    private function customerIrrdbFiltered(): DiagnosticResult {
        $mainName = "IRRDB Filtered Status";

        if( !$this->customer->routeServerClient() ) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_INFO,
                narrative: "The member is not a route server client so IRRDB filtering not considered",
            );
        }

        if( $this->customer->fullyIrrdbFiltered() ) {

            if($this->customer->irrdbMoreSpecificsAllowed()) {
                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_WARN,
                    narrative: "The member is IRRDB filtered but note that more specific prefixes are allowed on at least one VLAN interface",
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
            narrative: "The member is a route server client but is not IRRDB filtered on at least one VLAN interface",
        );
    }


    /**
     * Examine the customer IRRDB filtering status and provide information on it.
     *
     * @param int $proto
     * @return DiagnosticResult
     * @throws GeneralException
     */
    private function customerIrrdbAsnsPresent( int $proto ): DiagnosticResult {
        $mainName = "IRRDB - " . IXP::protocol($proto) . " ASNs Present and Current";

        if( !$this->customer->isIPvXEnabled($proto) ) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_TRACE,
                narrative: IXP::protocol($proto) . ' not enabled for this member',
            );
        }

        try {
            $irrdblog = IrrdbUpdateLog::where( [ 'cust_id' => $this->customer->id ] )->firstOrFail();

            $m = 'asn_v' . $proto;

            if( $irrdblog->$m === null ) {
                // the exception is irrelevant as we just want to catch and send a diagnostic result.
                throw new Exception();
            }

        } catch( Exception $e ) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_ERROR,
                narrative: "IRRDB ASNs have never been updated for " . IXP::protocol($proto),
            );
        }

        $count = IrrdbAsn::where( 'customer_id', $this->customer->id )
            ->where( 'protocol', $proto )
            ->count();

        if( $count === 0 ) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_ERROR,
                narrative: "There are zero IRRDB ASNs for " . IXP::protocol($proto) . " (last update was " . $irrdblog->$m->diffForHumans() . ")",
            );
        }

        if( $irrdblog->$m < now()->subDay() ) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_WARN,
                narrative: "IRRDB ASNs (x{$count}) for " . IXP::protocol($proto) . " have not been updated since " . $irrdblog->$m->diffForHumans(),
            );
        }


        return new DiagnosticResult(
            name: $mainName,
            result: DiagnosticResult::TYPE_GOOD,
            narrative: "IRRDB ASNs (x{$count}) for " . IXP::protocol($proto) . " last updated " . $irrdblog->$m->diffForHumans(),
        );

    }


    /**
     * Examine the customer IRRDB filtering status and provide information on it.
     *
     * @param int $proto
     * @return DiagnosticResult
     * @throws GeneralException
     */
    private function customerIrrdbPrefixesPresent( int $proto ): DiagnosticResult {
        $mainName = "IRRDB - " . IXP::protocol($proto) . " Prefixes Present and Current";

        if( !$this->customer->isIPvXEnabled($proto) ) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_TRACE,
                narrative: IXP::protocol($proto) . ' not enabled for this member',
            );
        }

        try {
            $irrdblog = IrrdbUpdateLog::where( [ 'cust_id' => $this->customer->id ] )->firstOrFail();

            $m = 'prefix_v' . $proto;

            if( $irrdblog->$m === null ) {
                // the exception is irrelevant as we just want to catch and send a diagnostic result.
                throw new Exception();
            }
        } catch( Exception $e ) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_ERROR,
                narrative: "IRRDB prefixes have never been updated for " . IXP::protocol($proto),
            );
        }

        $count = IrrdbPrefix::where( 'customer_id', $this->customer->id )
            ->where( 'protocol', $proto )
            ->count();

        if( $count === 0 ) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_ERROR,
                narrative: "There are zero IRRDB prefixes for " . IXP::protocol($proto) . " (last update was " . $irrdblog->$m->diffForHumans() . ")",
            );
        }

        if( $irrdblog->$m < now()->subDay() ) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_WARN,
                narrative: "IRRDB prefixes (x{$count}) for " . IXP::protocol($proto) . " have not been updated since " . $irrdblog->$m->diffForHumans(),
            );
        }


        return new DiagnosticResult(
            name: $mainName,
            result: DiagnosticResult::TYPE_GOOD,
            narrative: "IRRDB prefixes (x{$count}) for " . IXP::protocol($proto) . " last updated " . $irrdblog->$m->diffForHumans(),
        );

    }



}