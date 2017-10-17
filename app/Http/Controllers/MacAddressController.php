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

use D2EM;

use Entities\{
    MACAddress      as MACAddressEntity
};
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;




/**
 * Mac address Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class MacAddressController extends Doctrine2Frontend {

    /**
     * The object being added / edited
     * @var MACAddressEntity
     */
    protected $object = null;

    /**
     * This function sets up the frontend controller
     */
    public function feInit(){

        $this->data[ 'feParams' ] =  $this->feParams = (object)[

            'entity'            => MACAddressEntity::class,
            'pagetitle'         => 'Discovered MAC Addresses',

            'titleSingular'     => 'MAC Address',
            'nameSingular'      => 'a MAC address',

            'defaultAction'     => 'list',
            'defaultController' => 'MacAddressController',

            'listOrderBy'       => 'customer',
            'listOrderByDir'    => 'ASC',

            'viewFolderName'    => 'mac-address',

            'readonly'      => true,

            'listColumns'       => [
                'id'        => [ 'title' => 'DB ID', 'display' => false ],

                'customer'  => [
                    'title'      => 'Customer',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'customer',
                    'action'     => 'view',
                    'idField'    => 'customerid'
                ],

                'interface'  => [
                    'title'      => 'Interface',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'virtual-interface',
                    'action'     => 'edit',
                    'idField'    => 'interfaceid'
                ],

                'ipv4'           => 'IPv4',
                'ipv6'           => 'IPv6',
                'mac'            => [
                        'title'         => 'Mac Address',
                        'type'          => self::$FE_COL_TYPES[ 'SCRIPT' ],
                        'script'        => 'mac-address/list-mac-format.foil.php'
                    ],

                'manufacturer'   => 'Manufacturer'
            ],
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = $this->feParams->listColumns;


    }


    /**
     * Provide array of rows for the list action and view action
     *
     * @param int $id The `id` of the row to load for `view` action`. `null` if `listAction`
     * @return array
     */
    protected function listGetData( $id = null ) {
        return D2EM::getRepository( MACAddressEntity::class )->getAllForFeList( $this->feParams, $id );
    }

    /**
     * Display the form to add/edit an object
     * @param   int $id ID of the row to edit
     * @return array
     */
    protected function addEditPrepareForm( $id = null ): array { }


    /**
     * Function to do the actual validation and storing of the submitted object.
     * @param Request $request
     * @return bool|RedirectResponse
     */
    public function doStore( Request $request ) {}

    protected function preList()
    {
        $this->view[ 'script' ]     = $this->resolveTemplate( 'layer2-address/js/clipboard' );
        return true;
    }

}
