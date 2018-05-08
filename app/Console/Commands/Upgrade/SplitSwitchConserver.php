<?php

namespace IXP\Console\Commands\Upgrade;

/*
 * Copyright (C) 2009-2018 Internet Neutral Exchange Association Company Limited By Guarantee.
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
    ConsoleServer               as ConsoleServerEntity,
    Switcher                    as SwitcherEntity,
    ConsoleServerConnection     as ConsoleServerConnectionEntity
};

/**
 * Class SplitSwitchConserver - tool to split the console servers from the switches
 *
 * @author Yann Robin <yann@islandbridgenetworks.ie>
 * @author Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @package IXP\Console\Commands\Upgrade
 * @copyright  Copyright (C) 2009-2018 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SplitSwitchConserver extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ixp-manager:upgrade:split-conservers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will split out any console servers from the switch table. It is part of the v4.7 to v4.8 upgrade procedure.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     *
     * @throws
     */
    public function handle() {

        if( !$this->confirm( "Are you sure you wish to proceed?\n\nThis command will split out any console servers from the switch table.\n\n"
            . "Generally, this command should only ever be run once when migrating from v4.7.x to v4.8\n" ) ) {
            return 1;
        }
        try{

            foreach( D2EM::getRepository( SwitcherEntity::class )->findBy( [ "switchtype" => SwitcherEntity::TYPE_CONSOLESERVER ] ) as $s ) {

                /** @var SwitcherEntity $s */
                D2EM::transactional( function( $em ) use ($s) {

                    /** @var \Doctrine\ORM\EntityManager $em */

                    $cscs = D2EM::getRepository( SwitcherEntity::class )->getConsoleServerConnections( $s->getId() );
                    $this->info( " - migrating {$s->getName()} and its " . count( $cscs ) . " console connections" );

                    $cs = new ConsoleServerEntity;
                    $cs->setName( $s->getName() );
                    $cs->setActive( $s->getActive() );
                    $cs->setHostname( $s->getHostName() );
                    $cs->setModel( $s->getModel() );
                    $cs->setNote( $s->getNotes() );
                    $cs->setSerialNumber( $s->getSerialNumber() == '(not implemented)' ? '' : $s->getSerialNumber() );
                    $cs->setCabinet( $s->getCabinet() );
                    $cs->setVendor( $s->getVendor() );
                    $em->persist( $cs );

                    /** @var ConsoleServerConnectionEntity $csc */
                    foreach( $cscs as $csc ) {
                        $csc->setConsoleServer( $cs );
                        $csc->setSwitcher( null );
                    }

                    $em->remove( $s );

                });

            }
        }
        catch( \Exception $e ){
            $this->info( '=========================================' );
            $this->error( $e->getMessage() );
            $this->info( 'Migration Aborted! Database changes for the errored switch rolled back.' );
            return $e;
        }

        $this->info( '=========================================' );
        $this->info( 'Migration completed successfully' );
        return 0;
    }
}
