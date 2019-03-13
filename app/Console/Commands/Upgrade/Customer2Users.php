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
    CustomerToUser  as CustomerToUserEntity,
    User            as UserEntity
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
    protected $signature = 'customer2users:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will migrate datas from Customer - User to the customer_to_users';

    /**
     * Execute the console command.
     *
     * Transfers data from the table 'customer' and 'user' to the table 'customer_to_users'
     *
     * @return mixed
     */
    public function handle() {
        if( !$this->confirm( 'Are you sure you wish to proceed? This command will CLEAR the customer_to_users table and then copy '
            . 'customer and user data in customer_to_user table. Generally, this command should only ever be run once when initially '
            . 'populating the new table.' ) ) {
            return 1;
        }

        // Delete all the rows from the table Layer2Address
        DB::table( 'customer_to_users' )->truncate();
        $this->info( 'The customer_to_users table has been truncated' );

        $this->info( 'Migration in progress, please wait...' );

        // get all the entries form the macaddress table
        DB::table( 'user' )->orderBy( 'id' )->chunk( 100, function( $listUsers ) {

            foreach( $listUsers as $user ) {

                /** @var UserEntity $u */
                $u = D2EM::getRepository( UserEntity::class )->find( $user->id );

                // create the new CustomerToUserEntity entity with the information of the current User and Customer table entries
                $c2u = new CustomerToUserEntity();
                $c2u->setUser(      $u )
                    ->setCustomer(  $u->getCustomer() )
                    ->setCreatedAt( new \DateTime )
                    ->setPrivs(     $u->getUserPrivs() );
                D2EM::persist(      $c2u );
                D2EM::flush();

            }
        });

        $this->info( 'Migration completed successfully' );
    }
}
