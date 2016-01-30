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
        Route::get( '/grapher/test', function(){dd( 'test grapher');});
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

        foreach( $this->app['config']['grapher']['backend'] as $backend ) {
            $this->app->singleton( $this->app['config']['grapher']['providers'][$backend], function($app) use ($backend) {
                return new $this->app['config']['grapher']['providers'][$backend]( $app['config']['grapher']['backends'][ $backend ] );
            });
        }

        $this->commands( $this->commands );
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array_merge( ['IXP\Contracts\Grapher'], array_values( $this->app['config']['grapher']['providers'] ) );
    }


}
