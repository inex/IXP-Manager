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

use Grapher;

use IXP\Services\Grapher\Graph;

use IXP\Models\{
    Customer,
    TrafficDailyPhysInt
};

 /**
  * Artisan command to upload member traffic stats to the database
  *
  * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
  * @author     Yann Robin      <yann@islandbridgenetworks.ie>
  * @category   Grapher
  * @package    IXP\Console\Commands
  * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
  */
class UploadPhysIntStatsToDb extends GrapherCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grapher:upload-pi-stats-to-db
                    {--B|backend= : Which graphing backend to use}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload individual physical interface stats to the database (daily task)';

    /**
     * Execute the console command.
     *
     * @throws
     */
    public function handle(): int
    {
        Grapher::backend( $this->option( 'backend' ) );
        $this->setGrapher( Grapher::getFacadeRoot() );

        // disable the cache if it's in-memory
        if( config('cache.default') === 'array' ) {
            $this->grapher()->disableCache();
        }

        // This should only be done once a day and if values already exist for 'today', just delete them.
        $today = now();
        TrafficDailyPhysInt::where( 'day', $today->format('Y-m-d') )->delete();

        $custs = Customer::getConnected( true );

        foreach( $custs as $cust )  {
            if( $this->isVerbosityVerbose() ) {
                $this->info( "\t- processing customer " . $cust->name );
            }

            foreach( $cust->virtualInterfaces as $vi ) {
                foreach( $vi->physicalInterfaces as $pi ) {
                    foreach( Graph::CATEGORIES as $category ) {
                        $graph = $this->grapher()->physint( $pi )->setCategory( $category );

                        $td = new TrafficDailyPhysInt;
                        $td->physicalinterface_id = $pi->id;
                        $td->day = $today;
                        $td->category = $category;

                        foreach( Graph::PERIOD_DESCS as $period => $name ) {
                            $stats = $graph->setPeriod( $period )->statistics();
                            $lname = strtolower( $name );
                            $fn = "{$lname}_avg_in";     $td->$fn = $stats->averageIn();
                            $fn = "{$lname}_avg_out";    $td->$fn = $stats->averageOut();
                            $fn = "{$lname}_max_in";     $td->$fn = $stats->maxIn();
                            $fn = "{$lname}_max_out";    $td->$fn = $stats->maxOut();
                            $fn = "{$lname}_max_in_at";  $td->$fn = $stats->maxInAt();
                            $fn = "{$lname}_max_out_at"; $td->$fn = $stats->maxOutAt();
                            $fn = "{$lname}_tot_in";     $td->$fn = $stats->totalIn();
                            $fn = "{$lname}_tot_out";    $td->$fn = $stats->totalOut();
                        }

                        unset( $graph );
                        $td->save();
                    }
                }
            }
        }

        if( config( 'grapher.cli.traffic_daily.delete_old', true ) ) {
            if( $this->isVerbosityVerbose() ) {
                $this->warn( "Deleting old daily traffic records that are no longer required" );
            }

            $day = new Carbon( "-" . config( 'grapher.cli.traffic_daily.delete_old_days', 140 ) . " days" );
            TrafficDailyPhysInt::where( 'day', '<', $day->format('Y-m-d') )->delete();
        }

        return 0;
    }
}