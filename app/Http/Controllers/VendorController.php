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

use Illuminate\Http\{
    Request,
    RedirectResponse
};

use IXP\Models\Vendor;

use IXP\Utils\Http\Controllers\Frontend\EloquentController;

/**
 * Vendor Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class VendorController extends EloquentController
{
    /**
     * The object being created / edited
     * @var Vendor
     */
    protected $object = null;

    /**
     * This function sets up the frontend controller
     */
    public function feInit(): void
    {
        $this->feParams         = (object)[
            'entity'            => Vendor::class,
            'pagetitle'         => 'Vendors',
            'titleSingular'     => 'Vendor',
            'nameSingular'      => 'a vendor',
            'listOrderBy'       => 'name',
            'listOrderByDir'    => 'ASC',
            'viewFolderName'    => 'vendor-e2f',

            'listColumns'    => [
                'id'             => [
                    'title' => 'UID',
                    'display' => false
                ],
                'name'           => 'Name',
                'shortname'      => 'Short Name',
                'bundle_name'    => 'Bundle Name'
            ]
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = $this->feParams->listColumns;
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
        return Vendor::getFeList( $this->feParams, $id );
    }

    /**
     * Display the form to create an object
     *
     * @return array
     */
    protected function createPrepareForm(): array
    {
        return [
            'object'       => $this->object,
        ];
    }

    /**
     * Display the form to edit an object
     *
     * @param   int $id ID of the row to edit
     *
     * @return array
     */
    protected function editPrepareForm( $id = null ): array
    {
        $this->object = Vendor::findOrFail( $id );

        Former::populate([
            'name'        => request()->old( 'name',        $this->object->name ),
            'shortname'   => request()->old( 'shortname',   $this->object->shortname ),
            'bundle_name' => request()->old( 'bundle_name', $this->object->bundle_name ),
        ]);

        return [
            'object'       => $this->object,
        ];
    }

    /**
     * Check if the form is valid
     *
     * @param $request
     */
    public function checkForm( Request $request ): void
    {
        $request->validate( [
            'name'              => 'required|string|max:255',
            'shortname'         => 'required|string|max:255',
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
        $this->object = Vendor::create( $request->all() );
        return true;
    }

    /**
     * Function to do the actual validation and updating of the submitted object.
     *
     * @param Request $request
     *
     * @param int $id
     * @return bool|RedirectResponse
     *
     * @throws
     */
    public function doUpdate( Request $request, int $id )
    {
        $this->object = Vendor::findOrFail( $id );
        $this->checkForm( $request );
        $this->object->update( $request->all() );
        return true;
    }
}