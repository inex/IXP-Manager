<?php

namespace IXP\Http\Controllers;

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

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

use Illuminate\View\View;

use IXP\Models\{
    CustomerToUser,
    User,
    UserLoginHistory
};

use IXP\Utils\Http\Controllers\Frontend\EloquentController;

/**
 * Login History Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class LoginHistoryController extends EloquentController
{
    /**
     * The object being created / edited
     *
     * @var UserLoginHistory
     */
    protected $object = null;

    /**
     * This function sets up the frontend controller
     */
    public function feInit(): void
    {
        $this->feParams         = (object)[
            'model'             => UserLoginHistory::class,
            'pagetitle'         => 'Login History',
            'titleSingular'     => 'Login History',
            'nameSingular'      => 'a Login History',
            'listOrderBy'       => 'last_login_date',
            'listOrderByDir'    => 'DESC',
            'readonly'          => 'true',
            'viewFolderName'    => 'login-history',
            'listColumns'    => [
                'username'          =>  'Username',
                'email'             =>  'Email',
                'cust_name'  => [
                    'title'      => 'Customer',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'customer',
                    'action'     => 'overview',
                    'idField'    => 'cust_id'
                ],
                'last_login_via'    => 'Via',
                'last_login_date'         => [
                    'title'     => 'Last Login',
                    'type'      => self::$FE_COL_TYPES[ 'DATETIME' ]
                ]
            ]
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = $this->feParams->listColumns;

        // phpunit / artisan trips up here without the cli test:
        if( PHP_SAPI !== 'cli' ) {
            // custom access controls:
            switch( Auth::check() ? Auth::getUser()->privs() : User::AUTH_PUBLIC ) {
                case User::AUTH_SUPERUSER:
                    break;
                default:
                    $this->unauthorized();
            }
        }
    }

    /**
     * @inheritdoc
     */
    public static function routes(): void
    {
        Route::group( [ 'prefix' => 'login-history' ], function() {
            Route::get(  'list',                'LoginHistoryController@list'   )->name( 'login-history@list'   );
            Route::get(  'view/{id}',           'LoginHistoryController@view'   )->name( 'login-history@view'   );
        });
    }

    /**
     * Provide array of rows for the list and view
     *
     * @param int|null $id The `id` of the row to load for `view`. `null` if `list`
     *
     * @return array
     */
    protected function listGetData( ?int $id = null ): array
    {
        $feParams = $this->feParams;
        return CustomerToUser::select( [
            'customer_to_users.last_login_date AS last_login_date',
            'customer_to_users.last_login_via AS last_login_via',
            'customer_to_users.id AS AS c2u_id',
            'user.id AS id',
            'user.username AS username',
            'user.email AS email',
            'cust.id AS cust_id',
            'cust.name AS cust_name'
        ] )
        ->join( 'user', 'user.id', 'customer_to_users.user_id' )
        ->join( 'cust', 'cust.id', 'customer_to_users.customer_id' )
        ->when( $feParams->listOrderBy , function( Builder $q, $orderby ) use ( $feParams )  {
            return $q->orderBy( $orderby, $feParams->listOrderByDir ?? 'ASC');
        })->get()->toArray();
    }

    /**
     * Display the login history list for a user/customer
     *
     * @param Request   $r
     * @param int       $id
     *
     * @return View
     */
    public function view( Request $r, int $id ): View
    {
        $u = User::findOrFail( $id );

        $limit = $r->limit ?? 0;
        return view( 'login-history/view' )->with( [
            'user'          => $u,
            'histories'     => UserLoginHistory::select( [ 'user_logins.*', 'user.id AS user_id', 'cust.name AS cust_name' ] )
                ->leftJoin( 'customer_to_users', 'customer_to_users.id', 'user_logins.customer_to_user_id' )
                ->leftJoin( 'cust', 'cust.id', 'customer_to_users.customer_id' )
                ->leftJoin( 'user', 'user.id', 'customer_to_users.user_id' )
                ->when( $u->id , function( Builder $q, $userid ) {
                    return $q->where( 'user.id', $userid );
                })
                ->when( $limit > 0 , function( Builder $q ) use( $limit ) {
                    return $q->limit( $limit );
                })
                ->orderByDesc( 'at' )
                ->get()->toArray(),
        ] );
    }
}