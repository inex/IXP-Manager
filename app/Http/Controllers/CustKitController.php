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

use D2EM, Former, Redirect, Validator;

use Entities\{
    CustomerEquipment   as CustomerEquipmentEntity,
    Cabinet             as CabinetEntity,
    Customer            as CustomerEntity
};
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;


/**
 * CustKit Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   VlanInterface
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CustKitController extends Doctrine2Frontend
{
    /**
     * The object being added / edited
     * @var CustomerEquipmentEntity
     */
    protected $object = null;

    /**
     * This function sets up the frontend controller
     */
    public function feInit()
    {
        $this->feParams         = (object)[
            'entity'            => CustomerEquipmentEntity::class,

            'pagetitle'         => 'Colocated Equipment',

            'titleSingular'     => 'Colocated Equipment',
            'nameSingular'      => 'colocated equipment',

            'listOrderBy'       => 'name',
            'listOrderByDir'    => 'ASC',

            'viewFolderName'    => 'cust-kit',

            'listColumns'    => [

                'id'        => [ 'title' => 'DB ID', 'display' => true ],

                'name'      => 'Name',

                'customer'  => [
                    'title'         => 'Customer',
                    'type'          => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller'    => 'customer',
                    'action'        => 'overview',
                    'idField'       => 'custid'
                ],

                'cabinet'  => [
                    'title'      => 'Rack',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'rack',
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
    protected function listGetData( $id = null )
    {
        return D2EM::getRepository( CustomerEquipmentEntity::class)->getAllForFeList( $this->feParams, $id );
    }


    /**
     * Display the form to add/edit an object
     * @param   int $id ID of the row to edit
     * @return array
     */
    protected function addEditPrepareForm( $id = null ): array
    {
        if( $id ) {

            if( !( $this->object = D2EM::getRepository( CustomerEquipmentEntity::class )->find( $id ) ) ) {
                abort(404);
            }

            Former::populate([
                'name'          => request()->old( 'name',        $this->object->getName() ),
                'custid'        => request()->old( 'custid',      $this->object->getCustomer()->getId() ),
                'cabinetid'     => request()->old( 'cabinetid',   $this->object->getCabinet()->getId() ),
                'descr'         => request()->old( 'descr',       $this->object->getDescr() ),
            ]);
        }

        return [
            'object'        => $this->object ,
            'cabinets'      => D2EM::getRepository( CabinetEntity::class    )->getAsArray(),
            'custs'         => D2EM::getRepository( CustomerEntity::class   )->getAsArray(),
        ];
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

        $validator = Validator::make( $request->all(), [
            'name'                  => 'required|string|max:255',
            'custid'                => 'required|integer|exists:Entities\Customer,id',
            'cabinetid'             => 'required|integer|exists:Entities\Cabinet,id',
            'descr'                 => 'nullable|string|max:255',
        ]);

        if( $validator->fails() ) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        if( $request->input( 'id', false ) ) {
            if( !( $this->object = D2EM::getRepository( CustomerEquipmentEntity::class )->find( $request->input( 'id' ) ) ) ) {
                abort(404);
            }
        } else {
            $this->object = new CustomerEquipmentEntity;
            D2EM::persist( $this->object );
        }

        $this->object->setName(     $request->input( 'name' ) );
        $this->object->setCabinet(  D2EM::getRepository( CabinetEntity::class  )->find( $request->input( 'cabinetid'    ) ) );
        $this->object->setCustomer( D2EM::getRepository( CustomerEntity::class )->find( $request->input( 'custid'       ) ) );
        $this->object->setDescr(    $request->input( 'descr' ) );

        D2EM::flush();

        return true;
    }
}
