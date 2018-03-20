<?php

namespace IXP\Http\Controllers;

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
    ConsoleServerConnection     as ConsoleServerConnectionEntity,
    Customer                    as CustomerEntity,
    Switcher                    as SwitcherEntity
};

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;


/**
 * ConsoleServerConnection Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009-2018 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ConsoleServerConnectionController extends Doctrine2Frontend {

    /**
     * The object being added / edited
     * @var ConsoleServerConnectionEntity
     */
    protected $object = null;

    /**
     * This function sets up the frontend controller
     */
    public function feInit() {

        $this->feParams         = (object)[

            'entity'            => ConsoleServerConnectionEntity::class,
            'pagetitle'         => 'Console Server Connections',

            'titleSingular'     => 'Console Server Connection',
            'nameSingular'      => 'a console server connection',

            'listOrderBy'       => 'description',
            'listOrderByDir'    => 'ASC',

            'viewFolderName'    => 'console-server-connection',

            'listColumns'    => [

                'id'        => [ 'title' => 'DB ID', 'display' => true ],

                'customer'  => [
                    'title'      => 'Customer',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'customer',
                    'action'     => 'view',
                    'idField'    => 'customerid'
                ],

                'description'  => 'Description',

                'port'    => 'Port'
            ]
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = array_merge(
            $this->feParams->listColumns,
            [
                'speed'       => 'Speed',
                'parity'      => 'Parity',
                'stopbits'    => 'Stopbits',
                'flowcontrol' => 'Flow Control',
                'autobaud'    => 'Autobaud',
                'notes'       => 'Notes'
            ]
        );


    }


    /**
     * Provide array of rows for the list action and view action
     *
     * @param int $id The `id` of the row to load for `view` action`. `null` if `listAction`
     *
     * @return array
     */
    protected function listGetData( $id = null ) {
        return D2EM::getRepository( ConsoleServerConnectionEntity::class )->getAllForFeList( $this->feParams, $id );
    }



    /**
     * Display the form to add/edit an object
     *
     * @param   int $id ID of the row to edit
     *
     * @return array
     */
    protected function addEditPrepareForm( $id = null ) : array {
        if( $id !== null ) {

            if( !( $this->object = D2EM::getRepository( ConsoleServerConnectionEntity::class )->find( $id) ) ) {
                abort(404, "Console Server Connection not found." );
            }

            $old = request()->old();

            Former::populate([
                'description'   => array_key_exists( 'description', $old ) ? $old['description']    : $this->object->getDescription(),
                'custid'        => array_key_exists( 'custid',      $old ) ? $old['custid']         : $this->object->getCustomer()->getId(),
                'switchid'      => array_key_exists( 'switchid',    $old ) ? $old['switchid']       : $this->object->getSwitcher()->getId(),
                'port'          => array_key_exists( 'port',        $old ) ? $old['port']           : $this->object->getPort(),
                'speed'         => array_key_exists( 'speed',       $old ) ? $old['speed']          : $this->object->getSpeed(),
                'parity'        => array_key_exists( 'parity',      $old ) ? $old['parity']         : $this->object->getParity(),
                'stopbits'      => array_key_exists( 'stopbits',    $old ) ? $old['stopbits']       : $this->object->getStopbits(),
                'flowcontrol'   => array_key_exists( 'flowcontrol', $old ) ? $old['flowcontrol']    : $this->object->getFlowcontrol(),
                'autobaud'      => array_key_exists( 'autobaud',    $old ) ? $old['autobaud']       : ( $this->object->getAutobaud() ?? false ),
                'notes'         => array_key_exists( 'notes',       $old ) ? $old['notes']          : $this->object->getNotes(),
            ]);
        }

        return [
            'object'                => $this->object,
            'custs'                 => D2EM::getRepository( CustomerEntity::class )->getAsArray(),
            'switches'              => D2EM::getRepository( SwitcherEntity::class )->getNames( true, SwitcherEntity::TYPE_CONSOLESERVER , false ),
        ];
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
    public function doStore( Request $request ) {
        $validator = Validator::make( $request->all(), [
            'description'           => 'required|string|max:255',
            'custid'                => 'required|int|exists:Entities\Customer,id',
            'switchid'              => 'required|int|exists:Entities\Switcher,id',
            'port'                  => 'required|string|max:255',
            'speed'                 => 'nullable|integer',
            'parity'                => 'nullable|integer',
            'stopbits'              => 'nullable|integer',
            'flowcontrol'           => 'nullable|integer',
            'autobaud'              => 'nullable|boolean',
            'notes'                 => 'nullable|string|max:65535',
        ]);

        if( $validator->fails() ) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        if( $request->input( 'id', false ) ) {
            if( !( $this->object = D2EM::getRepository( ConsoleServerConnectionEntity::class )->find( $request->input( 'id' ) ) ) ) {
                abort(404, "Console Server Connection not found." );
            }
        } else {
            $this->object = new ConsoleServerConnectionEntity;
            D2EM::persist( $this->object );
        }

        $this->object->setDescription(  $request->input( 'description'   ) );
        $this->object->setPort(         $request->input( 'port'         ) );
        $this->object->setSpeed(        $request->input( 'speed'        ) );
        $this->object->setParity(       $request->input( 'parity'       ) );
        $this->object->setStopbits(     $request->input( 'stopbits'     ) );
        $this->object->setFlowcontrol(  $request->input( 'flowcontrol'  ) );
        $this->object->setAutobaud(     $request->input( 'autobaud'     ) );
        $this->object->setNotes(        $request->input( 'notes'        ) );
        $this->object->setCustomer(     D2EM::getRepository( CustomerEntity::class  )->find( $request->input( 'custid'      ) ) );
        $this->object->setSwitcher(     D2EM::getRepository( SwitcherEntity::class  )->find( $request->input( 'switchid'    ) ) );

        D2EM::flush($this->object);

        return true;
    }
}
