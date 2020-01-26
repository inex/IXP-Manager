<?php

namespace IXP\Http\Controllers\Auth;

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

use Auth, Cookie, D2EM, Route;

use Entities\{
    Session                 as SessionEntity,
    UserRememberToken      as UserRememberTokenEntity,
    User                    as UserEntity
};

use IXP\Http\Controllers\Doctrine2Frontend;

/**
 * CustKit Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   VlanInterface
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class UserRememberTokenController extends Doctrine2Frontend
{
    /**
     * The object being added / edited
     * @var UserRememberTokenEntity
     */
    protected $object = null;

    protected static $route_prefix = "active-sessions";

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
     * Is this a read only controller?
     *
     * @var boolean
     */
    public static $read_only = true;

    /**
     * This function sets up the frontend controller
     */
    public function feInit()
    {
        $this->feParams         = (object)[
            'entity'            => UserRememberTokenEntity::class,

            'pagetitle'         => 'Your Active Login Sessions',

            'titleSingular'     => 'Active Login Session',
            'nameSingular'      => 'active login session',

            'listOrderBy'       => 'created',
            'listOrderByDir'    => 'ASC',

            'readonly'          => self::$read_only,

            'viewFolderName'    => 'user-remember-token',

            'listColumns'    => [

                'device'      => 'Device',

                'ip'          => 'IP',

                'created'      => [
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
     *
     * @param string $route_prefix
     * @return void
     */
    protected static function additionalRoutes( string $route_prefix )
    {
        // NB: this route is marked as 'read-only' to disable normal CRUD operations. It's not really read-only.
        Route::group( [  'prefix' => $route_prefix ], function() use ( $route_prefix ) {
            Route::post(  'delete',      'Auth\UserRememberTokenController@delete'         )->name( $route_prefix."@delete" );
        });
    }



    /**
     * Provide array of rows for the list and view
     *
     * @param int $id The `id` of the row to load for `view`. `null` if `list`
     * @return array
     */
    protected function listGetData( $id = null )
    {
        return D2EM::getRepository( UserRememberTokenEntity::class)->getAllForFeList( $this->feParams, request()->user()->getId(), $id );
    }

    /**
     * @inheritdoc
     */
    protected function preDelete() : bool
    {
        if(  $session = D2EM::getRepository( SessionEntity::class )->findOneBy( [ "id" => $this->object->getSessionId() ] ) )  {
            D2EM::remove( $session );
            D2EM::flush();
        }

        return true;
    }
    /**
     * @inheritdoc
     */
    protected function postDeleteRedirect() : bool
    {
        // Delete remember me token
        Cookie::queue( Cookie::forget( Auth::getRecallerName() ) );
        return false;
    }



}
