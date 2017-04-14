<?php namespace IXP\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'IXP\Console\Commands\Irrdb\UpdateAsnDb',
        'IXP\Console\Commands\Irrdb\UpdatePrefixDb',

        'IXP\Console\Commands\Router\GenerateConfiguration',

        'IXP\Console\Commands\Upgrade\MrtgTrunkConfig',

        \IXP\Console\Commands\Utils\Export\JsonSchema\Post::class,
        \IXP\Console\Commands\Utils\UpdateOuiDatabase::class,
        \IXP\Console\Commands\Upgrade\MigrateL2Addresses::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule) {
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
