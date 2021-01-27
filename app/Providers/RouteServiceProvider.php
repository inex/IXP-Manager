<?php

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace IXP\Providers;

use Entities\User as UserEntity;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider {

    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'IXP\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        //
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapWebRoutes();
        $this->mapWebDoctrine2FrontendRoutes();
        $this->mapWebAuthRoutes();
        $this->mapWebAuthSuperuserRoutes();
        $this->mapApiExternalAuthSuperuserRoutes();
        $this->mapApiV4Routes();
        $this->mapApiV4AuthRoutes();
        $this->mapApiAuthSuperuserRoutes();
        $this->mapPublicApiRoutes();

        // aliases that need to be deprecated:
        require base_path('routes/apiv1-aliases.php');

    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/web.php');
        });
    }

    /**
     * Define the "web" routes using Doctrine2Frontend for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebDoctrine2FrontendRoutes()
    {
        Route::group([
            'middleware' => config( 'google2fa.enabled' ) ? ['d2frontend', '2fa'] : ['d2frontend'],
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/web-doctrine2frontend.php');
        });
    }

    /**
     * Define the "web" routes for the application **WHICH REQUIRE ANY AUTHENTICATION**.
     *
     * These routes all receive session state, CSRF protection, etc and require an authenticated user.
     *
     * @return void
     */
    protected function mapWebAuthRoutes()
    {
        Route::group([
            'middleware' => config( 'google2fa.enabled' ) ? [ 'web', 'auth', '2fa'  ] : [ 'web', 'auth' ],
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/web-auth.php');
        });
    }

    /**
     * Define the "web" routes for the application **WHICH REQUIRE AUTHENTICATION**.
     *
     * These routes all receive session state, CSRF protection, etc and require an authenticated user.
     *
     * @return void
     */
    protected function mapWebAuthSuperuserRoutes()
    {
        Route::group([
                         'middleware' => config( 'google2fa.enabled' ) ? [ 'web' , 'auth' , '2fa' , 'assert.privilege:' . UserEntity::AUTH_SUPERUSER ] : [ 'web' , 'auth', 'assert.privilege:' . UserEntity::AUTH_SUPERUSER ],
                         'namespace' => $this->namespace,
                     ], function ($router) {
            require base_path('routes/web-auth-superuser.php');
        });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiV4Routes()
    {
        Route::group([
            'middleware' => [ 'web', 'public/api/v4' ],
            'namespace' => $this->namespace . '\\Api\\V4',
            'prefix' => 'api/v4',
        ], function ($router) {

//            if( class_exists( "\Debugbar" ) ) {
//                \Debugbar::disable();
//            }

            require base_path('routes/apiv4.php');
        });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiV4AuthRoutes()
    {
        Route::group([
            'middleware' => [ 'web', 'api/v4', 'auth' ],
            'namespace' => $this->namespace . '\\Api\\V4',
            'prefix' => 'api/v4',
        ], function ($router) {
            if( class_exists( "\Debugbar" ) ) {
                \Debugbar::disable();
            }

            require base_path('routes/apiv4-auth.php');
        });
    }


    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiAuthSuperuserRoutes()
    {
        Route::group([
             'middleware' => [
                 'web',
                 'api/v4',
                 'assert.privilege:' . UserEntity::AUTH_SUPERUSER
             ],
             'namespace' => $this->namespace . '\\Api\\V4',
             'prefix' => 'api/v4',
        ], function ($router) {

//            if( class_exists( "\Debugbar" ) ) {
//                \Debugbar::disable();
//            }

            require base_path('routes/apiv4-auth-superuser.php');
        });
    }


    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiExternalAuthSuperuserRoutes()
    {
        Route::group([
            'middleware' => [
                'api/v4',
                'assert.privilege:' . UserEntity::AUTH_SUPERUSER
            ],
            'namespace' => $this->namespace . '\\Api\\V4',
            'prefix' => 'api/v4',
        ], function ($router) {
            require base_path('routes/apiv4-ext-auth-superuser.php');
        });
    }


    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapPublicApiRoutes()
    {
        Route::group([
            'middleware' => 'publicapi',
            'namespace' => $this->namespace . '\\Api\\V4',
            'prefix' => 'api/v4/public',
        ], function ($router) {
            require base_path('routes/publicapi.php');
        });
    }
}
