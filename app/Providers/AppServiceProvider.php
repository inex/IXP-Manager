<?php namespace IXP\Providers;

use Illuminate\Support\ServiceProvider;

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
			$this->app->make('ZendFramework')->setOptions( [ 'config' => [
				['utils']['genurl']['host_mode']    => 'REPLACE',
				['utils']['genurl']['host_replace'] => config('identity.urls.forceUrl')
			]]);
			dd( $this->app->make('ZendFramework')->getOptions());
		}
		
		if( config('identity.urls.forceSchema') ) {
			URL::forceSchema(config('identity.urls.forceSchema'));
		}
	}
}
