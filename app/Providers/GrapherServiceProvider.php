<?php

namespace IXP\Providers;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Route;

use Foil\Engine;

use Illuminate\Support\ServiceProvider;

use IXP\Exceptions\Services\Grapher\ConfigurationException;

use IXP\Console\Commands\Grapher\{
    EmailPortsWithCounts,
    EmailPortUtilisation,
    EmailTrafficDeltas,
    GenerateConfiguration,
    UploadStatsToDb
};

use IXP\Models\User;

use IXP\Services\Grapher;
use IXP\Services\Grapher\Renderer\Extensions\Grapher as GrapherRendererExtension;

/**
 * Grapher Service Provider
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Providers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class GrapherServiceProvider extends ServiceProvider
{
    protected $defer = false;

    protected $commands = [
        EmailPortsWithCounts::class,
        EmailPortUtilisation::class,
        EmailTrafficDeltas::class,
        GenerateConfiguration::class,
        UploadStatsToDb::class,
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     *
     * @throws
     */
    public function boot(): void
    {
        Route::group( ['middleware' => 'grapher', 'namespace' => 'IXP\Http\Controllers\Services', 'as' => 'grapher::', 'prefix' => 'grapher'  ], function(){
            Route::get( 'ixp',               'Grapher@ixp'               );
            Route::get( 'infrastructure',    'Grapher@infrastructure'    );
            Route::get( 'vlan',              'Grapher@vlan'              );
            Route::get( 'switch',            'Grapher@switch'            );
            Route::get( 'location',          'Grapher@location'          );
            Route::get( 'trunk',             'Grapher@trunk'             );
            Route::get( 'corebundle',        'Grapher@coreBundle'        );
            Route::get( 'physicalinterface', 'Grapher@physicalInterface' ); // individual member port
            Route::get( 'virtualinterface',  'Grapher@virtualInterface'  ); // member LAG (of physint's)
            Route::get( 'customer',          'Grapher@customer'          ); // member agg over all physint's
            Route::get( 'vlaninterface',     'Grapher@vlanInterface'     ); // member vlan interface
            Route::get( 'p2p',               'Grapher@p2p'               ); // member vlan interface
            Route::get( 'latency',           'Grapher@latency'           );
        });

        Route::group(['middleware' => [ 'api/v4', 'assert.privilege:' . User::AUTH_SUPERUSER ],
                'namespace' => 'IXP\Http\Controllers\Services', 'as' => 'grapher::' ], function(){

            Route::get(  'api/v4/grapher/mrtg-config', 'Grapher\Api@generateConfiguration' );
            Route::get(  'api/v4/grapher/config',      'Grapher\Api@generateConfiguration' );
            Route::post( 'api/v4/grapher/config',      'Grapher\Api@generateConfiguration' );
        });
        
        // we have a few rendering functions we want to include here:
        $this->app->make( Engine::class )->loadExtension( new GrapherRendererExtension(), [] );
    }

    /**
     * Register the application services.
     *
     * @return void|Grapher
     *
     * @throws
     */
    public function register()
    {
        // make sure the config is okay:
        foreach( $this->app[ 'config' ][ 'grapher' ][ 'backend' ] as $backend ) {
            if( !isset( $this->app[ 'config' ][ 'grapher' ][ 'providers' ][ $backend ] ) ) {
                throw new ConfigurationException( "Requested grapher backend ({$backend}) is not available" );
            }
        }

        $this->app->singleton(
            Grapher::class, function() {
            return new Grapher;
        });

        $this->commands( $this->commands );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [ Grapher::class ];
    }
}