<?php namespace IXP\Providers;

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

            View::composer('*', function($view) {

                // FIXME @yannrobin - need to remove this once ZF is gone.
                if(app('request')->route() != null) {
                    $action = app('request')->route()->getAction();
                    if( isset( $action['controller'] ) ) {
                        $controller = class_basename( $action[ 'controller' ] );
                        $subFolder = isset( $action[ 'subFolder' ] ) ? $action[ 'subFolder' ] : '';
                        list( $controller, $action ) = explode( '@', $controller );
                    } else {
                        $subFolder  = '';
                        $controller = '';
                    }
                } else {
                    $action     = null;
                    $controller = null;
                    $subFolder  = null;
                }

                $switched_user_from = (isset($_SESSION['Application']['switched_user_from']))? true : false;
                $view->with('subFolder' , $subFolder )->with('controller' , $controller)->with('action',$action)->with('switched_user_from', $switched_user_from);

            });

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
