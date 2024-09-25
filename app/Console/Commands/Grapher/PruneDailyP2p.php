<?php

namespace IXP\Console\Commands\Grapher;

/*
 * Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee.
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
use Illuminate\Support\Facades\DB;

use IXP\Console\Commands\Command as IXPCommand;

use IXP\Models\{ApiKey, Log, P2pDailyStats, UserLoginHistory, UserRememberToken};

/**
 * Prune p2p_daily_stats
 *
 * @author Barry O'Donovan <barry@opensolutions.ie>
 * @package IXP\Console\Commands\Grapher
 * @copyright  Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PruneDailyP2p extends IXPCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grapher:prune-daily-p2p {--all} {--days=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete --all or records older than --days from p2p_daily_stats';

    public function handle()
    {
        if( $this->option('all') ) {
            $before = now();
        } else {
            if( !$this->option('days') || !is_numeric( $this->option('days') ) ) {
                $this->error( 'Please specify if you want to delete --all records or those from more than --days=x ago');
            }
            $before = now()->subDays( (int)$this->option('days' ) );
        }

        $cnt = P2pDailyStats::where( 'day', '<', $before->format('Y-m-d') )->delete();

        if( $this->isVerbosityVerbose() ) {
            $this->info( "Deleted $cnt records older than {$before->format('Y-m-d')}" );
        }

        return 0;
    }
}