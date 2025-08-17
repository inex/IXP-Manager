<?php

namespace IXP\Console\Commands\Grapher;

use Carbon\Carbon;
use IXP\Models\Aggregators\VlanInterfaceAggregator;
use IXP\Models\Customer;
use IXP\Models\P2pDailyStats;
use IXP\Models\VlanInterface;
use IXP\Support\Facades\Grapher;
use Illuminate\Support\Facades\DB;

use IXP\Services\Grapher\Graph;

class UploadDailyP2p extends GrapherCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grapher:upload-daily-p2p 
                    {day : target day in YYYY-MM-DD format}
                    {--customer-id= : Customer ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate and save daily p2p traffic stats between members';

    /**
     * Execute the console command.
     *
     * @psalm-return -1|0
     */
    public function handle(): int
    {
        if( !preg_match( "/\d\d\d\d\-\d\d\-\d\d/", $this->argument('day') ) ) {
            $this->error("Invalid day parameter - expected format is " . now()->subDay()->format('Y-m-d') );
            return -1;
        }

        $this->setGrapher( Grapher::getFacadeRoot() );

        $start     = Carbon::parse( $this->argument('day') . ' 00:00:00' );
        $end       = $start->copy()->endOfDay();
        $startTime = microtime(true);

        Customer::currentActive(true,true,true)
            ->when( $this->option('customer-id'), function ($query, string $cid) {
                $query->where('id', $cid);
            })
            ->each( function( Customer $c ) use ( $start, $end ) {

                $iterTime = microtime(true);

                if($this->isVerbosityNormal()) {
                    $this->info("Processing {$c->name} for " . $start->format('Y-m-d'));
                }

                $stats = $this->collectStatistics( $c, $start, $end );
                $this->storeStatistics( $stats, $c, $start );

                if($this->isVerbosityNormal()) {
                    $this->info("Completed {$c->name} in " . (microtime(true) - $iterTime) . " seconds");
                }
            });

        if($this->isVerbosityNormal()) {
            $this->info("All Completed in " . (microtime(true) - $startTime) . " seconds");
        }
        return 0;
    }

    /**
     * Collect Statistics data
     *
     * @param Customer $customer
     * @param Carbon $start
     * @param Carbon $end
     *
     * @return int[][]
     *
     * @throws \IXP\Exceptions\Services\Grapher\ParameterException
     *
     * @psalm-return array<''|int, array<string, int<min, max>>>
     */
    protected function collectStatistics(Customer $customer, Carbon $start, Carbon $end) : array
    {
        $stats = [];


        foreach($customer->virtualinterfaces as $vi) {

            /** @var VlanInterface $svli */
            foreach($vi->vlaninterfaces as $svli) {

                if(!$svli->vlan->export_to_ixf) {
                    continue;
                }

                foreach([4,6] as $protocol) {

                    if( !$svli->ipvxEnabled($protocol) ) {
                        continue;
                    }


                    /** @var VlanInterface $dvli */
                    foreach( VlanInterfaceAggregator::forVlan( $svli->vlan, $protocol ) as $dvli ) {

                        // skip if it's this customer's own vlan interface or another of their own connections
                        if( $svli->id === $dvli->id || $customer->id == $dvli->virtualInterface->custid ) {
                            continue;
                        }

                        if($this->isVerbosityVeryVerbose() ) {
                            $this->line( "\t- {$svli->vlan->name} ipv$protocol with {$dvli->virtualInterface->customer->name}" );
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


                        $graph = $this->grapher()->p2p($svli, $dvli)
                            ->setProtocol('ipv'.$protocol)
                            ->setPeriod(Graph::PERIOD_CUSTOM, $start, $end);

                        $statistics = $graph->statistics()->all();

                        $stats[$peerId]["ipv{$protocol}_total_in"]  += (int)$statistics['totalin'];
                        $stats[$peerId]["ipv{$protocol}_total_out"] += (int)$statistics['totalout'];
                        $stats[$peerId]["ipv{$protocol}_max_in"]    += (int)$statistics['maxin'];
                        $stats[$peerId]["ipv{$protocol}_max_out"]   += (int)$statistics['maxout'];
                    }


                }

            }

        }

        return $stats;
    }

    /**
     * Store the statistics data into the database
     *
     * @param array $stats
     * @param Customer $customer
     * @param Carbon $start
     * @return void
     */
    protected function storeStatistics(array $stats, Customer $customer, Carbon $start) : void {

        if( $this->isVerbosityVerbose() ) {
            $this->line( "\tStoring date for {$customer->name} in database" );
        }

        DB::transaction(function () use( $stats, $customer, $start ) {

            foreach( $stats as $peerId => $traffic ) {

                P2pDailyStats::updateOrCreate( [
                        'cust_id' => $customer->id,
                        'day'     => $start->format( 'Y-m-d' ),
                        'peer_id' => $peerId,
                    ],
                    $traffic
                );

            }
        });

    }
}
