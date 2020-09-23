<?php

namespace IXP\Http\Controllers\Customer;

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

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\{
    Request,
    RedirectResponse
};

use IXP\Models\CustomerTag;

use IXP\Utils\Http\Controllers\Frontend\EloquentController;
use Session;

/**
 * Customer Tag Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CustomerTagController extends EloquentController
{
    /**
     * The object being created / edited
     * @var CustomerTag
     */
    protected $object = null;

    /**
     * This function sets up the frontend controller
     */
    public function feInit(): void
    {
        $this->feParams         = ( object )[
            'entity'            => CustomerTag::class,
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
                'id'        => [
                    'title' => 'DB ID',
                    'display' => false
                ],
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
                'created_at'              =>
                    [
                        'title'        => 'Created',
                        'type'         => self::$FE_COL_TYPES[ 'DATETIME' ]
                    ],
                'updated_at'              =>
                    [
                        'title'        => 'Updated',
                        'type'         => self::$FE_COL_TYPES[ 'DATETIME' ]
                    ],
            ]
        );
    }


    /**
     * Provide array of rows for the list action and view action
     *
     * @param int|null $id The `id` of the row to load for `view` action`. `null` if `listAction`
     *
     * @return array
     */
    protected function listGetData( ?int $id = null ): array
    {
        $feParams = $this->feParams;
        return CustomerTag::when( $id , function( Builder $q, $id ) {
            return $q->where('id', $id );
        } )->when( $feParams->listOrderBy , function( Builder $q, $orderby ) use ( $feParams )  {
            return $q->orderBy( $orderby, $feParams->listOrderByDir ?? 'ASC');
        })->get()->toArray();
    }

    /**
     * Display the form to create an object
     *
     * @return array
     */
    protected function createPrepareForm(): array
    {
        return [
            'object'                => $this->object
        ];
    }

    /**
     * Display the form to edit an object
     *
     * @param   int $id ID of the row to edit
     *
     * @return array
     */
    protected function editPrepareForm( int $id ): array
    {
        $this->object = CustomerTag::findOrFail( $id );

        Former::populate([
            'tag'                   => request()->old( 'tag',               $this->object->tag ),
            'description'           => request()->old( 'description',       $this->object->description ),
            'display_as'            => request()->old( 'display_as',        $this->object->display_as ),
            'internal_only'         => request()->old( 'internal_only',     ( $this->object->internal_only ? 1 : 0 ) ),
        ]);

        return [
            'object'                => $this->object
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
            'tag' => [
                'required', 'string', 'max:255',
                function ($attribute, $value, $fail) use( $request ) {
                    $tag = CustomerTag::whereTag( $value )->get()->first();
                    if( $tag && $tag->exists() && $tag->id !== (int)$request->id ) {
                        return $fail( 'The tag must be unique.' );
                    }
                },
            ],
            'description'           => 'nullable|string|max:255',
            'display_as'            => 'required|string|max:255',
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

        $this->object = CustomerTag::create( array_merge( $request->except( 'tag' ),
            [
                'tag'   => preg_replace( "/[^a-z0-9\-]/" , "", strtolower( $request->tag ) )
            ]
        ));

        return true;
    }

    /**
     * Function to do the actual validation and updating of the submitted object.
     *
     * @param Request $request
     * @param int $id
     *
     * @return bool|RedirectResponse
     *
     * @throws
     */
    public function doUpdate( Request $request, int $id )
    {
        $this->object = CustomerTag::findOrFail( $request->id );
        $this->checkForm( $request );
        $this->object->update( array_merge( $request->except( 'tag' ),
            [
                'tag'   => preg_replace( "/[^a-z0-9\-]/" , "", strtolower( $request->tag ) )
            ]
        ));

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function preDelete(): bool
    {
        Session::remove( "cust-list-tag" );
        return true;
    }
}
