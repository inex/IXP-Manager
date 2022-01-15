<?php

namespace IXP\Http\Controllers\Switches;

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

use Auth, DateTime, Former, Route;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\{
    Request,
    RedirectResponse
};

use Illuminate\View\View;

use IXP\Http\Requests\Switches\StoreBySmtp as StoreBySmtpRequest;

use IXP\Models\{
    Aggregators\SwitcherAggregator,
    Aggregators\SwitchPortAggregator,
    Cabinet,
    Infrastructure,
    Location,
    PhysicalInterface,
    Switcher,
    SwitchPort,
    User,
    Vendor,
    Vlan};

use IXP\Rules\IdnValidate;

use IXP\Utils\Http\Controllers\Frontend\EloquentController;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use OSS_SNMP\{
    Exception as SNMPException,
    Platform,
    SNMP
};

/**
 * Switch Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\Switches
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SwitchController extends EloquentController
{
    /**
     * The object being created / edited
     *
     * @var Switcher
     */
    protected $object = null;

    /**
     * This function sets up the frontend controller
     */
    public function feInit(): void
    {
        $this->feParams         = (object)[
            'model'             => Switcher::class,
            'pagetitle'         => 'Switches',
            'titleSingular'     => 'Switch',
            'nameSingular'      => 'switch',
            'listOrderBy'       => 'name',
            'listOrderByDir'    => 'ASC',
            'viewFolderName'    => 'switches',
            'addRoute'          => route( static::route_prefix() . '@create-by-snmp' ),
            'documentation'     => 'https://docs.ixpmanager.org/usage/switches/',
            'listColumns'       => [
                'name'           => 'Name',
                'cabinet'  => [
                    'title'      => 'Rack',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'rack',
                    'action'     => 'view',
                    'idField'    => 'cabinetid'
                ],
                'vendor'  => [
                    'title'      => 'Vendor',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'vendor',
                    'action'     => 'view',
                    'idField'    => 'vendorid'
                ],
                'infrastructure' => 'Infrastructure',
                'active'       => [
                    'title'    => 'Active',
                    'type'     => self::$FE_COL_TYPES[ 'YES_NO' ]
                ],
                'poll'       => [
                    'title'    => 'Poll',
                    'type'     => self::$FE_COL_TYPES[ 'YES_NO' ]
                ],
                'model'          => 'Model',
                'ipv4addr'       => 'IPv4 Address',
            ]
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = array_merge(
            $this->feParams->listColumns,
            [
                'ipv6addr'       => 'IPv6 Address',
                'hostname'       => 'Hostname',
                'snmppasswd'     => 'SNMP Community',
                'os'             => 'OS',
                'osVersion'      => 'OS Version',
                'osDate'         => [
                    'title'      => 'OS Date',
                    'type'       => self::$FE_COL_TYPES[ 'DATETIME' ]
                ],
                'lastPolled'         => [
                    'title'      => 'Last Polled',
                    'type'       => self::$FE_COL_TYPES[ 'DATETIME' ]
                ],
                'serialNumber'   => 'Serial Number',
                'mauSupported'   => [
                    'title'    => 'MAU Supported',
                    'type'     => self::$FE_COL_TYPES[ 'YES_NO_NULL' ]
                ],
                'asn'            => 'ASN',
                'loopback_ip'    => 'Loopback IP',
                'loopback_name'  => 'Loopback Name',
                'mgmt_mac_address' => 'Mgmt MAC Address',
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

        // phpunit / artisan trips up here without the cli test:
        if( PHP_SAPI !== 'cli' ) {
            // custom access controls:
            switch( Auth::check() ? Auth::getUser()->privs() : User::AUTH_PUBLIC ) {
                case User::AUTH_SUPERUSER:
                    break;
                case User::AUTH_CUSTUSER || User::AUTH_CUSTADMIN:
                    switch( Route::current()->getName() ) {
                        case 'switch@configuration':
                            break;

                        default:
                            $this->unauthorized();
                    }
                    break;
                default:
                    $this->unauthorized();
            }
        }
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
            Route::get(  'create-by-snmp',      'Switches\SwitchController@addBySnmp'       )->name( 'switch@create-by-snmp'   );
            Route::get(  'port-report/{switch}','Switches\SwitchController@portReport'      )->name( "switch@port-report"   );
            Route::get(  'configuration',       'Switches\SwitchController@configuration'   )->name( "switch@configuration" );
            Route::post( 'store-by-snmp',       'Switches\SwitchController@storeBySmtp'     )->name( "switch@store-by-snmp" );
        });
    }

    /**
     * List the contents of a database table.
     *
     * @param Request $r
     *
     * @return View
     */
    public function list( Request $r  ) : View
    {
        if( ( $showActiveOnly = $r->activeOnly ) !== null  ) {
            $r->session()->put( "switch-list-active-only", $showActiveOnly );
        } else if( $r->session()->exists( "switch-list-active-only" ) ) {
            $showActiveOnly = $r->session()->get( "switch-list-active-only" );
        } else {
            $showActiveOnly = false;
        }

        if( $vtype = $r->vtype ) {
            $r->session()->put( "switch-list-vtype", $vtype );
        } elseif( $r->session()->exists( "switch-list-vtype" ) ) {
            $vtype = $r->session()->get( "switch-list-vtype" );
        } else {
            $r->session()->remove( "switch-list-vtype" );
            $vtype = Switcher::VIEW_MODE_DEFAULT;
        }

        if( $r->infra ) {
            if(  $infra = Infrastructure::find( $r->infra ) ) {
                $r->session()->put( "switch-list-infra", $infra );
            } else {
                $r->session()->remove( "switch-list-infra" );
                $infra = false;
            }
        } else if( $r->session()->exists( "switch-list-infra" ) ) {
            $infra = $r->session()->get( "switch-list-infra" );
        } else {
            $infra = false;
        }

        if( $vtype === Switcher::VIEW_MODE_OS ) {
            $this->setUpOsView();
        } else if( $vtype === Switcher::VIEW_MODE_L3 ){
            $this->setUpL3View();
        }

        $this->data[ 'params' ][ 'activeOnly' ]         = $showActiveOnly;
        $this->data[ 'params' ][ 'vtype' ]              = $vtype;
        $this->data[ 'params' ][ 'infra' ]              = $infra;
        $this->data[ 'rows' ] = $this->listGetData();

        $this->listIncludeTemplates();
        $this->preList();

        return $this->display( 'list' );
    }

    /**
     * Set Up the the table to display the OS VIEW
     *
     * @return void
     */
    private function setUpOsView(): void
    {
        $this->feParams->listColumns = [
            'id'        =>
                [ 'title' => 'UID',
                  'display' => false
                ],
            'name'           => 'Name',
            'vendor'  => [
                'title'      => 'Vendor',
                'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                'controller' => 'vendor',
                'action'     => 'view',
                'idField'    => 'vendorid'
            ],
            'model'          => 'Model',
            'os'             => 'OS',
            'osVersion'      => 'OS Version',
            'serialNumber'   => 'Serial Number',
            'osDate'         => [
                'title'      => 'OS Date',
                'type'       => self::$FE_COL_TYPES[ 'DATETIME' ]
            ],
            'lastPolled'         => [
                'title'      => 'Last Polled',
                'type'       => self::$FE_COL_TYPES[ 'DATETIME' ]
            ],
            'active'       => [
                'title'    => 'Active',
                'type'     => self::$FE_COL_TYPES[ 'YES_NO' ]
            ]
        ];
    }

    /**
     * Set Up the the table to display the OS VIEW
     *
     * @return void
     */
    private function setUpL3View(): void
    {
        $this->feParams->listColumns = [
            'id'                => [
                'title' => 'UID',
                'display' => false
            ],
            'name'              => 'Name',
            'hostname'          => 'Hostname',
            'asn'               => 'ASN',
            'loopback_ip'       => 'Loopback',
            'mgmt_mac_address'  => 'Mgmt Mac',
            'active'            => [
                'title'    => 'Active',
                'type'     => self::$FE_COL_TYPES[ 'YES_NO' ]
            ]
        ];
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
        $feParams   = $this->feParams;
        $data       = $this->data;
        return Switcher::select( [
            'switch.*',
            'i.name AS infrastructure',
            'v.id AS vendorid', 'v.name AS vendor',
            'c.id AS cabinetid', 'c.name AS cabinet'
        ] )->leftJoin( 'infrastructure AS i', 'i.id', 'switch.infrastructure')
        ->leftJoin( 'cabinet AS c', 'c.id', 'switch.cabinetid')
        ->leftJoin( 'vendor AS v', 'v.id', 'switch.vendorid')
        ->when( $id , function( Builder $q, $id ) {
            return $q->where('switch.id', $id );
        } )->when( isset( $data[ 'params' ][ 'activeOnly' ] ) && $data[ 'params' ][ 'activeOnly' ] , function( Builder $q ) {
            return $q->where('switch.active', true );
        } )->when( $feParams->listOrderBy , function( Builder $q, $orderby ) use ( $feParams )  {
            return $q->orderBy( $orderby, $feParams->listOrderByDir ?? 'ASC');
        })->get()->toArray();
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
            'addBySnmp'         => request()->old( 'add_by_snnp', false ),
            'preAddForm'        => false,
            'cabinets'          => Location::with( 'cabinets' )
                ->has( 'cabinets' )->get()->toArray(),// getting the cabinets via the location to build the grouped options dropdown
            'infra'             => Infrastructure::orderBy( 'name' )->get(),
            'vendors'           => Vendor::orderBy( 'name' )->get(),
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

        if( $r->asn && Switcher::where( 'asn', $r->asn )->exists() ) {
            AlertContainer::push( "Note: this ASN is already is use by at least one other switch. If you are using eBGP, this may cause prefixes to be black-holed.", Alert::WARNING );
        }

        $r->merge( [ 'mgmt_mac_address' => preg_replace( "/[^a-f0-9]/i", '', strtolower( $r->mgmt_mac_address ) ) ] );

        $this->object = Switcher::create( $r->all() );
        $this->extraAttributes( $r );
        return true;
    }

    /**
     * Display the form to edit an object
     *
     * @param   int $id ID of the row to edit
     *
     * @return array
     */
    protected function editPrepareForm( int $id ): array
    {
        $this->object = Switcher::findOrFail( $id );

        Former::populate([
            'name'              => request()->old( 'name',                  $this->object->name ),
            'hostname'          => request()->old( 'hostname',              $this->object->hostname ),
            'infrastructure'    => request()->old( 'infrastructure',        $this->object->infrastructure ),
            'ipv4addr'          => request()->old( 'ipv4addr',              $this->object->ipv4addr ),
            'ipv6addr'          => request()->old( 'ipv6addr',              $this->object->ipv6addr ),
            'snmppasswd'        => request()->old( 'snmppasswd',            $this->object->snmppasswd ),
            'vendorid'          => request()->old( 'vendorid',              $this->object->vendorid ),
            'model'             => request()->old( 'model',                 $this->object->model ),
            'active'            => request()->old( 'active',                ( $this->object->active ? 1 : 0 ) ),
            'poll'              => request()->old( 'poll',                  ( $this->object->poll ? 1 : 0 ) ),
            'asn'               => request()->old( 'asn',                   $this->object->asn ),
            'loopback_ip'       => request()->old( 'loopback_ip',           $this->object->loopback_ip ),
            'loopback_name'     => request()->old( 'loopback_name',         $this->object->loopback_name ),
            'mgmt_mac_address'  => request()->old( 'mgmt_mac_address',      $this->object->mgmt_mac_address ) ,
            'notes'             => request()->old( 'notes',                 $this->object->notes ) ,
        ]);

        return [
            'object'            => $this->object,
            'addBySnmp'         => request()->old( 'add_by_snnp', false ),
            'preAddForm'        => false,
            'cabinets'          => Location::with( 'cabinets' )
                ->has( 'cabinets' )->get()->toArray(),// getting the cabinets via the location to build the grouped options dropdown
            'infra'             => Infrastructure::orderBy( 'name' )->get(),
            'vendors'           => Vendor::orderBy( 'name' )->get()
        ];
    }

    /**
     * Function to do the actual validation and editing of the submitted object.
     *
     * @param Request   $r
     * @param int       $id
     *
     * @return bool|RedirectResponse
     *
     * @throws
     */
    public function doUpdate( Request $r, int $id ): bool|RedirectResponse
    {
        $this->object = Switcher::findOrFail( $id );

        $this->checkForm( $r );

        if( $r->asn && Switcher::where('asn', $r->asn )->where( 'id', '!=', $this->object->id )->exists() ){
            AlertContainer::push( "Note: this ASN is already is use by at least one other switch. If you are using eBGP, this may cause prefixes to be black-holed.", Alert::WARNING );
        }

        $r->merge( [ 'mgmt_mac_address' => preg_replace( "/[^a-f0-9]/i", '', strtolower( $r->mgmt_mac_address ) ) ] );

        $this->object->update( $r->all() );
        $this->extraAttributes( $r );
        return true;
    }

    /**
     * Display the form to add by SNMP
     *
     * @return View
     */
    public function addBySnmp(): View
    {
        // wipe any preexisting cached switch platform entry:
        session()->remove( "snmp-platform" );
        $this->addEditSetup();
        return $this->display( 'add-by-smtp-form' );
    }

    /**
     * Process the hostname and SNMP community, poll the switch and set up the proper add/edit form
     *
     * @param  StoreBySmtpRequest  $r
     *
     * @return View
     */
    public function storeBySmtp( StoreBySmtpRequest $r ): View
    {
        $vendorid = null;

        // can we get it by SNMP and discover some basic details?
        try {
            $snmp   = new SNMP( $r->hostname, $r->snmppasswd );
            $vendor = $snmp->getPlatform()->getVendor();

            // Store the platform in session to be able to get back the information when we will create the object
            $r->session()->put( "snmp-platform", $snmp->getPlatform() );

            if( $v = Vendor::where('name', $vendor )->first() ) {
                $vendorid = $v->id;
            }
        } catch( SNMPException $e ) {
            $snmp = null;
        }

        $sp = strpos( $r->hostname, '.' );

        Former::populate([
            'name'              => substr( $r->hostname, 0, $sp ?: strlen( $r->hostname ) ),
            'snmppasswd'        => $r->snmppasswd,
            'hostname'          => $r->hostname,
            'ipv4addr'          => resolve_dns_a(    $r->hostname ) ?? '',
            'ipv6addr'          => resolve_dns_aaaa( $r->hostname ) ?? '',
            'vendorid'          => $vendorid ?? "",
            'model'             => $snmp ? $snmp->getPlatform()->getModel() : "",
        ]);

        $this->feParams->titleSingular = "Switch via SNMP";
        $this->addEditSetup();

        $this->data[ 'params' ]['isAdd']        = true;
        $this->data[ 'params' ]['addBySnmp']    = true;
        $this->data[ 'params' ]['preAddForm']   = false;
        $this->data[ 'params' ]['object']       = null;
        $this->data[ 'params' ]['cabinets']     = Location::with( 'cabinets' )
            ->has( 'cabinets' )->get()->toArray();
        $this->data[ 'params' ]['infra']        = Infrastructure::orderBy( 'name' )->get();
        $this->data[ 'params' ]['vendors']      = Vendor::orderBy( 'name' )->get();

        return $this->display( 'edit' );
    }

    /**
     * @inheritdoc
     */
    protected function preDelete() : bool
    {
        $okay = true;

        if( $this->object->getPhysicalInterfaces()->count() ) {
            $okay = false;
            AlertContainer::push( "Cannot delete switch: there are switch ports assigned to one or more physical interfaces.", Alert::DANGER );
        }

        if( $this->object->getPatchPanelPorts()->count() ) {
            $okay = false;
            AlertContainer::push( "Cannot delete switch: there are switch ports assigned to patch panel ports", Alert::DANGER );
        }

        if( $okay ){
            $this->object->switchPorts()->delete();
        }

        return $okay;
    }

    /**
     * Display the Port report for a switch
     *
     * @param Switcher $switch ID for the switch
     *
     * @return view
     */
    public function portReport( Switcher $switch ) : View
    {
        $allPorts   = SwitchPortAggregator::getAllPortsForSwitch( $switch->id, [] , [], false );

        $ports      = SwitchPort::select( [
                'sp.id AS id', 'sp.name AS name', 'sp.type AS porttype',
                'pi.speed AS speed', 'pi.duplex AS duplex',
                'c.name AS custname'
            ] )->from( 'switchport AS sp' )
            ->join( 'physicalinterface AS pi', 'pi.switchportid', 'sp.id' )
            ->join( 'virtualinterface AS vi', 'vi.id', 'pi.virtualinterfaceid' )
            ->join( 'cust AS c', 'c.id', 'vi.custid' )
            ->where( 'sp.switchid', $switch->id )
            ->orderBy( 'id' )
            ->get()->keyBy( 'id' )->toArray();

        $matchingValues = array_uintersect( $ports, $allPorts , static function ( $val1, $val2 ){
            return strcmp( $val1['name'], $val2['name'] );
        });

        $diffValues = array_udiff( $allPorts, $ports , static function ( $val1, $val2 ){
            return strcmp( $val1['name'], $val2['name'] );
        });

        return view( 'switches/port-report' )->with([
            'switches'                  => Switcher::orderBy( 'name' )
                ->get()->keyBy( 'id' ),
            's'                         => $switch,
            'ports'                     => array_merge( $matchingValues, $diffValues ),
        ]);
    }

    /**
     * Display the switch configurations
     *
     * @param Request $r
     *
     * @return view
     *
     * @throws
     */
    public function configuration( Request $r ) : View
    {
        $infra  = $location = $speed = $switch = $vlan = $summary = false;

        $speeds = PhysicalInterface::selectRaw( 'DISTINCT physicalinterface.speed AS speed' )
            ->orderBy( 'speed' )->get()->pluck( 'speed' )->toArray();

        $rate_limits = PhysicalInterface::selectRaw( 'DISTINCT physicalinterface.rate_limit AS rate_limit' )
            ->whereNotNull( 'rate_limit' )
            ->orderBy( 'rate_limit' )->get()->pluck( 'rate_limit' )->toArray();

        $speeds = array_merge( $speeds, $rate_limits );
        asort( $speeds, SORT_NUMERIC );
        $speeds = array_values($speeds);

        if( $r->switch !== null ) {
            if(  $switch = Switcher::find( $r->switch ) ) {
                $r->session()->put( "switch-configuration-switch", $switch );
            } else {
                $r->session()->remove( "switch-configuration-switch" );
                $switch = false;
            }
        } else if( $r->session()->exists( "switch-configuration-switch" ) ) {
            $switch = $r->session()->get( "switch-configuration-switch" );
        }

        if( $r->infra !== null ) {
            if(  $infra = Infrastructure::find( $r->infra ) ) {
                $r->session()->put( "switch-configuration-infra", $infra );
            } else {
                $r->session()->remove( "switch-configuration-infra" );
                $infra = false;
            }
        } else if( $r->session()->exists( "switch-configuration-infra" ) ) {
            $infra = $r->session()->get( "switch-configuration-infra" );
        }

        if( $r->location !== null ) {
            if( $location = Location::find( $r->location ) ) {
                $r->session()->put( "switch-configuration-location", $location );
            } else {
                $r->session()->remove( "switch-configuration-location" );
                $location = false;
            }
        } else if( $r->session()->exists( "switch-configuration-location" ) ) {
            $location = $r->session()->get( "switch-configuration-location" );
        }

        if( $r->speed !== null ) {
            $speed = (int)$r->speed;
            if( in_array( $r->speed, $speeds, false ) ) {
                $r->session()->put( "switch-configuration-speed", $r->speed );
            } else {
                $r->session()->remove( "switch-configuration-speed" );
                $speed = false;
            }
        } else if( $r->session()->exists( "switch-configuration-speed" ) ) {
            $speed = $r->session()->get( "switch-configuration-speed" );
        }

        if( $r->vlan !== null ) {
            if( $vlan = Vlan::find( $r->vlan ) ) {
                $r->session()->put( "switch-configuration-vlan", $vlan );
            } else {
                $r->session()->remove( "switch-configuration-vlan" );
                $vlan = false;
            }
        } else if( $r->session()->exists( "switch-configuration-vlan" ) ) {
            $vlan = $r->session()->get( "switch-configuration-vlan" );
        }

        if( $switch || $infra || $location || $vlan ) {
            $summary = "Connections details for: ";

            if( $switch ) {
                $summary .= $switch->name . " (on " . $switch->infrastructureModel->name. " at " . $switch->cabinet->location->name . ")";
            } else {
                if( $infra ){
                    $summary .= $infra->name . ' (infrastructure); ';
                }
                if( $location ){
                    $summary .= $location->name . ' (facility); ';
                }
                if( $vlan ) {
                    $summary .= $vlan->name . ' (VLAN); ';
                }
                if( $speed ) {
                    $summary .= PhysicalInterface::$SPEED[ $speed ] . '; ';
                }
            }
        }

        $config = SwitcherAggregator::getConfiguration(
            $switch->id ?? null,
            $infra->id ?? null,
            $location->id ?? null,
            $speed,
            $vlan->id ?? null,
            (bool) $r->input('rs-client'),
            (bool) $r->input('ipv6-enabled')
        );

        return view( 'switches/configuration' )->with([
            's'                         => $switch,
            'speed'                     => $speed,
            'infra'                     => $infra,
            'vlan'                      => $vlan,
            'location'                  => $location,
            'summary'                   => $summary,
            'speeds'                    => $speeds,
            'infras'                    => $switch ? [ Infrastructure::find( $switch->infrastructure ) ] : Infrastructure::orderBy( 'name' )->get(),
            'vlans'                     => Vlan::orderBy( 'name' )->get(),
            'locations'                 => $switch ? [ Location::find( $switch->cabinet->locationid ) ] : Location::orderBy( 'name' )->get(),
            'switches'                  => SwitcherAggregator::getByLocationInfrastructureSpeed( $infra->id ?? null, $location->id ?? null, $speed ?: null ),
            'config'                    => $config,
        ]);
    }

    /**
     * Check if the form is valid
     *
     * @param Request $r
     */
    public function checkForm( Request $r ): void
    {
        $r->validate( [
            'name'              => 'required|string|max:255|unique:switch,name' . ( $r->id ? ',' . $r->id : ''  ),
            'hostname'          => [ 'required', 'string', 'max:255', new IdnValidate(), 'unique:switch,hostname' . ( $r->id ? ',' . $r->id : ''  ) ],
            'cabinetid'         => 'required|integer|exists:cabinet,id',
            'infrastructure'    => 'required|integer|exists:infrastructure,id',
            'snmppasswd'        => 'nullable|string|max:255',
            'vendorid'          => 'required|integer|exists:vendor,id',
            'ipv4addr'          => 'nullable|ipv4',
            'ipv6addr'          => 'nullable|ipv6',
            'model'             => 'nullable|string|max:255',
            'asn'               => 'nullable|integer|min:1',
            'loopback_ip'       => 'nullable|string|max:255|unique:switch,loopback_ip' . ( $r->id ? ',' . $r->id : ''  ),
            'loopback_name'     => 'nullable|string|max:255',
            'mgmt_mac_address'  => 'nullable|string|max:17|regex:/^[a-f0-9:\.\-]{12,17}$/i',
        ] );
    }

    /**
     * Add some extra attributes to the object
     *
     * @param Request $r
     *
     * @return void
     *
     * @throws
     */
    private function extraAttributes( Request $r ): void
    {
        if( $r->session()->exists( "snmp-platform" ) ) {
            /** @var Platform $platform */
            $platform = $r->session()->get( "snmp-platform" );
            $osDate = null;

            if( $platform->getOsDate() instanceof DateTime ) {
                $osDate = $platform->getOsDate();
            } else if( is_string( $platform->getOsDate() ) ) {
                $osDate = new DateTime( $platform->getOsDate() );
            }

            $this->object->os =             $platform->getOs();
            $this->object->osDate =         $osDate;
            $this->object->osVersion =      $platform->getOsVersion();
            $this->object->serialNumber =   $platform->getSerialNumber() ?? '(not implemented)';
            $this->object->save();
            $r->session()->remove( "snmp-platform" );
        }

        if( $r->add_by_snnp ) {
            $this->object->lastPolled =   now();
            $this->object->save();
        }
    }
}