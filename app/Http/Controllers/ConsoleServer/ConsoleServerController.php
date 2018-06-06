<?php

namespace IXP\Http\Controllers\ConsoleServer;

/*
 * Copyright (C) 2009-2018 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use D2EM, Former, Redirect, Validator;

use Entities\{
    ConsoleServer       as ConsoleServerEntity,
    Cabinet             as CabinetEntity,
    Vendor              as VendorEntity
};

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use IXP\Http\Controllers\Doctrine2Frontend;


/**
 * ConsoleServerConnection Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009-2018 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ConsoleServerController extends Doctrine2Frontend {

    /**
     * The object being added / edited
     * @var ConsoleServerEntity
     */
    protected $object = null;

    /**
     * This function sets up the frontend controller
     */
    public function feInit(){

        $this->feParams         = (object)[

            'entity'            => ConsoleServerEntity::class,

            'pagetitle'         => 'Console Servers',

            'titleSingular'     => 'Console Server',
            'nameSingular'      => 'a console server',

            'listOrderBy'       => 'id',
            'listOrderByDir'    => 'ASC',

            'viewFolderName'    => 'console-server',

            'listColumns'    => [

                'name'           => [
                    'title'      => 'Name',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'console-server-connection',
                    'action'     => 'list/port',
                    'idField'    => 'id'
                ],

                'facility'  => [
                    'title'      => 'Facility',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'facility',
                    'action'     => 'view',
                    'idField'    => 'locationid'
                ],

                'cabinet'  => [
                    'title'      => 'Cabinet',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'rack',
                    'action'     => 'view',
                    'idField'    => 'cabinetid'
                ],

                'vendor'  => [
                    'title'       => 'Vendor',
                    'type'        => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller'  => 'vendor',
                    'action'      => 'view',
                    'idField'     => 'vendorid'
                ],

                'model'           => 'Model',

                //'num_connections' => 'Connections',

                'num_connections' => [
                    'title'      => 'Connections',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'console-server-connection',
                    'action'     => 'list/port',
                    'idField'    => 'id'
                ],

                'active'       => [
                    'title'    => 'Active',
                    'type'     => self::$FE_COL_TYPES[ 'YES_NO' ]
                ]
            ]
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = array_merge(
            $this->feParams->listColumns,
            [
                'serialNumber'   => 'Serial Number',
                'notes'       => [
                    'title'         => 'Notes',
                    'type'          => self::$FE_COL_TYPES[ 'PARSDOWN' ]
                ]
            ]
        );


    }


    /**
     * Provide array of rows for the list action and view action
     *
     * @param int $id The `id` of the row to load for `view` action`. `null` if `listAction`
     * @return array
     */
    protected function listGetData( $id = null ) {

        return D2EM::getRepository( ConsoleServerEntity::class )->getAllForFeList( $this->feParams, $id );
    }



    /**
     * Display the form to add/edit an object
     * @param   int $id ID of the row to edit
     * @return array
     */
    protected function addEditPrepareForm( $id = null ): array {

        $old = request()->old();


        if( $id !== null ) {

            if( !( $this->object = D2EM::getRepository( ConsoleServerEntity::class )->find( $id) ) ) {
                abort(404, 'Console server not found' );
            }

            Former::populate([
                'name'              => array_key_exists( 'name',            $old ) ? $old['name']           : $this->object->getName(),
                'hostname'          => array_key_exists( 'hostname',        $old ) ? $old['hostname']       : $this->object->getHostname(),
                'model'             => array_key_exists( 'model',           $old ) ? $old['model']          : $this->object->getModel(),
                'serial_number'     => array_key_exists( 'serial_number',   $old ) ? $old['serial_number']  : $this->object->getSerialNumber(),
                'cabinet'           => array_key_exists( 'cabinet',         $old ) ? $old['cabinet']        : $this->object->getCabinet()->getId(),
                'vendor'            => array_key_exists( 'vendor',          $old ) ? $old['vendor']         : $this->object->getVendor()->getId(),
                'active'            => array_key_exists( 'active',          $old ) ? $old['active']         : ( $this->object->getActive() ? 1 : 0 ),
            ]);
        }

        return [
            'object'                => $this->object,
            'cabinets'              => D2EM::getRepository( CabinetEntity::class    )->getAsArray(),
            'vendors'               => D2EM::getRepository( VendorEntity::class     )->getAsArray(),
            'notes'                 => $id ? ( array_key_exists( 'notes',           $old ) ? $old['notes']           : $this->object->getNote() ) : ( array_key_exists( 'notes',           $old ) ? $old['notes']           : null )
        ];
    }


    /**
     * Function to do the actual validation and storing of the submitted object.
     * @param Request $request
     * @return bool|RedirectResponse
     * @throws
     */
    public function doStore( Request $request )
    {
        $validator = Validator::make( $request->all(), [
            'name'              => 'required|string|max:255|unique:Entities\ConsoleServer,name' . ( $request->input('id') ? ','. $request->input('id') : '' ),
            'vendor'            => 'required|int|exists:Entities\Vendor,id',
            'cabinet'           => 'required|int|exists:Entities\Cabinet,id',
            'model'             => 'nullable|string|max:255',
            'serial_number'     => 'nullable|string',
            'notes'             => 'nullable|string',
            'hostname'          => 'required|string',
            'active'            => 'string',
        ]);

        if( $validator->fails() ) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        if( $request->input( 'id', false ) ) {
            if( !( $this->object = D2EM::getRepository( ConsoleServerEntity::class )->find( $request->input( 'id' ) ) ) ) {
                abort( 404, 'Console server not found' );
            }
        } else {
            $this->object = new ConsoleServerEntity;
            D2EM::persist( $this->object );
        }

        $this->object->setName(         $request->input( 'name'             ) );
        $this->object->setSerialNumber( $request->input( 'serial_number'    ) );
        $this->object->setHostname(     $request->input( 'hostname'         ) );
        $this->object->setNote(         $request->input( 'notes'            ) );
        $this->object->setModel(        $request->input( 'model'            ) );
        $this->object->setActive(       $request->input( 'active' ) ? 1 : 0 );
        $this->object->setVendor(       D2EM::getRepository( VendorEntity::class    )->find( $request->input( 'vendor'     ) ) );
        $this->object->setCabinet(      D2EM::getRepository( CabinetEntity::class   )->find( $request->input( 'cabinet'    ) ) );

        D2EM::flush( $this->object );

        return true;
    }


    /**
     * Delete all console server connections before deleting the console server.
     *
     * @inheritdoc
     *
     * @return bool Return false to stop / cancel the deletion
     */
    protected function preDelete(): bool {

        foreach( $this->object->getConsoleServerConnections() as $csc ) {
            D2EM::remove( $csc );
        }

        return true;
    }

}
