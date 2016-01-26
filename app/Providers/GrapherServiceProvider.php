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
use IXP\Services\Grapher\ConfigurationException;
use Config;

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

    protected $defer = true;


    protected $commands = [
        // 'IXP\Console\Commands\Helpdesk\UpdateOrganisations'
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton( 'IXP\Contracts\Grapher', function($app)
        {
            $backend = $app['config']['grapher']['backend'];

            switch( $backend ) {
                case 'none':
                case 'dummy':
                    return new \IXP\Services\Grapher\Dummy( $app['config']['grapher']['backends'][ $backend ] );
                    break;

                case 'mrtg':
                    return new \IXP\Services\Grapher\Mrtg( $app['config']['grapher']['backends'][ $backend ] );
                    break;

                case 'sflow':
                    return new \IXP\Services\Grapher\Sflow( $app['config']['grapher']['backends'][ $backend ] );
                    break;

                default:
                    throw new ConfigurationException( 'Invalid, no or unimplemented graphing backend requested' );
            }
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
        return ['IXP\Contracts\Grapher'];
    }


}
