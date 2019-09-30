<?php

namespace IXP\Http\Controllers\ConsoleServer;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use D2EM, Former, Redirect, Route, Validator;

use Entities\{
    ConsoleServerConnection     as ConsoleServerConnectionEntity,
    ConsoleServer               as ConsoleServerEntity,
    Customer                    as CustomerEntity
};

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use IXP\Http\Controllers\Doctrine2Frontend;


/**
 * ConsoleServerConnection Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ConsoleServerConnectionController extends Doctrine2Frontend
{

    /**
     * The object being added / edited
     * @var ConsoleServerConnectionEntity
     */
    protected $object = null;

    /**
     * This function sets up the frontend controller
     */
    public function feInit()
    {

        $this->feParams         = (object)[

            'entity'            => ConsoleServerConnectionEntity::class,
            'pagetitle'         => 'Console Server Connections',

            'titleSingular'     => 'Console Server Connection',
            'nameSingular'      => 'a console server connection',

            'listOrderBy'       => 'customer, port',
            'listOrderByDir'    => 'ASC',

            'viewFolderName'    => 'console-server-connection',

            'listColumns'    => [

                'id'        => [ 'title' => 'DB ID', 'display' => true ],

                'customer'  => [
                    'title'      => 'Customer',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'customer',
                    'action'     => 'overview',
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
                'speed'       => [
                    'title'             => 'Speed',
                    'hideIfFieldTrue'   => "autobaud"
                ],
                'parity'       => [
                    'title'             => 'Parity',
                    'type'              => self::$FE_COL_TYPES[ 'CONST' ],
                    'const'             => ConsoleServerConnectionEntity::$PARITY,
                    'hideIfFieldTrue'   => "autobaud"
                ],
                'stopbits'       => [
                    'title'             => 'Stopbits',
                    'hideIfFieldTrue'   => "autobaud"
                ],
                'flowcontrol'       => [
                    'title'             => 'Flow Control',
                    'type'              => self::$FE_COL_TYPES[ 'CONST' ],
                    'const'             => ConsoleServerConnectionEntity::$FLOW_CONTROL,
                    'hideIfFieldTrue'   => "autobaud"
                ],
                'autobaud'       => [
                    'title'         => 'Autobaud',
                    'type'          => self::$FE_COL_TYPES[ 'YES_NO' ],
                ],
                'notes'       => [
                    'title'         => 'Notes',
                    'type'          => self::$FE_COL_TYPES[ 'PARSDOWN' ]
                ]
            ]
        );


    }

    protected static function additionalRoutes( string $route_prefix )
    {
        Route::group( [ 'prefix' => $route_prefix ], function() use ( $route_prefix ) {
            Route::get(     'list/port/{port}',               'ConsoleServer\ConsoleServerConnectionController@listPort'    )->name( $route_prefix . '@listPort'   );
        });
    }

    /**
     * Provide array of rows for the list action and view action
     *
     * @param int $id The `id` of the row to load for `view` action`. `null` if `listAction`
     *
     * @return array
     */
    protected function listGetData( $id = null )
    {
        return D2EM::getRepository( ConsoleServerConnectionEntity::class )->getAllForFeList( $this->feParams, $id );
    }



    /**
     * Display the form to add/edit an object
     *
     * @param   int $id ID of the row to edit
     *
     * @return array
     */
    protected function addEditPrepareForm( $id = null ) : array
    {
        if( $id !== null ) {
            if( !( $this->object = D2EM::getRepository( ConsoleServerConnectionEntity::class )->find( $id ) ) ) {
                abort(404, "Console Server Connection not found." );
            }

            Former::populate([
                'description'   => request()->old( 'description',    $this->object->getDescription() ),
                'custid'        => request()->old( 'custid',         $this->object->getCustomer()->getId() ),
                'serverid'      => request()->old( 'serverid',       $this->object->getId() ),
                'port'          => request()->old( 'port',           $this->object->getPort() ),
                'speed'         => request()->old( 'speed',          $this->object->getSpeed() ),
                'parity'        => request()->old( 'parity',         $this->object->getParity() ),
                'stopbits'      => request()->old( 'stopbits',       $this->object->getStopbits() ) ,
                'flowcontrol'   => request()->old( 'flowcontrol',    $this->object->getFlowcontrol() ),
                'autobaud'      => request()->old( 'autobaud',       ( $this->object->getAutobaud() ?? false ) ),
                'notes'         => request()->old( 'notes',       $this->object->getNotes() )
            ]);
        }

        return [
            'object'                => $this->object,
            'custs'                 => D2EM::getRepository( CustomerEntity::class )->getAsArray(),
            'servers'               => D2EM::getRepository( ConsoleServerEntity::class )->getAsArray(),
            'cs'                    => request()->input( "serverid", false )
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
    public function doStore( Request $request )
    {
        $validator = Validator::make( $request->all(), [
            'description'           => 'required|string|max:255',
            'custid'                => 'required|int|exists:Entities\Customer,id',
            'serverid'              => 'required|int|exists:Entities\ConsoleServer,id',
            'port'                  => 'required|string|max:255',
            'speed'                 => 'nullable|integer',
            'parity'                => 'nullable|string',
            'stopbits'              => 'nullable|string',
            'flowcontrol'           => 'nullable|string',
            'autobaud'              => 'boolean',
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

        $validator->after( function ( $validator ) use( $request ) {

            if( $request->input( 'serverid' ) != null && $request->input( 'port' ) != null ){
                if( $csFound = D2EM::getRepository( ConsoleServerConnectionEntity::class )->getByServerAndPort( $request->input( 'serverid' ), $request->input( 'port' ) ) ) {

                    if( $this->object->getId() !== $csFound[0]->getId() ) {

                        $validator->errors()->add( 'port', 'This port is already used by this console server.' );
                    }
                }
            }
        });

        if( $validator->fails() ) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        $this->object->setDescription(   $request->input( 'description'  ) );
        $this->object->setPort(          $request->input( 'port'         ) );
        $this->object->setSpeed(         $request->input( 'speed'        ) );
        $this->object->setParity(        $request->input( 'parity'       ) );
        $this->object->setStopbits(      $request->input( 'stopbits'     ) );
        $this->object->setFlowcontrol(   $request->input( 'flowcontrol'  ) );
        $this->object->setNotes(         $request->input( 'notes'        ) );
        $this->object->setAutobaud(     $request->input( 'autobaud'     ) ? 1 : 0  );
        $this->object->setCustomer(      D2EM::getRepository( CustomerEntity::class  )->find( $request->input( 'custid'      ) ) );
        $this->object->setConsoleServer( D2EM::getRepository( ConsoleServerEntity::class  )->find( $request->input( 'serverid'    ) ) );

        D2EM::flush();

        return true;
    }

    protected function preList()
    {
        $this->data[ 'params' ]         = [ 'css' => D2EM::getRepository( ConsoleServerEntity::class )->getAsArray( ) ];
    }

    /**
     * Display the Console Server Connections for a port
     *
     * @param int $port ID of the Console Server
     *
     * @return View
     */
    public function listPort( int $port = null )
    {
        /** @var ConsoleServerEntity $cs */
        if( $port && !( $cs = D2EM::getRepository( ConsoleServerEntity::class )->find( $port ) ) ) {
            abort(404);
        }

        $this->data[ 'rows' ]                           = D2EM::getRepository( ConsoleServerConnectionEntity::class )->getAllForFeList( $this->feParams, null, $cs->getId() );

        $this->data[ 'view' ][ 'listEmptyMessage']      = $this->resolveTemplate( 'list-empty-message', false );
        $this->data[ 'view' ][ 'listHeadOverride']      = $this->resolveTemplate( 'list-head-override', false );
        $this->data[ 'view' ][ 'listRowOverride']       = $this->resolveTemplate( 'list-row-override',  false );
        $this->data[ 'view' ][ 'listPreamble']          = $this->resolveTemplate( 'list-preamble',      false );
        $this->data[ 'view' ][ 'listPostamble']         = $this->resolveTemplate( 'list-postamble',     false );
        $this->data[ 'view' ][ 'listRowMenu']           = $this->resolveTemplate( 'list-row-menu',      false );
        $this->data[ 'view' ][ 'pageHeaderPreamble']    = $this->resolveTemplate( 'page-header-preamble',      false );
        $this->data[ 'view' ][ 'listScript' ]           = $this->resolveTemplate( 'js/list' );

        $this->preList();

        $this->data[ 'params' ][ "cs" ]                 = $cs->getId();

        return $this->display( 'list' );
    }


    /**
     * @inheritdoc
     */
    protected function postStoreRedirect()
    {
        if( request()->input( "serverid" ) && ( $cs = D2EM::getRepository( ConsoleServerEntity::class )->find( request()->input( "serverid" ) ) ) ){
            return route( 'console-server-connection@listPort' , [ "port" => request()->input( "serverid" ) ] ) ;
        } else {
            return route( 'console-server-connection@list' );
        }
    }

    /**
     * @inheritdoc
     *
     * @return null|string
     */
    protected function postDeleteRedirect()
    {
        return route('console-server-connection@listPort' , [ 'port' => $this->object->getConsoleServer()->getId() ] );
    }

}
