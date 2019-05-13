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
    User                as UserEntity
};

use IXP\Console\Commands\Command as IXPCommand;


/**
 * Class RemoveCustAdmin - tool to delete CustAdmin and everything linked from the datable
 *
 * @author      Yann Robin <yann@islandbridgenetworks.ie>
 * @author      Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @package     IXP\Console\Commands\Upgrade
 * @copyright   Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class RemoveCustAdmin extends IXPCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:remove-custadmins';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove all users with CustAdmin privilege (part of the upgrade to V5.0.0 process)';

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
    public function handle() {

        echo "\n\n";
        $this->warn( "ONLY RUN ONCE AND ONLY WHEN UPGRADING TO IXP Manager v5.0.0 from v4.9.x" );
        echo "\n";
        $this->warn( "THIS WILL DELETE ALL the CUSTADMIN USERS." );

        if( !$this->confirm( "\nThis command will remove all the custadmin users from the database.\n\n"
            ."Generally, this command should only ever be run once and only when migrating to V5.0.0.\n\n"
            . 'Are you sure you wish to proceed? ' ) ) {
            return 1;
        }

        $this->info( 'Deletion in progress, please wait...' );

        /** @var CustomerToUserEntity[] $C2Ucustadmin */
        $C2Ucustadmin = D2EM::getRepository( CustomerToUserEntity::class )->findBy( [ "privs" => UserEntity::AUTH_CUSTADMIN ] );

        $bar = $this->output->createProgressBar( count( $C2Ucustadmin ) );
        $bar->start();

        foreach( $C2Ucustadmin as $c2u ) {

            // Deleting the history user
            DB::table( 'user_logins' )->where( 'customer_to_user_id', $c2u->getId() )->delete();

            $user = $c2u->getUser();
            $user->removeCustomer( $c2u );
            D2EM::remove( $c2u );

            // Check if the user has c2u left, if not we delete the user
            if( !count( $c2u->getUser()->getCustomers2User() ) ) {

                // delete all the user's preferences
                DB::table( 'user_pref' )->where( 'user_id', $user->getId() )->delete();

                // delete all the user's API keys
                DB::table( 'api_keys' )->where( 'user_id', $user->getId() )->delete();

                D2EM::remove( $user );

            } else {
                // setting a new default customer for the user
                $user->setCustomer( $c2u->getUser()->getCustomers()[0] );
            }

            D2EM::flush();
            $bar->advance();
        };


        $bar->finish();
        echo "\n\n";
        $this->info( 'Migration completed successfully' );
        return 0;
    }
}
