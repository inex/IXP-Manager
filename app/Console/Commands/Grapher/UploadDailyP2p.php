<?php

namespace IXP\Console\Commands\Grapher;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use IXP\Models\Aggregators\VlanInterfaceAggregator;
use IXP\Models\Customer;
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
                    Log::debug("Processing {$c->name} for " . $start->format('Y-m-d'));
                }

                $stats = [];
                $peerId = null;

                foreach($c->virtualinterfaces as $vi) {

                    foreach($vi->vlaninterfaces as $svli) {

                        if(!$svli->vlan->export_to_ixf) { continue; }

                        foreach([4,6] as $protocol) {

                            $fnIpEnabled = "ipv{$protocol}enabled";
                            if( !$svli->$fnIpEnabled ) { continue; }


                            foreach( VlanInterfaceAggregator::forVlan( $svli->vlan->id, $protocol ) as $dvli ) {

                                if( $svli->id === $dvli->id ) { continue; }

                                if($this->isVerbosityNormal()) {
                                    Log::debug("Processing $c->name -> $svli->vlan->name ipv$protocol");
                                }

                                // todo: check the peer id for usability
                                $peerId = $c->id . "-" . $svli->id . "-" . $dvli->id;

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


                            }


                        }

                    }

                }


/*
                if( $peerId ) {
                    P2pDailyStats::updateOrCreate(
                        [ 'customer_id' => $c->id, 'day' => 'YYYY-MM-DD', 'peer_id' => $peerId  ]
                        [ stats .... ] => unsigned bigInts
                    );
                    isVerbosityVerbose-> Processing $custname -> $peername: stored in database
                }
*/


                if($this->isVerbosityNormal()) {
                    Log::debug("Completed {$c->name} in " . (microtime(true) - $itertime) . " seconds");
                }
            });

        return 0;
    }
}
