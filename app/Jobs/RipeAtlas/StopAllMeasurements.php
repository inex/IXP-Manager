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

use App;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Illuminate\Queue\{
    SerializesModels,
    InteractsWithQueue
};

use IXP\Jobs\Job;

use IXP\Models\{
    AtlasMeasurement
};
use IXP\Services\RipeAtlas\ApiCall;

/**
 * StopAllMeasurements
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Jobs\RipeAtlas
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class StopAllMeasurements extends Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    protected $atlas_id;

    /**
     * Create a new job instance.
     *
     * @param int $atlas_id
     *
     * @return void
     */
    public function __construct( int $atlas_id )
    {
        $this->atlas_id = $atlas_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     *
     * @throws
     */
    public function handle(): void
    {
        App::make(ApiCall::class )->atlasStopMeasurement( $this->atlas_id );
    }
}