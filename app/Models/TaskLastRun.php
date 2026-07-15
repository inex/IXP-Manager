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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskLastRun newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskLastRun newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskLastRun query()
 * @property string $task_key
 * @property string $last_run_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskLastRun whereLastRunAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskLastRun whereTaskKey($value)
 * @mixin \Eloquent
 */
class TaskLastRun extends Model
{
    const string SCHEDULER_CRON_JOB = 'scheduler_cron_job';

    protected $table = 'task_last_run';

    protected $casts = [
        'last_run_at' => 'datetime',
    ];

    public static function updateSchedulerCronJob(): void
    {
        self::updateOrInsert(['task_key' => self::SCHEDULER_CRON_JOB], ['last_run_at' => now()]);
    }
}