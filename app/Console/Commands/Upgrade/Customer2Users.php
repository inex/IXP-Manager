<?php

namespace IXP\Console\Commands\Upgrade;

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


use D2EM, DB;

use Entities\{
    CustomerToUser      as CustomerToUserEntity,
    User                as UserEntity,
    UserLoginHistory    as UserLoginHistoryEntity
};

use IXP\Console\Commands\Command as IXPCommand;


/**
 * Class Customer2User - tool to migrate the Customer/User datas to customer_to_users table
 *
 * @author      Yann Robin <yann@islandbridgenetworks.ie>
 * @author      Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @package     IXP\Console\Commands\Upgrade
 * @copyright   Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
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
    public function handle() {

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

        /** @var UserEntity[] $users */
        $users = D2EM::getRepository( UserEntity::class )->findAll();

        $bar = $this->output->createProgressBar(count($users));
        $bar->start();

        foreach( $users as $u ) {

            // create the new CustomerToUserEntity entity with the information of the current User and Customer table entries
            $c2u = new CustomerToUserEntity();
            $c2u->setUser(      $u )
                ->setCustomer(  $u->getCustomer() )
                ->setCreatedAt( new \DateTime )
                ->setPrivs(     $u->getUserPrivs() )
                ->setLastLoginAt(     new \DateTime( date( 'Y-m-d H:i:s' , $u->getPreference( 'auth.last_login_at' ) ) ) )
                ->setLastLoginFrom(     $u->getPreference( 'auth.last_login_from' ) )
                ->setExtraAttributes( [ "created_by" => [ "type" => "migration-script" ] ] );
            D2EM::persist( $c2u );
            D2EM::flush();

            /** @var UserLoginHistoryEntity $loginHistory */
            DB::table( 'user_logins' )->where( 'user_id', $u->getId() )->update( ['customer_to_user_id' => $c2u->getId() ] );

            $bar->advance();
        };

        $bar->finish();
        echo "\n\n";
        $this->info( 'Migration completed successfully' );
    }
}
