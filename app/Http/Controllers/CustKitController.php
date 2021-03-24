<?php

namespace IXP\Http\Controllers;

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

use Former;

use IXP\Models\{
    Cabinet,
    Customer,
    CustomerEquipment
};

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Http\{
    Request,
    RedirectResponse
};

use IXP\Utils\Http\Controllers\Frontend\EloquentController;

/**
 * CustKit Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\ConsoleServer
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CustKitController extends EloquentController
{
    /**
     * The object being created / edited
     *
     * @var CustomerEquipment
     */
    protected $object = null;

    /**
     * This function sets up the frontend controller
     */
    public function feInit(): void
    {
        $this->feParams         = (object)[
            'model'             => CustomerEquipment::class,
            'pagetitle'         => 'Colocated Equipment',
            'titleSingular'     => 'Colocated Equipment',
            'nameSingular'      => 'colocated equipment',
            'listOrderBy'       => 'name',
            'listOrderByDir'    => 'ASC',
            'viewFolderName'    => 'cust-kit',
            'listColumns'    => [
                'id'        => [ 'title' => 'DB ID', 'display' => false ],
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
                'descr'      => 'Description',
                'created_at'       => [
                    'title'         => 'Created',
                    'type'          => self::$FE_COL_TYPES[ 'DATETIME' ]
                ],
                'updated_at'       => [
                    'title'         => 'Updated',
                    'type'          => self::$FE_COL_TYPES[ 'DATETIME' ]
                ]

            ],


        );
    }

    /**
     * Provide array of rows for the list and view
     *
     * @param int|null $id The `id` of the row to load for `view`. `null` if `list`
     *
     * @return array
     */
    protected function listGetData( ?int $id = null ): array
    {
        $feParams = $this->feParams;
        return CustomerEquipment::select( [ 'custkit.*', 'cabinet.name AS cabinet', 'cust.name as customer' ] )
            ->leftJoin( 'cabinet', 'cabinet.id', 'custkit.cabinetid' )
            ->leftJoin( 'cust', 'cust.id', 'custkit.custid' )
            ->when( $id , function( Builder $q, $id ) {
                return $q->where('custkit.id', $id );
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
            'object'        => $this->object ,
            'cabinets'      => Cabinet::selectRaw( "id, concat( name, ' [', colocation, ']') AS name" )
                ->orderBy( 'name' )->get(),
            'custs'         => Customer::orderBy( 'name' )->get(),
        ];
    }

    /**
     * Function to do the actual validation and storing of the submitted object.
     *
     * @param Request $r
     *
     * @return bool|RedirectResponse
     *
     * @throws
     */
    public function doStore( Request $r )
    {
        $this->checkForm( $r );
        $this->object = CustomerEquipment::make( $r->all() );
        $this->object->custid = $r->custid;
        $this->object->save();
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
        $this->object = CustomerEquipment::findOrFail( $id );

        Former::populate([
            'name'          => request()->old( 'name',        $this->object->name       ),
            'custid'        => request()->old( 'custid',      $this->object->custid     ),
            'cabinetid'     => request()->old( 'cabinetid',   $this->object->cabinetid  ),
            'descr'         => request()->old( 'descr',       $this->object->descr      ),
        ]);

        return [
            'object'        => $this->object ,
            'cabinets'      => Cabinet::selectRaw( "id, concat( name, ' [', colocation, ']') AS name" )
                ->orderBy( 'name')->get(),
            'custs'         => Customer::orderBy( 'name' )->get(),
        ];
    }

    /**
     * Function to do the actual validation and storing of the submitted object.
     *
     * @param Request   $r
     * @param int       $id
     *
     * @return bool|RedirectResponse
     *
     * @throws
     */
    public function doUpdate( Request $r, int $id )
    {
        $this->object = CustomerEquipment::findOrFail( $id );
        $this->checkForm( $r );
        $this->object->fill( $r->all() );
        $this->object->custid = $r->custid;
        $this->object->save();
        return true;
    }

    /**
     * Check if the form is valid
     *
     * @param $r
     */
    public function checkForm( Request $r ): void
    {
        $r->validate( [
            'name'                  => 'required|string|max:255',
            'custid'                => 'required|integer|exists:cust,id',
            'cabinetid'             => 'required|integer|exists:cabinet,id',
            'descr'                 => 'nullable|string|max:255',
        ] );
    }
}