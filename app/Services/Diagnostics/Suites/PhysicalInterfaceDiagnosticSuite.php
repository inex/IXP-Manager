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
use IXP\Services\Diagnostics\DiagnosticSuite;

use OSS_SNMP\Exception;
use OSS_SNMP\SNMP;

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

class PhysicalInterfaceDiagnosticSuite extends DiagnosticSuite
{
    public const string DIAGNOSTIC_SUITE_NAME = 'Physical Interfaces Overview';

    public const string DIAGNOSTIC_SUITE_DESCRIPTION = "Physical Interfaces overview diagnostics.";

    public const string DIAGNOSTIC_SUITE_TYPE = 'PHYSICAL_INTERFACE';

    private VirtualInterface $vi;

    private SNMP|bool $snmpClient;

    public function __construct(
        private PhysicalInterface $pi,
    ) {

        if( $pi?->switchPort ) {
            $this->name = $pi->switchPort->switcher->name . ' :: ' . $pi->switchPort->name . ' [Physical Interface #' . $pi->id . ']';
        } else {
            $this->name        = 'Physical Interface #' . $pi->id;
        }

        $this->description = 'Physical Interfaces general diagnostics.';
        $this->type        = 'INTERFACE';

        $this->vi = $pi->virtualInterface;

        if( empty( $pi?->switchPort->switcher->snmppasswd ) ) {
            $this->snmpClient = false;
        } else {
            $this->snmpClient = new SNMP( $pi->switchPort->switcher->hostname, $pi->switchPort->switcher->snmppasswd );
        }

        parent::__construct();
    }

    /**
     * Run the diagnostics suite
     */
    public function run(): PhysicalInterfaceDiagnosticSuite
    {
        $this->results->add( $this->switchportLastPoll() );
        $this->results->add( $this->switchportCanPoll() );

        $this->results->add( $this->physicalInterfaceMtu() );

        if(!$this->snmpClient) {

            $this->results->add(
                new DiagnosticResult(
                    name: "SNMP diagnostics",
                    result: DiagnosticResult::TYPE_WARN,
                    narrative: "No SNMP host - diagnostics not available",
                )
            );

        } else {

            $this->results->add( $this->physicalInterfaceOperating() );
            $this->results->add( $this->physicalInterfaceSwitchPortStatus() );
            $this->results->add( $this->physicalInterfaceAdminStatus() );
            $this->results->add( $this->physicalInterfaceSwitchSpeed() );

        }

        return $this;
    }


    private function snmpReachError($mainName) {
        return new DiagnosticResult(
            name: $mainName,
            result: DiagnosticResult::TYPE_WARN,
            narrative: "SNMP host cannot be reached",
        );
    }

    /**
     * Examine the physical interface last poll and provide information on it.
     *
     * @return DiagnosticResult
     */
    private function switchportLastPoll(): DiagnosticResult
    {
        $mainName = "Switch port information current?";

        $lastPolled = Carbon::parse( $this->pi->switchPort->lastSnmpPoll );

        if( now()->diffInHours( $lastPolled ) >= 1 || is_null($this->pi->switchPort->lastSnmpPoll ) ) {
            return new DiagnosticResult(
                name: $mainName . " No, last polled: " . $lastPolled ? $lastPolled->diffForHumans() : 'never',
                result: DiagnosticResult::TYPE_WARN,
                narrative: "No, last polled: " . $lastPolled ? $lastPolled->diffForHumans() : 'never',
            );
        }

        return new DiagnosticResult(
            name: $mainName . " Yes, last polled: " . $lastPolled->diffForHumans(),
            result: DiagnosticResult::TYPE_GOOD,
            narrative: "SNMP information has been recently retrieved for this port.",
        );
    }

    /**
     * We want to poll the port now to (a) make sure we can and (b) use live data
     * for the remaining tests without making multiple snmp get requests.
     *
     * @return DiagnosticResult
     */
    private function switchportCanPoll(): DiagnosticResult
    {
        $mainName = "Can poll switch port via snmp?";

        $before = $this->pi->switchPort->lastSnmpPoll;

        while( $before === now()->format('Y-m-d H:i:s') ) {
            sleep(1);
        }

        $this->pi->switchPort->snmpUpdate( $this->snmpClient );

        if( $before !== $this->pi->switchPort->lastSnmpPoll->format('Y-m-d H:i:s') ) {
            return new DiagnosticResult(
                name: $mainName . " Yes, polled successfully",
                result: DiagnosticResult::TYPE_GOOD,
                narrative: "SNMP information has been retrieved for this port.",
            );
        }

        return new DiagnosticResult(
            name: $mainName . " No, could not poll the switch port",
            result: DiagnosticResult::TYPE_FATAL,
            narrative: "As we could not poll the switch port via SNMP, all other diagnostics tests relying on this information may not be accurate.",
        );

    }

