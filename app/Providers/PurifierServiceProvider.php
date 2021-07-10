<?php

namespace IXP\Providers;

// Based on https://github.com/LukeTowers/Purifier and embedded as this package
// is stale and does not support PHP 7.4. MIT license per 20210304 (BOD).

use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;

use IXP\Services\Purifier;

class PurifierServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Boot the service provider.
     *
     * @return null
     */
    public function boot()
    {
        $this->setupConfig();
    }

    /**
     * Setup the config.
     *
     * @return void
     */
    protected function setupConfig()
    {
        $source = config_path( 'purifier.php' );
        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('purifier.php')]);
        }
        $this->mergeConfigFrom($source, 'purifier');
    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('purifier', function (Container $app) {
            return new Purifier($app['files'], $app['config']);
        });

        $this->app->alias('purifier', Purifier::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['purifier'];
    }
}
