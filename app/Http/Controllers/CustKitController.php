<?php

namespace IXP\Http\Controllers;

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

use Former;

use IXP\Models\{
    Cabinet,
    Customer,
    CustomerEquipment
};

use Illuminate\Http\{
    Request,
    RedirectResponse
};

use IXP\Utils\Http\Controllers\Frontend\EloquentController;

/**
 * CustKit Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   VlanInterface
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CustKitController extends EloquentController
{
    /**
     * The object being added / edited
     * @var CustomerEquipment
     */
    protected $object = null;

    /**
     * This function sets up the frontend controller
     */
    public function feInit()
    {
        $this->feParams         = (object)[
            'entity'            => CustomerEquipment::class,
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
     *
     * @return array
     */
    protected function listGetData( $id = null ): array
    {
        return CustomerEquipment::getFeList( $this->feParams, $id  )->toArray();
    }

    /**
     * Display the form to create an object
     *
     * @return array
     */
    protected function createPrepareForm(): array
    {
        return [
            'object'        => $this->object ,
            'cabinets'      => Cabinet::get()->toArray(),
            'custs'         => Customer::getListAsArray(),
        ];
    }

    /**
     * Display the form to add/edit an object
     *
     * @param   int $id ID of the row to edit
     *
     * @return array
     */
    protected function editPrepareForm( $id = null ): array
    {
        $this->object = CustomerEquipment::findOrFail( $id );

        Former::populate([
            'name'          => request()->old( 'name',        $this->object->name ),
            'custid'        => request()->old( 'custid',      $this->object->custid ),
            'cabinetid'     => request()->old( 'cabinetid',   $this->object->cabinetid ),
            'descr'         => request()->old( 'descr',       $this->object->descr ),
        ]);

        return [
            'object'        => $this->object ,
            'cabinets'      => Cabinet::getListForDropdown()->toArray(),
            'custs'         => Customer::getListAsArray(),
        ];
    }

    /**
     * Check if the form is valid
     *
     * @param $request
     */
    public function checkForm( Request $request )
    {
        $request->validate( [
            'name'                  => 'required|string|max:255',
            'custid'                => 'required|integer|exists:Entities\Customer,id',
            'cabinetid'             => 'required|integer|exists:Entities\Cabinet,id',
            'descr'                 => 'nullable|string|max:255',
        ] );
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
        $this->checkForm( $request );
        $this->object = CustomerEquipment::create( $request->all() );

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
        $this->object = CustomerEquipment::findOrFail( $request->id );
        $this->checkForm( $request );
        $this->object->update( $request->all() );
        return true;
    }
}