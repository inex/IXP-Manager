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

use Auth, Cache, D2EM, Log, Redirect, Route;

use Entities\{
    Contact             as ContactEntity,
    User                as UserEntity
};

use Illuminate\Http\{
    RedirectResponse,
    Request
};

use IXP\Utils\View\Alert\Alert;
use IXP\Utils\View\Alert\Container as AlertContainer;




/**
 * Contact Controller
 *
 * TEMPORARY CONTROLLER BEFORE THAT THE CONTACT MANAGEMENT IS REBUILT UNDER LARAVEL
 *
 * NAME CONTACTS TO AVOID CONFLICT WITH THE ACTUAL CONTACT MANAGEMENT UNDER ZEND
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   VlanInterface
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ContactsController extends Doctrine2Frontend {
    /**
     * The object being added / edited
     * @var ContactEntity
     */
    protected $object = null;

    /**
     * The http Request
     * @var Request
     */
    protected $request = null;

    /**
     * This function sets up the frontend controller
     */
    public function feInit(){
        $this->feParams         = (object)[
            'entity'            => ContactEntity::class,

            'pagetitle'         => 'Contact',

            'titleSingular'     => 'Contact',

            'readonly'          => 'false',
        ];
    }


    protected static function additionalRoutes( string $route_prefix ){
        Route::group( [ 'prefix' => $route_prefix ], function() use ( $route_prefix ) {
            Route::post(     'delete-user',                          'ContactsController@deleteUser'    )->name( $route_prefix . '@delete-user' );
        });
    }

    /**
     * The default routes for a Doctrine2Frontend class
     */
    public static function routes() {


        // add leading slash to class name for absolute resolution:
        $class = '\\' . get_called_class();
        $route_prefix = self::route_prefix();

        Route::group( [ 'prefix' => $route_prefix ], function() use ( $class, $route_prefix ) {
            Route::post( 'delete',      $class . '@delete'  )->name( $route_prefix . '@delete'  );
        });

        $class::additionalRoutes( $route_prefix );
    }

    /**
     * Provide array of rows for the list and view
     *
     * @param int $id The `id` of the row to load for `view`. `null` if `list`
     *
     * @return array
     */
    protected function listGetData( $id = null ) {
        return [];
    }





}