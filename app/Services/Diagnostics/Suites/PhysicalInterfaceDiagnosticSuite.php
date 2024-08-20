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
    private SNMP|bool $snmpHost;

    public function __construct(
        private PhysicalInterface $pi,
    ) {
        $this->name        = 'Physical Interface #' . $pi->id;
        $this->description = 'Physical Interfaces general diagnostics.';
        $this->type        = 'INTERFACE';

        $this->vi = $pi->virtualInterface;

        if( !$pi->switchPort->switcher->snmppasswd || trim( $pi->switchPort->switcher->snmppasswd ) === '' ) {
            $this->snmpHost = false;
        } else {
            $this->snmpHost = new SNMP( $pi->switchPort->switcher->hostname, $pi->switchPort->switcher->snmppasswd );
        }

        parent::__construct();
    }

    /**
     * Run the diagnostics suite
     */
    public function run(): PhysicalInterfaceDiagnosticSuite
    {
        $this->results->add( $this->physicalInterfaceLastPoll() );
        $this->results->add( $this->physicalInterfaceMTU() );
        if(!$this->snmpHost) {

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
    private function physicalInterfaceLastPoll(): DiagnosticResult
    {
        $mainName = "Diagnostic tests";

        $lastPoll = $this->pi->switchPort->lastSnmpPoll;
        $diff = Carbon::now()->diffInDays(Carbon::parse($lastPoll));

        if ($diff >= 1 || is_null($this->pi->switchPort->lastSnmpPoll)) {
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
     * @return DiagnosticResult
     */
    private function physicalInterfaceMTU(): DiagnosticResult
    {
        $mainName = "MTU diagnostic";

        $viMtu = $this->vi->mtu;

        if ($this->pi->switchPort->ifMtu === $viMtu) {
            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_GOOD,
                narrative: "MTU values matching",
            );
        } else if ($this->pi->switchPort->ifMtu < $viMtu) {
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


    /**
     * Examine the physical interface operating and admin statuses and provide information on it.
     *
     * @return DiagnosticResult
     */
    private function physicalInterfaceOperating():  DiagnosticResult
    {
        $mainName = "Operating Status";

        try {

            $adminStatus = $this->snmpHost->useIface()->adminStates(1)[$this->pi->switchPort->ifIndex];
            $operationStatus = $this->snmpHost->useIface()->operationStates(1)[$this->pi->switchPort->ifIndex];

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

            $adminStatus = $this->snmpHost->useIface()->adminStates(1)[$this->pi->switchPort->ifIndex];

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

            $adminStatus = $this->snmpHost->useIface()->adminStates(1)[$this->pi->switchPort->ifIndex];

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

            $highSpeed = $this->snmpHost->useIface()->highSpeeds()[$this->pi->switchPort->ifIndex];

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