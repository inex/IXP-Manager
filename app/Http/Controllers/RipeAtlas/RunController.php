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

use DB, Former, Redirect, Route, Validator;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\{
    RedirectResponse,
    Request
};

use Illuminate\View\View;

use IXP\Jobs\RipeAtlas\{
    CompleteRequests    as CompleteRequestsJob,
    CreateMeasurements  as CreateMeasurementsJob,
    RunMeasurements     as RunMeasurementsJob
};

use IXP\Models\{
    Aggregators\CustomerAggregator,
    AtlasRun,
    Router,
    Vlan};

use IXP\Utils\Http\Controllers\Frontend\EloquentController as Eloquent2Frontend;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * Run Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\RipeAtlas
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class RunController extends Eloquent2Frontend
{
    /**
     * The object being created/edited
     *
     * @var AtlasRun
     */
    protected $object = null;

    /**
     * The URL prefix to use.
     *
     * Automatically determined based on the controller name if not set.
     *
     * @var string
     */
    protected static $route_prefix = "ripe-atlas/runs";

    /**
     * Do we disable to edit?
     *
     * @var boolean
     */
    public static $disable_edit = true;

    /**
     * This function sets up the frontend controller
     */
    public function feInit()
    {
        $this->feParams         = (object)[
            'model'             => AtlasRun::class,
            'pagetitle'         => 'Ripe Atlas :: Runs',
            'titleSingular'     => 'Run',
            'nameSingular'      => 'atlas run',
            'listOrderBy'       => 'created_at',
            'listOrderByDir'    => 'DESC',
            'disableEdit'       => self::$disable_edit,
            'viewFolderName'    => 'ripe-atlas/run',
            'listColumns'       => [
                'vlan_name'  => [
                    'title'      => 'Vlan',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'vlan',
                    'action'     => 'view',
                    'idField'    => 'vlan_id',
                ],
                'protocol'      => [
                    'title' => 'Protocol',
                    'type'      =>   self::$FE_COL_TYPES[ 'CONST' ],
                    'const'     =>   Router::$PROTOCOLS,
                ],
                'nb_run'         =>  'Total',
                'created_at'     =>  'Created At',
                'scheduled_at'   =>  'Scheduled At',
                'started_at'     =>  'Started At',
                'completed_at'   =>  'Completed At',
            ],
        ];
        // display the same information in the view as the list
        $this->feParams->viewColumns = $this->feParams->listColumns;
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
            Route::get( 'add-step-2',                   'RipeAtlas\RunController@addStep2'      )->name( $route_prefix . '@add-step-2'    );
            Route::put( 'run/complete/{atlasrun}',      'RipeAtlas\RunController@completeRun'   )->name( $route_prefix . '@complete-run'  );
        });
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
        $feParams = $this->feParams;

        return AtlasRun::select( [
            'atlas_runs.*',
            'vlan.name as vlan_name',
            DB::raw('COUNT( am.run_id ) as nb_am'),
            DB::raw('COUNT( am.atlas_create ) as nb_am_created'),
            DB::raw('COUNT( am.atlas_start ) as nb_am_started'),
            DB::raw('COUNT( am.atlas_stop ) as nb_am_stopped')
        ])
        ->leftJoin( 'vlan', 'atlas_runs.vlan_id','vlan.id' )
        ->leftJoin( 'atlas_measurements AS am' , 'atlas_runs.id', 'am.run_id' )
        ->when( $id, function( Builder $q, $id ) {
            return $q->where( 'atlas_runs.id', $id );
        } )
        ->when( $feParams->listOrderBy , function( Builder $q, $orderby ) use ( $feParams )  {
            return $q->orderBy( $orderby, $feParams->listOrderByDir ?? 'ASC');
        })->groupBy( 'atlas_runs.id' )->get()->toArray();
    }

    /**
     * Display the form to create an object
     *
     * @return array
     */
    protected function createPrepareForm(): array
    {
        Former::populate( [
            'protocol'          => request()->old( 'protocol',      Router::PROTOCOL_IPV4      ),
            'scheduled_at'      => request()->old( 'scheduled_at',  AtlasRun::SCHEDULED_AT_NOW ),
            'scheduled_date'    => request()->old( 'scheduled_date' ),
            'scheduled_time'    => request()->old( 'scheduled_time' ),
        ] );

        return [
            'object'        => null,
            'vlans'         => Vlan::publicOnly()->get(),
            'preAddForm'    => true
        ];
    }

    /**
     * Second step to create an Atlas run
     *
     * @param  Request  $r
     *
     * @return View
     */
    public function addStep2( Request $r ): View
    {
        $this->checkForm( $r );

        $this->feParams->titleSingular = "Step 2";
        $this->addEditSetup();

        $this->data[ 'params' ][ 'isAdd' ]          = true;
        $this->data[ 'params' ][ 'preAddForm' ]     = false;
        $this->data[ 'params' ][ 'object' ]         = null;
        $this->data[ 'params' ][ 'custs' ]          = CustomerAggregator::WithProbesForProtocol( $r->protocol, $r->vlan_id );
        $this->data[ 'params' ][ 'vlans' ]          = Vlan::publicOnly()->get();
        $this->data[ 'params' ][ 'protocol' ]       = $r->protocol;
        $this->data[ 'params' ][ 'vlanid' ]         = $r->vlan_id;
        $this->data[ 'params' ][ 'scheduled_at' ]   = $r->scheduled_at;
        $this->data[ 'params' ][ 'scheduled_date' ] = $r->scheduled_date;
        $this->data[ 'params' ][ 'scheduled_time' ] = $r->scheduled_time;

        return $this->display( 'edit' );
    }

    /**
     * Function to do the actual validation and storing of the submitted object.
     *
     * @param Request $r
     *
     * @return bool|RedirectResponse
     */
    public function doStore( Request $r ): bool|RedirectResponse
    {
        if( !$r->selected_custs || !count( $r->selected_custs ) ) {
            AlertContainer::push( "You need to select at least one " . config( "ixp_fe.lang.customer.one" ) . ".", Alert::DANGER );
            return Redirect::back()->withInput();
        }

        $this->checkForm( $r );

        $this->object = AtlasRun::create( [
            'protocol'      => $r->protocol,
            'scheduled_at'  => $r->scheduled_at === AtlasRun::SCHEDULED_AT_NOW ? now() : new Carbon( $r->scheduled_date . $r->scheduled_time ),
            'vlan_id'       => $r->vlan_id
        ] );

        CreateMeasurementsJob::dispatchNow( $this->object, $r->selected_custs );

        if( (int)$r->scheduled_at === AtlasRun::SCHEDULED_AT_NOW ) {
            $this->object->atlasMeasurements()->each( function( $am ) {
                RunMeasurementsJob::dispatchAfterResponse( $am );
            } );

            $this->object->update( [ 'started_at' => now() ] );
            AlertContainer::push( 'The command atlas run is processing.', Alert::SUCCESS );
        }

        return true;
    }

    /**
     * Run the Atlas complete job
     *
     * @param AtlasRun $atlasrun
     *
     * @return RedirectResponse
     */
    public function completeRun( AtlasRun $atlasrun ): RedirectResponse
    {
        if( $atlasrun->completed_at ) {
            AlertContainer::push( 'The command complete atlas run have already executed.', Alert::DANGER );
        } elseif( CompleteRequestsJob::dispatchNow( $atlasrun ) ) {
            AlertContainer::push( 'The command complete atlas run executed with success.', Alert::SUCCESS );
        } else {
            AlertContainer::push( 'The command complete atlas run cannot be executed, some atlas measurements are not ended.', Alert::DANGER );
        }

        return redirect( route( self::$route_prefix . "@list" ) );
    }

    /**
     * Delete all atlas measurements and atlas result before deleting the atlas run
     *
     * @inheritdoc
     *
     * @return bool Return false to stop / cancel the deletion
     */
    protected function preDelete(): bool
    {
        if( !$this->object->completed_at ) {
            AlertContainer::push( 'Impossible to delete, the atlas run must be completed or stopped to do this action.', Alert::DANGER );
            return false;
        }

        // Delete All Atlas results and Atlas measurements before deleting the Atlas run
        // Delete on cascade for the atlas result linked to the atlas measurements
        $this->object->atlasMeasurements()->delete();

        return true;
    }

    /**
     * Check if the form is valid
     *
     * @param Request $r
     */
    public function checkForm( Request $r ): void
    {
        $rules = [
            'vlan_id'            => 'required|integer|exists:Vlan,id',
            'protocol'           => 'required|integer|in:' . implode( ',', array_keys( Router::$PROTOCOLS      ) ),
            'scheduled_at'       => 'required|integer|in:' . implode( ',', array_keys( AtlasRun::$SCHEDULED_AT ) ),
        ];

        if( $r->scheduled_at === AtlasRun::SCHEDULED_AT_DATETIME ) {
            $validateScheduled =
                [
                    'scheduled_date'    => 'required|date',
                    'scheduled_time'    => 'required|date_format:H:i',
                ];

            $rules = array_merge( $rules, $validateScheduled );
        }

        $r->validate( $rules );
    }
}