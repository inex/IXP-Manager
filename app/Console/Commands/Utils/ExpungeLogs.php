<?php

namespace IXP\Console\Commands\Utils;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */


use Carbon\Carbon;
use D2EM;

use Repositories\UserLoginHistory as UserLoginHistoryRepo;

use IXP\Console\Commands\Command as IXPCommand;
use Repositories\UserLoginHistory;


/**
 * Class UpdateOuiDatabase - update OUI database from named file or IEEE website.
 *
 * A specific file can be passed via the `fromfile` parameter. You can also force a
 * database reset (drop all OUI entries and re-populate) via the `refresh` option.
 *
 * Neither of these options are typically necessary:
 *
 * Note that we bundle a recent OUI file in `data/oui` also.
 *
 * @author Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @package IXP\Console\Commands\Utils
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ExpungeLogs extends IXPCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'utils:expunge-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old data from database tables';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        // Deleting User login older than 6 months
        $sixmonthsago = Carbon::now()->subMonths(6)->format( 'Y-m-d 00:00:00' );

        $this->isVerbosityVerbose() && $this->output->write('Expunging user login records > 6 months...', false );
        D2EM::createQuery( 'DELETE FROM Entities\\UserLoginHistory ulh WHERE ulh.at < ?1' )->execute( [ 1 => $sixmonthsago ] );
        $this->isVerbosityVerbose() && $this->info(' [done]' );

        // Deleting Expired API Keys older than 3 months
        $threemonthsago = Carbon::now()->subMonths(3)->format( 'Y-m-d 00:00:00' );

        $this->isVerbosityVerbose() && $this->output->write('Expunging expired API Key records > 3 months...', false );
        D2EM::createQuery( 'DELETE FROM Entities\\ApiKey a WHERE a.expires < ?1' )->execute( [ 1 => $threemonthsago ] );
        $this->isVerbosityVerbose() && $this->info(' [done]' );

        return 0;
    }
}
