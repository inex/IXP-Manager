<?php

/*
 * Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee.
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

namespace IXP\Console\Commands;

use Doctrine\ORM\EntityRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use D2EM;

use Entities\{
    Layer2Address as Layer2AddressEntity,
    VirtualInterface as VirtualInterfaceEntity
};

class l2addresses extends Command {
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
    protected $description = 'This command will CLEAR the layer2address table and then copy addresses from the read-only macaddress table.';

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
     * Execute the console command. Allow to transfert the datas from the table macaddress to the table layer2address
     *
     * @return mixed
     */
    public function handle() {
        if ( $this->confirm( 'Are you sure you wish to proceed? This command will CLEAR the layer2address table and then copy addresses from the read-only macaddress table. Generally, this command should only ever be run once when initially populating the new table.' ) ) {

            // Delete all the rows from the table Layer2Address
            DB::table( 'l2address' )->truncate();

            // display the message in the console
            $this->info( 'The table has been l2address truncate' );

            $this->info( 'Migration processing, please wait.' );

            $lastVi = 0;
            // get all the entries form the macaddress table
            DB::table( 'macaddress' )->orderBy( 'virtualinterfaceid' )->chunk( 100, function ( $listMacAddresses ) use ( $lastVi ) {
                foreach ( $listMacAddresses as $mac) {
                    $vi = D2EM::getRepository( VirtualInterfaceEntity::class )->find( $mac->virtualinterfaceid );
                    /** @var VirtualInterfaceEntity $vi */

                    foreach ( $vi->getVlanInterfaces() as $vli ) {
                        // create the new Layer2Address entity with the information of the current macaddress table entry
                        $l2a = new Layer2AddressEntity();
                        /** @var Layer2AddressEntity $l2a */
                        $l2a->setMac( $mac->mac );
                        $l2a->setVlanInterface( $vli );
                        $l2a->setCreatedAt( new \DateTime );
                        $l2a->setFirstSeenAt( new \DateTime( $mac->firstseen ) );
                        $l2a->setLastSeenAt( new \DateTime( $mac->lastseen ) );
                        D2EM::persist( $l2a );
                    }

                    // if you create more than one layer2address for a virtualinterface
                    if( $lastVi == $mac->virtualinterfaceid){
                        $this->info( 'Created >1 layer2address for virtual interface ID: '. Env( 'APP_URL' ) .'/virtual-interface/edit/id/'.$mac->virtualinterfaceid );
                    }

                    $lastVi = $mac->virtualinterfaceid;
                }
                D2EM::flush();
            });

            $this->info( 'Migration has been done successfully' );
        }
    }
}
