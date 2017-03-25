<?php namespace IXP\Providers;

use Former;
use Illuminate\Support\ServiceProvider;
use IXP\Utils\Former\Framework\TwitterBootstrap3;
use URL;

class AppServiceProvider extends ServiceProvider {

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupUrls();

        view()->composer('layouts.master', function($view)
        {
            $view->with('controllerAction' , app('request')->route()->getAction()['as']);
        });

        Former::framework( TwitterBootstrap3::class );

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
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        $this->app->bind(
            'Illuminate\Contracts\Auth\Registrar',
            'IXP\Services\Registrar'
        );
    }


    /**
     * We need to allow forcing URLs when IXP Manager runs behind a proxy.
     */
    private function setupUrls() {
        if( config('identity.urls.forceUrl') ) {
            URL::forceRootUrl(config('identity.urls.forceUrl'));
        }

        if( config('identity.urls.forceSchema') ) {
            URL::forceScheme(config('identity.urls.forceSchema'));
        }
    }
}
