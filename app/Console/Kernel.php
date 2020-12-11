<?php

namespace IXP\Console;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     *
     * @return void
     */
    protected function schedule( Schedule $schedule ): void
    {
        // Expunge logs / GDPR data / etc.
        $schedule->command( 'utils:expunge-logs' )->dailyAt( '3:04' );
        
        // Grapher - https://docs.ixpmanager.org/grapher/mrtg/#inserting-traffic-data-into-the-database-reporting-emails
        $schedule->command( 'grapher:upload-stats-to-db' )->dailyAt( '2:00' )
            ->skip( function() { return env( 'TASK_SCHEDULER_SKIP_GRAPHER_UPLOAD_STATS_TO_DB', false ); } );
        $schedule->command( 'grapher:upload-pi-stats-to-db' )->dailyAt( '2:10' )
            ->skip( function() { return env( 'TASK_SCHEDULER_SKIP_GRAPHER_UPLOAD_STATS_TO_DB', false ); } );

        // https://docs.ixpmanager.org/features/peeringdb/#existence-of-peeringdb-records
        $schedule->command('ixp-manager:update-in-peeringdb')->daily()
            ->skip( function() { return env( 'TASK_SCHEDULER_SKIP_UPDATE_IN_PEERINGDB', false ); } );

        // https://docs.ixpmanager.org/features/manrs/
        $schedule->command('ixp-manager:update-in-manrs')->daily()
            ->skip( function() { return env( 'TASK_SCHEDULER_SKIP_UPDATE_IN_MANRS', false ); } );

        // IRRDB - https://docs.ixpmanager.org/features/irrdb/
        if( config( 'ixp.irrdb.bgpq3.path' ) && is_executable( config( 'ixp.irrdb.bgpq3.path' ) ) ) {
            $schedule->command( 'irrdb:update-prefix-db' )->cron( '7 */6 * * *' )
                ->skip( function() { return env( 'TASK_SCHEDULER_SKIP_IRRDB_UPDATE_PREFIX_DB', false ); } );

            $schedule->command( 'irrdb:update-asn-db' )->cron( '37 */6 * * *' )
                ->skip( function() { return env( 'TASK_SCHEDULER_SKIP_IRRDB_UPDATE_ASN_DB', false ); } );
        }

        // https://laravel.com/docs/5.8/telescope#data-pruning
        $schedule->command('telescope:prune --hours=72')->daily();

        // OUI Update - https://docs.ixpmanager.org/features/layer2-addresses/#oui-database
        $schedule->command( 'utils:oui-update' )->weekly()->mondays()->at('9:15')
            ->skip( function() { return env( 'TASK_SCHEDULER_SKIP_UTILS_OUI_UPDATE', false ); } );

        // Switch SNMP pool - https://docs.ixpmanager.org/usage/switches/#automated-polling-snmp-updates
        $schedule->command( 'switch:snmp-poll' )->everyFiveMinutes()
            ->skip( function() { return env( 'TASK_SCHEDULER_SKIP_SWITCH_SNMP_POLL', false ); } );

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}