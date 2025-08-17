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
 * Software Foundation, version v2.0 of the Licensefinal .
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
use OSS_SNMP\MIBS\Iface;
use OSS_SNMP\MIBS\MAU;
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

    private SNMP|bool|null $snmpClient = null;

    private bool $stale = true;

    private static string $badgeStale = '<span class="tw-inline-flex tw-items-center tw-rounded-full tw-bg-red-50   tw-px-1.5 tw-py-0.5 tw-text-xs tw-font-medium tw-text-red-700   tw-ring-1 tw-ring-inset tw-ring-red-600/10"  >Stale</span>';
    private static string $badgeLive  = '<span class="tw-inline-flex tw-items-center tw-rounded-full tw-bg-green-50 tw-px-1.5 tw-py-0.5 tw-text-xs tw-font-medium tw-text-green-700 tw-ring-1 tw-ring-inset tw-ring-green-600/20">Live</span>';



    public function __construct(
        private PhysicalInterface $pi,
    ) {

        if( $pi?->switchPort ) {
            $this->name = $pi->switchPort->switcher->name . ' :: ' . $pi->switchPort->name . ' / Physical Interface #' . $pi->id;
        } else {
            $this->name        = 'Physical Interface #' . $pi->id;
        }

        $this->description = 'Physical Interfaces general diagnostics.';
        $this->type        = 'INTERFACE';
        $this->link        = route( 'virtual-interface@edit', ['vi' => $pi->virtualInterface] );;

        parent::__construct();
    }

    /**
     * Get / instantiate the snmp client
     *
     * @return SNMP|bool
     */
    private function snmpClient(PhysicalInterface $pi): bool|SNMP|null {

        if( $this->snmpClient === null ) {
            if( empty( $pi?->switchPort->switcher->snmppasswd ) ) {
                $this->snmpClient = false;
            } else {
                $this->snmpClient = new SNMP( $pi->switchPort->switcher->hostname, $pi->switchPort->switcher->snmppasswd );
            }
        }

        return $this->snmpClient;
    }


    /**
     * Run the diagnostics suite
     */
    public function run(): PhysicalInterfaceDiagnosticSuite
    {
        $this->results->add( $this->switchportLastPoll() );

        // We want to poll the port now to (a) make sure we can and (b) use live data
        // for the remaining tests without making multiple snmp get requests.
        $this->results->add( $this->switchportCanPoll() );

        $this->results->add( new DiagnosticResult(
            name: "Switch port last change registered "
                . ( $this->pi->switchPort->ifLastChange ? Carbon::parse($this->pi->switchPort->ifLastChange)->diffForHumans() : 'never' ),
            result: DiagnosticResult::TYPE_DEBUG,
            narrative: "Switch port last change counter: "
                . ( $this->pi->switchPort->ifLastChange ? Carbon::parse($this->pi->switchPort->ifLastChange)->format('Y-m-d H:i:s') : 'never' ),
            infoBadge: $this->stale ? self::$badgeStale : self::$badgeLive
        ) );

        $this->results->add( $this->mtu() );

        $this->results->add( $this->adminStatus() );
        $this->results->add( $this->operatingStatus() );
        $this->results->add( $this->switchPortActive() );
        $this->results->add( $this->speed() );
        $this->results->add( $this->mauState() );


        return $this;
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
            result: DiagnosticResult::TYPE_DEBUG,
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

        try {

            $this->pi->switchPort->snmpUpdate( $this->snmpClient($this->pi) );

            if( $before !== $this->pi->switchPort->lastSnmpPoll->format('Y-m-d H:i:s') ) {
                $this->stale = false;
                return new DiagnosticResult(
                    name: $mainName . " Yes, refreshed successfully now",
                    result: DiagnosticResult::TYPE_DEBUG,
                    narrative: "SNMP information has been retrieved for this port.",
                );
            }

            $this->stale = true;
            return new DiagnosticResult(
                name: $mainName . " No, could not poll the switch port",
                result: DiagnosticResult::TYPE_FATAL,
                narrative: "As we could not poll the switch port via SNMP, all other diagnostics tests relying on this information may not be accurate.",
            );

        } catch(\Exception $e) {

            return new DiagnosticResult(
                name: $mainName . ' - diagnostic failed to run',
                result: DiagnosticResult::TYPE_UNKNOWN,
                narrativeHtml: $e->getMessage(),
            );

        }

    }

    /**
     * Examine the physical interface mtu and provide information on it.
     *
     * @return DiagnosticResult
     */
    private function mtu(): DiagnosticResult
    {
        $mainName = " MTU - ";

        if ( $this->pi->switchPort->ifMtu < 1500 ) {

            return new DiagnosticResult(
                name: $mainName . "switch port is reporting a MTU of {$this->pi->switchPort->ifMtu} which is <1500",
                result: DiagnosticResult::TYPE_FATAL,
                narrative: "Switch port is reporting a MTU of <1500",
                infoBadge: $this->stale ? self::$badgeStale : self::$badgeLive
            );

        } else if( $this->pi->switchPort->ifMtu === $this->pi->virtualInterface->mtu ) {

            return new DiagnosticResult(
                name: $mainName . "both set to {$this->pi->virtualInterface->mtu}",
                result: DiagnosticResult::TYPE_DEBUG,
                narrative: "Switch port matches configured MTU of {$this->pi->virtualInterface->mtu}",
                infoBadge: $this->stale ? self::$badgeStale : self::$badgeLive
            );

        } else if ( !$this->pi->virtualInterface->mtu ) {

            return new DiagnosticResult(
                name: $mainName ."configured as null/0 but switch port reports " . $this->pi->switchPort->ifMtu ?: 'null',
                result: DiagnosticResult::TYPE_INFO,
                narrative: "Configured MTU is null/0 but switch port reports " . $this->pi->switchPort->ifMtu ?: 'null',
                infoBadge: $this->stale ? self::$badgeStale : self::$badgeLive
            );

        }

        return new DiagnosticResult(
            name: $mainName . ( $this->pi->virtualInterface->mtu ?? 'null' ) . " configured but switch port reports ({$this->pi->switchPort->ifMtu})",
            result: DiagnosticResult::TYPE_ERROR,
            narrative: "Configured MTU of {$this->pi->virtualInterface->mtu} does not match the switch port MTU of {$this->pi->switchPort->ifMtu}",
            infoBadge: $this->stale ? self::$badgeStale : self::$badgeLive
        );

    }




    /**
     * Examine the physical interface status and provide information on it.
     *
     * @return DiagnosticResult
     */
    private function adminStatus():  DiagnosticResult
    {
        $mainName = 'Admin status: ' . ( $this->pi->status ? 'Enabled' : 'Disabled' )
            . " in IXP Manager; Switch port configured as " . Iface::$IF_ADMIN_STATES[$this->pi->switchPort->ifAdminStatus];

        if( $this->pi->status && $this->pi->switchPort->ifAdminStatus != Iface::IF_ADMIN_STATUS_UP ) {

            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_ERROR,
                narrative: "The switch configuration state of the switch port is not up - perhaps it is shutdown/disabled in switch configuration? "
                    . "However, the configuration of the physical interface on IXP Manager is enabled.",
                infoBadge: $this->stale ? self::$badgeStale : self::$badgeLive
            );

        } else if( !$this->pi->status && $this->pi->switchPort->ifAdminStatus == Iface::IF_ADMIN_STATUS_UP ) {

            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_ERROR,
                narrative: "The switch configuration state of the switch port is up. "
                    . "However, the configuration of the physical interface on IXP Manager is disabled.",
                infoBadge: $this->stale ? self::$badgeStale : self::$badgeLive
            );

        } else if( $this->pi->status && $this->pi->switchPort->ifAdminStatus == Iface::IF_ADMIN_STATUS_UP ) {

            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_DEBUG,
                narrative: "The physical interface admin status is up.",
                infoBadge: $this->stale ? self::$badgeStale : self::$badgeLive
            );

        }

        return new DiagnosticResult(
            name: $mainName,
            result: DiagnosticResult::TYPE_WARN,
            narrative: "Unknown administrative (configuration) state on switch.",
            infoBadge: $this->stale ? self::$badgeStale : self::$badgeLive
        );

    }


    /**
     * Examine the physical interface status and provide information on it.
     *
     * @return DiagnosticResult
     */
    private function switchPortActive():  DiagnosticResult
    {
        $mainName = 'Within IXP Manager, the phsyical interface is '
            . ( $this->pi->status ? 'enabled' : 'disabled' )
            . ' and the switchport is '
            . ( $this->pi->switchPort->active ? 'active' : 'inactive' );

        if( $this->pi->status != $this->pi->switchPort->active ) {

            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_ERROR,
                narrative: "IXP Manager is configured with conflicted state for physical interface and switch port.",
                infoBadge: $this->stale ? self::$badgeStale : self::$badgeLive
            );

        }

        return new DiagnosticResult(
            name: $mainName,
            result: DiagnosticResult::TYPE_DEBUG,
            narrative: "IXP Manager is configured with a consistent state for physical interface and switch port.",
            infoBadge: $this->stale ? self::$badgeStale : self::$badgeLive
        );

    }



    /**
     * Examine the physical interface status and provide information on it.
     *
     * @return DiagnosticResult
     */
    private function operatingStatus():  DiagnosticResult
    {
        $mainName = 'Operating status: ' . ( $this->pi->status ? 'Enabled' : 'Disabled' )
            . " in IXP Manager; Switch port reports as " . Iface::$IF_OPER_STATES[$this->pi->switchPort->ifOperStatus];

        if( $this->pi->status && $this->pi->switchPort->ifOperStatus != Iface::IF_OPER_STATUS_UP ) {

            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_ERROR,
                narrative: "The switch port is not up - perhaps it is shutdown/disabled in switch configuration or disconnected or no light rx? "
                    . "The configuration of the physical interface on IXP Manager is enabled.",
                infoBadge: $this->stale ? self::$badgeStale : self::$badgeLive
            );

        } else if( !$this->pi->status && $this->pi->switchPort->ifOperStatus == Iface::IF_OPER_STATUS_UP ) {

            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_ERROR,
                narrative: "The switch port is up. "
                    . "However, the configuration of the physical interface on IXP Manager is disabled.",
                infoBadge: $this->stale ? self::$badgeStale : self::$badgeLive
            );

        } else if( $this->pi->status && $this->pi->switchPort->ifOperStatus == Iface::IF_OPER_STATUS_UP ) {

            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_GOOD,
                narrative: "The switch port is up/up.",
                infoBadge: $this->stale ? self::$badgeStale : self::$badgeLive
            );

        }

        return new DiagnosticResult(
            name: $mainName,
            result: DiagnosticResult::TYPE_WARN,
            narrative: "Unknown port operating state on switch.",
            infoBadge: $this->stale ? self::$badgeStale : self::$badgeLive
        );

    }



    /**
     * Examine the Switch Port physical speed and provide information on it.
     *
     * @return DiagnosticResult
     */
    private function speed():  DiagnosticResult
    {
        $mainName = "Switch port speed configured as {$this->pi->speed()}; actual switch port speed: " . ( PhysicalInterface::$SPEED[$this->pi->switchPort->ifSpeed] ?? $this->pi->switchPort->ifSpeed ?? 'null' );

        if( $this->pi->switchPort->ifSpeed == $this->pi->speed ) {

            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_DEBUG,
                narrative: "The configured and actual switch port speeds match",
                infoBadge: $this->stale ? self::$badgeStale : self::$badgeLive
            );

        } else {

            return new DiagnosticResult(
                name: $mainName,
                result: DiagnosticResult::TYPE_ERROR,
                narrative: "The configured and actual switch port speeds DO NOT match",
                infoBadge: $this->stale ? self::$badgeStale : self::$badgeLive
            );

        }

    }

    /**
     * Examine the Switch Port physical speed and provide information on it.
     *
     * @return DiagnosticResult[]
     *
     * @psalm-return list{0: DiagnosticResult, 1?: DiagnosticResult, 2?: DiagnosticResult}
     */
    private function mauState():  array
    {
        $results = [];

        if( !$this->pi->switchPort->switcher->mauSupported ) {

            return [ new DiagnosticResult(
                name: "Switch does not support MAU (optic) information via SNMP",
                result: DiagnosticResult::TYPE_INFO,
                narrative: "Switch does not support MAU (optic) information via SNMP",
                infoBadge: $this->stale ? self::$badgeStale : self::$badgeLive
            ) ];

        }


        $results[] = new DiagnosticResult(
            name: "Switch supports MAU (optic) information via SNMP",
            result: DiagnosticResult::TYPE_TRACE,
            narrative: "Switch supports MAU (optic) information via SNMP",
            infoBadge: $this->stale ? self::$badgeStale : self::$badgeLive
        );

        $results[] = new DiagnosticResult(
            name: "MAU type:  " . $this->pi->switchPort->mauType . ( $this->pi->switchPort->mauJacktype ? '(jack type: ' . $this->pi->switchPort->mauJacktype . ')' : '' ),
            result: DiagnosticResult::TYPE_INFO,
            narrative: "MAU type:  " . $this->pi->switchPort->mauType . ( $this->pi->switchPort->mauJacktype ? '(jack type: ' . $this->pi->switchPort->mauJacktype . ')' : '' ),
            infoBadge: $this->stale ? self::$badgeStale : self::$badgeLive
        );

        $results[] = new DiagnosticResult(
            name: "MAU state:  " . $this->pi->switchPort->mauState,
            result: DiagnosticResult::TYPE_DEBUG,
            narrative: "MAU state:  " . $this->pi->switchPort->mauState,
            infoBadge: $this->stale ? self::$badgeStale : self::$badgeLive
        );


        return $results;

    }



}