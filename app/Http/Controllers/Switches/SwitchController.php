<?php

namespace IXP\Http\Controllers\Switches;

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

use Auth, D2EM, Former, Redirect,Route, Validator;

use Entities\{
    Cabinet             as CabinetEntity,
    Infrastructure      as InfrastructureEntity,
    Location            as LocationEntity,
    Switcher            as SwitcherEntity,
    SwitchPort          as SwitchPortEntity,
    User                as UserEntity,
    Vendor              as VendorEntity
};

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use IXP\Http\Controllers\Doctrine2Frontend;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use Illuminate\View\View;

use OSS_SNMP\{
    Exception as SNMPException,
    Platform,
    SNMP
};


/**
 * Switch Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009-2018 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SwitchController extends Doctrine2Frontend {

    /**
     * The object being added / edited
     * @var SwitcherEntity
     */
    protected $object = null;


    /**
     * This function sets up the frontend controller
     */
    public function feInit(){

        $this->feParams         = (object)[

            'entity'            => SwitcherEntity::class,
            'pagetitle'         => 'Switches',

            'titleSingular'     => 'Switch',
            'nameSingular'      => 'a switch',

            'listOrderBy'       => 'name',
            'listOrderByDir'    => 'ASC',

            'viewFolderName'    => 'switches',

            'listColumns'       => [
                'id'        => [ 'title' => 'UID', 'display' => false ],
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
                ]
            ]
        );

        // phpunit / artisan trips up here without the cli test:
        if( php_sapi_name() !== 'cli' ) {

            // custom access controls:
            switch( Auth::check() ? Auth::user()->getPrivs() : UserEntity::AUTH_PUBLIC ) {
                case UserEntity::AUTH_SUPERUSER:
                    break;

                case UserEntity::AUTH_CUSTUSER:
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
     *
     * @param string $route_prefix
     * @return void
     */
    protected static function additionalRoutes( string $route_prefix ){
        // NB: this route is marked as 'read-only' to disable normal CRUD operations. It's not really read-only.

        Route::group( [  'prefix' => $route_prefix ], function() use ( $route_prefix ) {

            Route::get(  'add-by-snmp',         'Switches\SwitchController@addBySnmp'           )->name( "switch@add-by-snmp" );
            Route::get(  'port-report/{id}',    'Switches\SwitchController@portReport'          )->name( "switch@port-report" );
            Route::get(  'configuration',       'Switches\SwitchController@configuration'       )->name( "switch@configuration" );

            Route::post(  'store-by-snmp',      'Switches\SwitchController@storeBySmtp'         )->name( "switch@store-by-snmp" );
        });
    }

    public function list( Request $r  ) : View{

        if( ( $showActiveOnly = $r->input( 'active-only' ) ) !== null ) {
            $r->session()->put( "switch-list-active-only", $showActiveOnly );
        } else if( $r->session()->exists( "switch-list-active-only" ) ) {
            $showActiveOnly = $r->session()->get( "switch-list-active-only" );
        } else {
            $showActiveOnly = false;
        }

        if( ( $vtype = $r->input( 'vtype' ) ) !== null ) {
            $r->session()->put( "switch-list-vtype", $vtype );
        } else if( $r->session()->exists( "switch-list-vtype" ) ) {
            $vtype = $r->session()->get( "switch-list-vtype" );
        } else {
            $r->session()->remove( "switch-list-vtype" );
            $vtype = "Default";
        }



        if( $r->input( 'infra' )  !== null ) {
            /** @var SwitcherEntity $s */
            if(  $infra = D2EM::getRepository( InfrastructureEntity::class )->find( $r->input( 'infra' ) ) ) {
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


        if( $vtype == "OS View" ){
            $this->setUpOsView();
        } else if( $vtype == "L3 View" ){
            $this->setUpL3View();
        }

        $this->data[ 'params' ][ 'activeOnly' ]         = $showActiveOnly;
        $this->data[ 'params' ][ 'vtype' ]              = $vtype;
        $this->data[ 'params' ][ 'infra' ]              = $infra;

        $this->data[ 'rows' ] = $this->listGetData();

        $this->data[ 'view' ][ 'listEmptyMessage']      = $this->resolveTemplate( 'list-empty-message',     false );
        $this->data[ 'view' ][ 'listHeadOverride']      = $this->resolveTemplate( 'list-head-override',     false );
        $this->data[ 'view' ][ 'listRowOverride']       = $this->resolveTemplate( 'list-row-override',      false );
        $this->data[ 'view' ][ 'listPreamble']          = $this->resolveTemplate( 'list-preamble',          false );
        $this->data[ 'view' ][ 'listPostamble']         = $this->resolveTemplate( 'list-postamble',         false );
        $this->data[ 'view' ][ 'listRowMenu']           = $this->resolveTemplate( 'list-row-menu',          false );
        $this->data[ 'view' ][ 'pageHeaderPreamble']    = $this->resolveTemplate( 'page-header-preamble',   false );
        $this->data[ 'view' ][ 'listScript' ]           = $this->resolveTemplate( 'js/list' );

        $this->preList();

        return $this->display( 'list' );
    }


    /**
     * Set Up the the table to display the OS VIEW
     *
     * @return bool
     */
    private function setUpOsView( ){

        $this->feParams->listColumns = [
            'id'        => [ 'title' => 'UID', 'display' => false ],
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

        return true;
    }


    /**
     * Set Up the the table to display the OS VIEW
     *
     * @return bool
     */
    private function setUpL3View( ){

        $this->feParams->listColumns = [
            'id'                => [ 'title' => 'UID', 'display' => false ],
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

        return true;
    }

    /**
     * Provide array of rows for the list action and view action
     *
     * @param int $id The `id` of the row to load for `view` action`. `null` if `listAction`
     *
     * @return array
     */
    protected function listGetData( $id = null ) {
        return D2EM::getRepository( SwitcherEntity::class )->getAllForFeList( $this->feParams, $id, $this->data );
    }



    /**
     * Display the form to add/edit an object
     *
     * @param   int $id ID of the row to edit
     *
     * @return array
     */
    protected function addEditPrepareForm( $id = null ): array {

        $old = request()->old();

        if( $id !== null ) {

            if( !( $this->object = D2EM::getRepository( SwitcherEntity::class )->find( $id) ) ) {
                abort(404);
            }

            Former::populate([
                'name'              => array_key_exists( 'name',      $old         ) ? $old['name']               :  $this->object->getName(),
                'hostname'          => array_key_exists( 'hostname', $old          ) ? $old['hostname']           :  $this->object->getHostname(),
                'cabinetid'         => array_key_exists( 'cabinetid', $old         ) ? $old['cabinetid']          :  $this->object->getCabinet()         ? $this->object->getCabinet()->getId()          : null,
                'infrastructure'    => array_key_exists( 'infrastructure', $old    ) ? $old['infrastructure']     :  $this->object->getInfrastructure()  ? $this->object->getInfrastructure()->getId()   : null,
                'ipv4addr'          => array_key_exists( 'ipv4addr', $old          ) ? $old['ipv4addr']           :  $this->object->getIpv4addr(),
                'ipv6addr'          => array_key_exists( 'ipv6addr', $old          ) ? $old['ipv6addr']           :  $this->object->getIpv6addr(),
                'snmppasswd'        => array_key_exists( 'snmppasswd', $old        ) ? $old['snmppasswd']         :  $this->object->getSnmppasswd(),
                'vendorid'          => array_key_exists( 'vendorid', $old          ) ? $old['vendorid']           :  $this->object->getVendor() ? $this->object->getVendor()->getId() : null,
                'model'             => array_key_exists( 'model', $old             ) ? $old['model']              :  $this->object->getModel(),
                'active'            => array_key_exists( 'active', $old            ) ? ( $old['active'] ? 1 : 0 ) :  ( $this->object->getActive() ? 1 : 0 ),
                'asn'               => array_key_exists( 'asn', $old               ) ? $old['notes']              :  $this->object->getAsn(),
                'loopback_ip'       => array_key_exists( 'loopback_ip', $old       ) ? $old['loopback_ip']        :  $this->object->getLoopbackIP(),
                'loopback_name'     => array_key_exists( 'loopback_name', $old     ) ? $old['loopback_name']      :  $this->object->getLoopbackName(),
                'mgmt_mac_address'  => array_key_exists( 'mgmt_mac_address', $old  ) ? $old['mgmt_mac_address']   :  $this->object->getMgmtMacAddress(),
            ]);
        }


        return [
            'object'            => $this->object,
            'addBySnmp'         => array_key_exists( 'add_by_snnp', $old  ) ? $old['add_by_snnp']  : false,
            'preAddForm'        => false,
            'cabinets'          => D2EM::getRepository( CabinetEntity::class            )->getAsArray(),
            'infra'             => D2EM::getRepository( InfrastructureEntity::class     )->getAllAsArray(),
            'vendors'           => D2EM::getRepository( VendorEntity::class             )->getAsArray(),
            'notes'             => $id ? ( array_key_exists( 'notes',           $old ) ? $old['notes']           : $this->object->getNotes() ) : ( array_key_exists( 'notes', $old ) ? $old['notes'] : "" )
        ];
    }

    /**
     * Display the form to add by SNMP
     *
     * @return View
     */
    public function addBySnmp(): View {
        // wipe any preexisting cached switch platform entry:
        session()->remove( "snmp-platform" );

        $this->addEditSetup();
        return $this->display( 'add-by-smtp-form' );
    }

    /**
     * Process the hostname and SNMP community, poll the switch and set up the proper add/edit form
     *
     * @param Request $request
     * @return bool|RedirectResponse|View
     *
     * @throws
     */
    public function storeBySmtp( Request $request ) {

        $validator = Validator::make( $request->all(), [
            'snmppasswd' => 'required|string|max:255',
            'hostname'   => 'required|string|max:255|unique:Entities\Switcher,hostname' . ( $request->input('id') ? ','. $request->input('id') : '' ),
        ] );

        if( $validator->fails() ) {
            return Redirect::back()->withErrors( $validator )->withInput();
        }

        $vendorid = null;

        // can we get it by SNMP and discover some basic details?
        try {
            $snmp   = new SNMP( $request->input( 'hostname' ), $request->input( 'snmppasswd' ) );
            $vendor = $snmp->getPlatform()->getVendor();

            // Store the platform in session to be able to get back the information when we will create the object
            $request->session()->put( "snmp-platform", $snmp->getPlatform() );

            /** @var VendorEntity $vendorFound */
            if( $v = D2EM::getRepository( VendorEntity::class )->findOneBy( [ "name" => $vendor ] ) ) {
                $vendorid = $v->getId();
            }
        } catch( SNMPException $e ) {
            $snmp = null;
        }

        $sp = strpos( $request->input( 'hostname' ), '.' );

        Former::populate([
            'name'              => substr( $request->input( 'hostname' ), 0, $sp ? $sp : strlen( $request->input( 'hostname' ) ) ),
            'snmppasswd'        => $request->input( 'snmppasswd' ),
            'hostname'          => $request->input( 'hostname' ),
            'ipv4addr'          => resolve_dns_a(    $request->input( 'hostname' ) ) ?? '',
            'ipv6addr'          => resolve_dns_aaaa( $request->input( 'hostname' ) ) ?? '',
            'vendorid'          => $vendorid ?? "",
            'model'             => $snmp ? $snmp->getPlatform()->getModel() : "",
        ]);

        $this->feParams->titleSingular = "Switch via SNMP";
        $this->addEditSetup();

        $this->data[ 'params' ]['isAdd']        = true;
        $this->data[ 'params' ]['addBySnmp']    = true;
        $this->data[ 'params' ]['preAddForm']   = false;
        $this->data[ 'params' ]['object']       = null;
        $this->data[ 'params' ]['cabinets']     = D2EM::getRepository( CabinetEntity::class            )->getAsArray();
        $this->data[ 'params' ]['infra']        = D2EM::getRepository( InfrastructureEntity::class     )->getAllAsArray();
        $this->data[ 'params' ]['vendors']      = D2EM::getRepository( VendorEntity::class             )->getAsArray();


        return $this->display( 'edit' );
    }



    /**
     * Function to do the actual validation and storing of the submitted object.
     *
     * @param Request $request
     * @return bool|RedirectResponse
     *
     * @throws
     */
    public function doStore( Request $request ) {

        $validator = Validator::make( $request->all(), [
            'name'                      => 'required|string|max:255|unique:Entities\Switcher,name'      . ( $request->input('id') ? ','. $request->input('id') : '' ),
            'hostname'                  => 'required|string|max:255|unique:Entities\Switcher,hostname'  . ( $request->input('id') ? ','. $request->input('id') : '' ),
            'cabinetid'                 => 'required|integer|exists:Entities\Cabinet,id',
            'infrastructure'            => 'required|integer|exists:Entities\Infrastructure,id',
            'snmppasswd'                => 'nullable|string|max:255',
            'vendorid'                  => 'required|integer|exists:Entities\Vendor,id',
            'ipv4addr'                  => 'nullable|ipv4',
            'ipv6addr'                  => 'nullable|ipv6',
            'model'                     => 'nullable|string|max:255',
            'asn'                       => 'nullable|integer|min:1',
            'loopback_ip'               => 'nullable|string|max:255|unique:Entities\Switcher,loopback_ip' . ( $request->input('id') ? ','. $request->input('id') : '' ),
            'loopback_name'             => 'nullable|string|max:255',
            'mgmt_mac_address'          => 'nullable|string|max:17|regex:/^[a-f0-9:\.\-]{12,17}$/i',
        ] );

        if( $validator->fails() ) {
            return Redirect::to( route( "switch@add") )->withErrors( $validator )->withInput();
        }

        if( $request->input( 'id', false ) ) {
            if( !( $this->object = D2EM::getRepository( SwitcherEntity::class )->find( $request->input( 'id' ) ) ) ) {
                abort(404, "Unknown switch");
            }
        } else {
            $this->object = new SwitcherEntity;
            D2EM::persist( $this->object );
        }


        if( $request->input( 'asn' ) ){
            if( $s = D2EM::getRepository( SwitcherEntity::class )->findBy( ['asn' => $request->input( 'asn' ) ] ) ){
                $id = $this->object->getId();
                $asnExist = array_filter( $s , function ( $e ) use( $id ) {
                    /** @var $e SwitcherEntity */
                    return $e->getId() != $id;
                });

                if( count( $asnExist ) ){
                    AlertContainer::push( "WARNING: you have supplied a AS number that is already is use by at least one other switch. If you are using eBGP, this will be a problem.", Alert::WARNING );
                }
            }
        }

        $this->object->setName(           $request->input( 'name'               ) );
        $this->object->setHostname(       $request->input( 'hostname'           ) );
        $this->object->setIpv4addr(       $request->input( 'ipv4addr'           ) );
        $this->object->setIpv6addr(       $request->input( 'ipv6addr'           ) );
        $this->object->setModel(          $request->input( 'model'              ) );
        $this->object->setSnmppasswd(     $request->input( 'snmppasswd'         ) );

        $this->object->setNotes(          $request->input( 'notes'              ) );
        $this->object->setAsn(            $request->input( 'asn'                ) );
        $this->object->setLoopbackIP(     $request->input( 'loopback_ip'        ) );
        $this->object->setLoopbackName(   $request->input( 'loopback_name'      ) );
        $this->object->setMgmtMacAddress( preg_replace( "/[^a-f0-9]/i", '', strtolower( $request->input( 'mgmt_mac_address', '' ) ) ) );

        $this->object->setActive(  $request->input( 'active'             ) ?? false );

        $this->object->setCabinet(        D2EM::getRepository( CabinetEntity::class         )->find( $request->input( 'cabinetid'       ) ) );
        $this->object->setInfrastructure( D2EM::getRepository( InfrastructureEntity::class  )->find( $request->input( 'infrastructure'  ) ) );
        $this->object->setVendor(         D2EM::getRepository( VendorEntity::class          )->find( $request->input( 'vendorid'        ) ) );


        if( $request->session()->exists( "snmp-platform" ) ) {
            /** @var Platform $platform */
            $platform = $request->session()->get( "snmp-platform" );

            $this->object->setOs(           $platform->getOs() );
            $this->object->setOsDate(       new \DateTime( $platform->getOsDate() ) );
            $this->object->setOsVersion(    $platform->getOsVersion() );
            $this->object->setSerialNumber( $platform->getSerialNumber() );
            $request->session()->remove( "snmp-platform" );
        }

        if( $request->input( "add_by_snnp" ) ){
            $this->object->setLastPolled(   new \DateTime );
        }

        D2EM::flush();

        return true;
    }


    /**
     * Overriding optional method to clear cached entries:
     *
     * @param string $action Either 'add', 'edit', 'delete'
     * @return bool
     */
    protected function postFlush( string $action ): bool{
        // wipe cached entries
        // this is created in Repositories\Switcher::getAndCache()
        D2EM::getRepository( SwitcherEntity::class )->clearCacheAll();
        return true;
    }



    /**
     * @inheritdoc
     */
    protected function preDelete() : bool {
        $okay = $okayPPP = true;

        foreach( $this->object->getPorts() as $port ) {
            /** @var SwitchPortEntity $port */
            if( $port->getPhysicalInterface() ) {
                $okay = false;
                AlertContainer::push( "You cannot delete this switch there are switch(es) port assigned to a physical interface for a customer.", Alert::DANGER );
                break;
            }
        }

        if( $this->object->getConsoleServerConnections() && ( $cntCsc = count( $this->object->getConsoleServerConnections() ) ) ) {
            AlertContainer::push( "You cannot delete this switch there are {$cntCsc} console port connection exists for this switch", Alert::DANGER );
            $okay = false;
        }

        foreach( $this->object->getPorts() as $port ) {
            /** @var SwitchPortEntity $port */
            if( $port->getPatchPanelPort() ) {
                $okay = false;
                AlertContainer::push( "You cannot delete this switch there are switch(es) port assigned to patch panel ports", Alert::DANGER );
                break;
            }
        }

        if( $okay ){
            // if we got here, all switch ports are free
            foreach( $this->object->getPorts() as $p ){
                D2EM::remove( $p );
            }
        }

        return $okay;
    }


    /**
     * Display the Port report for a switch
     *
     * @param int $id ID for the switch
     *
     * @return view
     */
    function portReport( int $id = null ) : View {
        $s = false;

        if( $id && !( $s = D2EM::getRepository( SwitcherEntity::class )->find( $id ) ) ) {
            abort(404, "Unknown switch.");
        }

        $allPorts   = D2EM::getRepository( SwitcherEntity::class )->getAllPorts( $s->getId(), [] , [], false );
        $ports      = D2EM::getRepository( SwitcherEntity::class )->getAllPortsAssignedToPI( $s->getId() );

        foreach( $allPorts as $id => $port ) {
            if( isset( $ports[0] ) && $ports[0][ 'name' ] == $port[ 'name' ] ){
                $allPorts[ $port[ 'name' ] ] = array_shift( $ports );
            }
            else{
                $allPorts[ $port[ 'name' ] ] = $port;
            }

            $allPorts[ $port[ 'name' ] ]['porttype'] = SwitchPortEntity::$TYPES[ $allPorts[ $port[ 'name' ] ]['porttype'] ];

            unset( $allPorts[ $id ] );
        }


        return view( 'switches/port-report' )->with([
            'switches'                  => D2EM::getRepository( SwitcherEntity::class )->getNames(),
            's'                         => $s,
            'ports'                     => $allPorts,

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
    public function configuration( Request $r ) : View {

        if( $r->input( 'switch' )  !== null ) {
            /** @var SwitcherEntity $s */
            if(  $s = D2EM::getRepository( SwitcherEntity::class )->find( $r->input( 'switch' ) ) ) {

                $r->session()->put( "switch-configuration-switch", $s );
            } else {
                $r->session()->remove( "switch-configuration-switch" );
                $s = false;
            }
        } else if( $r->session()->exists( "switch-configuration-switch" ) ) {
            $s = $r->session()->get( "switch-configuration-switch" );
        } else {
            $s = false;
        }

        if( $r->input( 'infra' )  !== null ) {
            /** @var InfrastructureEntity $infra */
            if(  $infra = D2EM::getRepository( InfrastructureEntity::class )->find( $r->input( 'infra' ) ) ) {
                $r->session()->put( "switch-configuration-infra", $infra );
            } else {
                $r->session()->remove( "switch-configuration-infra" );
                $infra = false;
            }
        } else if( $r->session()->exists( "switch-configuration-infra" ) ) {
            $infra = $r->session()->get( "switch-configuration-infra" );
        } else {
            $infra = false;
        }


        if( $r->input( 'location' )  !== null ) {
            /** @var LocationEntity $facility */
            if(  $location = D2EM::getRepository( LocationEntity::class )->find( $r->input( 'location' ) ) ) {
                $r->session()->put( "switch-configuration-location", $location );
            } else {
                $r->session()->remove( "switch-configuration-location" );
                $location = false;
            }
        } else if( $r->session()->exists( "switch-configuration-location" ) ) {
            $location = $r->session()->get( "switch-configuration-location" );
        } else {
            $location = false;
        }



        if( $s || $infra || $location ){
            $summary = ":: Connections details for ";

            if( $s ){
                $summary .= $s->getName() . " (on " . $s->getInfrastructure()->getName() . " at " . $s->getCabinet()->getLocation()->getName() . ")";
            } elseif( $infra && $location ){
                $summary .= $infra->getName() . " at " . $location->getName();
            } elseif( $infra ){
                $summary .= $infra->getName();
            } elseif( $location ){
                $summary .= $location->getName();
            }

        } else{
            $summary = false;
        }


        return view( 'switches/configuration' )->with([
            's'                         => $s,
            'infra'                     => $infra,
            'location'                  => $location,
            'summary'                   => $summary,
            'infras'                    => $s ? [ $s->getInfrastructure()->getId()          => $s->getInfrastructure()->getName()           ] : D2EM::getRepository( InfrastructureEntity::class     )->getNames( true ),
            'locations'                 => $s ? [ $s->getCabinet()->getLocation()->getId()  => $s->getCabinet()->getLocation()->getName()   ] : D2EM::getRepository( LocationEntity::class           )->getNames(),
            'switches'                  => D2EM::getRepository( SwitcherEntity::class           )->getByLocationAndInfrastructure( $infra, $location ),
            'config'                    => D2EM::getRepository( SwitcherEntity::class           )->getConfiguration(  $s ? $s->getId() : null , null, null, Auth::getUser()->isSuperUser() , $infra ? $infra->getId() : null, $location ? $location->getId() : null  )
        ]);
    }

}
