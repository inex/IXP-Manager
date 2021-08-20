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
  * @category   Irrdb
  * @package    IXP\Console\Commands
  * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
  */
class InManrs extends  Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ixp-manager:update-in-manrs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Update customers 'in MANRS' state";

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Throwable
     */
    public function handle(): int
    {
        // get list of peeringdb networks:
        if( !( $json = file_get_contents( 'https://www.manrs.org/wp-json/manrs/v1/asn' ) ) ) {
            $this->error( 'Could not load ASNs via MANRS\'s API' );
            return 1;
        }

        $asns = json_decode( $json, true );

        if( !is_array( $asns ) || !count( $asns ) ) {
            $this->error( 'Empty or no ASNs returned from MANRS\'s API' );
            return 2;
        }

        // easiest thing to do here is, in a transaction, set all in_manrs to false
        // and then update those that are in MANRS to true

        DB::transaction( function () use ( $asns ) {
            $qualifying = Customer::trafficking()->current()->count();
            $before     = Customer::trafficking()->current()->where([ 'in_manrs' => true ])->count();
            $after      = 0;

            DB::table( 'cust' )->update( [ 'in_manrs' => false ] );

            foreach( Customer::trafficking()->current()->get() as $c ) {
                if( in_array( $c->autsys, $asns, false ) ) {
                    $c->in_manrs = true;
                    $c->save();
                    $after++;
                }
            }
            $this->info( "MANRS membership updated - before/after/missing: {$before}/{$after}/" . ( $qualifying - $after ) );

        });

        return 0;
    }
}
