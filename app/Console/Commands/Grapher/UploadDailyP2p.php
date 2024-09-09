<?php

namespace IXP\Console\Commands\Grapher;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use IXP\Console\Commands\Command;
use IXP\Models\Aggregators\VlanInterfaceAggregator;
use IXP\Models\Customer;
use IXP\Models\VlanInterface;
use Log;

class UploadDailyP2p extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grapher:upload-daily-p2p 
                    {day : target day in YYY-MM-DD format}
                    {--customer-id= : Customer ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate and save daily p2p traffic stats between members';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if( !preg_match( "/\d\d\d\d\-\d\d\-\d\d", $this->argument('day') ) ) {
            $this->error("Invalid day parameter - expected format is " . now()->subDay()->format('Y-m-d') );
            return -1;
        }

        $start     = Carbon::parse( $this->argument('day') . ' 00:00:00' );
        $end       = $start->copy()->endOfDay();
        $startTime = microtime(true);


        Customer::currentActive(true,true,true)
            ->when( $this->option('customer-id'), function (Builder $query, string $cid) {
                $query->where('id', $cid);
            })
            ->each( function( Customer $c ) use ( $start, $end, $startTime ) {

                $itertime = microtime(true);

                if($this->isVerbosityNormal()) {
                    $this->info("Processing {$c->name} for " . $start->format('Y-m-d'));
                }

                $stats = [];

                foreach($c->virtualinterfaces as $vi) {

                    /** @var VlanInterface $svli */
                    foreach($vi->vlaninterfaces as $svli) {

                        if(!$svli->vlan->export_to_ixf) { continue; }

                        foreach([4,6] as $protocol) {

                            $fnIpEnabled = "ipv{$protocol}enabled";
                            if( !$svli->$fnIpEnabled ) { continue; }


                            /** @var VlanInterface $dvli */
                            foreach( VlanInterfaceAggregator::forVlan( $svli->vlan, $protocol ) as $dvli ) {

                                // skip if it's this customer's own vlan interface or another of their own connections
                                if( $svli->id === $dvli->id || $c->id == $dvli->virtualInterface->custid ) { continue; }

                                if($this->isVerbosityVeryVerbose() ) {
                                    $this->line( "\t- $svli->vlan->name ipv$protocol with " . $dvli->virtualInterface->customer->name );
                                }

                                $peerId = $dvli->virtualInterface->custid;
                                if(!isset($stats[$peerId])) {
                                    $stats[$peerId] = [
                                        'ipv4_total_in' => 0,
                                        'ipv4_total_out' => 0,
                                        'ipv6_total_in' => 0,
                                        'ipv6_total_out' => 0,
                                        'ipv4_max_in' => 0,
                                        'ipv4_max_out' => 0,
                                        'ipv6_max_in' => 0,
                                        'ipv6_max_out' => 0,
                                    ];
                                }



                                // need to get p2p graph for $svli, $dvli
                                // need to get p2p stats for window yyyy-mm-dd 00:00:00 -> yyyy-mm-dd 23:59:59
                                // $p2pGraph = ...;
                                // add stats from p2pgraph statistics

                                //
                                // $stats[$peerId]['ipv4_total_in'] += .... >ipv4_total_in;



                            }


                        }

                    }

                }




                foreach( $stats as $peerId => $traffic ) {

                    // insert total customer data
//                    P2pDailyStats::updateOrCreate(
//                        [ 'customer_id' => $c->id, 'day' => 'YYYY-MM-DD', 'peer_id' => $peerId  ]
//                        [ stats .... ] => unsigned bigInts
//                    );
//                    isVerbosityVerbose-> Processing $custname -> $peername: stored in database

                }


                if($this->isVerbosityNormal()) {
                    Log::debug("Completed {$c->name} in " . (microtime(true) - $itertime) . " seconds");
                }
            });

        return 0;
    }
}
