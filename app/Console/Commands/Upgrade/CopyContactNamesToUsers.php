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


use IXP\Console\Commands\Command;

use D2EM;

use Entities\{
    Contact  as ContactEntity,
    User     as UserEntity
};

/**
 * Class CopyContactNamesToUsers - migration script to copy contact names to linked users
 *
 * @author Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @package IXP\Console\Commands\Upgrade
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CopyContactNamesToUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ixp-manager:upgrade:copy-contact-names';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will copy contact names to users as part of the v4.8 -> v4.9 upgrade process.';

    /**
     * Execute the console command.
     *
     * @return mixed
     *
     * @throws
     */
    public function handle() {

        if( !$this->confirm( "Are you sure you wish to proceed?\n\nThis command will copy contact names to users and break the database link and should only ever be run once when migrating from v4.8.x to v4.9.x\n" ) ) {
            return 1;
        }

        /** @var ContactEntity $c */
        foreach( D2EM::getRepository( ContactEntity::class )->findAll() as $c ) {

            if( !$c->getUser() ) {
                continue;
            }

            $c->getUser()->setName( $c->getName() );
            $c->setUser( null );
        }

        D2EM::flush();


        $this->info( '=========================================' );
        $this->info( 'Migration completed successfully' );
        return 0;
    }
}
