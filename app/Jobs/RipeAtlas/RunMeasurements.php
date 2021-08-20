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
    AtlasMeasurement,
    AtlasProbe,
};
use IXP\Services\RipeAtlas\ApiCall;

/**
 * RunMeasurements
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Jobs\RipeAtlas
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class RunMeasurements extends Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var AtlasMeasurement
     */
    protected $atlasMeasurement;

    /**
     * Create a new job instance.
     *
     * @param AtlasMeasurement $atlasMeasurement
     * d
     * @return void
     */
    public function __construct( AtlasMeasurement $atlasMeasurement )
    {
        $this->atlasMeasurement = $atlasMeasurement;
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
        $atlasRun   = $this->atlasMeasurement->atlasRun;
        $getAddress = $atlasRun->protocol === 4 ? 'address_v4' : 'address_v6';

        // get a random atlas probe for the protocol and customer
        $dprobe = AtlasProbe::forActiveProtocol( $atlasRun->protocol )->forCustomer( $this->atlasMeasurement->custDest->id )->get()->random();

        $sourceAS = $this->atlasMeasurement->custSource->autsys;
        $targetIP = $dprobe->$getAddress;


//            if( $this->isVerbose() ) {
//                $this->info( "Requesting measurement for {$this->atlasMeasurement->custSource->name} / {$this->atlasMeasurement->custDest->name}: {$sourceAS}/{$targetIP} IPv{$atlasRun->protocol}" );
//            }

        if( !$this->atlasMeasurement->atlas_id && ( $id = App::make(ApiCall::class )->requestAtlasTraceroute( $sourceAS, $targetIP, $atlasRun->protocol ) ) ) {
            $this->atlasMeasurement->update( [ 'atlas_id' => $id, 'atlas_create' => now() ] );
        }
    }
}