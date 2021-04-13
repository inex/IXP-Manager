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

use IXP\Jobs\RipeAtlas\CompleteRequests as CompleteRequestsJob;

use IXP\Models\AtlasRun;

/**
 * Artisan command to Complete the atlas measurements
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Console\Commands\RipeAtlas
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CompleteRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ripe-atlas:complete-requests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark requests complete if all measurements are complete';

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
            $this->info("---- COMPLETE REQUESTS START ----");
        }

        $ars = AtlasRun::whereNull( 'completed_at' )->get();

        $bar = $this->output->createProgressBar( $ars->count() );
        $bar->start();

        // find new requests:
        if( $ars->isEmpty() ) {
            if( $this->isVerbosityVerbose() ) {
                $this->info("No open requests to process");
            }
            return 0;
        }

        $ars->each( function ( $ar ) use ( $bar ) {
            if( CompleteRequestsJob::dispatchNow( $ar ) && $this->isVerbosityVerbose() ) {
                $this->info("Marking request: Atals Run ID:{$ar->id} complete");
            }
            $bar->advance();
        });

        $bar->finish();

        if( $this->isVerbosityVerbose() ) {
            $this->info("\n---- COMPLETE REQUESTS STOP  ----");
        }
        return 0;
    }
}