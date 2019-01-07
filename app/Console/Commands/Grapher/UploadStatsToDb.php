<?php namespace IXP\Console\Commands\Grapher;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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


use IXP\Contracts\Grapher\Backend as GrapherBackend;

use D2EM;
use DateTime;
use Entities\TrafficDaily as TrafficDailyEntity;
use Grapher;
use IXP\Services\Grapher\Graph;


 /**
  * Artisan command to upload member traffic stats to the database
  *
  * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
  * @category   Grapher
  * @package    IXP\Console\Commands
  * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
  */
class UploadStatsToDb extends GrapherCommand {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grapher:upload-stats-to-db
                    {--B|backend= : Which graphing backend to use}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload port stats to the database (daily task)';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int {

        Grapher::backend( $this->option( 'backend' ) );
        $this->setGrapher( Grapher::getFacadeRoot() );

        // This should only be done once a day and if values already exist for 'today', just delete them.
        $day = date( 'Y-m-d' );
        D2EM::getRepository( 'Entities\TrafficDaily' )->deleteForDay( $day );

        $custs = D2EM::getRepository( 'Entities\Customer')->getConnected( false, true );

        foreach( $custs as $cust )  {
            if( $this->isVerbosityVerbose() ) {
                $this->info( "\t- processing customer " . $cust->getName() );
            }

            foreach( Graph::CATEGORIES as $category ) {
                $graph = $this->grapher()->customer( $cust )->setCategory( $category );

                $td = new TrafficDailyEntity;
                $td->setDay( new DateTime( $day ) );
                $td->setCategory( $category );
                $td->setCustomer( $cust );
                $td->setIXP( D2EM::getRepository('Entities\IXP')->getDefault() );

                foreach( Graph::PERIOD_DESCS as $period => $name ) {
                    $stats = $graph->setPeriod($period)->statistics();

                    $fn = "set{$name}AvgIn";  $td->$fn( $stats->averageIn()  );
                    $fn = "set{$name}AvgOut"; $td->$fn( $stats->averageOut() );
                    $fn = "set{$name}MaxIn";  $td->$fn( $stats->maxIn()      );
                    $fn = "set{$name}MaxOut"; $td->$fn( $stats->maxOut()     );
                    $fn = "set{$name}TotIn";  $td->$fn( $stats->totalIn()    );
                    $fn = "set{$name}TotOut"; $td->$fn( $stats->totalOut()   );
                }

                D2EM::persist( $td );
            }

            D2EM::flush();
        }

        if( config( 'grapher.cli.traffic_daily.delete_old', true ) ) {
            if( $this->isVerbosityVerbose() ) {
                $this->warn( "Deleting old daily traffic records that are no longer required" );
            }

            D2EM::getRepository( 'Entities\TrafficDaily' )->deleteBefore(
                new DateTime( "-" . config( 'grapher.cli.traffic_daily.delete_old_days', 140 ) . " days" )
            );
        }

        return 0;
    }
}
