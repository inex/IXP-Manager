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
    Cabinet         as CabinetEntity,
    Location        as LocationEntity
};
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};



/**
 * Infrastructure Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CabinetController extends Doctrine2Frontend {

    /**
     * The object being added / edited
     * @var CabinetEntity
     */
    protected $object = null;

    protected static $route_prefix = "rack";
    /**
     * This function sets up the frontend controller
     */
    public function feInit(){

        $this->feParams         = ( object )[

            'entity'            => CabinetEntity::class,
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

                'location'  => [
                    'title'      => 'Facility',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'facility',
                    'action'     => 'view',
                    'idField'    => 'locationid'
                ],

                'name'         => 'Name',
                'cololocation' => 'Colo Location',
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
                    'xlator'         => CabinetEntity::$U_COUNTS_FROM
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
    protected function listGetData( $id = null ) {
        return D2EM::getRepository( CabinetEntity::class )->getAllForFeList( $this->feParams, $id );
    }



    /**
     * Display the form to add/edit an object
     * @param   int $id ID of the row to edit
     * @return array
     */
    protected function addEditPrepareForm( $id = null ): array {

        $old = request()->old();

        if( $id !== null ) {

            if( !( $this->object = D2EM::getRepository( CabinetEntity::class )->find( $id ) ) ) {
                abort(404);
            }



            Former::populate([
                'name'                  => array_key_exists( 'name',          $old ) ? $old['name']          : $this->object->getName(),
                'locationid'            => array_key_exists( 'locationid',    $old ) ? $old['locationid']    : $this->object->getLocation()->getId(),
                'colocation'            => array_key_exists( 'colocation',    $old ) ? $old['colocation']    : $this->object->getCololocation(),
                'type'                  => array_key_exists( 'type',          $old ) ? $old['type']          : $this->object->getType(),
                'height'                => array_key_exists( 'height',        $old ) ? $old['height']        : $this->object->getHeight(),
                'u_counts_from'         => array_key_exists( 'u_counts_from', $old ) ? $old['u_counts_from'] : $this->object->getUCountsFrom(),
            ]);
        }

        return [
            'object'                => $this->object,
            'locations'             => D2EM::getRepository( LocationEntity::class )->getAsArray(),
            'notes'                 => $id ? ( array_key_exists( 'notes',           $old ) ? $old['notes']           : $this->object->getNotes() ) : ( array_key_exists( 'notes',           $old ) ? $old['notes']           : "" )
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
            'locationid'            => 'required|integer|exists:Entities\Location,id',
            'colocation'            => 'required|string|max:255',
            'height'                => 'nullable|integer',
            'type'                  => 'nullable|string|max:255',
            'notes'                 => 'nullable|string|max:65535',
            'u_counts_from'         => 'required|integer|in:' . implode( ',', array_keys( CabinetEntity::$U_COUNTS_FROM ) ),
        ]);

        if( $validator->fails() ) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        if( $request->input( 'id', false ) ) {
            if( !( $this->object = D2EM::getRepository( CabinetEntity::class )->find( $request->input( 'id' ) ) ) ) {
                abort(404);
            }
        } else {
            $this->object = new CabinetEntity;
            D2EM::persist( $this->object );
        }

        $this->object->setName(              $request->input( 'name'            ) );
        $this->object->setCololocation(      $request->input( 'colocation'      ) );
        $this->object->setType(              $request->input( 'type'            ) );
        $this->object->setHeight(            $request->input( 'height'          ) );
        $this->object->setUCountsFrom(       $request->input( 'u_counts_from'   ) );
        $this->object->setNotes(             $request->input( 'notes'           ) );
        $this->object->setLocation(          D2EM::getRepository( LocationEntity::class )->find( $request->input( 'locationid' ) ) );

        D2EM::flush($this->object);

        return true;
    }


    /**
     * @inheritdoc
     */
    protected function preDelete(): bool {
        $okay = true;

        if( ( $cnt = count( $this->object->getCustomerEquipment() ) ) ) {
            AlertContainer::push( "Could not delete the rack as at least one piece of customer equipment is located here. Reassign or delete that kit first.", Alert::DANGER );
            $okay = false;
        }

        if( ( $cnt = count( $this->object->getSwitches() ) ) ) {
            AlertContainer::push( "Could not delete the rack as at least one switch is located here. Reassign or delete the switch first.", Alert::DANGER );
            $okay = false;
        }

        return $okay;
    }


}
