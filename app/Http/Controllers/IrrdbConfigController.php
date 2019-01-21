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
    IRRDBConfig      as IRRDBConfigEntity
};
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};



/**
 * Irrdb Config Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IrrdbConfigController extends Doctrine2Frontend {

    /**
     * The object being added / edited
     * @var IRRDBConfigEntity
     */
    protected $object = null;

    /**
     * This function sets up the frontend controller
     */
    public function feInit() {

        $this->feParams         = (object)[

            'entity'            => IRRDBConfigEntity::class,
            'pagetitle'         => 'IRRDB Sources',

            'titleSingular'     => 'IRRDB Source',
            'nameSingular'      => 'an IRRDB Sources',

            'listOrderBy'       => 'host',
            'listOrderByDir'    => 'ASC',

            'viewFolderName'    => 'irrdb-config',

            'documentation'     => 'https://docs.ixpmanager.org/features/irrdb/',

            'listColumns'       => [
                'id'        => [ 'title' => 'DB ID', 'display' => false ],
                'host'      => 'Host',
                'protocol'  => 'Protocol',
                'source'    => 'Source'
            ],
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = array_merge(
            $this->feParams->listColumns,
            [
                'notes' => [
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
        return D2EM::getRepository( IRRDBConfigEntity::class )->getAllForFeList( $this->feParams, $id );
    }



    /**
     * Display the form to add/edit an object
     * @param   int $id ID of the row to edit
     * @return array
     */
    protected function addEditPrepareForm( $id = null ): array {
        $old = request()->old();

        if( $id !== null ) {

            if( !( $this->object = D2EM::getRepository( IRRDBConfigEntity::class )->find( $id) ) ) {
                abort(404);
            }


            Former::populate([
                'host'              => array_key_exists( 'host',        $old ) ? $old['host']       : $this->object->getHost(),
                'protocol'          => array_key_exists( 'protocol',    $old ) ? $old['protocol']   : $this->object->getProtocol(),
                'source'            => array_key_exists( 'source',      $old ) ? $old['source']     : $this->object->getSource()  ,
            ]);
        }

        return [
            'object'                => $this->object,
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
            'host'                  => 'required|string|max:255',
            'protocol'              => 'required|string|max:255',
            'source'                => 'required|string|max:255',
        ]);

        if( $validator->fails() ) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        if( $request->input( 'id', false ) ) {
            if( !( $this->object = D2EM::getRepository( IRRDBConfigEntity::class )->find( $request->input( 'id' ) ) ) ) {
                abort(404);
            }
        } else {
            $this->object = new IRRDBConfigEntity;
            D2EM::persist( $this->object );
        }

        $this->object->setHost(         $request->input( 'host'     ) );
        $this->object->setProtocol(     $request->input( 'protocol' ) );
        $this->object->setSource(       $request->input( 'source'   ) );
        $this->object->setNotes(        $request->input( 'notes'    ) );


        D2EM::flush($this->object);


        return true;
    }

    /**
     * @inheritdoc
     */
    protected function preDelete() : bool {
        $okay = true;
        if( ( $cnt = count( $this->object->getCustomers() ) ) ) {
            AlertContainer::push( "You cannot delete this IRRDB Source there are {$cnt} customer(s) associated with it. ", Alert::DANGER );
            $okay = false;
        }


        return $okay;
    }

}
