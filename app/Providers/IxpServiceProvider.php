<?php

namespace IXP\Providers;

use Illuminate\Support\ServiceProvider;

use Entities\{
    Customer    as CustomerEntity
};

use Auth, Cache, D2EM, View;

class IxpServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        app()->bind('DatabaseTokenRepository', function() {
            // your binding logic
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->resolving('view', function($view) {

            View::composer('*', function($view) {
                if( ( Auth::check() && Auth::getUser()->isSuperUser() ) || env( 'IXP_PHPUNIT_RUNNING', false ) ) {

                    // get an array of customer id => names
                    if( !( $customers = Cache::get( 'admin_home_customers' ) ) ) {
                        $customers = D2EM::getRepository( CustomerEntity::class )->getNames( true );
                        Cache::put( 'admin_home_customers', $customers, 3600 );
                    }

                    $view->with( 'customers', $customers );
                }

            });
        });
    }
}
