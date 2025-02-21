<?php
/** @noinspection UnknownColumnInspection */

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
use IXP\Services\Diagnostics\DiagnosticSuite;

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

class IrrdbDiagnosticSuite extends DiagnosticSuite
{

    public function __construct(
        private readonly Customer $customer
    ) {
        $this->name        = 'IRRDB Filtering';
        $this->description = "Diagnostics related to IRRDB filtering.";
        $this->type        = 'CUSTOMER';

        parent::__construct();
    }

    /**
     * Run the diagnostics suite
     * @throws GeneralException
     */
    public function run(): IrrdbDiagnosticSuite
    {
        // ordering here will determine order on view
        $this->results->add( $this->customerIrrdbFiltered( $this->customer ) );

        if( $this->customer->routeServerClient() && $this->customer->irrdbFiltered() ) {
            $this->results->add( $this->customerIrrdbAsnsPresent( $this->customer,IXP::IPv4 ) );
            $this->results->add( $this->customerIrrdbAsnsPresent( $this->customer,IXP::IPv6 ) );
            $this->results->add( $this->customerIrrdbPrefixesPresent( $this->customer,IXP::IPv4 ) );
            $this->results->add( $this->customerIrrdbPrefixesPresent( $this->customer,IXP::IPv6 ) );
        }

        return $this;
    }


    /**
     * Examine the customer IRRDB filtering status and provide information on it.
     *
     * @param Customer $customer
     * @return DiagnosticResult
     * @throws GeneralException
     */
    public function customerIrrdbFiltered( Customer $customer ): DiagnosticResult {

        if( !$this->customer->routeServerClient() ) {
            return new DiagnosticResult(
                name: 'IRRDB Filtering: not a route server client so no IRRDB filtering',
                result: DiagnosticResult::TYPE_DEBUG,
                narrative: "The member is not a route server client so IRRDB filtering not considered",
            );
        }

        if($customer->fullyIrrdbFiltered() ) {

            if($this->customer->irrdbMoreSpecificsAllowed()) {
                return new DiagnosticResult(
                    name: 'IRRDB Filtering: yes but more specifics allowed',
                    result: DiagnosticResult::TYPE_INFO,
                    narrative: "The member is IRRDB filtered but note that more specific prefixes are allowed on at least one VLAN interface",
                );
            }

            return new DiagnosticResult(
                name: 'IRRDB Filtering: yes',
                result: DiagnosticResult::TYPE_DEBUG,
                narrative: "The member is IRRDB filtered",
            );

        }

        return new DiagnosticResult(
            name: 'IRRDB Filtering: no, route server sessions not secured with IRRDB',
            result: DiagnosticResult::TYPE_ERROR,
            narrative: "The member is a route server client but is not IRRDB filtered on at least one VLAN interface",
        );
    }


    /**
     * Examine the customer IRRDB filtering status and provide information on it.
     *
     * @param Customer $customer
     * @param int $proto
     * @return DiagnosticResult
     * @throws GeneralException
     */
    public function customerIrrdbAsnsPresent( Customer $customer, int $proto ): DiagnosticResult {

        if( !$this->customer->isIPvXEnabled($proto) ) {
            return new DiagnosticResult(
                name: "No IRRDB ASNs as " . IXP::protocol($proto) . ' not enabled for this member',
                result: DiagnosticResult::TYPE_TRACE,
                narrative: IXP::protocol($proto) . ' not enabled for this member',
            );
        }

        try {
            $irrdblog = IrrdbUpdateLog::where( [ 'cust_id' =>$customer->id ] )->firstOrFail();

            $m = 'asn_v' . $proto;

            if( $irrdblog->$m === null ) {
                // the exception is irrelevant as we just want to catch and send a diagnostic result.
                throw new Exception();
            }

        } catch( Exception ) {
            return new DiagnosticResult(
                name: "IRRDB ASNs have never been updated for " . IXP::protocol($proto),
                result: DiagnosticResult::TYPE_ERROR,
                narrative: "IRRDB ASNs have never been updated for " . IXP::protocol($proto),
            );
        }

        $count = IrrdbAsn::where( 'customer_id',$customer->id )
            ->where( 'protocol', $proto )
            ->count();

        if( $count === 0 ) {
            return new DiagnosticResult(
                name: "Zero IRRDB ASNs for " . IXP::protocol($proto) . "(last update was " . $irrdblog->$m->diffForHumans() . ")",
                result: DiagnosticResult::TYPE_ERROR,
                narrative: "There are zero IRRDB ASNs for " . IXP::protocol($proto) . " (last update was " . $irrdblog->$m->diffForHumans() . ")",
            );
        }

        if( $irrdblog->$m < now()->subDay() ) {
            return new DiagnosticResult(
                name: "IRRDB ASNs (x{$count}) for " . IXP::protocol($proto) . " have not been updated since " . $irrdblog->$m->diffForHumans(),
                result: DiagnosticResult::TYPE_WARN,
                narrative: "IRRDB ASNs (x{$count}) for " . IXP::protocol($proto) . " have not been updated since " . $irrdblog->$m->diffForHumans(),
            );
        }


        return new DiagnosticResult(
            name: "IRRDB ASNs (x{$count}) for " . IXP::protocol($proto) . " last updated " . $irrdblog->$m->diffForHumans(),
            result: DiagnosticResult::TYPE_GOOD,
            narrative: "IRRDB ASNs (x{$count}) for " . IXP::protocol($proto) . " last updated " . $irrdblog->$m->diffForHumans(),
        );

    }


