<?php

namespace IXP\Console\Commands\Vagrant;

use Illuminate\Console\Command;
use IXP\Models\VlanInterface;

class GenerateClientRouterConfigurationsCommand extends Command
{
    protected $signature = 'vagrant:generate-client-router-configurations {--directory=/srv/clients}';

    protected $description = 'Vagrant development tool - generate client router configurations';

    public function handle(): void
    {
        // directory exists and writable?
        if( !is_dir($this->option('directory')) || !is_writable($this->option('directory'))) {
            $this->error("Directory path {$this->option('directory')} is not writable");
            exit(1);
        }

        $vlis = VlanInterface::where( 'rsclient', 1 )->whereIn( 'vlanid', [1,2])->get();

        $confNames = [];

        foreach( $vlis as $vli ) {

            // skip route servers, collector and as112
            if( in_array( $vli->virtualInterface->customer->autsys, [ 112, 65500, 65501 ] ) ) { continue; }





            $this->info( "Generating route server client for {$vli->virtualInterface->customer->name} / {$vli->vlan->name}" );

            $confName = "as{$vli->virtualInterface->customer->autsys}-"
                . strtolower($vli->virtualInterface->physicalInterfaces[0]->switchPort->switcher->infrastructureModel->shortname)
                . "{$vli->id}";

            $confNames[] = $confName;

            $confFile = $this->option('directory') . '/' . $confName . '.conf';

            file_put_contents(
                $confFile,
                view('vagrant/router-client', [ 'vli' => $vli, 'confName' => $confName ] )->render()
            );
        }

        file_put_contents(
        $this->option('directory').'/start-reload-clients.sh',
                view('vagrant/router-client-script', [
                    'directory' => $this->option('directory' ),
                    'confNames' => $confNames,
                ])->render()
        );

    }
}
