<?php

namespace IXP\Console\Commands\Grapher;

use Carbon\Carbon;
use Illuminate\Console\Command;
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
                    {date : target day in YYY-MM-DD format}
                    {--C|customer-id= : Customer ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store daily p2p traffic of customer(s)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $customerId = $this->option('customer-id');

        $date = $this->argument('date');
        $startDate = Carbon::parse($date . " 00:00:00");
        $endDate = Carbon::parse($date . " 23:59:59");

        if($customerId) {
            $allCustomer = Customer::currentActive(true,true,true)
                ->where('id', $customerId)
                ->get();
        } else {
            $allCustomer = Customer::currentActive(true,true,true)->get();
        }

        if($allCustomer) {
            foreach($allCustomer as $c) {
                if($this->isVerbosityNormal()) {
                    Log::debug("Processing $c->name for date $date");
                }
                $startTime = microtime();

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


                $endTime = microtime();
                $workTime = $endTime - $startTime;
                if($this->isVerbosityNormal()) {
                    Log::debug("Completed $c->name took $workTime seconds");
                }
            }

        }

    }
}
