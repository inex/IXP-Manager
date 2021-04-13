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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
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

use IXP\Jobs\Job;

use IXP\Models\{
    Aggregators\CustomerAggregator,
    AtlasMeasurement,
    AtlasRun,
    Customer};

/**
 * CreateMeasurements
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Jobs\RipeAtlas
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CreateMeasurements extends Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var AtlasRun
     */
    protected $atlasRun;

    /**
     * @var array of customer
     */
    protected $customers;

    /**
     * Create a new job instance.
     *
     * @param AtlasRun  $atlasRun
     * @param array     $customers
     *
     * @return void
     */
    public function __construct( AtlasRun $atlasRun, array $customers = [] )
    {
        $this->atlasRun     = $atlasRun;
        $this->customers    = $customers;
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
        // find all candidate networks. I.e. networks with probes for this protocol
        $networks = CustomerAggregator::withProbesForProtocol( $this->atlasRun->protocol, $this->atlasRun->vlan->id , $this->customers );

        $networks->each( function ( $src ) use ( $networks ) {
            $networks->each( function ( $dest ) use ( $src ) {
                if( $src->id !== $dest->id ) {
                    AtlasMeasurement::create( [ 'run_id' => $this->atlasRun->id, 'cust_source' => $src->id, 'cust_dest' => $dest->id ] );
                }
            });
        });
    }
}