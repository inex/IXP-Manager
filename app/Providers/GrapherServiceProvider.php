<?php namespace IXP\Providers;

/*
 * Copyright (C) 2009-2016 Internet Neutral Exchange Association Limited.
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

use IXP\Exceptions\Services\Grapher\ConfigurationException;
use IXP\Services\Grapher\Renderer\Extensions\Grapher as GrapherRendererExtension;

use Cache;
use Config;
use Route;

/**
 * Grapher Service Provider
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Providers
 * @copyright  Copyright (c) 2009 - 2016, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class GrapherServiceProvider extends ServiceProvider {

    protected $defer = false;


    protected $commands = [
        'IXP\Console\Commands\Grapher\GenerateConfiguration'
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Route::group(['namespace' => 'IXP\Http\Controllers\Services', 'as' => 'grapher::', 'prefix' => 'grapher', 'middleware' => 'grapher' ], function(){
            Route::get( 'ixp',            'Grapher@ixp'            );
            Route::get( 'infrastructure', 'Grapher@infrastructure' );
            Route::get( 'vlan',           'Grapher@vlan'           );
            Route::get( 'switch',         'Grapher@switch'         );
        });

        // we have a few rendering functions we want to include here:
        $this->app->make('League\Plates\Engine')->loadExtension(new GrapherRendererExtension());
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // make sure the config is okay:
        foreach( $this->app['config']['grapher']['backend'] as $backend ) {
            if( !isset( $this->app['config']['grapher']['providers'][$backend] ) ) {
                throw new ConfigurationException( "Requested grapher backend ({$backend}) is not available" );
            }
        }

        $this->app->singleton( 'IXP\Services\Grapher', function($app) {
            return new \IXP\Services\Grapher;
        });

        $this->commands( $this->commands );
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [ 'IXP\Services\Grapher' ];
    }


}
