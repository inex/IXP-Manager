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

use Auth, D2EM, Former, Log, Route;

use Entities\{
    IRRDBConfig      as IRRDBConfigEntity
};
use IXP\Http\Requests\StoreIrrdbConfig as StoreIrrdbConfigRequest;

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
class IrrdbConfigController extends Doctrine2Frontend
{

    /**
     * The object being added / edited
     * @var IRRDBConfigEntity
     */
    protected $object = null;

    /**
     * Sometimes we need to pass a custom request object for validation / authorisation.
     *
     * Set the name of the function here and the route for store will be pointed to it instead of doStore()
     *
     * @var string
     */
    protected static $storeFn = 'customStore';

    /**
     * This function sets up the frontend controller
     */
    public function feInit()
    {
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
    protected function listGetData( $id = null )
    {
        return D2EM::getRepository( IRRDBConfigEntity::class )->getAllForFeList( $this->feParams, $id );
    }



    /**
     * Display the form to add/edit an object
     * @param   int $id ID of the row to edit
     * @return array
     */
    protected function addEditPrepareForm( $id = null ): array
    {
        if( $id ) {
            if( !( $this->object = D2EM::getRepository( IRRDBConfigEntity::class )->find( $id) ) ) {
                abort(404);
            }

            Former::populate([
                'host'              => request()->old( 'host',         $this->object->getHost() ),
                'protocol'          => request()->old( 'protocol',     $this->object->getProtocol() ),
                'source'            => request()->old( 'source',       $this->object->getSource() ),
                'notes'             => request()->old( 'notes',       $this->object->getNotes() ),
            ]);
        }

        return [
            'object'                => $this->object
        ];
    }


    /**
     * Function to do the actual validation and storing of the submitted object.
     *
     * @param StoreIrrdbConfigRequest $request
     *
     * @return bool|RedirectResponse
     *
     * @throws
     */
    public function customStore( StoreIrrdbConfigRequest $request )
    {

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


        D2EM::flush();


        $action = $request->input( 'id', '' )  ? "edited" : "added";

        Log::notice( ( Auth::check() ? Auth::user()->getUsername() : 'A public user' ) . ' ' . $action . ' ' . $this->feParams->nameSingular . ' with ID ' . $this->object->getId() );

        AlertContainer::push( $this->store_alert_success_message ?? $this->feParams->titleSingular . " " . $action, Alert::SUCCESS );

        return redirect()->to( $this->postStoreRedirect() ?? route( self::route_prefix() . '@' . 'list' ) );

    }

    /**
     * @inheritdoc
     */
    protected function preDelete() : bool
    {
        $okay = true;
        if( ( $cnt = count( $this->object->getCustomers() ) ) ) {
            AlertContainer::push( "You cannot delete this IRRDB Source there are {$cnt} customer(s) associated with it. ", Alert::DANGER );
            $okay = false;
        }

        return $okay;
    }

}
