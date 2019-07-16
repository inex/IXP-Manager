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

use Auth, D2EM, Route;

use Entities\{
    CustomerToUser as CustomerToUserEntity,
    User as UserEntity,
    UserLoginHistory as UserLoginHistoryEntity,
};

use Illuminate\Http\Request;
use Illuminate\View\View;


/**
 * Login History Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   VlanInterface
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class LoginHistoryController extends Doctrine2Frontend {
    /**
     * The object being added / edited
     * @var UserLoginHistoryEntity
     */
    protected $object = null;

    /**
     * This function sets up the frontend controller
     */
    public function feInit(){

        $this->feParams         = (object)[
            'entity'            => UserLoginHistoryEntity::class,

            'pagetitle'         => 'Login History',

            'titleSingular'     => 'Login History',
            'nameSingular'      => 'a Login History',

            'listOrderBy'       => 'last_login_date',
            'listOrderByDir'    => 'DESC',

            'readonly'       => 'true',

            'viewFolderName'    => 'login-history',

            'listColumns'    => [

                'username'          => [ 'title' => 'Username' ],

                'email'             => [ 'title' => 'Email' ],

                'cust_name'  => [
                    'title'      => 'Customer',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'customer',
                    'action'     => 'overview',
                    'idField'    => 'cust_id'
                ],

                'last_login_date'         => [
                    'title'     => 'Last Login',
                    'type'      => self::$FE_COL_TYPES[ 'DATETIME' ]
                ]
            ]
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = $this->feParams->listColumns;

        // phpunit / artisan trips up here without the cli test:
        if( php_sapi_name() !== 'cli' ) {

            // custom access controls:
            switch( Auth::check() ? Auth::user()->getPrivs() : UserEntity::AUTH_PUBLIC ) {
                case UserEntity::AUTH_SUPERUSER:
                    break;

                default:
                    $this->unauthorized();
            }
        }
    }

    /**
     * @inheritdoc
     */
    public static function routes() {
        Route::group( [ 'prefix' => 'login-history' ], function() {
            Route::get(  'list',                'LoginHistoryController@list'   )->name( 'login-history@list'   );
            Route::get(  'view/{id}',           'LoginHistoryController@view'   )->name( 'login-history@view'   );
        });
    }


    /**
     * Provide array of rows for the list and view
     *
     * @param int $id The `id` of the row to load for `view`. `null` if `list`
     * @return array
     */
    protected function listGetData( $id = null ) {
        return D2EM::getRepository( UserEntity::class)->getLastLoginsForFeList( $this->feParams );
    }


    /**
     * Display the login history list for a user/customer
     *
     * @inheritdoc
     */
    public function view( Request $r, $id ): View
    {
        /** @var $user UserEntity */
        if( !( $user = D2EM::getRepository( UserEntity::class )->find( $id ) ) ) {
            abort(404 );
        }

        return view( 'login-history/view' )->with([
            'histories'                 => D2EM::getRepository( UserLoginHistoryEntity::class)->getAllForFeList( $user->getId(), $r->input( 'limit', 0 ) ),
            'user'                      => $user,
        ]);
    }
}
