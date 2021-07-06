<?php

namespace IXP\Console\Commands\RipeAtlas;

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

use IXP\Console\Commands\Command;

use IXP\Jobs\RipeAtlas\UpdateProbes as UpdateProbesJob;

use IXP\Models\Customer;

/**
 * Artisan command to update the Ripe Atlas probes
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Console\Commands\RipeAtlas
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class UpdateProbes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ripe-atlas:update-probes
                        {customer? : Customer ASN, ID or shortname (in that order). Otherwise all customers.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the Ripe Atlas probes for all customers (or a given customer by ASN/ID/shortname)';

    /**
     * Execute the console command.
     *
     * @return mixed
     *
     * @throws
     */
    public function handle(): int
    {
        if( $this->isVerbosityVerbose() ) {
            $this->info("---- PROBES UPDATE START ----");
        }

        $customers = $this->resolveCustomers();

        $bar = $this->output->createProgressBar( count( $customers ) );
        $bar->start();

        foreach( $customers as $c ){
            UpdateProbesJob::dispatchNow( $c );
            $bar->advance();
        }

        $bar->finish();

        if( $this->isVerbosityVerbose() ) {
            $this->info("---- PROBES UPDATE STOP ----");
        }

        return 0;
    }


    /**
     * Returns all customers or, if specified on the command line, a specific customer
     *
     * @return array Customer
     */
    protected function resolveCustomers()
    {
        $cust = $this->argument('customer');

        // if no customer, return all appropriate ones:
        if( !$cust ) {
            return Customer::CurrentActive( true, false, false )->get();
        }

        // assume ASN first:
        if( is_numeric( $cust ) && ( $c = Customer::where( 'autsys', $cust )->first() ) ) {
            return [ $c ];
        }

        // then ID:
        if( is_numeric( $cust ) && ( $c = Customer::find( $cust ) ) ) {
            return [ $c ];
        }

        // then check shortname:
        if( $c = Customer::where( 'shortname', $cust )->first() ) {
            return [ $c ];
        }

        $this->error( "Could not find a customer matching asn/id/shortname: " . $cust );
        exit( 0 );
    }
}