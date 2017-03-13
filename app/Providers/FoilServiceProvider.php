<?php namespace IXP\Providers;

// based on: https://github.com/franzliedke/laravel-plates

use Illuminate\Support\ServiceProvider;
use IXP\Services\FoilEngine as Engine;

use IXP\Utils\Foil\Extensions\IXP as IXPFoilExtensions;

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

            View::composer('*', function($view) {
                if(app('request')->route() != null) {
                    $action = app('request')->route()->getAction();
                    $controller = class_basename($action['controller']);
                    list($controller, $action) = explode('@', $controller);
                } else {
                    $action = null;
                    $controller = null;
                }

                $switched_user_from = (isset($_SESSION['Application']['switched_user_from']))? true : false;
                $view->with('controller' , $controller)->with('action',$action)->with('switched_user_from', $switched_user_from);
            });

            // we have a few rendering functions we want to include here:
            $engine->engine()->loadExtension( new IXPFoilExtensions(), [] );

            $view->addExtension('foil.php', 'foil', function() use ($app, $engine) {
                return $engine;
            });

            $view->addExtension('foil.js', 'foil', function() use ($app, $engine) {
                return $engine;
            });
        });
    }

}
