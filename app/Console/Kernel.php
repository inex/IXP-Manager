<?php namespace IXP\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use IXP\Console\Commands\Audit\PostSpeeds;
use IXP\Console\Commands\Upgrade\RouterImport;

class Kernel extends ConsoleKernel {

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [

        \IXP\Console\Commands\Audit\PortSpeeds::class,

        \IXP\Console\Commands\Irrdb\UpdateAsnDb::class,
        \IXP\Console\Commands\Irrdb\UpdatePrefixDb::class,

        \IXP\Console\Commands\Router\GenerateConfiguration::class,

        \IXP\Console\Commands\Utils\ConvertPlaintextPasswords::class,
        \IXP\Console\Commands\Utils\UpdateOuiDatabase::class,

        \IXP\Console\Commands\Upgrade\MigrateL2Addresses::class,
        \IXP\Console\Commands\Upgrade\MrtgTrunkConfig::class,
        \IXP\Console\Commands\Upgrade\RouterImport::class,

        \IXP\Console\Commands\Utils\Export\JsonSchema\Post::class,

        \IXP\Console\Commands\Switches\SnmpPoll::class,

        \IXP\Console\Commands\Rir\GenerateObject::class,

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
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
