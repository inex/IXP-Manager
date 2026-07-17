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

namespace IXP\Utils\Validation\Validators;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use IXP\Contracts\Validation\ValidationBackend;
use IXP\Contracts\Validation\Validator;
use IXP\Models\Infrastructure;
use IXP\Models\Router;
use IXP\Models\TaskLastRun;
use IXP\Models\Vlan;

/**
 * This validator checks
 */
class NagiosMonitoringValidator implements Validator
{

    public function getName(): string
    {
        return "Nagios monitoring validator";
    }

    public function getDescription(): string
    {
        return "Checks that nagios configurations have been refreshed recently.";
    }

    public function getPriority(): int
    {
        return 14;
    }

    public function run( ValidationBackend $backend ): void
    {
        $warningIntervalDef = "24 seconds";
        $warningInterval = CarbonInterval::fromString($warningIntervalDef);

        $lastRunNagiosCustomers = TaskLastRun::whereTaskKey( TaskLastRun::NAGIOS_CUSTOMERS )->get();
        if ( $lastRunNagiosCustomers->isEmpty() ) {
            $backend->suggestion( "Did you know IXP-Manager can generate nagios configuration to monitor your customers VLAN interfaces?" );
        } else {
            foreach( $lastRunNagiosCustomers as $lastRun ) {
                if( $lastRun->last_run_at->lessThan(now()->sub($warningInterval))) {
                    $backend->warning("Nagios customer configuration (for VLAN ID #" . $lastRun->parameters['vlan'] .
                        " IPv" . $lastRun->parameters['protocol'] . " with template " . $lastRun->parameters['template'] .
                        ") has not been refreshed in over " . $warningIntervalDef . "!");
                }
            }
        }

        $lastRunNagiosSwitches = TaskLastRun::whereTaskKey( TaskLastRun::NAGIOS_SWITCHES )->get();
        if ( $lastRunNagiosSwitches->isEmpty() ) {
            $backend->suggestion( "Did you know IXP-Manager can generate nagios configuration to monitor your switches in a given infrastructure?" );
        } else {
            foreach( $lastRunNagiosSwitches as $lastRun ) {
                if( $lastRun->last_run_at->lessThan(now()->sub($warningInterval))) {
                    $infra = Infrastructure::find($lastRun->parameters['infrastructure'])->name ?? "infrastructure ID #" . $lastRun->parameters['infrastructure'];
                    $backend->warning("Nagios switch configuration (for infrastructure " . $infra .
                        " with template " . $lastRun->parameters['template'] .
                        ") has not been refreshed in over " . $warningIntervalDef . "!");
                }
            }
        }

        $lastRunNagiosBirdseye = TaskLastRun::whereTaskKey( TaskLastRun::NAGIOS_BIRDSEYE_DAEMONS )->get();
        if ( $lastRunNagiosBirdseye->isEmpty() ) {
            $backend->suggestion( "Did you know IXP-Manager can generate nagios configuration to monitor your birdseye daemons?" );
        } else {
            foreach( $lastRunNagiosBirdseye as $lastRun ) {
                if( $lastRun->last_run_at->lessThan(now()->sub($warningInterval))) {
                    $backend->warning("Nagios birdseye daemon configuration (using template " . $lastRun->parameters['template'] .
                        ($lastRun->parameters['vlan'] !== null ? " and VLAN ID " . $lastRun->parameters['vlan'] : "") .
                        ") has not been refreshed in over " . $warningIntervalDef . "!");
                }
            }
        }

        $lastRunNagiosBirdseyeBgpDaemon = TaskLastRun::whereTaskKey( TaskLastRun::NAGIOS_BIRDSEYE_BGP_SESSIONS )->get();
        if ( $lastRunNagiosBirdseyeBgpDaemon->isEmpty() ) {
            $backend->suggestion( "Did you know IXP-Manager can generate nagios configuration to monitor your birdseye BGP sessions?" );
        } else {
            foreach( $lastRunNagiosBirdseyeBgpDaemon as $lastRun ) {
                if( $lastRun->last_run_at->lessThan(now()->sub($warningInterval))) {
                    $backend->warning("Nagios birdseye BGP session configuration (for ". Router::$TYPES[$lastRun->parameters['type']] .
                        "on VLAN id #".$lastRun->parameters['vlan'] . " IPv" . $lastRun->parameters['protocol'] .
                        " using template " . $lastRun->parameters['template'] .
                        ") has not been refreshed in over " . $warningIntervalDef . "!");
                }
            }
        }
    }
}