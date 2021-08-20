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

use Carbon\Carbon;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Illuminate\Queue\{
    SerializesModels,
    InteractsWithQueue
};

use IXP\Events\RipeAtlas\MeasurementComplete as MeasurementCompleteEvent;

use IXP\Jobs\Job;

use IXP\Models\{
    AtlasMeasurement,
};
use IXP\Services\RipeAtlas\ApiCall;

/**
 * UpdateMeasurements
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Jobs\RipeAtlas
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class UpdateMeasurements extends Job implements ShouldQueue
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
     * @return void
     */
    public function __construct( AtlasMeasurement $atlasMeasurement )
    {
        $this->atlasMeasurement = $atlasMeasurement;
    }

    /**
     * Execute the job.
     *
     * @return int
     *
     * @throws
     */
    public function handle(): int
    {
        if( !( $atlasId = $this->atlasMeasurement->atlas_id ) ) {
            return 0;
        }

        $measurement = App::make(ApiCall::class )->updateAtlasMeasurement( $atlasId )[ "response" ];

        $this->atlasMeasurement->update( [ 'atlas_request' => json_encode( $measurement, JSON_THROW_ON_ERROR ) ] );

        if( !$this->atlasMeasurement->atlas_start ){
            $this->atlasMeasurement->update( [ 'atlas_start' => now() ] );
        }

        if( isset( $measurement->status->name ) ) {
            $this->atlasMeasurement->update( [ 'atlas_state' => $measurement->status->name ] );

            if( $measurement->status->name === "Stopped" ) {
                $this->atlasMeasurement->update( [
                    'atlas_stop' => now(),
                    'atlas_data' => file_get_contents( "https://atlas.ripe.net/api/v2/measurements/" . $atlasId . '/results' )
                ] );

            } else if( in_array( $measurement->status->name, [ "Failed", "No suitable probes" ] ) ) {
                $this->atlasMeasurement->update( [ 'atlas_stop' => now() ] );
            }
        }

        // if both in about out is complete with data, emit an event
        if( $this->atlasMeasurement->atlas_stop && $this->atlasMeasurement->atlas_data ) {
            //            if( $this->isVerbose() ) {
            //                $this->info( 'Emitting measurement complete event for measurement ' . $atlasId );
            //            }

            event( new MeasurementCompleteEvent( $this->atlasMeasurement ) );
            return 1;
        }

        // after an hour, consider outstanding measurements as dead
        if( $this->atlasMeasurement->atlas_start && !$this->atlasMeasurement->atlas_stop && Carbon::parse( $this->atlasMeasurement->atlas_start )->diffInMinutes( now() ) >= 120 ) {
            //            if( $this->isVerbose() ) {
            //                $this->info( 'Expiring in measurement ' . $atlasId );
            //            }

            $this->atlasMeasurement->update( [
                'atlas_stop'    => now(),
                'atlas_state'   => 'ABANDONNED'
            ] );

            App::make(ApiCall::class )->atlasStopMeasurement( $atlasId );
        }

        return 0;
    }
}