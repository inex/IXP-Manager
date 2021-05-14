<?php

namespace IXP\Http\Controllers\Customer;

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
use Former, Route, Session;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Http\{
    Request,
    RedirectResponse
};

use Illuminate\View\View;

use IXP\Models\{
    Customer,
    CustomerToCustomerTag,
    CustomerTag
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use IXP\Utils\Http\Controllers\Frontend\EloquentController;

/**
 * Customer Tag Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\Customer
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CustomerTagController extends EloquentController
{
    /**
     * The object being created / edited
     *
     * @var CustomerTag
     */
    protected $object = null;

    /**
     * This function sets up the frontend controller
     */
    public function feInit(): void
    {
        $this->feParams         = ( object )[
            'model'             => CustomerTag::class,
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
                'created_at'           =>
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
     * Additional routes
     *
     * @param string $route_prefix
     *
     * @return void
     */
    protected static function additionalRoutes( string $route_prefix ): void
    {
        Route::group( [ 'prefix' => $route_prefix ], static function() use ( $route_prefix ) {
            Route::get(     'cust/{cust}',  'Customer\CustomerTagController@linkCustomer' )->name( $route_prefix . '@link-customer' );
            Route::post(    'link/{cust}',  'Customer\CustomerTagController@link'         )->name( $route_prefix . '@link'          );
        });
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
            'object'    => $this->object
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
            'tag'                   => request()->old( 'tag',               $this->object->tag              ),
            'description'           => request()->old( 'description',       $this->object->description      ),
            'display_as'            => request()->old( 'display_as',        $this->object->display_as       ),
            'internal_only'         => request()->old( 'internal_only',     $this->object->internal_only    ),
        ]);

        return [
            'object'                => $this->object
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
        $r->request->set( 'tag', preg_replace( "/[^a-z0-9\-]/" , "", strtolower( $r->tag ) ) );
        $this->object = CustomerTag::create( $r->all() );
        return true;
    }

    /**
     * Function to do the actual validation and updating of the submitted object.
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
        $this->object = CustomerTag::findOrFail( $r->id );
        $this->checkForm( $r );
        $r->request->set( 'tag', preg_replace( "/[^a-z0-9\-]/" , "", strtolower( $r->tag ) ) );
        $this->object->update( $r->all() );
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function preDelete(): bool
    {
        $this->object->customers()->detach();
        Session::remove( "cust-list-tag" );
        return true;
    }

    /**
     * Display the form to link/unlink customer/tags
     *
     * @param Customer $cust    The Customer
     *
     * @return View
     */
    public function linkCustomer( Customer $cust ): View
    {
        return view( 'customer/tag/cust' )->with([
            'c'             => $cust,
            'tags'          => CustomerTag::orderBy( 'display_as' )->get(),
        ]);
    }

    /**
     * Add or edit a customer (set all the data needed)
     *
     * @param   Request     $r      instance of the current HTTP request
     * @param   Customer    $cust
     *
     * @return  RedirectResponse
     * @throws
     */
    public function link( Request $r, Customer $cust ): RedirectResponse
    {
        $tagsToCreate  = array_diff( $r->tags ?? [], array_keys( $cust->tags->keyBy( 'id' )->toArray() ) );
        $tagsToDelete  = array_diff( array_keys( $cust->tags->keyBy( 'id' )->toArray() ), $r->tags ?? [] );

        // Tags that have been unchecked => unlink from the customer
        foreach( $tagsToDelete as $dtag ){
            CustomerToCustomerTag::where( 'customer_tag_id' ,$dtag )
                ->where( 'customer_id', $cust->id )->delete();
        }

        // Tags that have been checked => link to the customer
        foreach( $tagsToCreate as $ctag ){
            if( CustomerToCustomerTag::where( 'customer_tag_id' , $ctag )->where( 'customer_id', $cust->id )->doesntExist() ){
                CustomerToCustomerTag::create([
                    'customer_tag_id'   => $ctag,
                    'customer_id'       => $cust->id,
                ]);
            }
        }

        AlertContainer::push( "Tags updated.", Alert::SUCCESS );
        return redirect( route( "customer@overview" , [ "cust" => $cust->id ] ) );
    }

    /**
     * Check if the form is valid
     *
     * @param Request $r
     */
    public function checkForm( Request $r ): void
    {
        $r->validate( [
            'tag'               => 'required|string|max:255|unique:cust_tag,tag' . ( $this->object ? ',' . $this->object->id : '' ),
            'description'       => 'nullable|string|max:255',
            'display_as'        => 'required|string|max:255',
        ] );
    }
}