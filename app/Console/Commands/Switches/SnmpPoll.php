<?php

namespace IXP\Console\Commands\Switches;

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

use OSS_SNMP\{
    Exception,
    SNMP
};

use Entities\{
    Switcher    as SwitcherEntity
};

/**
 * Class SnmpPoll
 *
 * @author      Yann Robin          <yann@islandbridgenetworks.ie>
 * @author      Barry O'Donovan     <barry@islandbridgenetworks.ie>
 * @package     IXP\Console\Commands
 * @copyright   Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SnmpPoll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'switch:snmp-poll
                        {switch? : The name of the switch, if not name specified the command will loop over all switches}
                        {--noflush : If specified no modification will be made to the database}
                        {--log : Output detailed polling information to the log}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Poll and update switches and switch ports via SNMP';

    /**
     * Execute the console command.
     *
     * @return mixed
     *
     * @throws
     */
    public function handle() {

        if( $this->argument('switch') ) {

            if( ! ( $switches = D2EM::getRepository( SwitcherEntity::class )->findBy( [ "name" => $this->argument('switch') ] ) ) ) {
                $this->error( "ERR: No switch found with name: " . $this->argument('switch' ) );
                return -1;
            }

        } else {
            $switches = D2EM::getRepository( SwitcherEntity::class )->getPollable();
        }

        if( count( $switches ) ){

            foreach( $switches as $s ) {

                /** @var $s SwitcherEntity */

                if( $s->getSnmppasswd() === null || trim( $s->getSnmppasswd() ) === '' ) {
                    if( !$this->isVerbosityQuiet() ) {
                        $this->info( "Skipping {$s->getName()} as no SNMP password set" );
                    }
                    continue;
                }

                if( !$this->isVerbosityQuiet() ) {
                    if( $s->getLastPolled() === null ) {
                        $this->info( "First time polling {$s->getName()} with SNMP request to {$s->getHostname()}" );
                    } else {
                        $this->info( "Polling {$s->getName()} with SNMP request to {$s->getHostname()}" );
                    }
                }

                try {
                    $sPolled = false;
                    $host = new SNMP( $s->getHostname(), $s->getSnmppasswd() );
                    $s->snmpPoll( $host, $this->option( 'log', false ) );
                    $sPolled = true;

                    $s->snmpPollSwitchPorts( $host, $this->option( 'log', false ) );

                    if( $this->option( 'noflush', false ) ){
                        $this->warn( '    *** --noflush parameter set - NO CHANGES MADE TO DATABASE' );
                    } else{
                        D2EM::flush();
                    }

                } catch( Exception $e ) {
                    if( $sPolled ){
                        $this->error("ERROR: OSS_SNMP exception polling switch {$s->getName()} by SNMP");
                    } else {
                        $this->error("ERROR: OSS_SNMP exception polling switch ports for {$s->getName()} by SNMP");
                    }

                }
            }

        }
        return 0;
    }
}
