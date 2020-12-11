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
use IXP\Console\Commands\Command as IXPCommand;

use IXP\Models\{
    CustomerToUser,
    User
};

/**
 * Class PromoteCustUser - tool to promote the CustUser into CustAdmin
 *
 * @author      Yann Robin <yann@islandbridgenetworks.ie>
 * @author      Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @package     IXP\Console\Commands\Upgrade
 * @copyright   Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PromoteCustUser extends IXPCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:promote-custusers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Promote users with CustUser privilege to CustAdmin (part of the upgrade to V5.0.0 process)';

    /**
     * Execute the console command.
     *
     * Delete data from the table 'User'
     *
     * @return mixed
     *
     * @throws
     *
     */
    public function handle()
    {
        echo "\n\n";
        $this->warn( "ONLY RUN ONCE AND ONLY WHEN UPGRADING TO IXP Manager v5.0.0 from v4.9.x" );
        echo "\n";
        $this->warn( "THIS HAS TO BE RUN AFTER THE COMMAND: update:remove-custadmins" );
        echo "\n";
        $this->warn( "THIS WILL PROMOTE ALL the CUSTUSER users to CUSTADMIN users." );

        if( !$this->confirm( "\nThis command will promote all the CustUser users to CustAdmin users.\n\n"
            ."Generally, this command should only ever be run once and only when migrating to V5.0.0.\n\n"
            . 'Are you sure you wish to proceed? ' ) ) {
            return 1;
        }

        $this->info( 'Migration in progress, please wait...' );

        $C2UCustUser = CustomerToUser::where( 'privs', User::AUTH_CUSTUSER )->get();

        $bar = $this->output->createProgressBar( $C2UCustUser->count() );
        $bar->start();

        foreach( $C2UCustUser as $c2u ) {
            // Changing user privilege
            $c2u->privs = User::AUTH_CUSTADMIN;
            $c2u->save();

            $bar->advance();
        }

        $bar->finish();
        echo "\n\n";
        $this->info( 'Migration completed successfully' );

        return 0;
    }
}