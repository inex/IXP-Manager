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

use Entities\{
    Location   as LocationEntity
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;


/**
 * CustKit Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   VlanInterface
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class LocationController extends Doctrine2Frontend {

    /**
     * The object being added / edited
     * @var LocationEntity
     */
    protected $object = null;

    /**
     * This function sets up the frontend controller
     */
    public function feInit() {

        $this->data[ 'feParams' ] =  $this->feParams = (object)[
            'entity'            => LocationEntity::class,

            'pagetitle'         => 'Locations',

            'titleSingular'     => 'Location',
            'nameSingular'      => 'a location',

            'defaultAction'     => 'list',
            'defaultController' => 'LocationController',

            'listOrderBy'       => 'name',
            'listOrderByDir'    => 'ASC',

            'viewFolderName'    => 'location',

            'listColumns'    => [

                'id'        => [ 'title' => 'UID', 'display' => false ],
                'name'      => 'Name',
                'shortname' => 'Shortname',
                'tag'       => 'Tag',
                'nocphone'  => 'NOC Phone',
                'nocemail'  => 'NOC Email'
            ]
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = array_merge(
            $this->feParams->listColumns, [
                'address'     => 'Address',
                'nocfax'      => 'NOC Fax',
                'officephone' => 'Office Phone',
                'officefax'   => 'Office Fax',
                'officeemail' => 'Office Email',
                'notes'       => 'Notes'
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
        return D2EM::getRepository( LocationEntity::class )->getAllForFeList( $this->feParams, $id );
    }


    /**
     * Display the form to add/edit an object
     * @param   int $id ID of the row to edit
     * @return array
     */
    protected function addEditPrepareForm( $id = null ): array {
        if( $id != null ) {
            if( !( $this->object = D2EM::getRepository( LocationEntity::class )->find( $id ) ) ) {
                abort(404);
            }

            Former::populate([
                'name'                  => $this->object->getName(),
                'shortname'             => $this->object->getShortname(),
                'tag'                   => $this->object->getTag(),
                'address'               => $this->object->getAddress(),
                'nocphone'              => $this->object->getNocphone(),
                'nocfax'                => $this->object->getNocfax(),
                'nocemail'              => $this->object->getNocemail(),
                'officephone'           => $this->object->getOfficephone(),
                'officefax'             => $this->object->getOfficefax(),
                'officeemail'           => $this->object->getOfficeemail(),
                'notes'                 => $this->object->getNotes(),
            ]);
        }

        return [
            'object'       => $this->object,
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
            'shortname'         => 'required|string|max:255',
            'tag'               => 'required|string|max:255',
            'nocemail'          => 'nullable|email',
            'officeemail'       => 'nullable|email',
        ]);

        if( $validator->fails() ) {
            return Redirect::back()->withErrors( $validator )->withInput();
        }

        if( $request->input( 'id', false ) ) {
            if( !( $this->object = D2EM::getRepository( LocationEntity::class )->find( $request->input( 'id' ) ) ) ) {
                abort(404);
            }
        } else {
            $this->object = new LocationEntity;
            D2EM::persist( $this->object );
        }

        $this->object->setName(            $request->input( 'name'             ) );
        $this->object->setShortname(       $request->input( 'shortname'        ) );
        $this->object->setTag(             $request->input( 'tag'              ) );
        $this->object->setTag(             $request->input( 'tag'              ) );
        $this->object->setAddress(         $request->input( 'address'          ) );
        $this->object->setNocphone(        $request->input( 'nocphone'         ) );
        $this->object->setNocfax(          $request->input( 'nocfax'           ) );
        $this->object->setNocemail(        $request->input( 'nocemail'         ) );
        $this->object->setOfficephone(     $request->input( 'officephone'      ) );
        $this->object->setOfficefax(       $request->input( 'officefax'        ) );
        $this->object->setOfficeemail(     $request->input( 'officeemail'      ) );
        $this->object->setNotes(           $request->input( 'notes'            ) );
        $this->object->setPdbFacilityId(   $request->input( 'pdb_facility_id'  ) );

        D2EM::flush( $this->object );

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function preDelete(): bool {
        if( ( $cnt = count( $this->object->getCabinets() ) ) ) {
            AlertContainer::push( "Could not delete the location ({$this->object->getName()}) as at least one cabinet is located here. Reassign or delete the cabinet first.", Alert::DANGER );
            return false;
        }

        return true;
    }

}