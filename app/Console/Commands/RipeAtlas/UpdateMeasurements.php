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

use IXP\Jobs\RipeAtlas\UpdateMeasurements as UpdateMeasurementsJob;

use IXP\Models\AtlasMeasurement;

/**
 * Artisan command to update pending atlas measurements
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Console\Commands\RipeAtlas
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class UpdateMeasurements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ripe-atlas:update-measurements';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update any pending atlas measurements';

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
            $this->info("---- UPDATE MEASUREMENTS START ----");
        }

        $ams = AtlasMeasurement::whereNull( 'atlas_stop' )->get();

        $bar = $this->output->createProgressBar( $ams->count() );

        $bar->start();

        // find uncompleted measurements:
        $ams->each( function( $am ) use( $bar ) {
            UpdateMeasurementsJob::dispatchNow( $am );
            $bar->advance();
        });

        $bar->finish();

        if( $this->isVerbosityVerbose() ) {
            $this->info("\n---- UPDATE MEASUREMENTS STOP ----");
        }
        return 0;
    }
}