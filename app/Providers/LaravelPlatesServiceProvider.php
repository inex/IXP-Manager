<?php namespace IXP\Providers;

// based on: https://github.com/franzliedke/laravel-plates

use Illuminate\Support\ServiceProvider;
use League\Plates\Engine as PlatesEngine;
use IXP\Services\PlatesEngine as Engine;

class LaravelPlatesServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $app = $this->app;

        $app->singleton('League\Plates\Engine', function () use ($app) {
            $paths = config('view.paths');
            $path = array_shift( $paths );
            $engine = new PlatesEngine($path, 'plates.php');
            
    		if( count($paths) ) {
    			$engine->addFolder( 'skin', array_shift( $paths ), true );
    		}
    		
            return $engine;
        });

        $app->resolving('view', function($view) use ($app) {
            $view->addExtension('plates.php', 'plates', function() use ($app) {
                return new Engine($app->make('League\Plates\Engine'));
            });
        });
    }

}
