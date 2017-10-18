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

use D2EM, Route;

use Illuminate\View\View;

use Entities\{
    VlanInterface as VlanInterfaceEntity,
    Layer2Address as Layer2AddressEntity
};


/**
 * Layer2Address Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   VlanInterface
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Layer2AddressController extends Doctrine2Frontend {

    /**
     * The object being added / edited
     * @var Layer2AddressEntity
     */
    protected $object = null;

    /**
     * Is this a read only controller?
     *
     * @var boolean
     */
    public static $read_only = true;

    /**
     * This function sets up the frontend controller
     */
    public function feInit(){

        $this->data[ 'feParams' ] =  $this->feParams = (object)[

            'entity'            => Layer2AddressEntity::class,
            'pagetitle'         => 'Layer2 Addresses',

            'titleSingular'     => 'Layer2 Addresses',
            'nameSingular'      => 'a layer2 Addresses',

            'defaultAction'     => 'list',
            'defaultController' => 'Layer2AddressController',

            'listOrderBy'       => 'customer',
            'listOrderByDir'    => 'ASC',

            'viewFolderName'    => 'layer2-address',

            'readonly'          => self::$read_only,

            'listColumns'       => [

                'id'                => [ 'title' => 'DB ID', 'display' => false ],
                'customer'          => 'Customer',
                'switchport'        => 'Interface(s)',
                'vlan'              => 'VLAN',
                'ip4'               => 'IPv4',
                'ip6'               => 'IPv6',
                'mac'               => 'MAC Address',
                'organisation'      => 'Manufacturer'
            ],
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = $this->feParams->listColumns;


    }

    /**
     * Additional routes
     *
     *
     * @param string $route_prefix
     * @return void
     */
    protected static function additionalRoutes( string $route_prefix )
    {
        Route::group( [ 'prefix' => $route_prefix ], function() {
            Route::get(     'vlan-interface/{vliid}',   'Layer2AddressController@forVlanInterface' );
            Route::post(    'delete/{id}',              'Layer2AddressController@delete' );
        });
    }

    /**
     * Provide array of rows for the list action and view action
     *
     * @param int $id The `id` of the row to load for `view` action`. `null` if `listAction`
     * @return array
     */
    protected function listGetData( $id = null ) {
        return D2EM::getRepository( Layer2AddressEntity::class )->getAllForFeList( $this->feParams, $id );
    }

    /**
     * Display all the layer2addresses for a VlanInterface
     *
     * @param  int $vliid ID if the VlanInterface
     * @return  View
     */
    public function forVlanInterface( int $vliid ): View {
        if( !( $vli = D2EM::getRepository( VlanInterfaceEntity::class )->find( $vliid ) ) ) {
            return abort( '404' );
        }

        return view( 'layer2-address/vlan-interface' )->with([
            'vli'       => $vli
        ]);
    }

}
