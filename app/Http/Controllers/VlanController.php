<?php

namespace IXP\Http\Controllers;

/*
 * Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Cache, D2EM, Former, Redirect, Route, Validator;

use Entities\{
    Vlan                as VlanEntity,
    Infrastructure      as InfrastructureEntity
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;


/**
 * CustKit Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class VlanController extends Doctrine2Frontend {

    /**
     * The object being added / edited
     * @var VlanEntity
     */
    protected $object = null;

    /**
     * This function sets up the frontend controller
     */
    public function feInit() {

        $this->data[ 'feParams' ] =  $this->feParams = (object)[
            'entity'            => VlanEntity::class,

            'pagetitle'         => 'VLANs',

            'titleSingular'     => 'VLAN',
            'nameSingular'      => 'a VLAN',

            'defaultAction'     => 'list',
            'defaultController' => 'VlanController',

            'listOrderBy'       => 'number',
            'listOrderByDir'    => 'ASC',

            'viewFolderName'    => 'vlan',

            'listColumns'    => [
                'id'          => [ 'title' => 'DB ID' ],
                'name'        => 'Description',
                'config_name' => 'Config Name',
                'number'      => '802.1q Tag',
                'ixp'         => 'IXP',
                'infrastructure'    => 'Infrastructure',

                'private'        => [
                    'title'          => 'Private',
                    'type'           => self::$FE_COL_TYPES[ 'XLATE' ],
                    'xlator'         => VlanEntity::$PRIVATE_YES_NO
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
                    'xlator'         => VlanEntity::$PRIVATE_YES_NO
                ],

                'peering_manager' => [
                    'title'          => 'Peering Manager',
                    'type'           => self::$FE_COL_TYPES[ 'XLATE' ],
                    'xlator'         => VlanEntity::$PRIVATE_YES_NO
                ],

                'notes' => 'Notes'
            ]
        );

    }


    protected static function additionalRoutes( string $route_prefix )
    {
        Route::group( [ 'prefix' => $route_prefix ], function() use ( $route_prefix ) {
            Route::get(     'privates',                         'VlanController@listPrivate'    )->name( $route_prefix . '@private'        );
            Route::get(     'privates/infra/{id}',              'VlanController@listPrivate'    )->name( $route_prefix . '@privateInfra'   );
            Route::get(     'list/infra/{id}',                  'VlanController@listInfra'      )->name( $route_prefix . '@infra'          );
            Route::get(     'list/infra/{id}/public/{public}',  'VlanController@listInfra'      )->name( $route_prefix . '@infraPublic'    );

        });
    }
    /**
     * Provide array of rows for the list and view
     *
     * @param int $id The `id` of the row to load for `view`. `null` if `list`
     * @return array
     */
    protected function listGetData( $id = null ) {
        return D2EM::getRepository( VlanEntity::class )->getAllForFeList( $this->feParams, $id );
    }


    /**
     * Display the form to add/edit an object
     * @param   int $id ID of the row to edit
     * @return array
     */
    protected function addEditPrepareForm( $id = null ): array {
        if( $id != null ) {
            if( !( $this->object = D2EM::getRepository( VlanEntity::class )->find( $id ) ) ) {
                abort(404);
            }

            Former::populate([
                'name'                      => $this->object->getName(),
                'number'                    => $this->object->getNumber(),
                'infrastructureid'          => $this->object->getInfrastructure()->getId(),
                'config_name'               => $this->object->getConfigName(),
                'private'                   => $this->object->getPrivate()          ? 1 : 0,
                'peering_matrix'            => $this->object->getPeeringMatrix()    ? 1 : 0,
                'peering_manager'           => $this->object->getPeeringManager()   ? 1 : 0,
                'notes'                     => $this->object->getNotes(),

            ]);
        }

        return [
            'object'            => $this->object,
            'infrastructure'    => D2EM::getRepository( InfrastructureEntity::class )->getNames( ),
        ];
    }


    /**
     * Function to do the actual validation and storing of the submitted object.
     * @param Request $request
     * @return bool|RedirectResponse
     */
    public function doStore( Request $request )
    {
        $validator = Validator::make( $request->all(), [
            'name'              => 'required|string|max:255',
            'number'            => 'required|integer',
            'infrastructureid'  => 'required|integer|exists:Entities\Infrastructure,id',

        ]);

        if( $validator->fails() ) {
            return Redirect::back()->withErrors( $validator )->withInput();
        }

        if( $request->input( 'id', false ) ) {
            if( !( $this->object = D2EM::getRepository( VlanEntity::class )->find( $request->input( 'id' ) ) ) ) {
                abort(404);
            }
        } else {
            $this->object = new VlanEntity;
            D2EM::persist( $this->object );
        }

        $this->object->setName(             $request->input( 'name'                 ) );
        $this->object->setNumber(           $request->input( 'number'               ) );
        $this->object->setConfigName(       $request->input( 'config_name'          ) );
        $this->object->setNotes(            $request->input( 'notes'                ) );
        $this->object->setPrivate(          $request->input( 'private'              ) ?? false );
        $this->object->setPeeringManager(   $request->input( 'peering_manager'      ) ?? false );
        $this->object->setPeeringMatrix(    $request->input( 'peering_matrix'       ) ?? false );
        $this->object->setInfrastructure(   D2EM::getRepository( InfrastructureEntity::class )->find( $request->input( 'infrastructureid' ) ) );
        D2EM::flush( $this->object );

        return true;
    }

    /**
     * Overriding optional method to clear cached entries:
     *
     * @param string $action Either 'add', 'edit', 'delete'
     * @return bool
     */
    protected function postFlush( string $action ): bool
    {
        // this is created in Repositories\Vlan::getNames()
        Cache::forget( VlanEntity::ALL_CACHE_KEY );
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function preDelete(): bool {
        if( ( $cnt = count( $this->object->getRouters() ) ) ) {
            AlertContainer::push( "Could not delete this Vlan as {$cnt} router(s) are assigned to it", Alert::DANGER );
            return false;
        }

        if( ( $cnt = count( $this->object->getIPv4Addresses() ) ) ) {
            AlertContainer::push( "Could not delete this Vlan as {$cnt} IPv4 address(es) are assigned to it", Alert::DANGER );
            return false;
        }

        if( ( $cnt = count( $this->object->getIPv6Addresses() ) ) ) {
            AlertContainer::push( "Could not delete this Vlan as {$cnt} IPv6 address(es) are assigned to it", Alert::DANGER );
            return false;
        }

        return true;
    }

    /**
     * Display the private Vlan
     *
     * @param int $id ID of the vlan to display
     * @return View
     */
    public function listPrivate( int $id = null ){
        $infra = null;
        if( $id && !( $infra = D2EM::getRepository( InfrastructureEntity::class )->find( $id ) ) ) {
            abort(404);
        }

        $this->data[ 'data' ]           = D2EM::getRepository( VlanEntity::class )->getPrivateVlanDetails( $infra );
        $this->params                   = [ 'infra' => $infra ];

        return $this->display( 'private' );
    }

    /**
     * Display the Vlan for an Infrastructure
     *
     * @param int $id ID of the Infrastructure
     * @param bool $public only the public vlan ?
     *
     * @return View
     */
    public function listInfra( int $id, $public = null ){

        if( !( $infra = D2EM::getRepository( InfrastructureEntity::class )->find( $id ) ) ) {
            abort(404);
        }

        if( $public ){
            $this->feParams->public = true;
        }

        $this->feParams->infra = $infra;

        return $this->list();
    }



}