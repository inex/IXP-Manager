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

use Countries, Former;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Http\{
    Request,
    RedirectResponse
};

use IXP\Models\Location;

use IXP\Utils\Http\Controllers\Frontend\EloquentController;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * Location Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class LocationController extends EloquentController
{
    /**
     * The object being created / edited
     *
     * @var Location
     */
    protected $object = null;

    /**
     * The URL prefix to use.
     *
     * Automatically determined based on the controller name if not set.
     *
     * @var string|null
     */
    protected static $route_prefix = "facility";

    /**
     * This function sets up the frontend controller
     */
    public function feInit(): void
    {
        $this->feParams = (object)[
            'model'             => Location::class,
            'pagetitle'         => 'Facilities',
            'titleSingular'     => 'Facility',
            'nameSingular'      => 'facility',
            'listOrderBy'       => 'name',
            'listOrderByDir'    => 'ASC',
            'viewFolderName'    => 'location',
            'listColumns'    => [
                'id'        => [
                    'title' => 'UID',
                    'display' => false
                ],
                'name'      => 'Name',
                'shortname' => 'Shortname',
                'tag'       => 'Tag',
                'nocphone'  => 'NOC Phone',
                'nocemail'  => 'NOC Email',
                'pdb_facility_id' => [
                    'title'    => 'PeeringDB ID',
                    'type'     => self::$FE_COL_TYPES[ 'REPLACE' ],
                    'subject'  => '<a href="https://www.peeringdb.com/api/fac/%%COL%%" target="_blank">%%COL%%</a>',
                ],
            ]
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = array_merge(
            $this->feParams->listColumns, [
                'address'     => 'Address',
                'city'        => 'City',
                'country'     => [
                    'title' => 'Country',
                    'type' => self::$FE_COL_TYPES[ 'COUNTRY' ]
                ],
                'nocfax'      => 'NOC Fax',
                'officephone' => 'Office Phone',
                'officefax'   => 'Office Fax',
                'officeemail' => 'Office Email',
                'notes'       => [
                    'title' => 'Notes',
                    'type'  => self::$FE_COL_TYPES[ 'PARSDOWN' ]
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
     * Provide array of rows for the list and view
     *
     * @param int|null $id The `id` of the row to load for `view`. `null` if `list`
     *
     * @return array
     */
    protected function listGetData( ?int $id = null ): array
    {
        $feParams = $this->feParams;
        return Location::when( $id , function( Builder $q, $id ) {
            return $q->where('id', $id );
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
            'object'            => $this->object,
            'countries'         => Countries::getList('name' )
        ];
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
        $this->object = Location::findOrFail( $id );

        Former::populate([
            'name'                  => request()->old( 'name',        $this->object->name           ),
            'shortname'             => request()->old( 'shortname',   $this->object->shortname      ),
            'tag'                   => request()->old( 'tag',         $this->object->tag            ),
            'address'               => request()->old( 'address',     $this->object->address        ),
            'city'                  => request()->old( 'city',        $this->object->city           ),
            'country'               => request()->old( 'country', in_array( $this->object->country, array_values( Countries::getListForSelect( 'iso_3166_2' ) ), false ) ? $this->object->country : null ),
            'nocphone'              => request()->old( 'nocphone',    $this->object->nocphone       ),
            'nocfax'                => request()->old( 'nocfax',      $this->object->nocfax         ),
            'nocemail'              => request()->old( 'nocemail',    $this->object->nocemail       ),
            'officephone'           => request()->old( 'officephone', $this->object->officephone    ),
            'officefax'             => request()->old( 'officefax',   $this->object->officefax      ),
            'officeemail'           => request()->old( 'officeemail', $this->object->officeemail    ),
            'notes'                 => request()->old( 'notes',       $this->object->notes          ),
        ]);

        return [
            'object'            => $this->object,
            'countries'         => Countries::getList('name' )
        ];
    }

    /**
     * Function to do the actual validation and storing of the submitted object.
     *
     * @param Request $r
     *
     * @return bool|RedirectResponse
     */
    public function doStore( Request $r ): bool|RedirectResponse
    {
        $this->checkForm( $r );
        $this->object = Location::create( $r->all() );
        return true;
    }

    /**
     * Function to do the actual validation and updating of the submitted object.
     *
     * @param Request   $r
     * @param int       $id
     *
     * @return bool|RedirectResponse
     */
    public function doUpdate( Request $r, int $id ): bool|RedirectResponse
    {
        $this->object = Location::findOrFail( $id );
        $this->checkForm( $r );
        $this->object->update( $r->all() );
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function preDelete(): bool
    {
        if( $this->object->cabinets->count() ) {
            AlertContainer::push( "Could not delete the Facility ({$this->object->name}) as at least one rack is located here. Reassign or delete the rack first.", Alert::DANGER );
            return false;
        }
        return true;
    }

    /**
     * Check if the form is valid
     *
     * @param Request $r
     */
    public function checkForm( Request $r ): void
    {
        $r->validate( [
            'name'              => 'required|string|max:255',
            'shortname'         => 'required|string|max:255|unique:location,shortname' . ( $r->id ? ',' . $r->id : '' ),
            'tag'               => 'required|string|max:255',
            'city'              => 'required|string|max:50',
            'country'           => 'required|string|max:2|in:' . implode( ',', array_values( Countries::getListForSelect( 'iso_3166_2' ) ) ),
            'nocemail'          => 'nullable|email',
            'officeemail'       => 'nullable|email',
        ] );
    }
}