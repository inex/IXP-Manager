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

use Grapher, Mail;

use IXP\Mail\Grapher\PortUtilisation as PortUtilisationMail;

use IXP\Models\{
    Customer,
};

use IXP\Services\Grapher\Graph;

 /**
  * Artisan command to email port utilisation records
  *
  * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
  * @author     Yann Robin      <yann@islandbridgenetworks.ie>
  * @category   Grapher
  * @package    IXP\Console\Commands
  * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
  */
class EmailPortUtilisation extends GrapherCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grapher:email-port-utilisations {email}
                        {--T|threshold=80 : Min percentage usage to include in report (default 80)}
                        {--B|backend= : Which graphing backend to use}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email a port utilisation report (separate multiple emails with commas)';


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

        $custs = Customer::currentActive(true, true )->get();
        $excess = [];
        foreach( $custs as $c ) {
            foreach( $c->virtualInterfaces as $vi ) {
                if( ( $speed = $vi->speed() * 1000 * 1000 ) === 0 ) {
                    continue;
                }

                if( $vi->physicalInterfaces->count() === 1 ) {
                    $graph = $this->grapher()->physint( $vi->physicalInterfaces()->first() )->setCategory( Graph::CATEGORY_BITS )->setPeriod( Graph::PERIOD_WEEK );
                } else {
                    $graph = $this->grapher()->virtint( $vi )->setCategory( Graph::CATEGORY_BITS )->setPeriod( Graph::PERIOD_WEEK );
                }

                $stats   = $graph->statistics();
                $utilIn  = ( $stats->maxIn()  * 100.0 ) / $speed;
                $utilOut = ( $stats->maxOut() * 100.0 ) / $speed;

                if( $utilIn > $this->option('threshold') || $utilOut > $this->option('threshold') ) {
                    $excess[ $c->id ]['cust'] = $c;

                    $port['speed']   = $speed/1000/1000/1000;
                    $port['utilIn']  = $utilIn;
                    $port['utilOut'] = $utilOut;
                    $port['switch']  = $vi->physicalInterfaces()->first()->switchPort->switcher;
                    $port['png']     = $graph->png();

                    if( $this->isVerbosityVerbose() ) {
                        $this->warn( sprintf( "%s\n\tIN %0.2f%%\tOUT: %0.2f%%", $c->name, $utilIn, $utilOut ) );
                    }

                    $excess[ $c->id ]['ports'][] = $port;

                } elseif( $this->isVerbosityVeryVerbose() ) {
                    $this->info( sprintf( "%s\n\tIN %0.2f%%\tOUT: %0.2f%%", $c->name, $utilIn, $utilOut ) );
                }
            }
        }

        if( count( $excess ) ) {
            Mail::to( explode( ',', $this->argument( 'email' ) ) )->send( new PortUtilisationMail( $excess, $this->option('threshold') ) );
        }

        return 0;
    }


    /**
     * Check the various arguments and options that have been password to the console command
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

        $t = $this->option('threshold');
        if( !is_numeric($t) || (float)$t < 0.0 || (float)$t > 100.0 ) {
            $this->error( "Invalid value for threshold. Must be between 0 and 100." );
            return 253;
        }

        // all good :-D
        return 0;
    }
}