    /**
     * Examine the customer IRRDB filtering status and provide information on it.
     *
     * @param Customer $customer
     * @param int $proto
     * @return DiagnosticResult
     * @throws GeneralException
     */
    public function customerIrrdbPrefixesPresent( Customer $customer , int $proto ): DiagnosticResult {

        if( !$this->customer->isIPvXEnabled($proto) ) {
            return new DiagnosticResult(
                name: "No " . IXP::protocol($proto) . " IRRDB prefixes, " . IXP::protocol($proto) . ' not enabled for this member',
                result: DiagnosticResult::TYPE_TRACE,
                narrative: IXP::protocol($proto) . ' not enabled for this member',
            );
        }

        try {
            $irrdblog = IrrdbUpdateLog::where( [ 'cust_id' =>$customer->id ] )->firstOrFail();

            $m = 'prefix_v' . $proto;

            if( $irrdblog->$m === null ) {
                // the exception is irrelevant as we just want to catch and send a diagnostic result.
                throw new Exception();
            }
        } catch( Exception ) {
            return new DiagnosticResult(
                name: "IRRDB prefixes have never been updated for " . IXP::protocol($proto),
                result: DiagnosticResult::TYPE_ERROR,
                narrative: "IRRDB prefixes have never been updated for " . IXP::protocol($proto),
            );
        }

        $count = IrrdbPrefix::where( 'customer_id',$customer->id )
            ->where( 'protocol', $proto )
            ->count();

        if( $count === 0 ) {
            return new DiagnosticResult(
                name: "There are zero IRRDB prefixes for " . IXP::protocol($proto) . " (last update was " . $irrdblog->$m->diffForHumans() . ")",
                result: DiagnosticResult::TYPE_ERROR,
                narrative: "There are zero IRRDB prefixes for " . IXP::protocol($proto) . " (last update was " . $irrdblog->$m->diffForHumans() . ")",
            );
        }

        if( $irrdblog->$m < now()->subDay() ) {
            return new DiagnosticResult(
                name: "IRRDB prefixes (x{$count}) for " . IXP::protocol($proto) . " have not been updated since " . $irrdblog->$m->diffForHumans(),
                result: DiagnosticResult::TYPE_WARN,
                narrative: "IRRDB prefixes (x{$count}) for " . IXP::protocol($proto) . " have not been updated since " . $irrdblog->$m->diffForHumans(),
            );
        }


        return new DiagnosticResult(
            name: "IRRDB prefixes (x{$count}) for " . IXP::protocol($proto) . " last updated " . $irrdblog->$m->diffForHumans(),
            result: DiagnosticResult::TYPE_GOOD,
            narrative: "IRRDB prefixes (x{$count}) for " . IXP::protocol($proto) . " last updated " . $irrdblog->$m->diffForHumans(),
        );

    }



}