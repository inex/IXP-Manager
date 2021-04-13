<?php

namespace IXP\Jobs\RipeAtlas;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */
use Illuminate\Bus\Queueable;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Illuminate\Queue\{
    SerializesModels,
    InteractsWithQueue
};

use IXP\Models\{
    AtlasRun,
};

use IXP\Jobs\Job;

/**
 * CompleteRequests
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Jobs\RipeAtlas
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CompleteRequests extends Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var AtlasRun
     */
    protected $atlasRun;

    /**
     * Create a new job instance.
     *
     * @param AtlasRun $atlasRun
     * @return void
     */
    public function __construct( AtlasRun $atlasRun )
    {
        $this->atlasRun = $atlasRun;
    }

    /**
     * Execute the job.
     *
     * @return bool
     *
     * @throws
     */
    public function handle()
    {
        $ams = $this->atlasRun->atlasMeasurements()->get();

        // Does All the measurements are stopped ?
        if( $ams->contains( 'atlas_stop', null ) ) {
            return 0;
        }

        $this->atlasRun->update( [ 'completed_at' => now() ] );
        return 1;
    }
}