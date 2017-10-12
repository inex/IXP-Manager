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

use D2EM, Former, Redirect, Validator;

use Entities\{
    ConsoleServerConnection     as ConsoleServerConnectionEntity,
    Customer                    as CustomerEntity,
    Switcher                    as SwitcherEntity
};
use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;


/**
 * ConsoleServerConnection Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
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
    public function feInit(){

        $this->data[ 'feParams' ] =  $this->feParams = (object)[

            'entity'            => ConsoleServerConnectionEntity::class,
            'pagetitle'         => 'Console Server Connections',

            'titleSingular'     => 'Console Server Connection',
            'nameSingular'      => 'a console server connection',

            'defaultAction'     => 'list',
            'defaultController' => 'ConsoleServerConnectionController',

            'listOrderBy'       => 'description',
            'listOrderByDir'    => 'ASC',

            'viewFolderName'    => 'console-server-connection',

            'listColumns'    => [

                'id'        => [ 'title' => 'UID', 'display' => false ],

                'customer'  => [
                    'title'      => 'Customer',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'customer',
                    'action'     => 'view',
                    'idField'    => 'customerid'
                ],

                'description'  => 'Description',

                'switch'  => [
                    'title'      => 'Console Server',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'switch',
                    'action'     => 'view',
                    'idField'    => 'switchid'
                ],

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
     * @return array
     */
    protected function listGetData( $id = null ) {
        return D2EM::getRepository( ConsoleServerConnectionEntity::class )->getAllForFeList( $this->feParams, $id );
    }



    /**
     * Display the form to add/edit an object
     * @param   int $id ID of the row to edit
     * @return array
     */
    protected function addEditPrepareForm( $id = null ): array {
        if( $id !== null ) {

            if( !( $this->object = D2EM::getRepository( ConsoleServerConnectionEntity::class )->find( $id) ) ) {
                abort(404);
            }

            Former::populate([
                'description'               => $this->object->getDescription(),
                'cust'                      => $this->object->getCustomer()->getId(),
                'switch'                    => $this->object->getSwitcher()->getId(),
                'port'                      => $this->object->getPort(),
                'speed'                     => $this->object->getSpeed(),
                'parity'                    => $this->object->getParity(),
                'stopbits'                  => $this->object->getStopbits(),
                'flowcontrol'               => $this->object->getFlowcontrol(),
                'autobaud'                  => $this->object->getAutobaud(),
                'notes'                     => $this->object->getNotes(),
            ]);
        }

        return [
            'object'                => $this->object,
            'custs'                 => D2EM::getRepository( CustomerEntity::class )->getAsArray(),
            'switches'              => D2EM::getRepository( SwitcherEntity::class )->getNames( true, SwitcherEntity::TYPE_CONSOLESERVER , false),
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
            'description'           => 'required|string|max:255',
            'cust'                  => 'required|int|exists:Entities\Customer,id',
            'switch'                => 'required|int|exists:Entities\Switcher,id',
            'port'                  => 'required|string|max:255',
            'speed'                 => 'nullable|integer',
            'parity'                => 'nullable|integer',
            'stopbits'              => 'nullable|integer',
            'flowcontrol'           => 'nullable|integer',
        ]);

        if( $validator->fails() ) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        if( $request->input( 'id', false ) ) {
            if( !( $this->object = D2EM::getRepository( ConsoleServerConnectionEntity::class )->find( $request->input( 'id' ) ) ) ) {
                abort(404);
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
        $this->object->setCustomer(     D2EM::getRepository( CustomerEntity::class  )->find( $request->input( 'cust'    ) ) );
        $this->object->setSwitcher(     D2EM::getRepository( SwitcherEntity::class  )->find( $request->input( 'switch'  ) ) );

        D2EM::flush($this->object);

        return true;
    }

    protected function preList()
    {
        $switchesWithoutConsole = [];
        foreach( D2EM::getRepository( SwitcherEntity::class )->getAndCache( true, SwitcherEntity::TYPE_SWITCH  ) as $switch ) {
            if( count( $switch->getConsoleServerConnections() ) == 0 )
                $switchesWithoutConsole[] = $switch->getName();
        }

        if( count( $switchesWithoutConsole ) > 0 ) {
            AlertContainer::push( "Warning: the following switch(es) have no recorded console server connection: ". implode( ', ', $switchesWithoutConsole ) ."." , Alert::DANGER );
        }
    }

}
