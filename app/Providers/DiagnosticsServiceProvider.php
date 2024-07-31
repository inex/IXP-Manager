<?php

namespace IXP\Providers;

/*
 * Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Route;
use Illuminate\Support\ServiceProvider;
use IXP\Models\User;
use IXP\Services\Diagnostics;

/**
 * Diagnostics Service Provider
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @author     Laszlo Kiss <laszlo@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Providers
 * @copyright  Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class DiagnosticsServiceProvider extends ServiceProvider
{
    protected $defer = true;


    /**
     * Bootstrap the application services.
     *
     * @return void
     *
     * @throws
     */
    public function boot(): void
    {
        Route::group([
            'middleware'    => config( 'google2fa.enabled' )
                ? [ 'web' , 'auth' , '2fa' , 'assert.privilege:' . User::AUTH_SUPERUSER ]
                : [ 'web' , 'auth',          'assert.privilege:' . User::AUTH_SUPERUSER ],
            'namespace' => 'IXP\Http\Controllers' ], function(){

            Route::get(  'customer/diagnostics/{customer}', 'DiagnosticsController@run')->name('diagnostics@run' );
        });

        // we have a few rendering functions we want to include here:
        // $this->app->make( Engine::class )->loadExtension( new GrapherRendererExtension(), [] );
    }

    /**
     * Register the application services.
     *
     * @return void|Diagnostics
     *
     * @throws
     */
    public function register()
    {
        $this->app->singleton(
            Diagnostics::class, function() {
            return new Diagnostics;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [ Diagnostics::class ];
    }
}