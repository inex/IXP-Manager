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

// based on: https://github.com/franzliedke/laravel-plates

use Illuminate\Support\ServiceProvider;
use IXP\Services\FoilEngine as Engine;

use IXP\Utils\Foil\Extensions\Bird as BirdFoilExtensions;
use IXP\Utils\Foil\Extensions\IXP  as IXPFoilExtensions;

use View;

class FoilServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $app = $this->app;

        $app->singleton('Foil\Engine', function () use ($app) {

            $engine = \Foil\engine([
                'folders'          => config('view.paths'),
                'ext'              => 'foil.php',
                'autoescape'       => false,                    // enabling this is a serious performance hit
                                                                // e.g. >30secs to generate INEX's MRTG config
                                                                // vs. x without it
                'strict_variables' => true,                     // enabled as using undef'd vars is a programming error
                'alias'            => 't'                       // $t is now shorthand for $this
            ]);

            return $engine;
        });

        $app->resolving('view', function($view) use ($app) {

            $engine = new Engine($app->make('Foil\Engine'));

            // we have a few rendering functions we want to include here:
            $engine->engine()->loadExtension( new IXPFoilExtensions(), [ 'alerts' ] );
            $engine->engine()->loadExtension( new BirdFoilExtensions(), [] );


            $view->addExtension('foil.php', 'foil', function() use ($app, $engine) {
                return $engine;
            });

            $view->addExtension('foil.js', 'foil', function() use ($app, $engine) {
                return $engine;
            });
        });
    }

}
