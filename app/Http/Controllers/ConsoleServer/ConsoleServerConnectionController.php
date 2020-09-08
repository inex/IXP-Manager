<?php

namespace IXP\Http\Controllers\ConsoleServer;

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

use Former, Redirect, Route;

use Illuminate\Http\{
    Request,
    RedirectResponse
};

use Illuminate\View\View;

use IXP\Models\{
    ConsoleServer,
    ConsoleServerConnection,
    Customer
};

use IXP\Utils\Http\Controllers\Frontend\EloquentController;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * ConsoleServerConnection Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ConsoleServerConnectionController extends EloquentController
{
    /**
     * The object being created / edited
     *
     * @var ConsoleServerConnection
     */
    protected $object = null;

    /**
     * This function sets up the frontend controller
     */
    public function feInit(): void
    {
        $this->feParams         = (object)[
            'entity'            => ConsoleServerConnection::class,
            'pagetitle'         => 'Console Server Connections',
            'titleSingular'     => 'Console Server Connection',
            'nameSingular'      => 'a console server connection',
            'listOrderBy'       => [ 'c.name', 'csc.port' ],
            'listOrderByDir'    => 'ASC',
            'viewFolderName'    => 'console-server-connection',

            'listColumns'    => [
                'id'        => [
                    'title' => 'DB ID',
                    'display' => true
                ],
                'customer'  => [
                    'title'      => 'Customer',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'customer',
                    'action'     => 'overview',
                    'idField'    => 'customerid'
                ],
                'description'   => 'Description',
                'port'          => 'Port'
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
                    'const'             => ConsoleServerConnection::$PARITY,
                    'hideIfFieldTrue'   => "autobaud"
                ],
                'stopbits'       => [
                    'title'             => 'Stopbits',
                    'hideIfFieldTrue'   => "autobaud"
                ],
                'flowcontrol'       => [
                    'title'             => 'Flow Control',
                    'type'              => self::$FE_COL_TYPES[ 'CONST' ],
                    'const'             => ConsoleServerConnection::$FLOW_CONTROL,
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

    protected static function additionalRoutes( string $route_prefix ): void
    {
        Route::group( [ 'prefix' => $route_prefix ], static function() use ( $route_prefix ) {
            Route::get(     'list/port/{cs}',               'ConsoleServer\ConsoleServerConnectionController@listPort'    )->name( $route_prefix . '@listPort'   );
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
        return ConsoleServerConnection::getFeList( $this->feParams, $id );
    }

    protected function preList(): void
    {
        $this->data[ 'params' ]     = [
            'css' =>  ConsoleServer::orderBy( 'name', 'asc')->get()->keyBy( 'id' )->toArray()
        ];
    }

    /**
     * Display the form to create an object
     *
     * @return array
     */
    protected function createPrepareForm() : array
    {
        return [
            'object'                => $this->object,
            'custs'                 => Customer::orderBy( 'name' )->get()->toArray(),
            'servers'               => ConsoleServer::orderBy( 'name', 'asc')->get()->keyBy( 'id' )->toArray(),
            'cs'                    => request()->serverid
        ];
    }

    /**
     * Display the form to edit an object
     *
     * @param   int $id ID of the row to edit
     *
     * @return array
     */
    protected function editPrepareForm( $id = null ) : array
    {
        $this->object = ConsoleServerConnection::find( $id );

        Former::populate([
            'description'           => request()->old( 'description',           $this->object->description ),
            'custid'                => request()->old( 'custid',                $this->object->custid ),
            'console_server_id'     => request()->old( 'console_server_id',     $this->object->console_server_id ),
            'port'                  => request()->old( 'port',                  $this->object->port ),
            'speed'                 => request()->old( 'speed',                 $this->object->speed ),
            'parity'                => request()->old( 'parity',                $this->object->parity ),
            'stopbits'              => request()->old( 'stopbits',              $this->object->stopbits ) ,
            'flowcontrol'           => request()->old( 'flowcontrol',           $this->object->flowcontrol ),
            'autobaud'              => request()->old( 'autobaud',       $this->object->autobaud ? 1 : 0 ),
            'notes'                 => request()->old( 'notes',                 $this->object->notes )
        ]);

        return [
            'object'                => $this->object,
            'custs'                 => Customer::orderBy( 'name' )->get(),
            'servers'               => ConsoleServer::orderBy( 'name', 'asc')->get()->keyBy( 'id' )->toArray(),
            'cs'                    => request()->serverid
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
            'description'           => 'required|string|max:255',
            'custid'            => [ 'required', 'integer',
                function( $attribute, $value, $fail ) {
                    if( !Customer::whereId( $value )->exists() ) {
                        return $fail( 'Customer is invalid / does not exist.' );
                    }
                }
            ],
            'console_server_id'            => [ 'required', 'integer',
                function( $attribute, $value, $fail ) {
                    if( !ConsoleServer::whereId( $value )->exists() ) {
                        return $fail( 'Console Server is invalid / does not exist.' );
                    }
                }
            ],
            'port'                  => 'required|string|max:255',
            'speed'                 => 'nullable|integer',
            'parity'                => 'nullable|string',
            'stopbits'              => 'nullable|string',
            'flowcontrol'           => 'nullable|string',
            'autobaud'              => 'boolean',
            'notes'                 => 'nullable|string|max:65535',
        ] );
    }

    /**
     * Check if there is a duplicate console server connection object with those values
     *
     * @param int|null  $objectid
     * @param Request   $request
     *
     * @return bool
     */
    private function checkIsDuplicate( int $objectid = null, Request $request ): bool
    {
        if( $cs = ConsoleServerConnection::where( "console_server_id" , $request->console_server_id  )->where( 'port' , $request->port )->get()->first() ) {
            if( $objectid !== $cs->id ) {
                AlertContainer::push( "This port is already used by this console server."   , Alert::DANGER );
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

        $this->object = ConsoleServerConnection::create( $request->all() );
        return true;
    }

    /**
     * Function to do the actual validation and storing of the submitted object.
     *
     * @param Request $request
     * @param int $id
     *
     * @return bool|RedirectResponse
     *
     */
    public function doUpdate( Request $request, int $id )
    {
        $this->object = ConsoleServerConnection::findOrFail( $id );
        $this->checkForm( $request );
        if( $this->checkIsDuplicate( $this->object->id, $request ) ) {
            return Redirect::back()->withInput();
        }

        $this->object->update( $request->all() );
        return true;
    }

    /**
     * Display the Console Server Connections for a port
     *
     * @param ConsoleServer $cs ID of the Console Server
     *
     * @return View
     */
    public function listPort( ConsoleServer $cs ): View
    {
        $this->data[ 'rows' ]               = ConsoleServerConnection::getFeList( $this->feParams, null, $cs->id );
        $this->data[ 'params' ][ "cs" ]     = $cs->id;
        $this->listIncludeTemplates();
        $this->preList();

        return $this->display( 'list' );
    }


    /**
     * @inheritdoc
     */
    protected function postStoreRedirect(): ?string
    {
        if( $cs = ConsoleServer::find( request()->console_server_id ) ) {
            return route( 'console-server-connection@listPort' , [ "cs" => $cs->id ] ) ;
        }

        return route( 'console-server-connection@list' );
    }

    /**
     * @inheritdoc
     *
     * @return null|string
     */
    protected function postDeleteRedirect(): ?string
    {
        return route('console-server-connection@listPort' , [ 'cs' => $this->object->console_server_id ] );
    }
}