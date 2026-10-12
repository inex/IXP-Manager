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

class ExpungeApiKeys extends IXPCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'utils:expunge-api-keys {days? : Number of days to retain for logs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expunge old (expired) API keys';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $date = Carbon::now()->subDays(28);
        
        $count = DB::table('api_keys')
            ->whereNotNull('expires')
            ->where('expires', '<', $date)
            ->delete();
        
        $this->isVerbosityVerbose() && $this->info("Deleted {$count} API keys that have expired more than 28 days ago.");
        
        return 0;
    }
}
