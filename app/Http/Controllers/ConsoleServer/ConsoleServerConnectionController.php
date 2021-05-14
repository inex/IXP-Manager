<?php

namespace IXP\Http\Controllers\ConsoleServer;

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
    Request,
    RedirectResponse
};

use Illuminate\View\View;

use IXP\Models\{
    Aggregators\ConsoleServerConnectionAggregatore,
    ConsoleServer,
    ConsoleServerConnection,
    Customer,
};

use IXP\Utils\Http\Controllers\Frontend\EloquentController;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * ConsoleServerConnection Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\ConsoleServer
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
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
            'model'             => ConsoleServerConnection::class,
            'pagetitle'         => 'Console Server Connections',
            'titleSingular'     => 'Console Server Connection',
            'nameSingular'      => 'a console server connection',
            'listOrderBy'       => [ 'c.name', 'csc.port' ],
            'listOrderByDir'    => 'ASC',
            'viewFolderName'    => 'console-server-connection',
            'listColumns'    => [
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
                ],
                'created_at'       => [
                    'title'         => 'Created',
                    'type'          => self::$FE_COL_TYPES[ 'DATETIME' ]
                ],
                'updated_at'       => [
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
            Route::get(     'list/port/{cs}',               'ConsoleServer\ConsoleServerConnectionController@listPort'    )->name( $route_prefix . '@listPort'   );
        });
    }

    /**
     * Provide array of rows for the list action and view action
     *
     * @param int|null $id The `id` of the row to load for `view` action`. `null` if `listAction`
     *
     * @return array
     */
    protected function listGetData( ?int $id = null ): array
    {
        return ConsoleServerConnectionAggregatore::getFeList( $this->feParams, $id );
    }


    protected function preList(): void
    {
        $this->data[ 'params' ]     = [
            'css' =>  ConsoleServer::orderBy( 'name' )
                ->get()->keyBy( 'id' )->toArray()
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
            'custs'                 => Customer::orderBy( 'name' )->get(),
            'servers'               => ConsoleServer::orderBy( 'name' )->get()->keyBy( 'id' )->toArray(),
            'cs'                    => request()->console_server_id
        ];
    }

    /**
     * Display the form to edit an object
     *
     * @param   int $id ID of the row to edit
     *
     * @return array
     */
    protected function editPrepareForm( int $id ) : array
    {
        $this->object = ConsoleServerConnection::find( $id );

        Former::populate([
            'description'           => request()->old( 'description',           $this->object->description          ),
            'custid'                => request()->old( 'custid',                $this->object->custid               ),
            'console_server_id'     => request()->old( 'console_server_id',     $this->object->console_server_id    ),
            'port'                  => request()->old( 'port',                  $this->object->port                 ),
            'speed'                 => request()->old( 'speed',                 $this->object->speed                ),
            'parity'                => request()->old( 'parity',                $this->object->parity               ),
            'stopbits'              => request()->old( 'stopbits',              $this->object->stopbits             ),
            'flowcontrol'           => request()->old( 'flowcontrol',           $this->object->flowcontrol          ),
            'autobaud'              => request()->old( 'autobaud',              $this->object->autobaud             ),
            'notes'                 => request()->old( 'notes',                 $this->object->notes                )
        ]);

        return [
            'object'                => $this->object,
            'custs'                 => Customer::orderBy( 'name' )->get(),
            'servers'               => ConsoleServer::orderBy( 'name' )->get()->keyBy( 'id' )->toArray(),
            'cs'                    => request()->console_server_id
        ];
    }

    /**
     * Function to do the actual validation and storing of the submitted object.
     *
     * @param Request $r
     *
     * @return bool|RedirectResponse
     *
     * @throws
     */
    public function doStore( Request $r )
    {
        $this->checkForm( $r );
        if( $this->checkIsDuplicate( $r ) ) {
            return Redirect::back()->withInput();
        }

        $this->object = ConsoleServerConnection::make( $r->all() );
        $this->object->custid = $r->custid;
        $this->object->save();
        return true;
    }

    /**
     * Function to do the actual validation and storing of the submitted object.
     *
     * @param Request   $r
     * @param int       $id
     *
     * @return bool|RedirectResponse
     *
     */
    public function doUpdate( Request $r, int $id )
    {
        $this->object = ConsoleServerConnection::findOrFail( $id );
        $this->checkForm( $r );
        if( $this->checkIsDuplicate( $r,  $this->object->id ) ) {
            return Redirect::back()->withInput();
        }

        $this->object->fill( $r->all() );
        $this->object->custid = $r->custid;
        $this->object->save();
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
        $this->listIncludeTemplates();
        $this->preList();
        $this->data[ 'rows' ]               = ConsoleServerConnectionAggregatore::getFeList( $this->feParams, null, $cs->id );
        $this->data[ 'params' ][ "cs" ]     = $cs->id;
        return $this->display( 'list' );
    }

    /**
     * @inheritdoc
     */
    protected function postStoreRedirect(): ?string
    {
        if( $cs = ConsoleServer::find( request()->cs ) ) {
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

    /**
     * Check if the form is valid
     *
     * @param $r
     */
    public function checkForm( Request $r ): void
    {
        $r->validate( [
            'description'           => 'required|string|max:255',
            'custid'                => 'required|integer|exists:cust,id',
            'console_server_id'     => 'required|integer|exists:console_server,id',
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
     * @param Request $r
     * @param int|null  $objectid
     *
     * @return bool
     */
    private function checkIsDuplicate( Request $r , int $objectid = null ): bool
    {
        $exist = ConsoleServerConnection::where( "console_server_id" , $r->console_server_id  )
            ->where( 'port' , $r->port )
            ->when( $objectid , function( Builder $q, $objectid ) {
                return $q->where( 'id', '!=',  $objectid );
            })->count() ? true : false;

        if( $exist ) {
            AlertContainer::push( "This port is already used by this console server."   , Alert::DANGER );
            return true;
        }
        return false;
    }
}