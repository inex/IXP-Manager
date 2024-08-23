<?php

namespace IXP\Console\Commands\Vagrant;

use Illuminate\Console\Command;
use IXP\Models\Router;
use IXP\Models\VlanInterface;

class GenerateBirdseyeConfigurationsCommand extends Command
{
    protected $signature = 'vagrant:generate-birdseye-configurations {--directory=/srv/birdseye}';

    protected $description = 'Vagrant development tool - generate birdseye configurations for all routers';

    public function handle(): void
    {
        // directory exists and writable?
        if( !is_dir( $this->option( 'directory' ) ) || !is_writable( $this->option( 'directory' ) ) ) {
            $this->error( "Directory path {$this->option('directory')} is not writable" );
            exit( 1 );
        }

        foreach( Router::get() as $router ) {

            file_put_contents( $this->option( 'directory' ) . '/birdseye-' . $router->handle . '.env',

                "#
# Bird's Eye - Vagrant generated configuration

BIRDC=\"/usr/bin/sudo /srv/birdseye/bin/birdc -2 -s /var/run/bird/bird-{$router->handle}.ctl\"
CACHE_DRIVER=array
MAX_ROUTES=100000
"

            );

        }
    }
}

