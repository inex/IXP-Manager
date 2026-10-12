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

namespace IXP\Console\Commands\Utils;

use Illuminate\Support\Facades\DB;
use IXP\Console\Commands\Command as IXPCommand;

/**
 * ClearTaskLastRun
 *
 * This command empties the `task_last_run` table
 */
class ClearTaskHistory extends IXPCommand
{
    protected $signature = 'utils:clear-task-history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "This command will empty all records about when tasks were last run. This may be helpful if you have changed deployment routines, but the system validator is complaining you haven't run some task recently";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $records = DB::table('task_last_run')->delete();
        $this->info("Task history has been cleared. " . $records . " records deleted.");

        return 0;
    }
}