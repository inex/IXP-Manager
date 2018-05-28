<?php

namespace IXP\Console\Commands\Switches;

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

use OSS_SNMP\{
    Exception,
    SNMP
};

use Entities\{
    Switcher                    as SwitcherEntity
};

/**
 * Class SnmpPoll
 *
 * @author      Yann Robin          <yann@islandbridgenetworks.ie>
 * @author      Barry O'Donovan     <barry@islandbridgenetworks.ie>
 * @package     IXP\Console\Commands\Upgrade
 * @copyright   Copyright (C) 2009-2018 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SnmpPoll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'ixp-manager:switch:snmp-poll
                        {switch? : The name of the switch, if not name specified the command will loop all the switches}
                        {--noflush : If specified no modification will be made on the Database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will Poll and update switch objects via SNMP';

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

        if( !$this->confirm( "Are you sure you wish to proceed?\n\nThis command will Poll and update switch objects via SNMP\n" ) ) {
            return 1;
        }

        if( $this->argument('switch') ) {

            if( ! ( $switches = D2EM::getRepository( SwitcherEntity::class )->findBy( [ "name" => $this->argument('switch') ] ) ) ) {
                $this->info( "ERR: No switch found with name " . $this->argument('switch' ) );
                return 0;
            }

        } else {
            $switches = D2EM::getRepository( SwitcherEntity::class )->getActive();
        }

        if( count( $switches ) ){

            foreach( $switches as $s ) {

                if( trim( $s->getSnmppasswd() ) == '' ) {
                    $this->info( "Skipping {$s->getName()} as no SNMP password set" );
                    continue;
                }

                if( $s->getLastPolled() == null ){
                    $this->info( "First time polling of {$s->getName()} with SNMP request to {$s->getHostname()}" );

                } else{
                    $this->info( "Polling {$s->getName()} with SNMP request to {$s->getHostname()}" );
                }


                $sPolled = false;

                try {
                    $sPolled = false;
                    $host = new SNMP( $s->getHostname(), $s->getSnmppasswd() );
                    $s->snmpPoll( $host, true );
                    $sPolled = true;

                    $s->snmpPollSwitchPorts( $host, true );

                    if( $this->option( 'noflush' ) ){
                        $this->info( '*** noflush parameter set - NO CHANGES MADE TO DATABASE' );
                    } else{
                        D2EM::flush();
                    }

                } catch( Exception $e ) {
                    if( $sPolled ){
                        $this->error("ERROR: OSS_SNMP exception polling {$s->getName()} by SNMP");
                    } else {
                        $this->error("ERROR: OSS_SNMP exception polling ports for {$s->getName()} by SNMP");
                    }

                }

                $this->info( '=========================================' );

            }

        }


        $this->info( 'Process finish' );
        return 0;
    }
}
