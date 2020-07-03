<?php

namespace IXP\Http\Controllers\Switches;

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

use Former, Log, Redirect, Route;

use Illuminate\Http\{
    RedirectResponse,
    JsonResponse,
    Request
};

use Illuminate\View\View;

use IXP\Models\{
    Switcher,
    SwitchPort
};

use IXP\Utils\Http\Controllers\Frontend\EloquentController;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use OSS_SNMP\{
    Exception,
    SNMP
};

use OSS_SNMP\MIBS\Iface;

/**
 * Switch Port Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SwitchPortController extends EloquentController
{
    /**
     * The object being created / edited
     * @var SwitchPort
     */
    protected $object = null;

    /**
     * This function sets up the frontend controller
     */
    public function feInit()
    {
        $this->feParams         = (object)[
            'entity'            => SwitchPort::class,
            'pagetitle'         => 'Switch Ports',
            'titleSingular'     => 'Switch Port',
            'nameSingular'      => 'a switch port',
            'listOrderBy'       => 'name',
            'listOrderByDir'    => 'ASC',
            'viewFolderName'    => 'switch-port',
            'route_action'      => 'list',
            'route_prefix_page_title'   => 'switch',
            'pagetitlepostamble'         => 'Switch Port',

            'listColumns'       => [
                'id'        => [
                    'title' => 'UID',
                    'display' => false
                ],
                'switchname'  => [
                    'title'      => 'Switch',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'switch',
                    'action'     => 'view',
                    'idField'    => 'switchid'
                ],
                'name'           => 'Description',
                'ifName'         => 'Name',
                'ifAlias'        => 'Alias',
                'active'       => [
                    'title'    => 'Active',
                    'type'     => self::$FE_COL_TYPES[ 'YES_NO' ],
                ],
                'type'  => [
                    'title'    => 'Type',
                    'type'     => self::$FE_COL_TYPES[ 'XLATE' ],
                    'xlator'   => SwitchPort::$TYPES
                ]
            ]
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = $this->feParams->listColumns;
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
        // NB: this route is marked as 'read-only' to disable normal CRUD operations. It's not really read-only.
        Route::group( [  'prefix' => $route_prefix ], function() {
            Route::get(  'unused-optics',       'Switches\SwitchPortController@unusedOptics'   )->name( "switch-port@unused-optics"     );
            Route::get(  'optic-inventory',     'Switches\SwitchPortController@opticInventory' )->name( "switch-port@optic-inventory"   );
            Route::get(  'optic-list',          'Switches\SwitchPortController@opticList'      )->name( "switch-port@optic-list"        );
            Route::get(  'list-mau/{switch}',   'Switches\SwitchPortController@listMau'        )->name( "switch-port@list-mau"          );
            Route::get(  'op-status/{switch}',  'Switches\SwitchPortController@listOpStatus'   )->name( "switch-port@list-op-status"    );
            Route::get(  'snmp-poll/{switch}',  'Switches\SwitchPortController@snmpPoll'       )->name( "switch-port@snmp-poll"         );

            Route::post( 'set-type',            'Switches\SwitchPortController@setType'        )->name( "switch-port@set-type"          );
            Route::post( 'delete-snmp-poll',    'Switches\SwitchPortController@deleteSnmpPoll' )->name( "switch-port@delete-snmp-poll"  );
            Route::post( 'change-status',       'Switches\SwitchPortController@changeStatus'   )->name( "switch-port@change-status"     );
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
        return SwitchPort::getFeList( $this->feParams, $id, $this->data );
    }

    public function list( Request $r ) : View
    {
        $s = false;

        if( $r->switchid  !== null ) {
            if(  $s = Switcher::find( $r->switchid ) ) {
                $sid = $s->id;
                $r->session()->put( "switch-port-list", $sid );
            } else {
                $r->session()->remove( "switch-port-list" );
                $sid = false;
            }
        } else if( $r->session()->exists( "switch-port-list" ) ) {
            if( $s = Switcher::find( $r->session()->get( "switch-port-list" ) ) ) {
                $sid = $s->id;
            }
        } else {
            $sid = false;
        }

        $this->data[ 'params' ][ 'switchid' ]       = $sid;
        $this->data[ 'params' ][ 'switch' ]         = $s;
        $this->data[ 'params' ][ 'switches' ]       = Switcher::getListAsArray();

        $this->data[ 'rows' ] = $this->listGetData();

        $this->listIncludeTemplates();
        $this->preList();

        return $this->display( 'list' );
    }

    /**
     * Display the form to add/edit an object
     */
    protected function addForm()
    {
        $this->addEditSetup();
        $this->data[ 'params' ]['isAdd']        = true;
        $this->data[ 'params' ]['switches']     = Switcher::getListAsArray();

        return $this->display( 'add-form' );
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
            'switches'          => Switcher::getListAsArray()
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
        $this->object = SwitchPort::findOrFail( $id );

        Former::populate([
            'switchid'          => request()->old( 'switchid',    $this->object->switchid ),
            'name'              => request()->old( 'name',        $this->object->name ),
            'type'              => request()->old( 'type',        $this->object->type ),
            'active'            => request()->old( 'active',      ( $this->object->active ? 1 : 0 ) ) ,
        ]);

        return [
            'object'            => $this->object,
            'switches'          => Switcher::getListAsArray()
        ];
    }

    /**
     * Function to do the actual validation and storing of the submitted object.
     *
     * @param Request $request
     * @return bool|RedirectResponse
     *
     * @throws
     */
    public function doStore( Request $request )
    {
        $rules = [
            'switchid' => [
                'required', 'integer',
                function( $attribute, $value, $fail ) {
                    if( !Switcher::whereId( $value )->exists() ) {
                        return $fail( 'Switch is invalid / does not exist.' );
                    }
                }
            ],
            'numfirst'                  => 'required|integer|min:0',
            'numports'                  => 'required|integer|min:1|max:48',
            'type'                      => 'required|integer|in:' . implode( ',', array_keys( SwitchPort::$TYPES ) ),
        ];

        for( $i = 0; $i < $request->numports; $i++ ) {
            $rules[ 'portName'.$i ] = 'required|string|max:255';
            $rules[ 'portType'.$i ] = 'required|integer|in:' . implode( ',', array_keys( SwitchPort::$TYPES ) );
        }

        $request->validate( $rules );

        for( $i = 0; $i < $request->numports; $i++ ) {
            $this->object = Switcher::create( [
                    'switchid' => $request->switchid,
                    'type' => $request->input('portType' . $i ),
                    'name' => $request->input('portName' . $i ),
                    'active' => $request->active,
                ] );
        }

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
     * @throws
     */
    public function doUpdate( Request $request, int $id )
    {
        $this->object = SwitchPort::findOrFail( $id );

        $request->validate( [
            'switchid' => [
                'required', 'integer',
                function( $attribute, $value, $fail ) {
                    if( !Switcher::whereId( $value )->exists() ) {
                        return $fail( 'Switch is invalid / does not exist.' );
                    }
                }
            ],
            'name'                      => 'required|string|max:255',
            'type'                      => 'required|integer|in:' . implode( ',', array_keys( SwitchPort::$TYPES ) ),
        ] );

        $this->object->update( $request->all() );

        return true;
    }


    /**
     * Allow D2F implementations to override where the post-store redirect goes.
     *
     * To implement this, have it return a valid route name
     *
     * @return string|null
     */
    protected function postStoreRedirect(): ?string
    {
        if( request()->isAdd ) {
            return route( "switch-port@list", [ "switch" => request()->switchid ] );
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    protected function preDelete(): bool
    {
        if( ( $this->object->physicalInterface() ) ) {
            $c = $this->object->physicalInterface->virtualInterface->customer();
            AlertContainer::push( "You cannot delete the switch port {$this->object->name} as it is assigned to a physical interface for "
                . "<a href=\"" . route('customer@overview', [ "id" => $c->id, "tab" => "ports" ]) . "\">{$c->name}</a>.", Alert::DANGER );
            return false;
        }

        if(  $this->object->patchPanelPort() ) {
            $ppp = $this->object->patchPanelPort;
            AlertContainer::push( "You cannot delete the switch port {$this->object->name} as it is assigned to a patch panel port for "
                . "<a href=\"" . route('patch-panel-port/list/patch-panel', [ "ppid" => $ppp->patchPanel->id ] ) . "\">{$ppp->name}</a>.", Alert::DANGER );
            return false;
        }

        return true;
    }

    /**
     * Set up all the information to display the Unused optics list
     *
     * @bool
     */
    public function setUpUnusedOptics(): bool
    {
        $this->feParams->listOrderBy                = 'switchname';
        $this->feParams->pagetitle                  = 'Switches';
        $this->feParams->pagetitlepostamble         = 'Unused Optics';
        $this->feParams->route_prefix_page_title    = 'switch';
        $this->feParams->readonly                   = true;
        $this->feParams->hideactioncolumn           = true;

        $this->feParams->listColumns = [
            'ifIndex'       => [
                'title' => 'UID',
                'display' => false
            ],
            'switchname'    => 'Switch',
            'ifName'        => 'Port',
            'type'          => 'Type',
            'mauType'       => 'MAU Type',
            'mauState'      => 'MAU State',
            'mauJacktype'   => 'Jack Type',
        ];

        return true;
    }

    /**
     * Display the unused optics
     *
     * @return view
     *
     * @throws
     */
    public function unusedOptics() : View
    {
        $this->setUpUnusedOptics();

        $this->data[ 'rows' ] =  SwitchPort::getUnusedOpticsForFeList( $this->feParams );

        $this->listIncludeTemplates();
        $this->data[ 'view' ][ 'pageBreadcrumbs'] = $this->resolveTemplate( 'page-bread-crumbs',false );
        $this->preList();

        AlertContainer::push( "A list of ports from <b>switches that support the IANA MAU MIB</b> where the operational status
        is down, the port is populated with an optic / SFP and the port type is not management.
        Data valid at time of last SNMP poll.", Alert::INFO );

        return $this->display( 'list' );
    }

    /**
     * Set up all the information to display the Unused optics list
     *
     *
     * @bool
     */
    public function setUpListMau()
    {
        $this->feParams->pagetitle                  = 'Switches';
        $this->feParams->route_prefix_page_title    = 'switch';
        $this->feParams->route_action               = 'list-mau';
        $this->feParams->listOrderBy                = 'id';
        $this->feParams->readonly                   = true;
        $this->feParams->hideactioncolumn           = true;

        $this->feParams->listColumns = [
            'id'                    =>
                [ 'title' => 'UID',
                  'display' => true
                ],
            'ifName'                => 'Name',
            'type'                  => [
                'title'     =>  'Type',
                'type'      =>   self::$FE_COL_TYPES[ 'CONST' ],
                'const'     =>   SwitchPort::$TYPES,
            ],
            'state'                 => [
                'title'     =>  'State (Admin/Op)',
                'type'      =>   self::$FE_COL_TYPES[ 'SCRIPT' ],
                'script'    =>   'switch-port/port-admin-status',
                'params'    =>   [
                                "adminState"    => "ifAdminStatus",
                                "operState"     => "ifOperStatus",
                ],
            ],
            'mauType'               => 'MAU Type',
            'mauState'              => 'MAU State',
            'mauAvailability'       => 'MAU Availability',
            'mauJacktype'           => 'Jack Type',
            'mauAutoNegAdminState'  => [
                'title'     =>  'Auto Neg',
                'type'      =>   self::$FE_COL_TYPES[ 'SCRIPT' ],
                'script'    =>   'switch-port/port-auto-neg',
                'params'    =>   [
                    "mauAutoNegAdminState"    => "mauAutoNegAdminState",
                ],
            ]
        ];

        return true;
    }


    /**
     * Display the unused optics
     *
     * @param Switcher $switch
     *
     * @return RedirectResponse|View
     *
     * @throws
     */
    public function listMau( Switcher $switch  )
    {
        if( !$switch->mauSupported ) {
            return redirect::to( route( "switch@list" ) );
        }

        $this->setUpListMau();
        $this->data[ 'rows' ] =  SwitchPort::getListMau( $this->feParams, $switch->id );

        $this->feParams->pagetitlepostamble             = 'MAU Interface Detail for ' . $switch->name ;

        $this->data[ 'params' ][ 'switches' ]           = Switcher::where( 'mauSupported', true )->get()->keyBy( 'id' )->toArray();
        $this->data[ 'params' ][ 'switch' ]             = $switch;
        $this->data[ 'params' ][ 'switchid' ]           = $switch->id;

        $this->listIncludeTemplates();
        $this->data[ 'view' ][ 'pageBreadcrumbs']       = $this->resolveTemplate( 'page-bread-crumbs',          false );

        $this->preList();

        AlertContainer::push( "Data valid at time of last SNMP poll: " . $switch->lastpolled, Alert::INFO );

        return $this->display( 'list' );
    }


    /**
     * Set up all the information to display the Unused optics list
     *
     * @bool
     */
    public function setUpOpStatus( )
    {
        $this->feParams->listOrderBy                = 'ifIndex';
        $this->feParams->pagetitle                  = 'Switches';
        $this->feParams->route_prefix_page_title    = 'switch';
        $this->feParams->route_action               = 'list-op-status';

        $this->feParams->listColumns = [
            'ifIndex'       => 'Index',
            'name'          => 'Description',
            'ifName'        => 'Name',
            'ifAlias'       => 'Alias',
            'lagIfIndex'    => 'LAG',
            'ifHighSpeed'   => 'Speed',
            'ifMtu'         => 'MTU',
            'ifAdminStatus' => [
                'title'    => 'Admin State',
                'type'     => self::$FE_COL_TYPES[ 'SCRIPT' ],
                'script'   => 'switch-port/port-status',
                'params'    =>
                    [
                        "state"    => "ifAdminStatus",
                    ]
            ],
            'ifOperStatus' => [
                'title'    => 'Operational State',
                'type'     => self::$FE_COL_TYPES[ 'SCRIPT' ],
                'script'   => 'switch-port/port-status',
                'params'  =>
                    [
                        "state"    => "ifOperStatus",

                    ],
                    'active'       => [
                            'title'    => 'Active',
                            'type'     => self::$FE_COL_TYPES[ 'YES_NO' ],
                    ],
            ]
        ];

        return true;
    }

    /**
     * Display the switch ports operation status for a switch
     *
     * @param Switcher $switch
     *
     * @return view
     *
     * @throws
     */
    public function listOpStatus( Switcher $switch  ): view
    {
        // to refresh switch and switch port details via SNMP
        try
        {
            $host = new SNMP( $switch->hostname, $switch->snmppasswd );

            $switch->snmpPoll( $host, true );
            $switch->snmpPollSwitchPorts( $host, true );
            $switch->save();

            AlertContainer::push( "The below is <b>live information</b> gathered via SNMP", Alert::INFO );
        } catch( Exception $e ) {
            $lastpolled = is_null( $switch->lastPolled) ? "never" : $switch->lastPolled;

            AlertContainer::push( "<b>Could not update switch and switch port details via SNMP poll.</b> " .
                "Last successful poll: " . $lastpolled . "</b>.", Alert::DANGER );
        }

        $this->setUpOpStatus();

        $this->data[ 'params' ][ 'portStates' ]     = Iface::$IF_OPER_STATES;
        $this->data[ 'params' ][ 'switch' ]         = $switch;
        $this->data[ 'params' ][ 'switchid' ]       = $switch->id;
        $this->data[ 'params' ][ 'switches']        = Switcher::getListAsArray();

        $this->data[ 'rows' ] =  $this->listGetData();

        $this->feParams->pagetitlepostamble             = 'List Live Port State for ' . $switch->name ;

        $this->listIncludeTemplates();

        return $this->display( 'list' );
    }


    /**
     * This action will find all ports on a switch, match them (where possible) to existing
     * ports of that switch in the database and allow the user to:
     *
     *  - view name (ifDescr), ifName and ifAlias
     *  - set the switchport type in bulk
     *  - remove port(s)
     *  - manage these actions in bulk (e.g. phpMyAdmin type row management)
     *
     *  Should this be in the SwitchController? Possibly...
     *
     * @param Switcher $switch Switch
     *
     * @return view
     * @throws
     */
    public function snmpPoll( Switcher $switch ): view
    {
        if( !$switch->active ) {
            AlertContainer::push( "SNMP Polling of ports is only valid for switches that are active", Alert::DANGER );
            redirect::to( route( "switch@list" ) );
        }

        $results = [];
        try {
            $host = new SNMP( $switch->hostname, $switch->snmppasswd );
            $switch->snmpPoll( $host, true );
            $switch->snmpPollSwitchPorts( $host, true, $results );
            $switch->save();

        } catch( Exception $e ) {
            AlertContainer::push( "Error polling switch via SNMP.", Alert::DANGER );
            redirect::to( route( "switch@list" ) );
        }

        return view( 'switch-port/snmp-poll' )->with([
            'switches'                  => Switcher::getListAsArray(),
            's'                         => $switch,
            'ports'                     => $results,

        ]);
    }

    /**
     * Sets port type for port loaded
     *
     * @param   Request     $r          HTTP instance
     *
     * @return JsonResponse
     *
     * @throws
     */
    public function setType( Request $r ): JsonResponse
    {
        if( !$r->spid ) {
            return response()->json( [ 'success' => false ] );
        }

        foreach( $r->spid as $id ) {
            $sp = SwitchPort::findOrFail( $id );

            if( !array_key_exists( $r->type, SwitchPort::$TYPES ) ){
                return response()->json( [ 'success' => false ] );
            }

            $sp->type = $r->type;
            $sp->save();
        }

        if( $r->returnMessage ) {
            AlertContainer::push( "The selected switch ports have been updated", Alert::SUCCESS );
        }

        return response()->json( [ 'success' => true ] );
    }


    /**
     * Sets port type for port loaded
     *
     * @param   Request     $r          HTTP instance
     *
     * @return JsonResponse
     *
     * @throws
     */
    public function deleteSnmpPoll( Request $r ): JsonResponse
    {

        if( !$r->spid) {
            return response()->json( [ 'success' => false ] );
        }

        foreach( $r->spid as $id ) {
            $error = false;
            Log::debug( 'Html\Controllers\Switches\SwitchPort::deleteSnmpPoll() - Processing switch port ID: ' . $id );

            $sp = SwitchPort::findOrFail( $id );

            if( $sp->physicalInterface() ) {
                $cust = $sp->physicalInterface->virtualInterface->customer;
                AlertContainer::push( "Could not delete switch port {$sp->name} as it is assigned to a physical interface for "
                    . "<a href=\""
                    . route( "customer@overview" , [ 'id' => $cust->id, 'tab' => 'ports' ]  )
                    . "\">{$cust->name}</a>.", Alert::DANGER
                );

                $error = true;
            }

            if( $sp->patchPanelPort ) {
                $ppp = $sp->patchPanelPort;
                AlertContainer::push( "Could not delete switch port {$sp->name} as it is assigned to a patch panel port for "
                    . "<a href=\""
                    . route( "patch-panel-port/list/patch-panel" , [ 'ppid' => $ppp->id ]  )
                    . "\">{$ppp->name}</a>.", Alert::DANGER
                );

                $error = true;
            }

            if( !$error ) {
                $sp->delete();
            }
        }

        AlertContainer::push(
            "<b>Please Note:</b> It is not possible to delete real physical Ethernet switch ports as "
            . "the switch is re-polled and these ports are added back into the system as new ports automatically. "
            . "The purpose of delete is to remove ports that were manually added to the database that do not match "
            . "up with physical ports on the switch. You can, however, deactivate switch ports.",
            Alert::INFO
        );

        AlertContainer::push( "The selected switch ports - where possible - have been deleted", Alert::SUCCESS );

        return response()->json( [ 'success' => true ] );
    }

    /**
     * Change the port status for the Switch Ports
     *
     * @param   Request     $r          HTTP instance
     *
     * @return JsonResponse
     *
     * @throws
     */
    public function changeStatus( Request $r ): JsonResponse
    {
        if( $r->spid ) {
            foreach( $r->spid as $id ) {
                $sp = SwitchPort::findOrFail( $id );
                $sp->active = $r->active ? 1 : 0;
                $sp->save();
            }

            AlertContainer::push(  "The selected switch ports have been updated.", Alert::SUCCESS );
            return response()->json( [ 'success' => true ] );
        }
        return response()->json( [ 'success' => false ] );
    }

    /**
     * Set up all the information to display the optic inventory
     *
     * @bool
     */
    public function setUpOpticInventory(): bool
    {
        $this->feParams->listOrderBy                = 'cnt';
        $this->feParams->pagetitle                  = 'Switches';
        $this->feParams->pagetitlepostamble         = 'Optic Inventory';
        $this->feParams->route_prefix_page_title    = 'switch';
        $this->feParams->route_action               = 'optic-inventory';
        $this->feParams->hideactioncolumn           = true;
        $this->feParams->listOrderByDir             = "DESC";

        $this->feParams->listColumns = [
            'mauType'           => 'Type',
            'cnt'  => [
                'title'                 => 'Count',
                'type'                  => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                'controller'            => 'switch-port',
                'action'                => 'optic-list',
                'nameIdOptionalParam'   => 'mau-type',
                'idField'               => 'mauType'
            ],
        ];

        return true;
    }

    /**
     * Display the Optic Inventory
     *
     * @return view
     *
     * @throws
     */
    public function opticInventory(): view
    {
        $this->setUpOpticInventory();
        $this->data[ 'rows' ] =  SwitchPort::getOpticInventory( $this->feParams );
        $this->listIncludeTemplates();
        return $this->display( 'list' );
    }


    /**
     * Set up all the information to display the optics list
     *
     *
     * @bool
     */
    public function setUpOpticList(): bool
    {
        $this->feParams->pagetitle                  = 'Switches';
        $this->feParams->pagetitlepostamble         = 'Optic List';
        $this->feParams->route_prefix_page_title    = 'switch';
        $this->feParams->listOrderBy                = 'ifName';
        $this->feParams->readonly                   = true;
        $this->feParams->hideactioncolumn           = true;

        $this->feParams->listColumns = [
            'id'                    =>
                [ 'title' => 'UID',
                  'display' => false
                ],
            'ifName'                => 'Name',
            'switch'                => 'Switch',
            'custname'  => [
                'title'                 => 'Customer',
                'type'                  => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                'controller'            => 'customer',
                'action'                => 'overview',
                'idField'               => 'custid'
            ],
            'type'                  => [
                'title'     =>  'Type',
                'type'      =>   self::$FE_COL_TYPES[ 'CONST' ],
                'const'     =>   SwitchPort::$TYPES,
            ],

            'state'                 => [
                'title'     =>  'State (Admin/Op)',
                'type'      =>   self::$FE_COL_TYPES[ 'SCRIPT' ],
                'script'    =>   'switch-port/port-admin-status',
                'params'    =>   [
                    "adminState"    => "ifAdminStatus",
                    "operState"     => "ifOperStatus",
                ],
            ],
            'mauType'               => 'MAU Type',
            'mauState'              => 'MAU State',
        ];

        return true;
    }

    /**
     * Display the Optic list
     *
     * @return view
     *
     * @throws
     */
    public function opticList(): view
    {
        $this->setUpOpticList();
        $this->data[ 'rows' ] =  SwitchPort::getListMauForType( $this->feParams, request()->input( "mau-type" ) );
        $this->listIncludeTemplates();
        $this->preList();
        return $this->display( 'list' );
    }
}