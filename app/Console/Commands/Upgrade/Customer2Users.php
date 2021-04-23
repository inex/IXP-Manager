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
use DB;

use IXP\Console\Commands\Command as IXPCommand;
use IXP\Models\{CustomerToUser, User, UserLoginHistory};

/**
 * Class Customer2User - tool to migrate the Customer/User data to customer_to_users table
 *
 * @author      Yann Robin <yann@islandbridgenetworks.ie>
 * @author      Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @package     IXP\Console\Commands\Upgrade
 * @copyright   Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Customer2Users extends IXPCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:customer2users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate customer/user from 1:m to n:m (part of the upgrade to V5.0.0 process)';

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
    public function handle()
    {
        echo "\n\n";
        $this->warn( "ONLY RUN ONCE AND ONLY WHEN UPGRADING TO IXP Manager v5.0.0 from v4.9.x" );
        echo "\n";
        $this->warn( "THIS WILL TRUNCATE THE customer:user n:m TABLE - meaning any users created after upgrading to v5.0.0 will be unlinked from their customers." );

        if( !$this->confirm( "\nThis command will restructure the customer/user data from 1:m to n:m.\n\n"
            ."Generally, this command should only ever be run once and only when migrating to V5.0.0.\n\n"
            . 'Are you sure you wish to proceed? ' ) ) {
            return 1;
        }

        // Delete all the rows from the table Customer2User
        DB::table( 'customer_to_users' )->delete();

        $this->info( 'The customer_to_users table has been truncated' );

        $this->info( 'Migration in progress, please wait...' );

        $users = User::all();

        $bar = $this->output->createProgressBar( $users->count() );
        $bar->start();

        foreach( $users as $u ) {
            // create the new CustomerToUserEntity entity with the information of the current User and Customer table entries
            $c2u = new CustomerToUser;
            $c2u->user_id =             $u->id;
            $c2u->customer_id =         $u->custid;
            $c2u->privs =               $u->privs;
            $c2u->last_login_date =     null;
            $c2u->last_login_from =     null;
            $c2u->extra_attributes =      [ "created_by" => [ "type" => "migration-script" ] ];
            $c2u->save();

            UserLoginHistory::where( 'user_id', $u->id )->update( ['customer_to_user_id' => $c2u->id ] );

            $bar->advance();
        }

        $bar->finish();
        echo "\n\n";
        $this->info( 'Migration completed successfully' );
    }
}