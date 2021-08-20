<?php

namespace IXP\Http\Controllers\User;

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

use Auth, Route;

use Illuminate\Auth\Recaller;

use Illuminate\Database\Eloquent\Builder;

use IXP\Models\{
    User,
    UserRememberToken
};

use IXP\Utils\Http\Controllers\Frontend\EloquentController;

/**
 * UserRememberTokenController Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\User
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class UserRememberTokenController extends EloquentController
{
    /**
     * The object being created / edited
     *
     * @var UserRememberToken
     */
    protected $object = null;

    /**
     * The URL prefix to use.
     *
     * Automatically determined based on the controller name if not set.
     *
     * @var string|null
     */
    protected static $route_prefix = "active-sessions";

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
     * Is this a read only controller?
     *
     * @var boolean
     */
    public static $read_only = true;

    /**
     * Should we allow a read only controller to delete
     *
     * @var boolean
     */
    public static $allow_delete_for_read_only = true;

    /**
     * This function sets up the frontend controller
     */
    public function feInit(): void
    {
        $this->feParams         = (object)[
            'model'                     => UserRememberToken::class,
            'pagetitle'                 => 'Your Active Login Sessions',
            'titleSingular'             => 'Active Login Session',
            'nameSingular'              => 'active login session',
            'listOrderBy'               => 'created_at',
            'listOrderByDir'            => 'ASC',
            'readonly'                  => self::$read_only,
            'allowDeleteForReadOnly'    => self::$allow_delete_for_read_only,
            'viewFolderName'            => 'user-remember-token',
            'listColumns'    => [
                'device'      => 'Device',
                'ip'          => 'IP',
                'created_at'      => [
                    'title'        => 'Created',
                    'type'         => self::$FE_COL_TYPES[ 'DATETIME' ]
                ],
                'expires'      => [
                    'title'        => 'Expires',
                    'type'         => self::$FE_COL_TYPES[ 'DATETIME' ]
                ],
            ]
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = $this->feParams->listColumns;
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
            Route::delete(  'delete',   'User\UserRememberTokenController@delete' )->name( $route_prefix."@delete" );
        });
    }

    /**
     * Function which can be over-ridden to perform any pre-list tasks
     *
     * E.g. adding elements to $this->view for the pre/post-amble templates.
     *
     * @return void
     */
    protected function preList(): void
    {
        // We want to indicate which session is the user's //current// session so they can avoid logging themselves out.
        // We identify it by matching the remember me cookie token with the database token:
        $token = null;

        if( $r = request()->cookies->get( Auth::getRecallerName() ) ) {
            $recaller = new Recaller( $r );
            $token = $recaller->token();
        }

        $this->data['session_token'] = $token;
    }

    /**
     * Provide array of rows for the list and view
     *
     * @param int|null $id The `id` of the row to load for `view`. `null` if `list`
     *
     * @return array
     */
    protected function listGetData( int $id = null ): array
    {
        $feParams = $this->feParams;
        return UserRememberToken::where( 'user_id', request()->user()->id )
            ->when( $id , function( Builder $q, $id ) {
                return $q->where('id', $id );
            } )->when( $feParams->listOrderBy , static function( Builder $q, $orderby ) use ( $feParams )  {
                return $q->orderBy( $orderby, $feParams->listOrderByDir ?? 'ASC');
            })->get()->toArray();
    }

    /**
     * @inheritdoc
     */
    protected function preDelete(): bool
    {
        // ensure a user can only delete their own sessions:
        return $this->object->user_id === Auth::id();
    }

    /**
     * Allow D2F implementations to override where the post-delete redirect goes.
     *
     * To implement this, have it return a valid route url (e.g. `return route( "route-name" );`
     *
     * For UserRememberToken, we need to log the user out if they deleted the current sessions remember me token.
     *
     * @return null|string
     */
    protected function postDeleteRedirect(): ?string
    {
        if( $r = request()->cookies->get( Auth::getRecallerName() ) ) {
            $recaller = new Recaller( $r );
            if( $this->object->token === $recaller->token() ) {
                return route('login@logout');
            }
        }
        return null;
    }
}