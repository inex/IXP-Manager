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

use IXP\Jobs\RipeAtlas\RunMeasurements as RunMeasurementsJob;

use IXP\Models\AtlasMeasurement;

/**
 * Artisan command to run the atlas measurements
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   RipeAtlas
 * @package    IXP\Console\Commands
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class RunMeasurements extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ripe-atlas:run-measurements';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run queued measurements';

    /**
     * Execute the console command.
     *
     * @return mixed
     *
     * @throws
     */
    public function handle(): int
    {
        $ams = AtlasMeasurement::select('am.*' )
            ->from( 'atlas_measurements AS am' )
            ->LeftJoin( 'atlas_runs AS ar' , 'am.run_id', 'ar.id' )
            ->where( 'ar.scheduled_at', '<=', now()->toDateTimeString() )
            ->whereNull( 'am.atlas_id' )->get();

        if( $ams->isEmpty() ) {
            if( $this->isVerbosityVerbose() ) {
                $this->info("No queued measurements to process");
            }
            return 0;
        }

        if( $this->isVerbosityVerbose() ) {
            $this->info("---- RUN MEASUREMENTS START ----");
        }

        $bar = $this->output->createProgressBar( $ams->count() );
        $bar->start();

        $atlasRuns = [];

        foreach( $ams as $am ){
            $atlasRuns[ $am->run_id ] = $am->atlasRun();
            RunMeasurementsJob::dispatchNow( $am );
            $bar->advance();
        }

        foreach( $atlasRuns  as $run ){
            $run->update( [ 'started_at' => now() ] );
        }

        $bar->finish();

        if( $this->isVerbosityVerbose() ) {
            $this->info("\n---- RUN MEASUREMENTS STOP ----");
        }

        return 0;
    }
}