<?php

namespace IXP\Console\Commands\Switches;

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Illuminate\Database\Eloquent\Collection;
use IXP\Console\Commands\Command;


use OSS_SNMP\{
    Exception,
    SNMP
};

use IXP\Models\Aggregators\SwitcherAggregator;
use IXP\Models\Switcher;
use IXP\Models\Vlan;
use IXP\Tasks\Switch\FdbTrawl as FdbTrawler;

/**
 * Class SnmpPoll
 *
 * @author      Nick Hilliard       <nick@inex.ie>
 * @author      Barry O'Donovan     <barry@islandbridgenetworks.ie>
 * @package     IXP\Console\Commands
 * @copyright   Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class FdbTrawl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'switch:fdb-trawl
                        {vlandbid : The database ID of the VLAN to trawl}
                        {switch? : The name of the switch, if not name specified the command will loop over all switches linked to the VLAN}
                        {--nosave : If specified no modification will be made to the database}
                        {--log : Output detailed polling information to the log}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trawl the FDB table of a switch and update the MAC address table in IXP Manager';

    /**
     * Execute the console command.
     *
     * @return mixed
     *
     * @throws
     */
    public function handle()
    {
        if( !( $vlan = Vlan::find( $this->argument('vlandbid')) ) ) {
            $this->error( "ERR: No VLAN found with database ID " . $this->argument('vlandbid' ) );
            return -3;
        }

        if( $this->argument('switch') ) {
            if( ! ( $switches = Switcher::where( 'name', $this->argument('switch') )->get() ) ) {
                $this->error( "ERR: No switch found with name: " . $this->argument('switch' ) );
                return -1;
            }
        } else {
            $switches = SwitcherAggregator::getForVlan($vlan);
        }

        if( !count( $switches ) ) {
            $this->warn('No switches matches - exiting');
            return -2;
        }

        foreach( $switches as $s ) {

            $trawler = ( new FdbTrawler( $s, $vlan ) )->trawl();
die("\n\n\==ENDS==\n\n");
            if( !$this->isVerbosityQuiet() ) {
                $this->info( "Polling {$s->name} with SNMP requests to {$s->hostname}" );
            }

            try {
                $sPolled = false;
                $host = new SNMP( $s->hostname, $s->snmppasswd );
                $s->snmpPoll( $host, $this->option( 'log', false ), $this->option( 'nosave', false )  );
                $sPolled = true;

                $s->snmpPollSwitchPorts( $host, $this->option( 'log', false ), false , $this->option( 'nosave', false ) );

                if( $this->option( 'nosave', false ) ){
                    $this->warn( '    *** --nosave parameter set - NO CHANGES MADE TO DATABASE' );
                }
            } catch( Exception $e ) {
                if( $sPolled ){
                    $this->error("ERROR: OSS_SNMP exception polling switch {$s->name} by SNMP");
                } else {
                    $this->error("ERROR: OSS_SNMP exception polling switch ports for {$s->name} by SNMP");
                }

            }
        }

        return 0;
    }


}