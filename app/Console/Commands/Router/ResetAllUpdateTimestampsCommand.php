<?php

namespace IXP\Console\Commands\Router;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use IXP\Models\Router;

class ResetAllUpdateTimestampsCommand extends Command
{
    protected $signature = 'router:reset-all-update-timestamps';

    protected $description = 'Resets all router update timestamps to null';

    public function handle(): void
    {
        DB::table('routers')->update( [
            'last_update_started' => null,
            'last_updated' => null,
        ]);
    }
}
