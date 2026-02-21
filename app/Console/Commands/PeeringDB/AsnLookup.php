<?php

namespace IXP\Console\Commands\PeeringDB;

use Illuminate\Console\Command;
use IXP\Services\PeeringDb;

class AsnLookup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'peeringdb:asn-lookup {asn}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lookup an ASN in PeeringDB';

    /**
     * Execute the console command.
     *
     * @return int
     *
     * @psalm-return -1|0
     */
    public function handle(): int
    {
        $pdb = app()->make( PeeringDb::class );

        foreach( $pdb->warnOnBadAuthMethods() as $w ) {
            $this->warn( $w );
        }

        if( $net = $pdb->getNetworkByAsn( (integer)$this->argument('asn') ) ) {
            echo $pdb->netAsAscii($net);
            return 0;
        }

        if( $pdb->status === 404 ) {
            $this->line( $pdb->error );
            return 0;
        }

        $this->error( $pdb->error );
        return -1;
    }
}
