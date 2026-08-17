<?php
/*
 * Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee.
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

declare(strict_types=1);

namespace IXP\Services\Validation\Validators;

use Carbon\CarbonInterval;
use IXP\Contracts\Validation\ValidationBackend;
use IXP\Contracts\Validation\Validator;
use IXP\Models\Infrastructure;
use IXP\Models\Router;
use IXP\Models\TaskLastRun;

/**
 * This validator checks
 */
class NagiosMonitoring implements Validator
{

    #[\Override]
    public function getName(): string
    {
        return "Nagios monitoring validator";
    }

    #[\Override]
    public function getDescription(): string
    {
        return "Checks that nagios configurations have been refreshed recently.";
    }

    #[\Override]
    public function getPriority(): int
    {
        return 14;
    }

    #[\Override]
    public function run( ValidationBackend $backend ): void
    {
        $warningIntervalDef = "24 hours";
        $warningInterval = CarbonInterval::fromString($warningIntervalDef);

        $lastRunNagiosCustomers = TaskLastRun::whereTaskKey( TaskLastRun::NAGIOS_CUSTOMERS )->get();
        if ( $lastRunNagiosCustomers->isEmpty() ) {
            $backend->suggestion( "Did you know IXP Manager can generate nagios configuration to monitor your customers VLAN interfaces?")
                ->withDocsPath("features/nagios/#monitoring-member-reachability" );
        } else {
            $backend->info("IXP Manager is generating customer VLAN monitoring configuration");
            foreach( $lastRunNagiosCustomers as $lastRun ) {
                if( $lastRun->last_run_at->lessThan(now()->sub($warningInterval))) {
                    $backend->warning("Nagios customer configuration (for VLAN ID #" . $lastRun->parameters['vlan'] .
                        " IPv" . $lastRun->parameters['protocol'] . " with template " . $lastRun->parameters['template'] .
                        ") has not been refreshed in over " . $warningIntervalDef . "!")
                        ->withDocsPath("features/nagios/#monitoring-member-reachability");
                }
            }
        }

        $lastRunNagiosSwitches = TaskLastRun::whereTaskKey( TaskLastRun::NAGIOS_SWITCHES )->get();
        if ( $lastRunNagiosSwitches->isEmpty() ) {
            $backend->suggestion( "Did you know IXP Manager can generate nagios configuration to monitor your switches in a given infrastructure?" )
                ->withDocsPath("features/nagios/#switch-monitoring");
        } else {
            $backend->info("IXP Manager is generating switch monitoring configuration");
            foreach( $lastRunNagiosSwitches as $lastRun ) {
                if( $lastRun->last_run_at->lessThan(now()->sub($warningInterval))) {
                    // This fallback is in case someone has deleted an old infrastructure, and we don't have its name
                    // anymore. See also the clear task history command.
                    $infra = Infrastructure::find($lastRun->parameters['infrastructure'])->name ?? "infrastructure ID #" . $lastRun->parameters['infrastructure'];
                    $backend->warning("Nagios switch configuration (for infrastructure " . $infra .
                        " with template " . $lastRun->parameters['template'] .
                        ") has not been refreshed in over " . $warningIntervalDef . "!" )
                        ->withDocsPath("features/nagios/#switch-monitoring");
                }
            }
        }

        $lastRunNagiosBirdseye = TaskLastRun::whereTaskKey( TaskLastRun::NAGIOS_BIRDSEYE_DAEMONS )->get();
        if ( $lastRunNagiosBirdseye->isEmpty() ) {
            $backend->suggestion( "Did you know IXP Manager can generate nagios configuration to monitor your birdseye daemons?")
                ->withDocsPath("features/nagios/#birdseye-daemon-monitoring");
        } else {
            $backend->info("IXP Manager is generating birdseye daemon monitoring configuration");
            foreach( $lastRunNagiosBirdseye as $lastRun ) {
                if( $lastRun->last_run_at->lessThan(now()->sub($warningInterval))) {
                    $backend->warning("Nagios birdseye daemon configuration (using template " . $lastRun->parameters['template'] .
                        ($lastRun->parameters['vlan'] !== null ? " and VLAN ID " . $lastRun->parameters['vlan'] : "") .
                        ") has not been refreshed in over " . $warningIntervalDef . "!")
                        ->withDocsPath("features/nagios/#birdseye-daemon-monitoring");
                }
            }
        }

        $lastRunNagiosBirdseyeBgpDaemon = TaskLastRun::whereTaskKey( TaskLastRun::NAGIOS_BIRDSEYE_BGP_SESSIONS )->get();
        if ( $lastRunNagiosBirdseyeBgpDaemon->isEmpty() ) {
            $backend->suggestion( "Did you know IXP Manager can generate nagios configuration to monitor your birdseye BGP sessions?")
                ->withDocsPath("features/nagios/#birdseye-bgp-session-monitoring");
        } else {
            $backend->info("IXP Manager is generating birdseye BGP session monitoring configuration");
            foreach( $lastRunNagiosBirdseyeBgpDaemon as $lastRun ) {
                if( $lastRun->last_run_at->lessThan(now()->sub($warningInterval))) {
                    $backend->warning("Nagios birdseye BGP session configuration (for ". Router::$TYPES[$lastRun->parameters['type']] .
                        "on VLAN id #".$lastRun->parameters['vlan'] . " IPv" . $lastRun->parameters['protocol'] .
                        " using template " . $lastRun->parameters['template'] .
                        ") has not been refreshed in over " . $warningIntervalDef . "!")
                        ->withDocsPath("features/nagios/#birdseye-bgp-session-monitoring");
                }
            }
        }
    }
}