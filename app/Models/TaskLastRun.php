<?php

declare(strict_types=1);

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

namespace IXP\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * IXP\Models\TaskLastRun
 *
 * @property string $task_key
 * @property array<array-key, mixed> $parameters
 * @property \Illuminate\Support\Carbon $last_run_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskLastRun newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskLastRun newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskLastRun query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskLastRun whereLastRunAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskLastRun whereParameters($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskLastRun whereTaskKey($value)
 * @mixin \Eloquent
 */
class TaskLastRun extends Model
{
    const string SCHEDULER_CRON_JOB = 'scheduler_cron_job';
    const string NAGIOS_CUSTOMERS = 'nagios_customers';
    const string NAGIOS_SWITCHES = 'nagios_switches';
    const string NAGIOS_BIRDSEYE_DAEMONS = 'nagios_birdseye_daemons';
    const string NAGIOS_BIRDSEYE_BGP_SESSIONS = 'nagios_birdseye_bgp_sessions';

    protected $table = 'task_last_run';

    public $timestamps = false;

    protected $casts = [
        'last_run_at' => 'datetime',
        'parameters' => 'array',
    ];

    /**
     * Record the last run of the scheduler command
     */
    public static function updateSchedulerCronJob(): void
    {
        self::upsert( [ 'task_key' => self::SCHEDULER_CRON_JOB, 'last_run_at' => now() ], [ 'task_key' ], [ 'last_run_at' ] );
    }

    /**
     * Record the last run of the nagios customers monitoring config generation
     */
    public static function updateNagiosCustomers(array $parameters): void
    {
        self::upsert( [ 'task_key' => self::NAGIOS_CUSTOMERS, 'parameters' => json_encode( $parameters ), 'last_run_at' => now() ], [ 'task_key', 'parameters' ], [ 'last_run_at' ] );
    }

    /**
     * Record the last run of the nagios switch monitoring config generation
     */
    public static function updateNagiosSwitches(array $parameters): void
    {
        self::upsert( [ 'task_key' => self::NAGIOS_SWITCHES, 'parameters' => json_encode( $parameters ), 'last_run_at' => now() ], [ 'task_key', 'parameters' ], [ 'last_run_at' ] );
    }

    /**
     * Record the last run of the nagios birdseye daemon monitoring config generation
     */
    public static function updateNagiosBirdseyeDaemons(array $parameters): void
    {
        self::upsert( [ 'task_key' => self::NAGIOS_BIRDSEYE_DAEMONS, 'parameters' => json_encode( $parameters ), 'last_run_at' => now() ], [ 'task_key', 'parameters' ], [ 'last_run_at' ] );
    }

    /**
     * Record the last run of the nagios birdseye bpg session monitoring config generation
     */
    public static function updateNagiosBgpSessions(array $parameters): void
    {
        self::upsert( [ 'task_key' => self::NAGIOS_BIRDSEYE_BGP_SESSIONS, 'parameters' => json_encode( $parameters ), 'last_run_at' => now() ], [ 'task_key', 'parameters' ], [ 'last_run_at' ] );
    }
}