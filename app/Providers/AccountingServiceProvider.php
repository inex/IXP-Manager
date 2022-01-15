<?php

namespace IXP\Providers;

/*
 * Copyright (C) 2009 - 2022 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use IXP\Services\Accounting;

/**
 * Looking Glass Service Provider
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP\Providers
 * @copyright  Copyright (C) 2009 - 2022 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class AccountingServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {


        Route::group( [ 'middleware' => 'accounting', 'namespace' => 'IXP\Http\Controllers\Services',
                        'as' => 'accounting::', 'prefix' => 'accounting' ], function() {

            Route::get( '',                                                 'Accounting@index'             )->name('index');
        });

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton( Accounting::class, function( $app ) {
            return new LookingGlass;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [ Acc::class ];
    }
}