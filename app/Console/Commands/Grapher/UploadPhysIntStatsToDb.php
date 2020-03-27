<?php namespace IXP\Console\Commands\Grapher;

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


use Entities\Customer;
use IXP\Contracts\Grapher\Backend as GrapherBackend;

use D2EM;
use Carbon\Carbon;
use Entities\TrafficDailyPhysInt;
use Grapher;
use IXP\Services\Grapher\Graph;


 /**
  * Artisan command to upload member traffic stats to the database
  *
  * @author     Barry O'Donovan <barry@opensolutions.ie>
  * @category   Grapher
  * @package    IXP\Console\Commands
  * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
  */
class UploadPhysIntStatsToDb extends GrapherCommand {

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
     * @return mixed
     * @throws \IXP\Exceptions\Services\Grapher\ParameterException
     * @throws \Exception
     */
    public function handle(): int {

        Grapher::backend( $this->option( 'backend' ) );
        $this->setGrapher( Grapher::getFacadeRoot() );

        // disable the cache if it's in-memory
        if( config('cache.default') === 'array' ) {
            $this->grapher()->disableCache();
        }

        // This should only be done once a day and if values already exist for 'today', just delete them.
        $today = now();
        D2EM::getRepository( 'Entities\TrafficDailyPhysInt' )->deleteForDay( $today );

        /** @var Customer[] $custs */
        $custs = D2EM::getRepository( 'Entities\Customer')->getConnected( false, true );

        foreach( $custs as $cust )  {
            if( $this->isVerbosityVerbose() ) {
                $this->info( "\t- processing customer " . $cust->getName() );
            }

            foreach( $cust->getVirtualInterfaces() as $vi ) {

                foreach( $vi->getPhysicalInterfaces() as $pi ) {

                    foreach( Graph::CATEGORIES as $category ) {

                        $graph = $this->grapher()->physint( $pi )->setCategory( $category );

                        $td = new TrafficDailyPhysInt;
                        $td->setDay( $today );
                        $td->setCategory( $category );
                        $td->setPhysicalInterface( $pi );

                        foreach( Graph::PERIOD_DESCS as $period => $name ) {
                            $stats = $graph->setPeriod($period)->statistics();

                            $fn = "set{$name}AvgIn";    $td->$fn( $stats->averageIn()   );
                            $fn = "set{$name}AvgOut";   $td->$fn( $stats->averageOut()  );
                            $fn = "set{$name}MaxIn";    $td->$fn( $stats->maxIn()       );
                            $fn = "set{$name}MaxOut";   $td->$fn( $stats->maxOut()      );
                            $fn = "set{$name}MaxInAt";  $td->$fn( $stats->maxInAt()     );
                            $fn = "set{$name}MaxOutAt"; $td->$fn( $stats->maxOutAt()     );
                            $fn = "set{$name}TotIn";    $td->$fn( $stats->totalIn()     );
                            $fn = "set{$name}TotOut";   $td->$fn( $stats->totalOut()    );
                        }

                        unset( $graph );
                        D2EM::persist( $td );
                    }
                }
            }

            D2EM::flush();
        }

        if( config( 'grapher.cli.traffic_daily.delete_old', true ) ) {
            if( $this->isVerbosityVerbose() ) {
                $this->warn( "Deleting old daily traffic records that are no longer required" );
            }

            D2EM::getRepository( 'Entities\TrafficDailyPhysInt' )->deleteBefore(
                new Carbon( "-" . config( 'grapher.cli.traffic_daily.delete_old_days', 140 ) . " days" )
            );
        }

        return 0;
    }
}
