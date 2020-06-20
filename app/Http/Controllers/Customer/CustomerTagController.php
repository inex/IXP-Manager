<?php

namespace IXP\Http\Controllers\Customer;

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
    CustomerTag         as CustomerTagEntity
};
use Illuminate\Http\{
    Request,
    RedirectResponse
};

use IXP\Http\Controllers\Doctrine2Frontend;



/**
 * Customer Tag Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CustomerTagController extends Doctrine2Frontend {

    /**
     * The object being added / edited
     * @var CustomerTagEntity
     */
    protected $object = null;

    /**
     * This function sets up the frontend controller
     */
    public function feInit(){

        $this->feParams         = ( object )[

            'entity'            => CustomerTagEntity::class,
            'pagetitle'         => ucfirst( config( 'ixp_fe.lang.customer.one' ) ) . ' Tags',

            'titleSingular'     => ucfirst( config( 'ixp_fe.lang.customer.one' ) ) . ' Tag',
            'nameSingular'      => config( 'ixp_fe.lang.customer.one' ) . ' tag',

            'defaultAction'     => 'list',
            'defaultController' => 'CustomerTagController',

            'listOrderBy'       => 'tag',
            'listOrderByDir'    => 'ASC',

            'viewFolderName'    => 'customer/tag',

            'extraDeleteMessage' => "<b>This tag will be removed from all " . config( 'ixp_fe.lang.customer.many' ) . " tagged with it.</b>",

            'documentation'     => 'https://docs.ixpmanager.org/usage/customer-tags/',

            'listColumns'    => [

                'id'        => [ 'title' => 'DB ID', 'display' => false ],

                'tag'               => 'Tag',
                'display_as'        => 'Display As',
                'internal_only'     => [
                                'title' => 'Internal Only',
                                'type' => self::$FE_COL_TYPES[ 'YES_NO' ]
                ],
            ]
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = array_merge(
            $this->feParams->listColumns,
            [
                'description'          => 'Description',
                'created'              => [
                        'title'        => 'Created',
                        'type'         => self::$FE_COL_TYPES[ 'DATETIME' ]
                    ],
                'updated'              => [
                        'title'        => 'Updated',
                        'type'         => self::$FE_COL_TYPES[ 'DATETIME' ]
                    ],
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
        return D2EM::getRepository( CustomerTagEntity::class )->getAllForFeList( $this->feParams, $id );
    }



    /**
     * Display the form to add/edit an object
     * @param   int $id ID of the row to edit
     * @return array
     */
    protected function addEditPrepareForm( $id = null ): array
    {
        if( $id ) {

            if( !( $this->object = D2EM::getRepository( $this->feParams->entity )->find( $id ) ) ) {
                abort(404);
            }

            Former::populate([
                'tag'                   => request()->old( 'tag',               $this->object->getTag() ),
                'description'           => request()->old( 'description',       $this->object->getDescription() ),
                'display_as'            => request()->old( 'display_as',        $this->object->getDisplayAs() ),
                'internal_only'         => request()->old( 'internal_only',     ( $this->object->isInternalOnly() ? 1 : 0 ) ),
            ]);
        }

        return [
            'object'                => $this->object
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
            'tag'                   => 'required|string|max:255|unique:Entities\CustomerTag,tag'. ( $request->input('id') ? ','. $request->input('id') : '' ),
            'description'           => 'nullable|string|max:255',
            'display_as'            => 'required|string|max:255',
        ]);

        if( $validator->fails() ) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        if( $request->input( 'id', false ) ) {
            if( !( $this->object = D2EM::getRepository( $this->feParams->entity )->find( $request->input( 'id' ) ) ) ) {
                abort(404, "Unknown" . $this->feParams->titleSingular );
            }
            $this->object->setUpdated( new \DateTime );
        } else {
            $this->object = new $this->feParams->entity;
            D2EM::persist( $this->object );
            $this->object->setCreated( new \DateTime );
            $this->object->setUpdated( new \DateTime );
        }

        $this->object->setTag(                      preg_replace( "/[^a-z0-9\-]/" , "", strtolower( $request->input( 'tag' ) ) ) );
        $this->object->setDescription(              $request->input( 'description'  ) );
        $this->object->setDisplayAs(                $request->input( 'display_as'   ) );
        $this->object->setInternalOnly( $request->input( 'internal_only' ) ? 1 : 0 );

        D2EM::flush();

        return true;
    }


    /**
     * @inheritdoc
     */
    protected function preDelete(): bool
    {
        request()->session()->remove( "cust-list-tag" );
        return true;
    }

}
