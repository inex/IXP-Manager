<?php namespace IXP\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider {

    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [

        'IXP\Events\Customer\BillingDetailsChanged' => [
            'IXP\Listeners\Customer\BillingDetailsChanged'
        ],

    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }

}
