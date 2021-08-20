<?php

namespace IXP\Console\Commands\Router;

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

use Cache;

use IXP\Console\Commands\Command;
use IXP\Jobs\FetchFilteredPrefixesForCustomer;
use IXP\Models\Customer;
use IXP\Utils\Foil\Extensions\Bird as BirdFoilExtension;


 /**
  * Artisan command to generate router configurations
  *
  * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
  * @author     Yann Robin <yann@islandbridgenetworks.ie>
  * @category   Router
  * @package    IXP\Console\Commands
  * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
  */
class FilteredPrefixes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'router:filtered-prefixes
                      {customer? : Customer ASN, ID or shortname (in that order). Otherwise all customers.}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate route server/collector/etc. configurations';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        $customer = $this->resolveCustomer();

        $this->info( "Checking route server filtering for " . $customer->abbreviatedName . ". Please wait..." );

        FetchFilteredPrefixesForCustomer::dispatchNow( $customer );
        $filteredPrefixes = Cache::get( 'filtered-prefixes-' . $customer->id );

        if( $filteredPrefixes === [] ) {
            $this->info( "No filtered prefixes found" );
        }

        $headers = [ "Prefix", "Reason", "Routers Where Filtered" ];
        $prefixes = [];
        $bfe = new BirdFoilExtension();

        foreach( $filteredPrefixes as $network => $p ) {
            $fp[0] = $network;

            $reason = '';
            foreach( $p['reasons'] as $r ) {
                $bfe_couple = $bfe->translateBgpFilteringLargeCommunity( substr( $r, strpos( $r, ':' ) ) );
                $reason .= ( $bfe_couple ? $bfe_couple[0] : 'UNKNOWN' ) . ' ';
            }
            $fp[1] = trim( $reason );
            $fp[2] = implode( ' ', array_keys( $p['age'] ) );
            $prefixes[] = $fp;
        }

        $this->table( $headers, $prefixes );

        return 0;
    }


    /**
     * Returns all customers or, if specified on the command line, a specific customer
     *
     * @return Customer
     */
    protected function resolveCustomer(): Customer
    {
        $custarg = $this->argument('customer');

        // assume ASN first:
        if( is_numeric( $custarg ) && ( $c = Customer::where( 'autsys', $custarg )->first() ) ) {
            return $c;
        }

        // then ID:
        if( is_numeric( $custarg ) && ( $c = Customer::find( $custarg ) ) ) {
            return $c;
        }

        if( $c = Customer::where( 'shortname', $custarg  )->first() ) {
            return $c;
        }

        $this->error( "Could not find a customer matching asn/id/shortname: " . $custarg );

        exit(1);
    }
}