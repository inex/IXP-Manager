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

use D2EM, Former;

use Illuminate\View\View;

use Entities\{
    CustomerEquipment   as CustomerEquipmentEntity,
    Cabinet             as CabinetEntity,
    Customer            as CustomerEntity
};

/**
 * CustKit Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   VlanInterface
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CustKitController extends Doctrine2Frontend {



    /**
     * This function sets up the frontend controller
     */
    public function feInit(){
        //$this->assertPrivilege( \Entities\User::AUTH_SUPERUSER );

        $this->data[ 'feParams' ] =  $this->feParams = (object)[
            'entity'        => '\\Entities\\CustomerEquipment',

            'pagetitle'     => 'Customer Equipment',

            'titleSingular' => 'Customer Equipment',
            'nameSingular'  => 'customer equipment',

            'defaultAction' => 'list',                    // OPTIONAL; defaults to 'list'

            'listOrderBy'    => 'name',
            'listOrderByDir' => 'ASC',

            'viewFolderName' => 'cust-kit',

            'listColumns'    => [

                'id'        => [ 'title' => 'UID', 'display' => false ],

                'customer'  => [
                    'title'      => 'Customer',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'customer',
                    'action'     => 'overview',
                    'idField'    => 'custid'
                ],

                'cabinet'  => [
                    'title'      => 'Cabinet',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'cabinet',
                    'action'     => 'view',
                    'idField'    => 'cabinetid'
                ],

                'name'      => 'Name'
            ]
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = array_merge(
            $this->feParams->listColumns,
            [
                'descr'      => 'Description'
            ]
        );


    }

    /**
     * Display the form to edit a physical interface
     *
     * @param   int $id ID of the customer equipment
     *
     * @return View
     */
    public function editAction( int $id = null ) {
        /** @var CustomerEquipmentEntity $custKit */
        $custKit = false;

        if( $id != null ) {
            if( !( $custKit = D2EM::getRepository( CustomerEquipmentEntity::class )->find( $id) ) ) {
                abort(404);
            }

            Former::populate([
                'name'                  => $custKit->getName(),
                'cust'                  => $custKit->getCustomer()->getId(),
                'cabinet'               => $custKit->getCabinet()->getId(),
                'description'           => $custKit->getDescription(),
            ]);
        }

        return view( $this->feParams->viewFolderName.'/edit' )->with([
            'data'                              => $this->data,
            'custKit'                           => $custKit,
            'cabinets'                          => D2EM::getRepository( CabinetEntity::class )->getAsArray(),
            'custs'                             => D2EM::getRepository( CustomerEntity::class )->getAsArray(),
        ]);
    }

    /**
     * Provide array of users for the listAction and viewAction
     *
     * @param int $id The `id` of the row to load for `viewAction`. `null` if `listAction`
     * @return array
     */
    protected function listGetData( $id = null ) {
        return D2EM::getRepository( CustomerEquipmentEntity::class)->getAll( $id, $this->feParams );
    }


}
