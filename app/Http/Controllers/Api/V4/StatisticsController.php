<?php

namespace IXP\Http\Controllers\Api\V4;

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Cache, Grapher, Carbon\Carbon;

use Illuminate\Http\JsonResponse;

use IXP\Services\Grapher\Graph;

/**
 * StatisticsController
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin      <yann@islandbridgenetworks.ie>
 * @category   APIv4
 * @package    IXP\Http\Controllers\Api\V4
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class StatisticsController extends Controller
{
    /**
     * Function to export max traffic stats by month (past six) as JSON
     *
     * Response is of the form:
     *
     *     [
     *         {
     *             "start": "2017-10-01T00:00:00Z",
     *             "startTs": 1506816000,
     *             "end": "2017-10-31T23:59:59Z",
     *             "endTs": 1509494399,
     *             "max": 206917750576
     *         },
     *         ...
     *     ]
     *
     * Access via https://ixp.example.com/statistics/overall-by-month
     *
     * Respects Grapher authorisation settings:
     *   http://docs.ixpmanager.org/features/grapher/#accessibility-of-aggregate-graphs
     *
     * This is mostly used by https://www.inex.ie/ 's front page graph.
     *
     * @return JsonResponse
     */
    public function overallByMonth(): JsonResponse
    {
        $data = Cache::remember( 'public_overall_stats_by_month', 14400, function() {
            $graph = Grapher::ixp()->setPeriod( Graph::PERIOD_YEAR );
            $graph->authorise();

            $mrtg       = $graph->data();
            $data       = [];
            $start      = Carbon::now()->subMonths( 5 )->startOfMonth();
            $startTs    = $start->timestamp;
            $i          = 0;

            while( $start->lt( Carbon::now() ) ) {
                $data[ $i ][ 'start' ]      = $start->copy();
                $data[ $i ][ 'startTs' ]    = $start->timestamp;
                $data[ $i ][ 'end' ]        = $start->endOfMonth()->copy();
                $data[ $i ][ 'endTs' ]      = $start->endOfMonth()->timestamp;
                $data[ $i ][ 'max' ]        = 0;

                $start->startOfMonth()->addMonth();
                $i++;
            }

            $endTs = $data[ $i - 1 ][ 'endTs' ];

            foreach( $mrtg as $m ) {
                if( count( $m ) !== 5 ) {
                    continue;
                }

                if( $m[ 0 ] < $startTs || $m[ 0 ] > $endTs ) {
                    continue;
                }

                foreach( $data as $i => $d ) {
                    if( $m[ 0 ] >= $d[ 'startTs' ] && $m[ 0 ] <= $d[ 'endTs' ] ) {
                        if( $m[ 3 ] > $data[ $i ][ 'max' ] ) {
                            $data[ $i ][ 'max' ] = $m[ 3 ];
                        }
                        if( $m[ 4 ] > $data[ $i ][ 'max' ] ) {
                            $data[ $i ][ 'max' ] = $m[ 4 ];
                        }
                        break;
                    }
                }
            }

            // scale to bps
            foreach( $data as $i => $d ) {
                /** @var Carbon $start */
                $start = $data[ $i ][ 'start' ];
                /** @var Carbon $end */
                $end = $data[ $i ][ 'end' ];

                $data[ $i ][ 'start' ] = $start->format( 'Y-m-d\TH:i:s\Z' );
                $data[ $i ][ 'end' ]   = $end->format( 'Y-m-d\TH:i:s\Z' );
            }

            return $data;
        });

        return response()->json( $data );
    }
}