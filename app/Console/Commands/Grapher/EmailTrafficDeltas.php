<?php

namespace IXP\Console\Commands\Grapher;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Carbon\Carbon;
use D2EM;


use Grapher;

use IXP\Mail\Grapher\TrafficDeltas as TrafficDeltasMailable;
use IXP\Models\Customer;
use IXP\Models\TrafficDaily;
use IXP\Services\Grapher\Graph;

use Mail;

 /**
  * Artisan command to email ports where the standard deviation has changed significantly
  *
  * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
  * @author     Yann Robin      <yann@islandbridgenetworks.ie>
  * @category   Grapher
  * @package    IXP\Console\Commands
  * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
  */
class EmailTrafficDeltas extends GrapherCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grapher:email-traffic-deltas {email}
                        {--stddev=1.5 : Multiple of the stddev to report on (default 1.5)}
                        {--B|backend= : Which graphing backend to use}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email ports with a swing in the standard deviation';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        Grapher::backend( $this->option( 'backend' ) );
        $this->setGrapher( Grapher::getFacadeRoot() );

        if( ( $retval = $this->verifyArgsAndOptions() ) !== 0 ) {
            return $retval;
        }
        $day = Carbon::now()->subDays( 1 );
        $ports = $this->portsWithDelta( $day, (float)$this->option( 'stddev' ) );

        if( count( $ports ) ) {
            Mail::to( explode( ',', $this->argument( 'email' ) ) )->send( new TrafficDeltasMailable( $ports, (float)$this->option( 'stddev' ), $day ) );
        } else if( $this->isVerbosityVerbose() ) {
            $this->info("No ports have a traffic delta within the requested deviation");
        }

        return 0;
    }

    /**
     * Find ports with the given stddev delta
     *
     * @param Carbon    $day
     * @param float     $stddev
     *
     * @return array
     *
     * @throws
     */
    private function portsWithDelta( Carbon $day, float $stddev ): array
    {
        $custs = Customer::currentActive(true, true )->get();
        $ports = [];

        foreach( $custs as $c ) {
            $tds = TrafficDaily::where( 'cust_id', $c->id )
                ->where( 'category', Graph::CATEGORY_BITS )
                ->where( 'day', '<=', $day->format( 'Y-m-d' ) )
                ->whereRaw( "DATE_FORMAT(day,'%w') = DATE_FORMAT( '" . $day->format( 'Y-m-d' ) . "','%w') ")
                ->orderByDesc( 'day' )
                ->limit( config('grapher.cli.traffic_differentials.stddev_calc_length', 60)+1 )
                ->get()->toArray();


            if( $tds === null || count( $tds ) <= 3 ) {
                continue;
            }

            $port = [];

            $port['cust'] = $c;
            $port['days'] = config('grapher.cli.traffic_differentials.stddev_calc_length');

            $port['meanIn']  = 0.0; $port['stddevIn']  = 0.0;
            $port['meanOut'] = 0.0; $port['stddevOut'] = 0.0;
            $port['count'] = 0.0;

            $t = array_shift($tds);
            $port['todayAvgIn']  = $t['day_avg_in'];
            $port['todayAvgOut'] = $t['day_avg_out'];

            foreach( $tds as $t ) {
                $port['count']     += 1.0;
                $port['meanIn']    += $t['day_avg_in'];
                $port['meanOut']   += $t['day_avg_out'];
            }

            $port['meanIn']  /= $port['count'];
            $port['meanOut'] /= $port['count'];

            foreach( $tds as $t ) {
                $port['stddevIn']  += ( $t['day_avg_in']  - $port['meanIn']  ) * ( $t['day_avg_in']  - $port['meanIn']  );
                $port['stddevOut'] += ( $t['day_avg_out'] - $port['meanOut'] ) * ( $t['day_avg_out'] - $port['meanOut'] );
            }

            $port['stddevIn']  = sqrt( $port['stddevIn']  / ( $port['count'] - 1 ) );
            $port['stddevOut'] = sqrt( $port['stddevOut'] / ( $port['count'] - 1 ) );

            // so, is yesterday's traffic outside of the standard deviation? And is it an increase or decrease?
            $port['sIn']  = ( $port['todayAvgIn']  - $port['meanIn']   ) > 0 ? 'increase' : 'decrease';
            $port['sOut'] = ( $port['todayAvgOut'] - $port['meanOut']  ) > 0 ? 'increase' : 'decrease';
            $port['dIn']  = abs( $port['todayAvgIn']  - $port['meanIn']  );
            $port['dOut'] = abs( $port['todayAvgOut'] - $port['meanOut'] );

            $port['thresholdIn']  = $stddev * $port['stddevIn'];
            $port['thresholdOut'] = $stddev * $port['stddevOut'];

            $port['percentIn']  = $port['meanIn']  ? (int)( ( $port[ 'dIn' ] / $port[ 'meanIn' ] ) * 100 ) : $port['dIn'];
            $port['percentOut'] = $port['meanOut'] ? (int)( ( $port[ 'dOut' ] / $port[ 'meanOut' ] ) * 100 ) : $port['dOut'];


            if( $port['dIn'] > $port['thresholdIn'] || $port['dOut'] > $port['thresholdOut'] ) {
                if( $this->isVerbosityVerbose() ) {
                    $this->warn( $c->name );
                    $this->warn( sprintf( "\tIN  M: %d\tSD: %d\tDiff: %d\tT: %d\tR: %s",
                        (int)$port[ 'meanIn' ], (int)$port[ 'stddevIn' ], (int)$port[ 'dIn' ], $port['thresholdIn'], ( $port['dIn'] > $port['thresholdIn'] ? 'OUT' : 'IN' )
                    ) );
                    $this->warn( sprintf( "\tOUT M: %d\tSD: %d\tDiff: %d\tT: %d\tR: %s\n",
                        (int)$port[ 'meanOut' ], (int)$port[ 'stddevOut' ], (int)$port[ 'dOut' ], $port['thresholdOut'], ( $port['dOut'] > $port['thresholdOut'] ? 'OUT' : 'IN' )
                    ) );
                }

                $port['pngMonth'] = $this->grapher()->customer( $c )->setCategory( Graph::CATEGORY_BITS )->setPeriod( Graph::PERIOD_MONTH )->png();
                $port['pngYear']  = $this->grapher()->customer( $c )->setCategory( Graph::CATEGORY_BITS )->setPeriod( Graph::PERIOD_YEAR  )->png();
                $ports[] = $port;

            } else if( $this->isVerbosityVeryVerbose() ) {
                $this->info( $c->name );
                $this->info( sprintf( "\tIN  M: %d\tSD: %d\tDiff: %d\tT: %d\tR: %s",
                    (int)$port[ 'meanIn' ], (int)$port[ 'stddevIn' ], (int)$port[ 'dIn' ], $port['thresholdIn'], ( $port['dIn'] > $port['thresholdIn'] ? 'OUT' : 'IN' )
                ) );
                $this->info( sprintf( "\tOUT M: %d\tSD: %d\tDiff: %d\tT: %d\tR: %s\n",
                    (int)$port[ 'meanOut' ], (int)$port[ 'stddevOut' ], (int)$port[ 'dOut' ], $port['thresholdOut'], ( $port['dOut'] > $port['thresholdOut'] ? 'OUT' : 'IN' )
                ) );
            }
        }

        return $ports;
    }

    /**
     * Check the various arguments and options that have been password to the console command
     *
     * @return int 0 for success or else an error code
     */
    protected function verifyArgsAndOptions(): int
    {
        $emails = explode( ',', $this->argument('email') );

        foreach( $emails as $e ) {
            if( filter_var( $e, FILTER_VALIDATE_EMAIL ) === false ) {
                $this->error( "Invalid email address: $e" );
                return 254;
            }
        }

        if( !is_numeric( $this->option('stddev') ) ) {
            $this->error( "Invalid stddev: " . $this->option('stddev') );
            return 253;
        }

        return 0;
    }
}