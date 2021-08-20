<?php

namespace IXP\Console\Commands\Upgrade;

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
use DB;

use IXP\Models\{
    Layer2Address,
    VirtualInterface
};

use IXP\Console\Commands\Command as IXPCommand;
/**
 * Class L2Addresses - tool to copy MAC addresses from the read only global table
 * used previously to the new per-VLAN table.
 *
 * @author Yann Robin <yann@islandbridgenetworks.ie>
 * @author Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @package IXP\Console\Commands\Upgrade
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class MigrateL2Addresses extends IXPCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'l2addresses:populate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will clear the layer2address table and then copy addresses from the read-only macaddress table.';

    /**
     * Execute the console command.
     *
     * Transfers data from the table 'macaddress' to the table 'layer2address'
     *
     * @return mixed
     */
    public function handle()
    {
        if( !$this->confirm( 'Are you sure you wish to proceed? This command will CLEAR the layer2address table and then copy '
                . 'addresses from the read-only macaddress table. Generally, this command should only ever be run once when initially '
                . 'populating the new table.' ) ) {
            return 1;
        }

        // Delete all the rows from the table Layer2Address
        DB::table( 'l2address' )->truncate();
        $this->info( 'The layer2address table has been truncated' );

        $this->info( 'Migration in progress, please wait...' );

        // get all the entries form the macaddress table
        DB::table( 'macaddress' )->orderBy( 'virtualinterfaceid' )->chunk( 100, function( $listMacAddresses ) {
            foreach( $listMacAddresses as $mac) {
                $vi = VirtualInterface::find( $mac->virtualinterfaceid );
                $cnt = 0;
                foreach( $vi->vlanInterfaces as $vli ) {
                    // Ensure the MAC address is unique for this LAN:
                    $forVlan = Layer2Address::from( 'l2address AS l' )
                        ->join( 'vlaninterface AS vli', 'vli.id',  'l.vlan_interface_id' )
                        ->join( 'vlan AS v', 'v.id', 'vli.vlanid' )
                        ->where( 'mac' , $mac->mac )
                        ->where( 'v.id', $vli->vlanid )
                        ->get();

                    if( $forVlan->count() ) {
                        $this->alert( 'Could not add additional instance of ' . $mac->mac . ' for '
                            . $vi->customer->name . ' with virtual interface: ' . route('virtual-interface@edit', [ 'vi' => $vi->id ] )
                            . ' as it already exists in this Vlan ' . $vli->vlan->name
                        );
                        continue;
                    }

                    // create the new Layer2Address entity with the information of the current macaddress table entry
                    Layer2Address::create([
                        'mac'                   => $mac->mac,
                        'vlan_interface_id'     => $vli->id,
                        'firstseen'             => now(),
                    ]);
                    $cnt++;
                }

                // if you create more than one layer2address for a virtualinterface, let the user know
                if( $cnt > 1 ) {
                    $this->alert( 'Created >1 layer2address for ' . $vi->customer->name . ' with virtual interface: '
                        . route('virtual-interface@edit', [ 'vi' => $vi->id ] ) );
                }
            }
        });

        $this->info( 'Also consider checking your database with: select mac, count(mac) as c from l2address group by mac having count(mac) > 1;' );
        $this->info( 'Migration completed successfully' );
    }
}