<?php

namespace IXP\Http\Controllers\Contact;

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
    ContactGroup        as ContactGroupEntity,
    User                as UserEntity
};
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use IXP\Http\Controllers\Doctrine2Frontend;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};


/**
 * Contact Group Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ContactGroupController extends Doctrine2Frontend
{

    /**
     * The object being added / edited
     * @var ContactGroupEntity
     */
    protected $object = null;

    protected static $route_prefix = "contact-group";

    /**
     * The minimum privileges required to access this controller.
     *
     * If you set this to less than the superuser, you need to manage privileges and access
     * within your own implementation yourself.
     *
     * @var int
     */
    public static $minimum_privilege = UserEntity::AUTH_CUSTUSER;

    /**
     * This function sets up the frontend controller
     */
    public function feInit()
    {

        $this->feParams         = ( object )[

            'entity'            => ContactGroupEntity::class,
            'pagetitle'         => 'Contact Groups',

            'titleSingular'     => 'Contact Group',
            'nameSingular'      => 'contact group',

            'defaultAction'     => 'list',
            'defaultController' => 'ContactGroupController',

            'listOrderBy'       => 'type',
            'listOrderByDir'    => 'ASC',

            'viewFolderName'    => 'contact-group',

            'listColumns'    => [

                'type'       => [
                    'title'             => 'Group Name',
                    'type'              => self::$FE_COL_TYPES[ 'ARRAY' ],
                    'source'            => config( "contact_group.types" )
                ],

                'name'         => 'Option',

                'active'      => [
                    'title' => 'Active',
                    'type' => self::$FE_COL_TYPES[ 'YES_NO' ]
                ],

                'created'       => [
                    'title'     => 'Created',
                    'type'      => self::$FE_COL_TYPES[ 'DATETIME' ]
                ]
            ]

        ];


        // display the same information in the view as the list
        $this->feParams->viewColumns = array_merge(
            $this->feParams->listColumns,
            [
                'limit_to'    => [

                    'title' => 'Limit',
                    'type' => self::$FE_COL_TYPES[ 'INTEGER' ]

                ],
                'description' => 'Description'
            ]
        );

    }

    /**
     * Provide array of rows for the list action and view action
     *
     * @param int $id The `id` of the row to load for `view` action`. `null` if `listAction`
     * @return array
     *
     * @throws
     */
    protected function listGetData( $id = null )
    {
        return D2EM::getRepository( ContactGroupEntity::class )->getAllForFeList( $this->feParams, $id );
    }


    /**
     * @return RedirectResponse|null
     */
    protected function canList(): ?RedirectResponse
    {
        // are contact groups configured?
        if( config( 'contact_group.types', false ) === false ) {
            AlertContainer::push( 'Contact groups are not configured. Please see <a href="https://docs.ixpmanager.org/usage/contacts/#contact-groups">the documentation here</a>.', Alert::INFO );
            return redirect( route( 'contact@list' ) );
        }

        return null;
    }

    /**
     * Display the form to add/edit an object
     *
     * @param   int $id ID of the row to edit
     *
     * @return array
     *
     * @throws
     */
    protected function addEditPrepareForm( $id = null ): array
    {
        if( $id ) {

            if( !( $this->object = D2EM::getRepository( ContactGroupEntity::class )->find( $id ) ) ) {
                abort(404);
            }

            Former::populate( [
                'name'                      => request()->old( 'name',              $this->object->getName() ),
                'description'               => request()->old( 'description',       $this->object->getDescription() ),
                'type'                      => request()->old( 'type',              $this->object->getType() ),
                'active'                    => request()->old( 'active',            ( $this->object->getActive()      ? 1 : 0 ) ),
                'limit'                     => request()->old( 'limit',             $this->object->getLimitedTo() ),
            ] );
        }

        return [
            'object'                => $this->object,
            'types'                 => config( "contact_group.types" )
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
            'name'                  => 'required|string|max:255|unique:Entities\ContactGroup,name' . ( $request->input( 'id' ) ? ','. $request->input( 'id' ) : '' ),
            'description'           => 'required|string|max:255',
            'type'                  => 'required|string|in:' . implode( ',', array_keys( config( "contact_group.types" ) ) ),
            'limit'                 => 'required|integer|min:0',
        ]);


        if( $validator->fails() ) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        if( $request->input( 'id', false ) ) {
            if( !( $this->object = D2EM::getRepository( ContactGroupEntity::class )->find( $request->input( 'id' ) ) ) ) {
                abort(404);
            }
        } else {
            $this->object = new ContactGroupEntity;
            D2EM::persist( $this->object );
            $this->object->setCreated(  new \DateTime  );
        }

        $this->object->setName(           $request->input( 'name'           ) );
        $this->object->setDescription(    $request->input( 'description'    ) );
        $this->object->setType(           $request->input( 'type'           ) );
        $this->object->setActive(         $request->input( 'active'         ) ? 1 : 0 );
        $this->object->setLimitedTo(      $request->input( 'limit'          ) );

        D2EM::flush();

        return true;
    }

}
