<?php

namespace IXP\Http\Controllers\Contact;

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

use Former;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\{
    Request,
    RedirectResponse
};

use IXP\Models\{
    ContactGroup
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use IXP\Utils\Http\Controllers\Frontend\EloquentController;

/**
 * Contact Group Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\Contact
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ContactGroupController extends EloquentController
{
    /**
     * The object being created / edited
     *
     * @var ContactGroup
     */
    protected $object = null;

    protected static $route_prefix = "contact-group";

    /**
     * This function sets up the frontend controller
     */
    public function feInit(): void
    {
        $this->feParams         = ( object )[
            'model'             => ContactGroup::class,
            'pagetitle'         => 'Contact Groups',
            'titleSingular'     => 'Contact Group',
            'nameSingular'      => 'contact group',
            'defaultAction'     => 'list',
            'defaultController' => 'ContactGroupController',
            'listOrderBy'       => 'type',
            'listOrderByDir'    => 'ASC',
            'viewFolderName'    => 'contact-group',
            'documentation'     => 'https://docs.ixpmanager.org/usage/contacts/#contact-groups',
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
                'created_at'       => [
                    'title'     => 'Created',
                    'type'      => self::$FE_COL_TYPES[ 'DATETIME' ]
                ]
            ]
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = array_merge(
            $this->feParams->listColumns,
            [
                'updated_at'       => [
                    'title'     => 'Updated',
                    'type'      => self::$FE_COL_TYPES[ 'DATETIME' ]
                ],
                'limited_to'    => [
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
     * @param int|null $id The `id` of the row to load for `view` action`. `null` if `listAction`

     * @return array
     *
     * @throws
     */
    protected function listGetData( ?int $id = null ): array
    {
        $feParams = $this->feParams;
        return ContactGroup::when( $id , function( Builder $q, $id ) {
                return $q->where('id', $id );
            } )->when( $feParams->listOrderBy , function( Builder $q, $orderby ) use ( $feParams )  {
                return $q->orderBy( $orderby, $feParams->listOrderByDir ?? 'ASC');
            })->when( $types = config( "contact_group.types" ) , function( Builder $q, $types ) {
                return $q->whereIn('type', array_keys( $types ) );
            } )->get()->toArray();
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
     * Display the form to create an object
     *
     * @return array
     *
     * @throws
     */
    protected function createPrepareForm(): array
    {
        return [
            'object'        => $this->object,
            'types'         => config( 'contact_group.types' )
        ];
    }

    /**
     * Display the form to edit an object
     *
     * @param   int|null $id ID of the row to edit
     *
     * @return array
     *
     * @throws
     */
    protected function editPrepareForm( int $id = null ): array
    {
        $this->object = ContactGroup::findOrFail( $id );

        Former::populate( [
            'name'              => request()->old( 'name',              $this->object->name         ),
            'description'       => request()->old( 'description',       $this->object->description  ),
            'type'              => request()->old( 'type',              $this->object->type         ),
            'active'            => request()->old( 'active',            $this->object->active       ),
            'limited_to'        => request()->old( 'limit',             $this->object->limited_to   ),
        ] );

        return [
            'object'        => $this->object,
            'types'         => config( "contact_group.types" )
        ];
    }

    /**
     * Function to do the actual validation and storing of the submitted object.
     *
     * @param Request $r
     *
     * @return bool|RedirectResponse
     *
     * @throws
     */
    public function doStore( Request $r ): bool|RedirectResponse
    {
        $this->checkForm( $r );
        $this->object = ContactGroup::create( $r->all() );
        return true;
    }

    /**
     * Function to do the actual validation and storing of the submitted object.
     *
     * @param Request   $r
     * @param int       $id
     *
     * @return bool|RedirectResponse
     *
     * @throws
     */
    public function doUpdate( Request $r, int $id ): bool|RedirectResponse
    {
        $this->object = ContactGroup::findOrFail( $id );
        $this->checkForm( $r );
        $this->object->update( $r->all() );
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
            'name'                  => 'required|string|max:255|unique:contact_group,name' . ( $r->id ? ','. $r->id : '' ),
            'description'           => 'required|string|max:255',
            'type'                  => 'required|string|in:' . implode( ',', array_keys( config( 'contact_group.types' ) ) ),
            'limited_to'            => 'required|integer|min:0',
        ] );
    }
}