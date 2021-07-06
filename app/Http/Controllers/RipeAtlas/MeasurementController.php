<?php

namespace IXP\Http\Controllers\RipeAtlas;

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

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Route;

use Illuminate\Http\{
    RedirectResponse,
    Request
};

use Illuminate\View\View;

use IXP\Models\{
    AtlasMeasurement,
    AtlasRun
};

use IXP\Jobs\RipeAtlas\{
    RunMeasurements     as RunMeasurementsJob,
    StopAllMeasurements as StopAllMeasurementsJob,
    UpdateMeasurements  as UpdateMeasurementsJob
};

use IXP\Utils\Http\Controllers\Frontend\EloquentController as Eloquent2Frontend;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * Measurement Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\RipeAtlas
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class MeasurementController extends Eloquent2Frontend
{
    /**
     * The object being created / edited
     *
     * @var AtlasMeasurement
     */
    protected $object = null;

    /**
     * Is this a read only controller?
     *
     * @var boolean
     */
    public static $read_only = true;

    /**
     * The URL prefix to use.
     *
     * Automatically determined based on the controller name if not set.
     *
     * @var string
     */
    protected static $route_prefix = "ripe-atlas/measurements";

    /**
     * This function sets up the frontend controller
     */
    public function feInit()
    {
        $this->feParams         = (object)[
            'model'             => AtlasMeasurement::class,
            'pagetitle'         => 'Ripe Atlas :: Measurements',
            'titleSingular'     => 'Measurement',
            'nameSingular'      => 'an atlas measurement',
            'listOrderBy'       => 'id',
            'listOrderByDir'    => 'DESC',
            'viewFolderName'    => 'ripe-atlas/measurement',
            'readonly'          => self::$read_only,
            'listColumns'       => [
                'run_id'  => [
                    'title'      => 'ID Run',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'ripe-atlas/runs',
                    'action'     => 'view',
                    'idField'    => 'run_id',
                ],
                'cs_name'  => [
                    'title'      => 'Customer Source',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'customer',
                    'action'     => 'overview',
                    'idField'    => 'cust_source',
                ],
                'cd_name'  => [
                    'title'      => 'Customer Destination',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'customer',
                    'action'     => 'overview',
                    'idField'    => 'cust_dest',
                ],
                'atlas_id'  => [
                    'title'         => 'Atlas ID',
                    'type'          => self::$FE_COL_TYPES[ 'JSON' ],
                    'displayAs'     => 'text',
                    'valueFrom'     => 'url',
                    'value'         => url( '/api/v4/ripe-atlas/measurement/' ) . '/%%COL%%/info',
                ],
                'atlas_request'       => [
                    'title'         => 'Atlas Request',
                    'type'          => self::$FE_COL_TYPES[ 'JSON' ],
                    'displayAs'     => 'btn',
                    'valueFrom'     => 'DB',
                ],
                'atlas_state'  => 'State',
            ],
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = array_merge(
            $this->feParams->listColumns,
            [
                'atlas_data'       => [
                    'title'         => 'Atlas Data',
                    'type'          => self::$FE_COL_TYPES[ 'JSON' ],
                    'displayAs'     => 'btn',
                    'valueFrom'     => 'DB',
                ],
                'atlas_start'       => 'Atlas Start',
                'atlas_stop'        => 'Atlas Stop',
                'created_at'        => [
                    'title'      => 'Created at',
                    'type'       => self::$FE_COL_TYPES[ 'DATETIME' ],
                ],
                'updated_at'        => [
                    'title'      => 'Updated at',
                    'type'       => self::$FE_COL_TYPES[ 'DATETIME' ],
                ],
            ]
        );
    }

    /**
     * Function which can be over-ridden to add additional routes
     *
     * If you don't want to use the defaults as well as some additionals, override
     * `routes()` instead.
     *
     * @param string $route_prefix
     *
     * @return void
     */
    protected static function additionalRoutes( string $route_prefix ): void
    {
        Route::group( [ 'prefix' => $route_prefix ], function() use ( $route_prefix ) {
            Route::get( 'matrix/{atlasRun}',            'RipeAtlas\MeasurementController@matrix'                )->name( $route_prefix . '@matrix'              );
            Route::post('run/{atlasrun}',               'RipeAtlas\MeasurementController@runMeasurements'       )->name( $route_prefix . '@run-measurements'    );
            Route::put( 'update/{atlasrun}',            'RipeAtlas\MeasurementController@updateMeasurements'    )->name( $route_prefix . '@update-measurements' );
            Route::put( 'stop-measurements/{atlasrun}', 'RipeAtlas\MeasurementController@stopMeasurements'      )->name( $route_prefix . '@stop-measurements'   );
        });
    }

    /**
     * List the contents of a database table.
     *
     * @param  Request  $r
     *
     * @return View
     */
    public function list( Request $r  ) : View
    {
        $rid = false;
        if(  $run = AtlasRun::find( $r->atlasrun ) ) {
            $rid = $run->id;
        }

        $this->data[ 'params' ][ 'rid' ]    = $rid;
        $this->data[ 'rows' ]               = $this->listGetData();

        $this->listIncludeTemplates();
        $this->preList();

        return $this->display( 'list' );
    }

    /**
     * Provide array of rows for the list action and view action
     *
     * @param int $id The `id` of the row to load for `view` action`. `null` if `listAction`
     *
     * @return array
     */
    protected function listGetData( $id = null ): array
    {
        $rid        = $this->data[ 'params' ][ 'rid' ] ?? null;
        $feParams   = $this->feParams;

        return AtlasMeasurement::selectRaw(
            'atlas_measurements.*,
            cs.abbreviatedName as cs_name,
            cd.abbreviatedName as cd_name,
            atlas_results.id as atlas_result_id'
        )
        ->leftJoin( 'cust as cs',   'atlas_measurements.cust_source', 'cs.id' )
        ->leftJoin( 'cust as cd',   'atlas_measurements.cust_dest', 'cd.id' )
        ->leftJoin('atlas_results', 'atlas_measurements.id', 'atlas_results.measurement_id'  )
        ->when( $id, function( Builder $q, $id ) {
            return $q->where( 'atlas_measurements.id', $id );
        } )->when( $rid, function( Builder $q, $rid ) {
            return $q->where( 'atlas_measurements.run_id', $rid );
        } )->when( $feParams->listOrderBy , function( Builder $q ) use( $feParams ) {
            return $q->orderBy( $feParams->listOrderBy, $feParams->listOrderByDir ?? 'ASC');
        })->get()->toArray();
    }

    /**
     * Function that run measurements for an atlas run
     *
     * @param AtlasRun $atlasrun
     *
     * @return RedirectResponse
     */
    public function runMeasurements( AtlasRun $atlasrun ): RedirectResponse
    {
        $atlasrun->atlasMeasurements()->whereNull( 'atlas_id' )->each( function ( $am ) {
            RunMeasurementsJob::dispatchAfterResponse( $am );
        });

        $atlasrun->update( [ 'started_at' => now() ] );

        AlertContainer::push( 'The command run atlas measurements is processing.', Alert::SUCCESS );
        return redirect( route( RunController::route_prefix() . "@list" ) );
    }

    /**
     * Function that update measurements for an atlas run
     *
     * @param AtlasRun     $atlasrun
     *
     * @return RedirectResponse
     *
     */
    public function updateMeasurements( AtlasRun $atlasrun ): RedirectResponse
    {
        $ams = $atlasrun->atlasMeasurements()->whereNull( 'atlas_stop' )->get();

        if( $ams->isEmpty() ) {
            AlertContainer::push( 'All the atlas measurements have been executed already.', Alert::DANGER );
            return redirect( route( RunController::route_prefix() . "@list" ) );
        }

        $ams->each( function ( $am ) {
            UpdateMeasurementsJob::dispatchAfterResponse( $am );
        });

        AlertContainer::push( 'The command update atlas measurements is processing.', Alert::SUCCESS );
        return redirect( route( RunController::route_prefix() . "@list" ) );
    }

    /**
     * Function that stop all measurements for an atlas run
     *
     * @param AtlasRun     $atlasrun
     *
     * @return RedirectResponse
     *
     */
    public function stopMeasurements( AtlasRun $atlasrun ): RedirectResponse
    {
        $atlasrun->atlasMeasurements()->each( function( $am ) {/** @var $am AtlasMeasurement */
            StopAllMeasurementsJob::dispatchAfterResponse( $am->atlas_id );
        });

        AlertContainer::push( 'The command Stop atlas measurements is processing.', Alert::SUCCESS );
        return redirect( route( RunController::route_prefix() . "@list" ) );
    }

    /**
     * Display the ripe atlas measurements matrix for a run
     *
     * @param AtlasRun $atlasRun ID of the atlas run
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View|View
     */
    public function matrix( AtlasRun $atlasRun ): \Illuminate\Contracts\View\View|Factory|View|Application
    {
        $custs  = AtlasMeasurement::select( [
            'cust_dest.*',
            'cust_source.*'
        ] )
        ->join( 'atlas_runs', 'atlas_measurements.run_id', 'atlas_runs.id' )
        ->join( 'cust as cust_dest', 'atlas_measurements.cust_dest', 'cust_dest.id'  )
        ->join( 'cust as cust_source',  'atlas_measurements.cust_source', 'cust_source.id')
        ->where('atlas_measurements.run_id', $atlasRun->id )
        ->get()->keyBy( 'autsys' )->sortKeys();

        $asns   = $custs->keys();

        return view( $this->feParams->viewFolderName . '/matrix' )->with( [
            'custs'             => $custs,
            'routePrefix'       => self::$route_prefix,
            'asnStringFormat'   => $asns->isNotEmpty() ? "% " . strlen( $asns->last() ) . "s" : "% 0s" ,
        ] );
    }
}