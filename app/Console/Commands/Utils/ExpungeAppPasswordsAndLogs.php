<?php

namespace IXP\Console\Commands\Utils;


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

use IXP\Console\Commands\Command as IXPCommand;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExpungeAppPasswordsAndLogs extends IXPCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'utils:expunge-app-passwords-and-logs {days? : Number of days to retain for logs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expunge old app password login history, and expired passwords';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $days = $this->argument('days') ?: config('ixp_fe.app_passwords.history_retention_days', 90);
        $date = Carbon::now()->subDays($days);

        $count = DB::table('app_passwords_last_logins')
            ->where('last_seen_at', '<', $date)
            ->delete();
        
        $this->isVerbosityVerbose() && $this->info("Successfully expunged {$count} login histories older than {$days} days.");
        
        $date = Carbon::now()->subDays(28);
        
        $count = DB::table('app_passwords')
            ->where('expires', '<', $date)
            ->delete();
        
        $this->isVerbosityVerbose() && $this->info("Deleted {$count} app passwords that have expired more than 28 days ago.");
        
        return 0;
    }
}
