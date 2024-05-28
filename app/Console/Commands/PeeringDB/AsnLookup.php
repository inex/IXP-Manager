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
     */
    public function handle()
    {
        // are we set up for auth?
        if( !config('ixp_api.peeringDB.api-key') ) {
            $this->warn(
                str_repeat( '-', 70 )
                . "\nNo API key defined in .env for PeeringDB.\n\n"
                . "See https://docs.peeringdb.com/howto/api_keys/ and set IXP_API_PEERING_DB_API_KEY in .env.\n\n"
                . "Without an API key, only public information will be returned and PeeringDB request throttling will apply.\n"
                . str_repeat( '-', 70 ) . "\n\n"
            );
        }

        if( config('ixp_api.peeringDB.username') && config('ixp_api.peeringDB.password') ) {
            $this->warn(
                str_repeat( '-', 70 ) . "\n"
                . "Username and password are set in .env for PeeringDB. This is deprecated and they should be replaced with an API key.\n\n"
                . "See https://docs.peeringdb.com/howto/api_keys/ and set IXP_API_PEERING_DB_API_KEY in .env.\n"
                . str_repeat( '-', 70 ) . "\n\n"
            );
        }

        $pdb = app()->make( PeeringDb::class );

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
