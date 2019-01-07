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

use Illuminate\Support\ServiceProvider;

use Entities\{
    Customer    as CustomerEntity
};

use Auth, Cache, D2EM, View;

class IxpServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        app()->bind('DatabaseTokenRepository', function() {
            // your binding logic
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->resolving('view', function($view) {

            View::composer('*', function($view) {
                if( ( Auth::check() && Auth::getUser()->isSuperUser() ) || env( 'IXP_PHPUNIT_RUNNING', false ) ) {

                    // get an array of customer id => names
                    if( !( $customers = Cache::get( 'admin_home_customers' ) ) ) {
                        $customers = D2EM::getRepository( CustomerEntity::class )->getNames( true );
                        Cache::put( 'admin_home_customers', $customers, 3600 );
                    }

                    $view->with( 'customers', $customers );
                }

            });
        });
    }
}
