<?php

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace IXP\Providers;

use Auth, Former, Horizon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use IXP\Models\{
    DocstoreCustomerDirectory,
    DocstoreDirectory
};
use IXP\Observers\DocstoreCustomerDirectoryObserver;
use IXP\Observers\DocstoreDirectoryObserver;
use IXP\Utils\Former\Framework\TwitterBootstrap4;

/**
 * App Service Provider
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Providers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        view()->composer( [ 'telescope::layout' ], function ( $view ) {
            $view->with( 'telescopeScriptVariables', [
                'path'      => config( 'telescope.url_path' ),
                'timezone'  => config('app.timezone'),
                'recording' => !cache('telescope:pause-recording'),
            ]);
        });

        Former::framework( TwitterBootstrap4::class );
        // observer for docstore directory
        DocstoreDirectory::observe( DocstoreDirectoryObserver::class );
        // observer for docstore customer directory
        DocstoreCustomerDirectory::observe( DocstoreCustomerDirectoryObserver::class );

        Paginator::useBootstrap();
    }

    /**
     * Register any application services.
     *
     * This service provider is a great spot to register your various container
     * bindings with the application. As you can see, we are registering our
     * "Registrar" implementation here. You can add your own bindings too!
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(
            'Illuminate\Contracts\Auth\Registrar',
            'IXP\Services\Registrar'
        );
    }
}
