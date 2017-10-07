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

use D2EM, Former, Redirect, Validator;

use Illuminate\View\View;

use Entities\{
    CustomerEquipment   as CustomerEquipmentEntity,
    Cabinet             as CabinetEntity,
    Customer            as CustomerEntity
};
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
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

        $this->data[ 'feParams' ] =  $this->feParams = (object)[
            'entity'            => CustomerEquipmentEntity::class,

            'pagetitle'         => 'Customer Equipment',

            'titleSingular'     => 'Customer Equipment',
            'nameSingular'      => 'customer equipment',

            'defaultAction'     => 'list',
            'defaultController' => 'CustKitController',

            'listOrderBy'       => 'name',
            'listOrderByDir'    => 'ASC',

            'viewFolderName'    => 'cust-kit',

            'listColumns'    => [

                'id'        => [ 'title' => 'DB ID', 'display' => true ],

                'name'      => 'Name',

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

            ]
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = array_merge(
            $this->feParams->listColumns, [
                'descr'      => 'Description'
            ]
        );

    }



    /**
     * Provide array of rows for the list and view
     *
     * @param int $id The `id` of the row to load for `view`. `null` if `list`
     * @return array
     */
    protected function listGetData( $id = null ) {
        return D2EM::getRepository( CustomerEquipmentEntity::class)->getAllForFeList( $this->feParams, $id );
    }


    /**
     * Display the form to add/edit an object
     * @param   int $id ID of the row to edit
     * @return array
     */
    protected function addEditPrepareForm( $id = null ): array {

        $ck = false;

        if( $id != null ) {

            if( !( $ck = D2EM::getRepository( CustomerEquipmentEntity::class )->find( $id ) ) ) {
                abort(404);
            }

            Former::populate([
                'name'                  => $ck->getName(),
                'cust'                  => $ck->getCustomer()->getId(),
                'cabinet'               => $ck->getCabinet()->getId(),
                'description'           => $ck->getDescr(),
            ]);
        }

        return [
            'ck'       => $ck,
            'cabinets' => D2EM::getRepository( CabinetEntity::class )->getAsArray(),
            'custs'    => D2EM::getRepository( CustomerEntity::class )->getAsArray(),
        ];
    }


    /**
     * Function to do the actual validation and storing of the submitted object.
     * @param Request $request
     * @return bool|RedirectResponse
     */
    public function doStore( Request $request )
    {

        $validator = Validator::make( $request->all(), [
            'name'              => 'required|string|max:255',
            'cust'              => 'required|integer|exists:Entities\Customer,id',
            'cabinet'           => 'required|integer|exists:Entities\Cabinet,id',
            'description'       => 'nullable|string|max:255',
        ]);

        if( $validator->fails() ) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        if( $request->input( 'id', false ) ) {
            if( !( $ck = D2EM::getRepository( CustomerEquipmentEntity::class )->find( $request->input( 'id' ) ) ) ) {
                abort(404);
            }
        } else {
            $ck = new CustomerEquipmentEntity;
            D2EM::persist( $ck );
        }

        $ck->setName(     $request->input( 'name' ) );
        $ck->setCabinet(  D2EM::getRepository( CabinetEntity::class  )->find( $request->input( 'cabinet' ) ) );
        $ck->setCustomer( D2EM::getRepository( CustomerEntity::class )->find( $request->input( 'cust' ) ) );
        $ck->setDescr(    $request->input( 'description' ) );

        D2EM::flush($ck);

        $this->object = $ck;
        return true;
    }
}
