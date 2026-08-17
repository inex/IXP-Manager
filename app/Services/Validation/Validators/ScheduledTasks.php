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

use Carbon\Carbon;
use IXP\Contracts\Validation\ValidationBackend;
use IXP\Contracts\Validation\Validator;
use IXP\Models\TaskLastRun;
use IXP\Services\Validation\Dto\Result;

/**
 * This validator checks the laravel task scheduler is running
 */
class ScheduledTasks implements Validator
{

    #[\Override]
    public function getName(): string
    {
        return "Scheduler Task validator";
    }

    #[\Override]
    public function getDescription(): string
    {
        return "Checks the IXP Manager task scheduler is running.";
    }

    #[\Override]
    public function getPriority(): int
    {
        return 13;
    }

    #[\Override]
    public function run( ValidationBackend $backend ): void
    {
        $schedulerLastRun = TaskLastRun::whereTaskKey( TaskLastRun::SCHEDULER_CRON_JOB )->first();

        if ( !$schedulerLastRun ) {
            $backend->error( "No record of task scheduler running - your automated tasks are not running!")
                ->withDocsPath( "features/cronjobs/" );
        } else if( $schedulerLastRun->last_run_at->diffInMinutes( Carbon::now() ) > 10 ) {
            $backend->error( "Task scheduler hasn't run for 10 minutes - your automated tasks are not running!")
                ->withDocsPath( "features/cronjobs/" );
        }

        $tasksInfo = TaskLastRun::all();
        if (count($tasksInfo) === 0) {
            $backend->warning("Task history table is empty");
        } else {
            $backend->info("Task history is being tracked")
                ->each($tasksInfo, function(Result $result, TaskLastRun $taskLastRun) {
                    if ($taskLastRun->parameters !== null) {
                        $parameters = [];
                        foreach ($taskLastRun->parameters as $key => $value) {
                            $parameters[] = "$key: $value";
                        }
                        $result->addAdditionalInfoText($taskLastRun->task_key . " with parameters " . implode(", ", $parameters) . " last ran at " . $taskLastRun->last_run_at->toIso8601String());
                    } else {
                        $result->addAdditionalInfoText($taskLastRun->task_key . " last ran at " . $taskLastRun->last_run_at->toIso8601String());
                    }
                })
            ;
        }

    }
}