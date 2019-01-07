<?php namespace IXP\Console;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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
use IXP\Console\Commands\Audit\PostSpeeds;
use IXP\Console\Commands\Upgrade\RouterImport;

class Kernel extends ConsoleKernel {

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [

        \IXP\Console\Commands\Audit\PortSpeeds::class,

        \IXP\Console\Commands\Irrdb\UpdateAsnDb::class,
        \IXP\Console\Commands\Irrdb\UpdatePrefixDb::class,

        \IXP\Console\Commands\Router\GenerateConfiguration::class,

        \IXP\Console\Commands\Utils\ConvertPlaintextPasswords::class,
        \IXP\Console\Commands\Utils\UpdateOuiDatabase::class,

        \IXP\Console\Commands\Upgrade\MigrateL2Addresses::class,
        \IXP\Console\Commands\Upgrade\CopyContactNamesToUsers::class,

        \IXP\Console\Commands\Utils\Export\JsonSchema\Post::class,

        \IXP\Console\Commands\Switches\SnmpPoll::class,

        \IXP\Console\Commands\Rir\GenerateObject::class,

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule) {
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
