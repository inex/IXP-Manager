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
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class LocationController extends Doctrine2Frontend {

    /**
     * The object being added / edited
     * @var LocationEntity
     */
    protected $object = null;

    protected static $route_prefix = "facility";
    /**
     * This function sets up the frontend controller
     */
    public function feInit() {

        $this->feParams = (object)[
            'entity'            => LocationEntity::class,

            'pagetitle'         => 'Facilities',

            'titleSingular'     => 'Facility',
            'nameSingular'      => 'a facility',

            'listOrderBy'       => 'name',
            'listOrderByDir'    => 'ASC',

            'viewFolderName'    => 'location',

            'listColumns'    => [

                'id'        => [ 'title' => 'UID', 'display' => false ],
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
                'nocfax'      => 'NOC Fax',
                'officephone' => 'Office Phone',
                'officefax'   => 'Office Fax',
                'officeemail' => 'Office Email',
                'notes'       => [
                    'title'         => 'Notes',
                    'type'          => self::$FE_COL_TYPES[ 'PARSDOWN' ]
                ]
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
     *
     * @param   int $id ID of the row to edit
     *
     * @return array
     */
    protected function addEditPrepareForm( $id = null ): array {
        $old = request()->old();

        if( $id != null ) {
            if( !( $this->object = D2EM::getRepository( LocationEntity::class )->find( $id ) ) ) {
                abort(404);
            }


            Former::populate([
                'name'                  => array_key_exists( 'name',        $old ) ? $old['name']        : $this->object->getName(),
                'shortname'             => array_key_exists( 'shortname',   $old ) ? $old['shortname']   : $this->object->getShortname(),
                'tag'                   => array_key_exists( 'tag',         $old ) ? $old['tag']         : $this->object->getTag(),
                'address'               => array_key_exists( 'address',     $old ) ? $old['address']     : $this->object->getAddress(),
                'nocphone'              => array_key_exists( 'nocphone',    $old ) ? $old['nocphone']    : $this->object->getNocphone(),
                'nocfax'                => array_key_exists( 'nocfax',      $old ) ? $old['nocfax']      : $this->object->getNocfax(),
                'nocemail'              => array_key_exists( 'nocemail',    $old ) ? $old['nocemail']    : $this->object->getNocemail(),
                'officephone'           => array_key_exists( 'officephone', $old ) ? $old['officephone'] : $this->object->getOfficephone(),
                'officefax'             => array_key_exists( 'officefax',   $old ) ? $old['officefax']   : $this->object->getOfficefax(),
                'officeemail'           => array_key_exists( 'officeemail', $old ) ? $old['officeemail'] : $this->object->getOfficeemail(),
                'notes'                 => array_key_exists( 'notes',       $old ) ? $old['notes']       : $this->object->getNotes(),
            ]);
        }

        return [
            'object'       => $this->object,
            'notes'                 => $id ? ( array_key_exists( 'notes',           $old ) ? $old['notes']           : $this->object->getNotes() ) : ( array_key_exists( 'notes',           $old ) ? $old['notes']           : null )
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
            'shortname'         => 'required|string|max:255|unique:Entities\Location,shortname' . ( $request->input('id') ? ','. $request->input('id') : '' ),
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
            AlertContainer::push( "Could not delete the Facility ({$this->object->getName()}) as at least one rack is located here. Reassign or delete the rack first.", Alert::DANGER );
            return false;
        }

        return true;
    }

}