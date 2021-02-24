<?php

namespace IXP\Console\Commands\Switches;

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
 * @author      Barry O'Donovan     <barry@islandbridgenetworks.ie>
 * @package     IXP\Console\Commands
 * @copyright   Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ReindexIfIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'switch:reindex-ifindex
                        {switch : The name of the switch}
                        {--noflush : If specified no modification will be made to the database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reindex switch ports\' ifIndex based on ifName';

    /**
     * Execute the console command.
     *
     * @return mixed
     *
     * @throws
     */
    public function handle() {

        /** @var $s SwitcherEntity */

        if( ! ( $s = D2EM::getRepository( SwitcherEntity::class )->findOneBy( [ "name" => $this->argument('switch') ] ) ) ) {
            $this->error( "ERR: No switch found with name: " . $this->argument('switch' ) );
            return -1;
        }

        try {
            $host = new SNMP( $s->getHostname(), $s->getSnmppasswd() );

            // array in ifIndex => ifName format:
            $snmpports = $host->useIface()->names();

            foreach( $s->getPorts() as $sp ) {
                foreach( $snmpports as $ifIndex => $ifName ) {
                    if( $sp->getIfName() != $ifName ) {
                        continue;
                    }

                    if( $sp->getIfIndex() == $ifIndex ) {
                        $this->comment( " - {$sp->getIfName()} unchanged, ifIndex remains the same");
                    } else {
                        $this->info(" - {$sp->getIfName()} ifIndex changed from {$sp->getIfIndex()} to {$ifIndex}");
                        $sp->setIfIndex( $ifIndex );
                    }

                    unset( $snmpports[$ifIndex] );
                    break;
                }
            }

            if( $this->option( 'noflush', false ) ){
                $this->error( "\n*** --noflush parameter set - NO CHANGES MADE TO DATABASE" );
            } else{
                D2EM::flush();
            }

        } catch( Exception $e ) {
            $this->error("ERROR: OSS_SNMP exception polling switch ports for {$s->getName()} by SNMP");
            $this->error( $e->getMessage() );
            return -2;
        }

        return 0;
    }
}
