<?php

namespace IXP\Http\Controllers;

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

use Auth, Former, Hash, Redirect, Route, Str;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\{
    Request,
    RedirectResponse
};

use Illuminate\View\View;

use IXP\Models\{
    ApiKey,
    User
};

use IXP\Utils\Http\Controllers\Frontend\EloquentController;

/**
 * ApiKey Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ApiKeyController extends EloquentController
{
    /**
     * The object being created / updated
     * @var ApiKey
     */
    protected $object = null;

    /**
     * The minimum privileges required to access this controller.
     *
     * If you set this to less than the superuser, you need to manage privileges and access
     * within your own implementation yourself.
     *
     * @var int
     */
    public static $minimum_privilege = User::AUTH_CUSTUSER;

    /**
     * This function sets up the frontend controller
     */
    public function feInit(): void
    {
        $this->feParams         = (object)[
            'entity'            => ApiKey::class,
            'pagetitle'         => 'API Keys',
            'titleSingular'     => 'API Key',
            'nameSingular'      => 'API key',
            'listOrderBy'       => 'created',
            'listOrderByDir'    => 'ASC',
            'viewFolderName'    => 'api-key',
            'documentation'     => 'https://docs.ixpmanager.org/features/api/',

            'listColumns'    => [
                'id'           => [ 'title' => 'UID', 'display' => false ],
                'apiKey'       => [
                    'title'        => 'API Key',
                    'type'         => config( 'ixp_fe.api_keys.show_keys' ) ? self::$FE_COL_TYPES[ 'TEXT' ] : self::$FE_COL_TYPES[ 'LIMIT' ],
                    'limitTo'      => 6
                ],
                'created'      => [
                    'title'        => 'Created',
                    'type'         => self::$FE_COL_TYPES[ 'DATETIME' ]
                ],
                'expires'      => [
                    'title'        => 'Expires',
                    'type'         => self::$FE_COL_TYPES[ 'DATE' ]
                ],
                'lastseenAt'   => [
                    'title'        => 'Lastseen',
                    'type'         => self::$FE_COL_TYPES[ 'DATETIME' ]
                ],
                'lastseenFrom' => 'Lastseen From'
            ]
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = $this->feParams->listColumns;

        // phpunit / artisan trips up here without the cli test:
        if( PHP_SAPI !== 'cli' ) {
            // custom access controls:
            switch( Auth::check() ? Auth::user()->getPrivs() : User::AUTH_PUBLIC ) {
                case User::AUTH_SUPERUSER:
                case User::AUTH_CUSTUSER || User::AUTH_CUSTADMIN:
                    break;

                default:
                    $this->unauthorized();
                    break;
            }
        }
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
        // NB: this route is marked as 'read-only' to disable normal CRUD operations. It's not really read-only.
        Route::group( [  'prefix' => $route_prefix ], static function() use ( $route_prefix ) {
            Route::post(  'list-show-keys',      'ApiKeyController@listShowKeys' )->name( 'api-key@list-show-keys' );
        });
    }

    /**
     * Provide array of rows for the list action and view action
     *
     * @param int|null $id The `id` of the row to load for `view` action`. `null` if `listAction`
     *
     * @return array
     */
    protected function listGetData( $id = null ): array
    {
        $feParams = $this->feParams;
        return ApiKey::where( 'user_id', $id )
            ->when( $id , function( Builder $q, $id ) {
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
            'object'          => $this->object
        ];
    }

    /**
     * Display the form to edit an object
     *
     * @param   int|null $id ID of the row to edit
     *
     * @return array
     */
    protected function editPrepareForm( $id = null ): array
    {
        $this->object = ApiKey::findOrFail( $id );

        Former::populate( [
            'apiKey'            => request()->old( 'apiKey',    config( 'ixp_fe.api_keys.show_keys' ) ? $this->object->apiKey : Str::limit( $this->object->apiKey, 6 ) ),
            'description'       => request()->old( 'description',       $this->object->description ),
            'expires'           => request()->old( 'expires',         ( $this->object->expires ? $this->object->expires->format( "Y-m-d" ) : null ) )
        ] );

        return [
            'object'          => $this->object,
        ];
    }

    /**
     * Check if the form is valid
     *
     * @param $request
     *
     * @return void
     */
    public function checkForm( Request $request ): void
    {
        $request->validate( [
            'description'        => 'nullable|string|max:255',
            'expires'            => 'nullable|date|after:' . now()->format( "Y-m-d" ),
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
        if( count( $request->user()->getApiKeys() ) >= config( 'ixp_fe.api_keys.max_keys' ) ) {
            AlertContainer::push( "We currently have a limit of " . config( 'ixp_fe.api_keys.max_keys' ) . " API keys per user. Please contact us if you require more.", Alert::DANGER );
            return Redirect::back()->withInput();
        }

        $this->checkForm( $request );

        $this->object = ApiKey::create( [
            'user_id'       => $request->user()->getId(),
            'apiKey'        => $key = Str::random(48),
            'created'       => now(),
            'expires'       => $request->expires,
            'description'   => $request->description,
        ]);

        AlertContainer::push( "API key created: <code>" . $key . "</code>.", Alert::SUCCESS );

        return true;
    }

    /**
     * Function to do the actual validation and storing of the submitted object.
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
        if( count( $request->user()->getApiKeys() ) >= config( 'ixp_fe.api_keys.max_keys' ) ) {
            AlertContainer::push( "We currently have a limit of " . config( 'ixp_fe.api_keys.max_keys' ) . " API keys per user. Please contact us if you require more.", Alert::DANGER );
            return Redirect::back()->withInput();
        }

        $this->object = ApiKey::findOrFail( $request->id );
        $this->checkForm( $request );
        $this->object->update( $request->all() );

        return true;
    }

    /**
     * Show the API Keys if the password match
     *
     * @param Request $r
     *
     * @return View
     */
    public function listShowKeys( Request $r ): View
    {
        if( !Hash::check( $r->pass , $r->user()->getPassword() ) ) {
            AlertContainer::push( 'Incorrect password entered', Alert::DANGER );
        } else {
            AlertContainer::push( 'API keys are visible for this request only. You will need to re-enter your password to view them again.', Alert::SUCCESS );
            config( [ 'ixp_fe.api_keys.show_keys' => true ] );
        }

        return $this->list( $r );
    }
}