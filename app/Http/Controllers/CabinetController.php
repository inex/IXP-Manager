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

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\{
    Request,
    RedirectResponse
};

use IXP\Models\{
    Cabinet,
    Location
};

use IXP\Utils\Http\Controllers\Frontend\EloquentController;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * Cabinet Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CabinetController extends EloquentController
{
    /**
     * The object being created / edited
     *
     * @var Cabinet
     */
    protected $object = null;

    /**
     * The URL prefix to use.
     *
     * Automatically determined based on the controller name if not set.
     *
     * @var string|null
     */
    protected static $route_prefix = "rack";

    /**
     * This function sets up the frontend controller
     */
    public function feInit(): void
    {
        $this->feParams         = ( object )[
            'model'             => Cabinet::class,
            'pagetitle'         => 'Racks',
            'titleSingular'     => 'Rack',
            'nameSingular'      => 'rack',
            'defaultAction'     => 'list',
            'defaultController' => 'CabinetController',
            'listOrderBy'       => 'name',
            'listOrderByDir'    => 'ASC',
            'viewFolderName'    => 'cabinet',
            'listColumns'    => [
                'id'        => [ 'title' => 'DB ID', 'display' => false ],
                'locationname'  => [
                    'title'      => 'Facility',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'facility',
                    'action'     => 'view',
                    'idField'    => 'locationid'
                ],
                'name'          => 'Name',
                'colocation'    => 'Colo Location',
                'height'        => 'Height'
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
                ],
                'created_at'       => [
                    'title'         => 'Created',
                    'type'          => self::$FE_COL_TYPES[ 'DATETIME' ]
                ],
                'updated_at'       => [
                    'title'         => 'Updated',
                    'type'          => self::$FE_COL_TYPES[ 'DATETIME' ]
                ]
            ]
        );
    }

    /**
     * Provide array of rows for the list action and view action
     *
     * @param int|null $id The `id` of the row to load for `view` action`. `null` if `listAction`

     * @return array
     */
    protected function listGetData( ?int $id = null ): array
    {
        $feParams = $this->feParams;
        return Cabinet::select( [ 'cabinet.*', 'l.name AS locationname' ] )
            ->leftJoin( 'location AS l', 'l.id', 'cabinet.locationid' )
        ->when( $id , function( Builder $q, $id ) {
            return $q->where('cabinet.id', $id );
        } )->when( $feParams->listOrderBy , function( Builder $q, $orderby ) use ( $feParams )  {
            return $q->orderBy( $orderby, $feParams->listOrderByDir ?? 'ASC');
        })->get()->toArray();
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
            'locations'             => Location::orderBy( 'name' )->get(),
        ];
    }

    /**
     * Display the form to create/edit an object
     *
     * @param   int $id ID of the row to edit
     *
     * @return array
     */
    protected function editPrepareForm( int $id ): array
    {
        $this->object = Cabinet::findOrFail( $id );

        Former::populate( [
            'name'                  => request()->old( 'name',          $this->object->name             ),
            'locationid'            => request()->old( 'locationid',    $this->object->locationid       ),
            'colocation'            => request()->old( 'colocation',    $this->object->colocation       ),
            'type'                  => request()->old( 'type',          $this->object->type             ),
            'height'                => request()->old( 'height',        $this->object->height           ),
            'u_counts_from'         => request()->old( 'u_counts_from', $this->object->u_counts_from    ),
            'notes'                 => request()->old( 'notes',         $this->object->notes            ),
        ] );

        return [
            'object'                => $this->object,
            'locations'             => Location::orderBy( 'name' )->get(),
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
        $this->object = Cabinet::create( $r->all() );
        return true;
    }

    /**
     * Function to do the actual validation and updating of the submitted object.
     *
     * @param Request   $r
     * @param int       $id
     *
     * @return bool|RedirectResponse
     *
     */
    public function doUpdate( Request $r, int $id )
    {
        $this->checkForm( $r );
        $this->object = Cabinet::findOrFail( $id );
        $this->object->update( $r->all() );
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

    /**
     * Check if the form is valid
     *
     * @param $r
     */
    public function checkForm( Request $r ): void
    {
        $r->validate( [
            'name'                  => 'required|string|max:255',
            'locationid'            => 'required|integer|exists:location,id',
            'colocation'            => 'required|string|max:255',
            'height'                => 'nullable|integer',
            'type'                  => 'nullable|string|max:255',
            'notes'                 => 'nullable|string|max:65535',
            'u_counts_from'         => 'required|integer|in:' . implode( ',', array_keys( Cabinet::$U_COUNTS_FROM ) ),
        ] );
    }
}