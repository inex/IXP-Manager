<?php

namespace IXP\Http\Controllers;

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

use Former, Redirect, Route;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Http\{
    RedirectResponse,
    Request
};

use Illuminate\View\View;

use IXP\Models\{
    Infrastructure,
    Vlan
};

use IXP\Utils\Http\Controllers\Frontend\EloquentController;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * Vlan Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class VlanController extends EloquentController
{
    /**
     * The object being created / edited
     *
     * @var Vlan
     */
    protected $object = null;

    /**
     * This function sets up the frontend controller
     */
    public function feInit(): void
    {
        $this->feParams         = (object)[
            'model'             => Vlan::class,
            'pagetitle'         => 'VLANs',
            'titleSingular'     => 'VLAN',
            'nameSingular'      => 'VLAN',
            'listOrderBy'       => 'number',
            'listOrderByDir'    => 'ASC',
            'viewFolderName'    => 'vlan',
            'listColumns'    => [
                'name'                   => 'Description',
                'config_name'            => 'Config Name',
                'number'                 => '802.1q Tag',
                'infrastructure_name'    => 'Infrastructure',
                'private'        => [
                    'title'          => 'Private',
                    'type'           => self::$FE_COL_TYPES[ 'YES_NO' ]
                ],
            ]
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = array_merge(
            $this->feParams->listColumns,
            [
                'peering_matrix' => [
                    'title'          => 'Peering Matrix',
                    'type'           => self::$FE_COL_TYPES[ 'YES_NO' ],
                ],
                'peering_manager' => [
                    'title'          => 'Peering Manager',
                    'type'           => self::$FE_COL_TYPES[ 'YES_NO' ],
                ],
                'notes' => [
                    'title'         => 'Notes',
                    'type'          => self::$FE_COL_TYPES[ 'PARSDOWN' ]
                ],
                'created_at' => [
                    'title'         => 'Created',
                    'type'          => self::$FE_COL_TYPES[ 'DATETIME' ]
                ],
                'updated_at' => [
                    'title'         => 'Updated',
                    'type'          => self::$FE_COL_TYPES[ 'DATETIME' ]
                ]
            ]
        );
    }

    /**
     * Additional routes
     *
     * @param string $route_prefix
     *
     * @return void
     */
    protected static function additionalRoutes( string $route_prefix ): void
    {
        Route::group( [ 'prefix' => $route_prefix ], static function() use ( $route_prefix ) {
            Route::get(     'private',                             'VlanController@listPrivate'    )->name( $route_prefix . '@private'        );
            Route::get(     'private/infra/{infra}',               'VlanController@listPrivate'    )->name( $route_prefix . '@privateInfra'   );
            Route::get(     'list/infra/{infra}',                  'VlanController@listInfra'      )->name( $route_prefix . '@infra'          );
            Route::get(     'list/infra/{infra}/public/{public}',  'VlanController@listInfra'      )->name( $route_prefix . '@infraPublic'    );
        });
    }

    /**
     * Provide array of rows for the list and view
     *
     * @param int|null $id The `id` of the row to load for `view`. `null` if `list`
     *
     * @return array
     */
    protected function listGetData( ?int $id = null ): array
    {
        $param = $this->feParams;
        return Vlan::select( [ 'vlan.*', 'i.shortname AS infrastructure_name' ] )
            ->leftJoin( 'infrastructure AS i', 'i.id', 'vlan.infrastructureid' )
            ->when( $id , function( Builder $q, $id ) {
                return $q->where('vlan.id', $id );
            } )
            ->when( $this->feParams->privateList ?? false, function( Builder $q ) {
                return $q->where( 'private', 1 );
            })
            ->when( $this->feParams->publicOnly ?? false, function( Builder $q ) {
                return $q->where( 'private', 0 );
            })
            ->when( $this->feParams->infra ?? false, function( Builder $q, $infra )  {
                return $q->where( 'infrastructureid', $infra->id );
            })
            ->when( $this->feParams->listOrderBy ?? false, function( Builder $q, $orderby ) use ( $param ) {
                return $q->orderBy( $orderby, $param->listOrderByDir ?? 'asc' );
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
            'infrastructure'    => Infrastructure::orderBy( 'name' )->get(),
        ];
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
        $this->checkForm( $r );
        if( $this->checkIsDuplicate( $r, null ) ) {
            return Redirect::back()->withInput();
        }
        $this->object = Vlan::create( $r->all() );
        return true;
    }

    /**
     * Display the form to edit an object
     *
     * @param null $id ID of the row to edit
     *
     * @return array
     */
    protected function editPrepareForm( $id = null ): array
    {
        $this->object = Vlan::findOrFail( $id );

        Former::populate([
            'name'                      =>  request()->old( 'name',               $this->object->name               ),
            'number'                    =>  request()->old( 'number',             $this->object->number             ),
            'infrastructureid'          =>  request()->old( 'infrastructureid',   $this->object->infrastructureid   ),
            'config_name'               =>  request()->old( 'config_name',        $this->object->config_name        ),
            'private'                   =>  request()->old( 'private',            $this->object->private            ),
            'peering_matrix'            =>  request()->old( 'peering_matrix',     $this->object->peering_matrix     ),
            'peering_manager'           =>  request()->old( 'peering_manager',    $this->object->peering_manager    ),
            'notes'                     =>  request()->old( 'notes',              $this->object->notes              ),
        ]);

        return [
            'object'            => $this->object,
            'infrastructure'    => Infrastructure::orderBy( 'name' )->get(),
        ];
    }

    /**
     * Function to do the actual validation and updating of the submitted object.
     *
     * @param Request   $r
     * @param int       $id
     *
     * @return bool|RedirectResponse
     */
    public function doUpdate( Request $r, int $id ): bool|RedirectResponse
    {
        $this->object = Vlan::findOrFail( $id );
        $this->checkForm( $r );

        if( $this->checkIsDuplicate( $r, $this->object->id  ) ){
            return Redirect::back()->withInput();
        }
        $this->object->update( $r->all() );
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
     * @param Infrastructure|null $infra ID of the vlan to display
     *
     * @return View
     */
    public function listPrivate( Infrastructure $infra = null ): View
    {
        $this->data[ 'rows' ] = Vlan::where( 'private', 1 )
            ->when( $infra, function( $q, $infra ) {
                return $q->where('infrastructureid', $infra->id);
            })
            ->with([
                'vlanInterfaces.virtualInterface.customer',
                'vlanInterfaces.virtualInterface.physicalInterfaces.switchport.switcher.cabinet.location'
            ])->get();

        $this->data[ 'params' ] = [ 'infra' => $infra ];
        return $this->display( 'private' );
    }

    /**
     * Display the Vlan for an Infrastructure
     *
     * @param  Infrastructure  $infra
     * @param  null  $public  only the public vlan ?
     *
     * @return View|RedirectResponse
     */
    public function listInfra( Infrastructure $infra, $public = null ): View|RedirectResponse
    {
        if( $public ) {
            $this->feParams->publicOnly = true;
        }

        $this->feParams->infra = $infra;
        return $this->list( request() );
    }

    /**
     * Check if the form is valid
     *
     * @param Request $r
     */
    public function checkForm( Request $r ): void
    {
        $r->validate( [
            'name'              => 'required|string|max:255',
            'number'            => 'required|integer|min:1|max:4096',
            'config_name'       => 'required|string|max:32|alpha_dash',
            'infrastructureid'  => 'required|integer|exists:infrastructure,id',
        ] );
    }

    /**
     * Check if there is a duplicate vlan object with those values
     *
     * @param int|null      $objectid
     * @param Request       $r
     *
     * @return bool
     */
    private function checkIsDuplicate( Request $r, int $objectid = null ): bool
    {
        $exist = Vlan::where( 'infrastructureid', $r->infrastructureid )
            ->where( 'config_name', $r->config_name )
            ->when( $objectid , function( Builder $q, $objectid ) {
                return $q->where( 'id', '!=',  $objectid );
            })->count();

        if( $exist ) {
            AlertContainer::push( "The couple Infrastructure and config name already exist.", Alert::DANGER );
            return true;
        }

        return false;
    }
}