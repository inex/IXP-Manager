<?php

namespace IXP\Http\Controllers;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Cache, Former, Route;

use Illuminate\Database\Eloquent\Builder;
use Redirect;
use Illuminate\Http\{
    RedirectResponse,
    Request
};

use Illuminate\View\View;

use IXP\Models\{Aggregators\VlanAggregator, Infrastructure, Vlan};

use IXP\Utils\Http\Controllers\Frontend\EloquentController;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * Vlan Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class VlanController extends EloquentController
{
    /**
     * The object being created / edited
     * @var Vlan
     */
    protected $object = null;

    /**
     * This function sets up the frontend controller
     */
    public function feInit(): void
    {
        $this->feParams         = (object)[
            'entity'            => Vlan::class,
            'pagetitle'         => 'VLANs',
            'titleSingular'     => 'VLAN',
            'nameSingular'      => 'VLAN',
            'listOrderBy'       => 'number',
            'listOrderByDir'    => 'ASC',
            'viewFolderName'    => 'vlan',

            'listColumns'    => [
                'id'          => [
                    'title' => 'DB ID'
                ],
                'name'                   => 'Description',
                'config_name'            => 'Config Name',
                'number'                 => '802.1q Tag',
                'infrastructure_name'    => 'Infrastructure',
                'private'        => [
                    'title'          => 'Private',
                    'type'           => self::$FE_COL_TYPES[ 'XLATE' ],
                    'xlator'         => Vlan::$PRIVATE_YES_NO
                ],
            ]
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = array_merge(
            $this->feParams->listColumns,
            [
                'peering_matrix' => [
                    'title'          => 'Peering Matrix',
                    'type'           => self::$FE_COL_TYPES[ 'XLATE' ],
                    'xlator'         => Vlan::$PRIVATE_YES_NO
                ],
                'peering_manager' => [
                    'title'          => 'Peering Manager',
                    'type'           => self::$FE_COL_TYPES[ 'XLATE' ],
                    'xlator'         => Vlan::$PRIVATE_YES_NO
                ],
                'notes' => [
                    'title'         => 'Notes',
                    'type'          => self::$FE_COL_TYPES[ 'PARSDOWN' ]
                ]
            ]
        );
    }

    protected static function additionalRoutes( string $route_prefix ): void
    {
        Route::group( [ 'prefix' => $route_prefix ], static function() use ( $route_prefix ) {
            Route::get(     'private',                              'VlanController@listPrivate'    )->name( $route_prefix . '@private'        );
            Route::get(     'private/infra/{infra}',                'VlanController@listPrivate'    )->name( $route_prefix . '@privateInfra'   );
            Route::get(     'list/infra/{infra}',                  'VlanController@listInfra'      )->name( $route_prefix . '@infra'          );
            Route::get(     'list/infra/{infra}/public/{public}',  'VlanController@listInfra'      )->name( $route_prefix . '@infraPublic'    );
        });
    }

    /**
     * Provide array of rows for the list and view
     *
     * @param int $id The `id` of the row to load for `view`. `null` if `list`
     *
     * @return array
     */
    protected function listGetData( $id = null ): array
    {
        return Vlan::select( [ 'vlan.*', 'i.shortname AS infrastructure_name' ] )
            ->leftJoin( 'infrastructure AS i', 'i.id', 'vlan.infrastructureid' )
            ->when( $id , function( Builder $q, $id ) {
                return $q->where('vlan.id', $id );
            } )
            ->when( $feParams->privateList ?? false, function( Builder $q ) {
                return $q->where( 'private', 1 );
            })
            ->when( $feParams->publicOnly ?? false, function( Builder $q ) {
                return $q->where( 'private', 0 );
            })
            ->when( $feParams->infra ?? false, function( Builder $q, $infra )  {
                return $q->where( 'infrastructureid', $infra->id );
            })
            ->when( $feParams->listOrderBy ?? false, function( Builder $q, $orderby ) use ( $feParams ) {
                return $q->orderBy( $orderby, $feParams->listOrderByDir ?? 'asc' );
            })
            ->get()->toArray();
    }

    /**
     * Display the form to create an object
     *
     * @return array
     */
    protected function createPrepareForm(): array
    {
        return [
            'object'            => $this->object,
            'infrastructure'    => Infrastructure::getListAsArray(),
        ];
    }

    /**
     * Display the form to edit an object
     *
     * @param   int $id ID of the row to edit
     *
     * @return array
     */
    protected function editPrepareForm( $id = null ): array
    {
        $this->object = Vlan::findOrFail( $id );

        Former::populate([
            'name'                      =>  request()->old( 'name',               $this->object->name ),
            'number'                    =>  request()->old( 'number',             $this->object->number ),
            'infrastructureid'          =>  request()->old( 'infrastructureid',   $this->object->infrastructureid ),
            'config_name'               =>  request()->old( 'config_name',        $this->object->config_name ),
            'private'                   =>  request()->old( 'private',            ( $this->object->private              ? 1 : 0 ) ),
            'peering_matrix'            =>  request()->old( 'peering_matrix',     ($this->object->peering_matrix        ? 1 : 0 ) ),
            'peering_manager'           =>  request()->old( 'peering_manager',    ( $this->object->peering_manager      ? 1 : 0 ) ),
            'notes'                     =>  request()->old( 'notes',              $this->object->notes ),
        ]);

        return [
            'object'            => $this->object,
            'infrastructure'    => Infrastructure::getListAsArray(),
        ];
    }

    /**
     * Check if the form is valid
     *
     * @param $request
     */
    public function checkForm( Request $request ): void
    {
        $request->validate( [
            'name'              => 'required|string|max:255',
            'number'            => 'required|integer|min:1|max:4096',
            'config_name'       => 'required|string|max:32|alpha_dash',
            'infrastructureid'  => [
                'required', 'integer',
                function ($attribute, $value, $fail) {
                    if( !Infrastructure::whereId( $value )->exists() ) {
                        return $fail( 'The infrastructure does not exist.' );
                    }
                },
            ],
        ] );
    }

    /**
     * Check if there is a duplicate vlan object with those values
     *
     * @param int|null      $objectid
     * @param Request       $request
     *
     * @return bool
     */
    private function checkIsDuplicate( int $objectid = null, Request $request ): bool
    {
        if( $vlanFound = Vlan::where( 'infrastructureid', $request->infrastructureid )->where( 'config_name', $request->input( 'config_name' ) )->get()->first() ){
            if( $objectid !== $vlanFound->id ) {
                AlertContainer::push( "The couple Infrastructure and config name already exist.", Alert::DANGER );
                return true;
            }
        }

        return false;
    }

    /**
     * Function to do the actual validation and storing of the submitted object.
     *
     * @param Request $request
     *
     * @return bool|RedirectResponse
     *
     * @throws
     */
    public function doStore( Request $request )
    {
        $this->checkForm( $request );
        if( $this->checkIsDuplicate( null, $request ) ) {
            return Redirect::back()->withInput();
        }
        $this->object = Vlan::create( $request->all() );

        return true;
    }

    /**
     * Function to do the actual validation and updating of the submitted object.
     *
     * @param Request $request
     * @param int $id
     *
     * @return bool|RedirectResponse
     *
     * @throws
     */
    public function doUpdate( Request $request, int $id )
    {
        $this->object = Vlan::findOrFail( $id );
        $this->checkForm( $request );
        if( $this->checkIsDuplicate( $this->object->id, $request ) ){
            return Redirect::back()->withInput();
        }
        $this->object->update( $request->all() );

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function preDelete(): bool
    {
        $okay = true;
        if( ( $cnt = $this->object->routers()->count() ) ) {
            AlertContainer::push( "Could not delete this Vlan as {$cnt} router(s) are assigned to it", Alert::DANGER );
            $okay = false;
        }

        if( ( $cnt = $this->object->ipv4addresses()->count() ) ) {
            AlertContainer::push( "Could not delete this Vlan as {$cnt} IPv4 address(es) are assigned to it", Alert::DANGER );
            $okay = false;
        }

        if( ( $cnt = $this->object->ipv6addresses()->count() ) ) {
            AlertContainer::push( "Could not delete this Vlan as {$cnt} IPv6 address(es) are assigned to it", Alert::DANGER );
            $okay = false;
        }

        if( ( $cnt = $this->object->vlanInterfaces()->count() ) ) {
            AlertContainer::push( "Could not delete this Vlan as {$cnt} Vlan Interfaces are assigned to it", Alert::DANGER );
            $okay = false;
        }

        return $okay;
    }

    /**
     * Display the private Vlan
     *
     * @param Infrastructure $infra ID of the vlan to display
     *
     * @return View
     */
    public function listPrivate( Infrastructure $infra = null ): View
    {
        // FIXME @yannrobin - can you fix the view for:

        $this->data[ 'rows' ] = Vlan::where( 'private', 1 )
            ->when( $infra, function( $q, $infra ) {
                return $q->where('infrastructureid', $infra->id);
            })
            ->with([
                'vlanInterfaces.virtualInterface.customer',
                'vlanInterfaces.virtualInterface.physicalInterfaces.switchport.switcher.cabinet.location'
            ])->get();

        $this->data[ 'params' ]         = [ 'infra' => $infra ];

        return $this->display( 'private' );
    }

    /**
     * Display the Vlan for an Infrastructure
     *
     * @param Infrastructure $infra
     * @param bool $public only the public vlan ?
     *
     * @return View
     */
    public function listInfra( Infrastructure $infra, $public = null ): View
    {
        if( $public ) {
            $this->feParams->publicOnly = true;
        }

        $this->feParams->infra = $infra;

        return $this->list( request() );
    }
}