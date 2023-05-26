<?php

namespace IXP\Console\Commands;

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
use Illuminate\Support\Facades\DB;

use IXP\Models\Customer;
 /**
  * Artisan command to update the in_peeringdb flag of members
  *
  * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
  * @author     Yann Robin <yann@islandbridgenetworks.ie>
  * @package    IXP\Console\Commands
  * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
  */
class InPeeringDb extends  Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ixp-manager:update-in-peeringdb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Update customers 'in peeringdb' state";

    /**
     * Execute the console command.
     *
     * @return mixed
     *
     * @throws
     */
    public function handle(): int
    {
        // get list of peeringdb networks:
        if( !( $json = file_get_contents( 'https://www.peeringdb.com/api/net.json?fields=asn' ) ) ) {
            $this->error( 'Could not load ASNs via PeeringDB\'s API' );
            return 1;
        }

        $peeringdb_asns = json_decode( $json, false );

        if( !isset( $peeringdb_asns->data ) || !count( $peeringdb_asns->data ) ) {
            $this->error( 'Empty or no ASNs returned from PeeringDB\'s API' );
            return 2;
        }

        $asns = [];
        foreach( $peeringdb_asns->data as $net ) {
            $asns[] = $net->asn;
        }

        // easiest thing to do here is, in a transaction, set all in_peeringdb to false
        // and then update those that are in peeringdb to true

        DB::transaction( function () use ( $asns ) {
            $qualifying = Customer::trafficking()->current()->count();
            $before     = Customer::trafficking()->current()->where([ 'in_peeringdb' => true ])->count();
            $after      = 0;

            DB::table( 'cust' )->update( [ 'in_peeringdb' => false ] );

            foreach( Customer::trafficking()->current()->get() as $c ) {
                if( in_array( $c->autsys, $asns, false ) ) {
                    $c->in_peeringdb = true;
                    $c->save();
                    $after++;
                }
            }

            $this->info( "PeeringDB membership updated - before/after/missing: {$before}/{$after}/" . ( $qualifying - $after ) );

        });
        return 0;
    }
}
