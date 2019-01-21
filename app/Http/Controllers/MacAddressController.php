<?php

namespace IXP\Http\Controllers;

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

use D2EM;

use Entities\{
    MACAddress      as MACAddressEntity
};



/**
 * Mac address Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class MacAddressController extends Doctrine2Frontend {

    /**
     * The object being added / edited
     * @var MACAddressEntity
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

        $this->feParams         = (object)[

            'entity'            => MACAddressEntity::class,
            'pagetitle'         => 'Discovered MAC Addresses',

            'titleSingular'     => 'MAC Address',
            'nameSingular'      => 'a MAC address',

            'listOrderBy'       => 'customer',
            'listOrderByDir'    => 'ASC',

            'viewFolderName'    => 'mac-address',

            'readonly'          => self::$read_only,

            'documentation'     => 'https://docs.ixpmanager.org/features/layer2-addresses/',

            'listColumns'       => [

                'id'             => [ 'title' => 'DB ID', 'display' => false ],
                'customer'       => 'Customer',
                'switchport'     => 'Interface(s)',
                'ip4'            => 'IPv4',
                'ip6'            => 'IPv6',
                'mac'            => 'MAC Address',
                'organisation'   => 'Manufacturer'
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
}
