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
        'IXP\Events\Layer2Address\Added' => [
            'IXP\Listeners\Layer2Address\Changed',
        ],
        'IXP\Events\Layer2Address\Deleted' => [
            'IXP\Listeners\Layer2Address\Changed',
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
