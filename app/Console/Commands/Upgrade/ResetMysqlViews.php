<?php

namespace IXP\Console\Commands\Upgrade;

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
/**
 * Reset MYSQL views
 *
 * @author      Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author      Yann Robin <yann@islandbridgenetworks.ie>
 * @package     IXP\Console\Commands\Upgrade
 * @copyright   Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ResetMysqlViews extends IXPCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:reset-mysql-views';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset MySQL views (idempotent and should be run after each upgrade)';

    /**
     * Execute the console command.
     *
     * Transfers data from the table 'customer' and 'user' to the table 'customer_to_users'
     *
     * @return mixed
     *
     * @throws
     *
     */
    public function handle(): int
    {
        DB::beginTransaction();
        DB::unprepared( resolve( 'Foil\Engine' )->render( 'database/views.foil.sql' ) );
        DB::commit();

        return 0;
    }
}