<?php
/*
 * Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee.
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

declare(strict_types=1);

namespace IXP\Providers;


use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use IXP\Models\User;

class ValidationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::group( [
            'middleware'    => config( 'google2fa.enabled' )
                ? [ 'web' , 'auth' , '2fa' , 'assert.privilege:' . User::AUTH_SUPERUSER ]
                : [ 'web' , 'auth',          'assert.privilege:' . User::AUTH_SUPERUSER ],
            'namespace' => 'IXP\Http\Controllers' ], function() {

            Route::get(  'admin/validation/start',       'ValidationController@startForm'   )->name( 'validation@start' );
            Route::post( 'admin/validation/start',       'ValidationController@startSubmit' )->name( 'validation@start-submit' );
            Route::get( 'admin/validation/result/{id}', 'ValidationController@apiResults'  )->name( 'validation@api-results' );
            Route::get(  'admin/validation/view/{id}',   'ValidationController@view'        )->name( 'validation@view'  );
            Route::get(  'admin/validation/list',        'ValidationController@list'        )->name( 'validation@list'  );
        });
    }

    public function register(): void
    {

    }
}