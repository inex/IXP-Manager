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
    Vendor   as VendorEntity
};

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;


/**
 * CustKit Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class VendorController extends Doctrine2Frontend
{

    /**
     * The object being added / edited
     * @var VendorEntity
     */
    protected $object = null;

    /**
     * This function sets up the frontend controller
     */
    public function feInit() {

        $this->feParams         = (object)[
            'entity'            => VendorEntity::class,

            'pagetitle'         => 'Vendors',

            'titleSingular'     => 'Vendor',
            'nameSingular'      => 'a vendor',

            'listOrderBy'       => 'name',
            'listOrderByDir'    => 'ASC',

            'viewFolderName'    => 'vendor-d2f',

            'listColumns'    => [
                'id'             => [ 'title' => 'UID', 'display' => false ],
                'name'           => 'Name',
                'shortname'      => 'Short Name',
//                'nagios_name'    => 'Nagios Name',
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
    protected function listGetData( $id = null )
    {
        return D2EM::getRepository( VendorEntity::class )->getAllForFeList( $this->feParams, $id );
    }


    /**
     * Display the form to add/edit an object
     *
     * @param   int $id ID of the row to edit
     *
     * @return array
     */
    protected function addEditPrepareForm( $id = null ): array
    {
        if( $id ) {
            if( !( $this->object = D2EM::getRepository( VendorEntity::class )->find( $id ) ) ) {
                abort(404);
            }

            Former::populate([
                'name'        => request()->old( 'name',        $this->object->getName() ),
                'shortname'   => request()->old( 'shortname',   $this->object->getShortname() ),
                'nagios_name' => request()->old( 'nagios_name', $this->object->getNagiosName() ),
                'bundle_name' => request()->old( 'bundle_name', $this->object->getBundleName() ),
            ]);
        }

        return [
            'object'       => $this->object,
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
            'name'              => 'required|string|max:255',
            'shortname'         => 'required|string|max:255',
            'nagios_name'       => 'nullable|string|max:255',

        ]);

        if( $validator->fails() ) {
            return Redirect::back()->withErrors( $validator )->withInput();
        }

        if( $request->input( 'id', false ) ) {
            if( !( $this->object = D2EM::getRepository( VendorEntity::class )->find( $request->input( 'id' ) ) ) ) {
                abort(404);
            }
        } else {
            $this->object = new VendorEntity;
            D2EM::persist( $this->object );
        }

        $this->object->setName(             $request->input( 'name'             ) );
        $this->object->setShortname(        $request->input( 'shortname'        ) );
        $this->object->setNagiosName(       $request->input( 'nagios_name'      ) );
        $this->object->setBundleName(       $request->input( 'bundle_name'      ) );

        D2EM::flush();

        return true;
    }
}