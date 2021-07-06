<?php

namespace IXP\Console\Commands\Grapher;

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

use Carbon\Carbon;

use Grapher, Mail;

use IXP\Mail\Grapher\PortsWithCounts as PortsWithCountsMail;

use IXP\Models\{
    Customer,
    TrafficDaily
};

use IXP\Services\Grapher\Graph;

 /**
  * Artisan command to email ports where a given error / discards count > 0
  *
  * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
  * @author     Yann Robin      <yann@islandbridgenetworks.ie>
  * @category   Grapher
  * @package    IXP\Console\Commands
  * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
  */
class EmailPortsWithCounts extends GrapherCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grapher:email-ports-with-counts {email}
                        {--errors : Ports with an error count (default)}
                        {--discards : Ports with a discard count}
                        {--B|backend= : Which graphing backend to use}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email ports with an error / discards count > 0';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        Grapher::backend( $this->option( 'backend' ) );
        $this->setGrapher( Grapher::getFacadeRoot() );

        if( ( $retval = $this->verifyArgsAndOptions() ) !== 0 ) {
            return $retval;
        }

        if( $this->option( 'discards' ) ) {
            $category = Graph::CATEGORY_DISCARDS;
        } else {
            $category = Graph::CATEGORY_ERRORS;
        }

        $ports = $this->emailPortsWithCounts( $category );

        if( count( $ports ) ) {
            Mail::to( explode( ',', $this->argument( 'email' ) ) )->send( new PortsWithCountsMail( $ports, $category ) );
        } else if( $this->isVerbosityVerbose() ) {
            $this->info("No ports with packet counts > 0");
        }

        return 0;
    }

    /**
     * @param $category
     *
     * @return array
     *
     * @throws
     */
    private function emailPortsWithCounts( $category ): array
    {
        $data   = TrafficDaily::loadTraffic( Carbon::yesterday(), $category );
        $ports  = [];

        foreach( $data as $d ) {
            if( $d[ 'day_tot_in' ] === 0 && $d[ 'day_tot_out' ] === 0 ) {
                continue;
            }

            $port = [];

            if( $this->isVerbosityVerbose() ) {
                $this->info( "{$d['name']}\n\t\tIN / OUT: {$d[ 'day_tot_in' ]} / {$d[ 'day_tot_out' ]}" );
            }

            $graph = $this->grapher()->customer( Customer::find( $d[ 'cust_id' ] ) )->setCategory( $category )->setPeriod( Graph::PERIOD_DAY );

            $port['cust_id']    = $d[ 'cust_id' ];
            $port['name']       = $d[ 'name' ];
            $port['in']         = $d[ 'day_tot_in' ];
            $port['out']        = $d[ 'day_tot_out' ];
            $port['png']        = $graph->png();

            $ports[] = $port;
        }

        return $ports;
    }

    /**
     * Check the various arguments and options that have been password to the console command
     *
     * @return int 0 for success or else an error code
     */
    protected function verifyArgsAndOptions(): int
    {
        $emails = explode( ',', $this->argument('email') );

        foreach( $emails as $e ) {
            if( filter_var( $e, FILTER_VALIDATE_EMAIL ) === false ) {
                $this->error( "Invalid email address: $e" );
                return 254;
            }
        }

        // all good :-D
        return 0;
    }
}
