<?php

declare(strict_types=1);

namespace IXP\Providers;

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



use Illuminate\Support\ServiceProvider;
use Route;

/**
 * Looking Glass Service Provider
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Providers
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class LookingGlassServiceProvider extends ServiceProvider {

    protected $defer = false;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if( config('ixp_fe.frontend.disabled.lg' ) ) {
            return;
        }
        
        Route::group( [ 'middleware' => 'lookingglass', 'namespace' => 'IXP\Http\Controllers\Services',
                        'as' => 'lg::', 'prefix' => 'lg' ], function() {

            Route::get( '',                                         'LookingGlass@index'             )->name('index');

            Route::get( '{handle}',                                 'LookingGlass@bgpSummary'        )->name( 'bgp-sum' );

            Route::get( '{handle}/routes/table/{table}',            'LookingGlass@routesForTable'    );
            Route::get( '{handle}/routes/protocol/{protocol}',      'LookingGlass@routesForProtocol' );
            Route::get( '{handle}/routes/export/{protocol}',        'LookingGlass@routesForExport'   );

            Route::get( '{handle}/route-search',                           'LookingGlass@routeSearch'       );
            Route::get( '{handle}/route/{net}/{mask}/protocol/{protocol}', 'LookingGlass@routeProtocol'     );
            Route::get( '{handle}/route/{net}/{mask}/table/{table}',       'LookingGlass@routeTable'        );

        });

        Route::group( [ 'middleware' => 'lookingglass', 'namespace' => 'IXP\Http\Controllers\Services',
            'as' => 'lg-api::', 'prefix' => 'api/v4/lg' ], function() {

            Route::get( '{handle}/status',      'LookingGlass@status'        )->name('status');
            Route::get( '{handle}/bgp-summary', 'LookingGlass@bgpSummaryApi' )->name('bgp-sum');
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton( 'IXP\Services\LookingGlass', function($app) {
            return new \IXP\Services\LookingGlass;
        });
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [ 'IXP\Services\LookingGlass' ];
    }


}
