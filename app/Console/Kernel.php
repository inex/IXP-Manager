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
    #[\Override]
    protected function schedule( Schedule $schedule ): void
    {
        $jitter = config('app.key') ? schedule_jitter() : 0;

        // Expunge logs / GDPR data / etc.
        $schedule->command( 'utils:expunge-logs' )->dailyAt( '3:04' );
        $schedule->command( 'utils:expunge-app-passwords-and-logs' )->dailyAt( '3:14' );
        
        // Grapher - https://docs.ixpmanager.org/latest/grapher/mrtg/#inserting-traffic-data-into-the-database-reporting-emails
        $schedule->command( 'grapher:upload-stats-to-db' )->dailyAt( '2:00' )
            ->skip( function() { return env( 'TASK_SCHEDULER_SKIP_GRAPHER_UPLOAD_STATS_TO_DB', false ); } )
            ->withoutOverlapping();

        $schedule->command( 'grapher:upload-pi-stats-to-db' )->dailyAt( '2:10' )
            ->skip( function() { return env( 'TASK_SCHEDULER_SKIP_GRAPHER_UPLOAD_STATS_TO_DB', false ); } )
            ->withoutOverlapping();

        if( config( 'grapher.backends.sflow.enabled' ) ) {
            $schedule->command( 'grapher:prune-daily-p2p --days=30' )->dailyAt( '0:05' );
            $schedule->command( 'grapher:upload-daily-p2p ' . now()->subDay()->format( 'Y-m-d' ) )
                ->dailyAt( '0:10' )->withoutOverlapping();
        }



        // https://docs.ixpmanager.org/latest/features/peeringdb/#existence-of-peeringdb-records
        $schedule->command('ixp-manager:update-in-peeringdb')->daily()->at( $this->jitterTime( $jitter, 1 ) )
            ->skip( function() { return env( 'TASK_SCHEDULER_SKIP_UPDATE_IN_PEERINGDB', false ); } );

        // https://docs.ixpmanager.org/latest/features/manrs/
        $schedule->command('ixp-manager:update-in-manrs')->dailyAt( $this->jitterTime( $jitter, 2 ) )
            ->skip( function() { return env( 'TASK_SCHEDULER_SKIP_UPDATE_IN_MANRS', false ); } );

        // IRRDB - https://docs.ixpmanager.org/latest/features/irrdb/
        if( ( $utility = config( 'ixp.irrdb.utility' ) ) && is_executable( config( 'ixp.irrdb.' . $utility . '.path' ) ) ) {
            $schedule->command( 'irrdb:update-prefix-db --alert-email' )->cron( $this->jitterMinute( $jitter, 7 ) . ' */6 * * *' )
                ->skip( function() { return env( 'TASK_SCHEDULER_SKIP_IRRDB_UPDATE_PREFIX_DB', false ); } )
                ->withoutOverlapping();

            $schedule->command( 'irrdb:update-asn-db --alert-email' )->cron( $this->jitterMinute( $jitter, 37 ) . ' */6 * * *' )
                ->skip( function() { return env( 'TASK_SCHEDULER_SKIP_IRRDB_UPDATE_ASN_DB', false ); } )
                ->withoutOverlapping();
        }

        // https://laravel.com/docs/5.8/telescope#data-pruning
        $schedule->command('telescope:prune --hours=72')->daily();

        // OUI Update - https://docs.ixpmanager.org/latest/features/layer2-addresses/#oui-database
        $schedule->command( 'utils:oui-update' )->weekly()->mondays()->at( $this->jitterTime( $jitter, 9, 15 ) )
            ->skip( function() { return env( 'TASK_SCHEDULER_SKIP_UTILS_OUI_UPDATE', false ); } )
            ->withoutOverlapping();

        $schedule->command( 'utils:asn-update' )->weekly()->tuesdays()->at( $this->jitterTime( $jitter, 10, 15 ) )
            ->skip( function() { return env( 'TASK_SCHEDULER_SKIP_UTILS_ASN_UPDATE', false ); } )
            ->withoutOverlapping();

        // Switch SNMP pool - https://docs.ixpmanager.org/latest/usage/switches/#automated-polling-snmp-updates
        $schedule->command( 'switch:snmp-poll' )->everyFiveMinutes()
            ->skip( function() { return env( 'TASK_SCHEDULER_SKIP_SWITCH_SNMP_POLL', false ); } )
            ->withoutOverlapping();

    }

    /**
     * Format $hour and $minute into a time string ("hh:mm"), factoring in $jitter to
     * roughly spread load around
     */
    private function jitterTime(int $jitter, int $hour = 0, int $minute = 0): string
    {
        return sprintf( '%02d:%02d', $hour, ($minute + $jitter) % 60 );
    }

    /**
     * Format $minute into a minute string ("mm"), factoring in $jitter to
     * roughly spread load around
     * @return string
     */
    private function jitterMinute(int $jitter, int $minute = 0): string
    {
        return sprintf( '%02d', ($minute + $jitter) % 60 );
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    #[\Override]
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}