    /**
     * Examine the physical interface mtu and provide information on it.
     *
     * @return DiagnosticResult
     */
    private function physicalInterfaceMtu(): DiagnosticResult
    {
        $mainName = "MTU - ";

        if ( $this->pi->switchPort->ifMtu < 1500 ) {

            return new DiagnosticResult(
                name: $mainName . "switch port is reporting a MTU of {$this->pi->switchPort->ifMtu} which is <1500",
                result: DiagnosticResult::TYPE_FATAL,
                narrative: "Switch port is reporting a MTU of <1500",
            );

        } else if( $this->pi->switchPort->ifMtu === $this->pi->virtualInterface->mtu ) {

            return new DiagnosticResult(
                name: $mainName . "both set to {$this->pi->virtualInterface->mtu}",
                result: DiagnosticResult::TYPE_GOOD,
                narrative: "Switch port matches configured MTU of {$this->pi->virtualInterface->mtu}",
            );

        } else if ( !$this->pi->virtualInterface->mtu ) {

            return new DiagnosticResult(
                name: $mainName ."configured as null/0 but switch port reports " . $this->pi->switchPort->ifMtu ?? 'null',
                result: DiagnosticResult::TYPE_INFO,
                narrative: "Configured MTU is null/0 but switch port reports " . $this->pi->switchPort->ifMtu ?? 'null',
            );

        }

        return new DiagnosticResult(
            name: $mainName . ( $this->pi->virtualInterface->mtu ?? 'null' ) . " configured but switch port reports ({$this->pi->switchPort->ifMtu})",
            result: DiagnosticResult::TYPE_ERROR,
            narrative: "Configured MTU of {$this->pi->virtualInterface->mtu} does not match the switch port MTU of {$this->pi->switchPort->ifMtu}",
        );

    }


    /**
     * Examine the physical interface operating and admin statuses and provide information on it.
     *
     * @return DiagnosticResult
     */
    private function physicalInterfaceOperating():  DiagnosticResult
    {
        $mainName = "Operating Status";

        try {

            $adminStatus = $this->snmpClient->useIface()->adminStates(1)[ $this->pi->switchPort->ifIndex];
            $operationStatus = $this->snmpClient->useIface()->operationStates(1)[ $this->pi->switchPort->ifIndex];

            if ($adminStatus !== $operationStatus) {

                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_WARN,
                    narrative: "Operating status failed",
                );

            } else {

                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_GOOD,
                    narrative: "Operating status good",
                );


            }

        } catch(Exception $exception) {
            info("Issue\n".$exception);

            return $this->snmpReachError($mainName);

        }

    }


    /**
     * Examine the switch port status and provide information on it.
     *
     * @return DiagnosticResult
     */
    private function physicalInterfaceSwitchPortStatus():  DiagnosticResult
    {
        $mainName = "Switch Port Status";

        try {

            $adminStatus = $this->snmpClient->useIface()->adminStates(1)[ $this->pi->switchPort->ifIndex];

            if ($adminStatus == "up" && $this->pi->switchPort->active == 1) {

                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_GOOD,
                    narrative: "The physical interface switch port is up and running",
                );

            } else if ($adminStatus != "up" && $this->pi->switchPort->active != 1) {

                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_WARN,
                    narrative: "The physical interface switch port is down",
                );

            } else {

                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_WARN,
                    narrative: "The physical interface switch port status is not match to the database",
                );

            }

        } catch(Exception $exception) {
            info("Issue\n".$exception);

            return $this->snmpReachError($mainName);

        }

    }


    /**
     * Examine the physical interface status and provide information on it.
     *
     * @return DiagnosticResult
     */
    private function physicalInterfaceAdminStatus():  DiagnosticResult
    {
        $mainName = "Physical Interface Status";

        try {

            $adminStatus = $this->snmpClient->useIface()->adminStates(1)[ $this->pi->switchPort->ifIndex];

            if ($adminStatus == "up" && $this->pi->status == 1) {

                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_GOOD,
                    narrative: "The physical interface is up and running",
                );

            } else if ($adminStatus != "up" && $this->pi->status != 1) {

                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_WARN,
                    narrative: "The physical interface is down",
                );

            } else {

                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_WARN,
                    narrative: "The physical interface admin status is not match to the database",
                );

            }

        } catch(Exception $exception) {
            info("Issue\n".$exception);

            return $this->snmpReachError($mainName);

        }

    }


    /**
     * Examine the Switch Port physical speed and provide information on it.
     *
     * @return DiagnosticResult
     */
    private function physicalInterfaceSwitchSpeed():  DiagnosticResult
    {
        $mainName = "Switch Port physical speed diagnostics";

        try {

            $highSpeed = $this->snmpClient->useIface()->highSpeeds()[ $this->pi->switchPort->ifIndex];

            if ($highSpeed == $this->pi->speed) {

                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_GOOD,
                    narrative: "The speed of the physical interface match the database data",
                );

            } else {

                return new DiagnosticResult(
                    name: $mainName,
                    result: DiagnosticResult::TYPE_WARN,
                    narrative: 'The speed of the physical interface don\'t match the database data',
                );

            }

        } catch(Exception $exception) {
            info("Issue\n".$exception);

            return $this->snmpReachError($mainName);

        }

    }



}