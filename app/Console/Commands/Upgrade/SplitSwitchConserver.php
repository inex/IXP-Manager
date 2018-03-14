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

use Illuminate\Console\Command;

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
    protected $signature = 'switch:split-console-servers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will split the console servers from the switches';

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

        if( !$this->confirm( 'Are you sure you wish to proceed? This command will split the console servers from the switches '
            . 'Generally, this command should only ever be run once when initially '
            . 'populating the new table.' ) ) {
            return 1;
        }

        $conn = D2EM::getConnection();
     
        foreach( D2EM::getRepository( SwitcherEntity::class )->findBy( [ "switchtype" => SwitcherEntity::TYPE_CONSOLESERVER ]) as $s ) {
            /** @var SwitcherEntity $s */
            $conn->beginTransaction();
            $conn->transactional(function( $conn ) use ( $s ) {

                try{
                    $cs = new ConsoleServerEntity;
                    D2EM::persist( $cs );
                    $cs->setName(           $s->getName()           );
                    $cs->setActive(         $s->getActive()         );
                    $cs->setHostname(       $s->getHostName()       );
                    $cs->setModel(          $s->getModel()          );
                    $cs->setNote(           $s->getNotes()          );
                    $cs->setSerialNumber(   $s->getSerialNumber()   );
                    $cs->setCabinet(        $s->getCabinet()        );
                    $cs->setVendor(         $s->getVendor()         );

                    D2EM::flush();

                    $this->info( 'The console server id:'. $cs->getName().' has been inserted into the database.' );

                    foreach( D2EM::getRepository( SwitcherEntity::class )->getConsoleServerConnections( $s->getId() ) as $csc ) {
                        /** @var ConsoleServerConnectionEntity $csc */
                        $csc->setConsoleServer( $cs );
                        $csc->setSwitcher( null );
                        $this->info( 'The console server connection id:' . $csc->getId(). ' name:' . $csc->getDescription() . ' has been linked to the new console server '. $cs->getName() );
                    }

                    $switchInfo = "id:". $s->getId(). " name:" .$s->getName();
                    D2EM::remove( $s );

                    $conn->commit();
                    D2EM::flush();
                    $this->info( 'The switch '. $switchInfo . ' has been deleted from the database ' );


                } catch (Exception $e) {
                    $this->error( $e->getMessage() );
                    $conn->rollBack();
                    $conn->close();
                }
            });
            $this->info( '=========================================' );
        }
        $this->info( 'Migration completed successfully' );
    }
}
