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
    Location
};

use Illuminate\Http\{
    Request,
    RedirectResponse
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use IXP\Utils\Http\Controllers\Frontend\EloquentController;

/**
 * Infrastructure Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CabinetController extends EloquentController
{
    /**
     * The object being added / edited
     * @var Cabinet
     */
    protected $object = null;

    protected static $route_prefix = "rack";

    /**
     * This function sets up the frontend controller
     */
    public function feInit()
    {
        $this->feParams         = ( object )[
            'entity'            => Cabinet::class,
            'pagetitle'         => 'Racks',
            'titleSingular'     => 'Rack',
            'nameSingular'      => 'a rack',
            'defaultAction'     => 'list',
            'defaultController' => 'CabinetController',
            'listOrderBy'       => 'name',
            'listOrderByDir'    => 'ASC',
            'viewFolderName'    => 'cabinet',
            'listColumns'    => [
                'id'        => [ 'title' => 'DB ID', 'display' => true ],
                'locationid'  => [
                    'title'      => 'Facility',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'facility',
                    'action'     => 'view',
                    'idField'    => 'locationid'
                ],
                'name'         => 'Name',
                'colocation' => 'Colo Location',
                'height'       => 'Height'
            ]
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = array_merge(
            $this->feParams->listColumns,
            [
                'u_counts_from'        => [
                    'title'          => "U's Count From",
                    'type'           => self::$FE_COL_TYPES[ 'XLATE' ],
                    'xlator'         => Cabinet::$U_COUNTS_FROM
                ],
                'type'       => 'Type',
                'notes'       => [
                    'title'         => 'Notes',
                    'type'          => self::$FE_COL_TYPES[ 'PARSDOWN' ]
                ]
            ]
        );
    }

    /**
     * Provide array of rows for the list action and view action
     *
     * @param int $id The `id` of the row to load for `view` action`. `null` if `listAction`

     * @return array
     */
    protected function listGetData( $id = null ): array
    {
        return Cabinet::getFeList( $this->feParams, $id )->toArray();
    }

    /**
     * Display the form to add/edit an object
     *
     * @return array
     */
    protected function createPrepareForm(): array
    {
        return [
            'object'                => $this->object,
            'locations'             => Location::getListForDropdown()->toArray(),
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
        $this->object = Cabinet::findOrFail( $id );

        Former::populate( [
            'name'                  => request()->old( 'name',          $this->object->name ),
            'locationid'            => request()->old( 'locationid',    $this->object->locationid ),
            'colocation'            => request()->old( 'colocation',    $this->object->colocation ),
            'type'                  => request()->old( 'type',          $this->object->type ),
            'height'                => request()->old( 'height',        $this->object->height ),
            'u_counts_from'         => request()->old( 'u_counts_from', $this->object->u_counts_from ),
            'notes'                 => request()->old( 'notes',         $this->object->notes ),
        ] );

        return [
            'object'                => $this->object,
            'locations'             => Location::getListForDropdown()->toArray(),
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
            'locationid'            => [ 'required', 'integer',
                function( $attribute, $value, $fail ) {
                    if( !Location::whereId( $value )->exists() ) {
                        return $fail( 'Location is invalid / does not exist.' );
                    }
                }
            ],
            'colocation'            => 'required|string|max:255',
            'height'                => 'nullable|integer',
            'type'                  => 'nullable|string|max:255',
            'notes'                 => 'nullable|string|max:65535',
            'u_counts_from'         => 'required|integer|in:' . implode( ',', array_keys( Cabinet::$U_COUNTS_FROM ) ),
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
        $this->object = Cabinet::create( $request->all() );
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
     */
    public function doUpdate( Request $request, int $id )
    {
        $this->checkForm( $request );
        $this->object = Cabinet::findOrFail( $request->id );
        $this->object->update( $request->all() );

        return true;
    }


    /**
     * @inheritdoc
     */
    protected function preDelete(): bool
    {
        $okay = true;
        if( ( $cnt = $this->object->customerEquipment()->count() ) ) {
            AlertContainer::push( "Could not delete the rack as at least one piece of customer equipment is located here. Reassign or delete that kit first.", Alert::DANGER );
            $okay = false;
        }

        if( ( $cnt = $this->object->switchers()->count() ) ) {
            AlertContainer::push( "Could not delete the rack as at least one switch is located here. Reassign or delete the switch first.", Alert::DANGER );
            $okay = false;
        }

        return $okay;
    }
}
