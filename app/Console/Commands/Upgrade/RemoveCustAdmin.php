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
    protected $description = 'Remove all the Custadmin from the datable (part of the upgrade to V5.0.0 process)';

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
        $this->warn( "THIS WILL DELETE ALL the CUSTADMIN from the USER TABLE." );

        if( !$this->confirm( "\nThis command will remove all the custadmin from the user table.\n\n"
            ."Generally, this command should only ever be run once and only when migrating to V5.0.0.\n\n"
            . 'Are you sure you wish to proceed? ' ) ) {
            return 1;
        }

        $this->info( 'Migration in progress, please wait...' );

        /** @var CustomerToUserEntity[] $C2Ucustadmin */
        $C2Ucustadmin = D2EM::getRepository( CustomerToUserEntity::class )->findBy( [ "privs" => UserEntity::AUTH_CUSTADMIN ] );

        $bar = $this->output->createProgressBar( count( $C2Ucustadmin ) );
        $bar->start();

        $c2uByUser = [];
        foreach( $C2Ucustadmin as $c2u ) {

            $user = $c2u->getUser();

            $user->removeCustomer( $c2u );

            foreach( $c2u->getUserLoginHistory() as $userLogin ){
                D2EM::remove( $userLogin );
            }

            D2EM::remove( $c2u );






            if( count( $c2u->getUser()->getCustomers2User() ) == 1 ){


                // delete all the user's preferences
                foreach( $user->getPreferences() as $pref ) {
                    $user->removePreference( $pref );
                    D2EM::remove( $pref );
                }

                // delete all the user's API keys
                foreach( $user->getApiKeys() as $ak ) {
                    $user->removeApiKey( $ak );
                    D2EM::remove( $ak );
                }

                D2EM::remove( $user );


            }



            $bar->advance();
        };

        D2EM::flush();

        $bar->finish();
        echo "\n\n";
        $this->info( 'Migration completed successfully' );
    }
}
