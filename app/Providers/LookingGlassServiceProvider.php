<?php

declare(strict_types=1);

namespace IXP\Providers;

/*
 * Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use IXP\Exceptions\Services\LookingGlass\ConfigurationException;
use Cache;
use Route;

/**
 * Looking Glass Service Provider
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Providers
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class LookingGlassServiceProvider extends ServiceProvider {

    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Route::group( [ 'middleware' => 'lookingglass', 'namespace' => 'IXP\Http\Controllers\Services',
                        'as' => 'lg', 'prefix' => 'lg' ], function() {
                            
            Route::get( '{handle}',         'LookingGlass@bgpSummary' );
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
