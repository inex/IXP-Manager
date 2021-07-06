<?php

namespace IXP\Utils\Grapher;

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

use IXP\Exceptions\Utils\Grapher\FileError as FileErrorException;

use IXP\Exceptions\Services\Grapher\GeneralException;

use IXP\Services\Grapher\Graph;

/**
 * A class to handle Mrtg log files
 *
 *
 * History:
 * * 20081126 Nick's latest version
 * * 20100219 Ported to IXP Manager by barryo
 * * 20160127 Ported to IXP Manager v4 by barryo
 *
 * @author     Nick Hilliard <nick@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Utils\Grapher
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Mrtg
{
    /**
     * the absolute path for the log file under investigation
     * @var string
     */
    protected $file = null;

    /**
     * Array of MRTG log data from the file
     * @var array
     */
    protected $array = null;

    /**
     * Period times.
     *
     * these values are taken from mrtg/src/rateup.c
     */
    public const PERIOD_TIME = [
        Graph::PERIOD_DAY   => 119988.0,     // ( 33.33 * 3600 ),
        Graph::PERIOD_WEEK  => 719712.0,     // ( 8.33  * 24 * 3600 ),
        Graph::PERIOD_MONTH => 2879712.0,    // ( 33.33 * 24 * 3600 ),
        Graph::PERIOD_YEAR  => 31622400.0    // ( 366 * 24 * 3600 )
    ];

    /**
     * Mrtg / SNMP options for the different traffic types
     *
     * @var array
     */
    public const TRAFFIC_TYPES = [
        Graph::CATEGORY_BITS   => [
            'in'      => 'oidInOctets',
            'out'     => 'oidOutOctets',
            'options' => 'growright,bits',
            'name'    => 'Bits'
        ],
        Graph::CATEGORY_PACKETS   => [
            'in'      => 'oidInUnicastPackets',
            'out'     => 'oidOutUnicastPackets',
            'options' => 'growright',
            'name'    => 'Packets'
        ],
        Graph::CATEGORY_ERRORS   => [
            'in'      => 'oidInErrors',
            'out'     => 'oidOutErrors',
            'options' => 'growright',
            'name'    => 'Errors'
        ],
        Graph::CATEGORY_DISCARDS   => [
            'in'      => 'oidInDiscards',
            'out'     => 'oidOutDiscards',
            'options' => 'growright',
            'name'    => 'Discards'
        ],
        Graph::CATEGORY_BROADCASTS  => [
            'in'      => 'oidInBroadcasts',
            'out'     => 'oidOutBroadcasts',
            'options' => 'growright',
            'name'    => 'Broadcasts'
        ],
    ];

    /**
     * Class constructor.
     *
     * @param string $file The MRTG log file to load for analysis
     */
    public function __construct( string $file )
    {
        $this->file  = $file;
        $this->array = $this->loadMrtgFile();
    }

    /**
     * Accessor for PERIOD_TIME
     *
     * @param string
     *
     * @return float
     */
    public function getPeriodTime( $period ): float
    {
        return self::PERIOD_TIME[ $period ] ?? 0.0;
    }

    /**
     * Load data from an MRTG log file and return it as an indexed array
     * of associative arrays where the five elements of these arrays are just like the
     * MRTG log file:
     *
     *     [
     *       [
     *         0 =>  unixtime stamp
     *         1 =>  average incoming rate
     *         2 =>  average outgoing rate
     *         3 =>  maximum incoming rate
     *         4 =>  maximum outgoing rate
     *       ],
     *       ....
     *     ]
     *
     * The above will be ordered with the newest first as per the log file.
     *
     * @return array
     *
     * @throws
     */
    protected function loadMrtgFile(): array
    {
        $values  = [];

        if( !( $fd = @fopen( $this->file, "r" ) ) ) {
            throw new FileErrorException("Could not open: {$this->file}");
        }

        while( $record = fgets( $fd, 4096 ) ) {
            $data = explode( " ", trim( $record ) );

            if( count( $data ) >= 3 ) {
                $values[] = [ (int)$data[0], (int)$data[1], (int)$data[2], isset($data[3]) ? (int)$data[3] : 0, isset($data[4]) ? (int)$data[4] : 0 ];
            }
        }

        // drop the first record as it's traffic counters from the most recent run of mrtg.
        array_shift( $values );
        fclose( $fd );

        return $values;
    }

    /**
     * From the data loaded from an MRTG log file, process it and  and return it in the same format
     * as loadMrtgFile().
     *
     * @param Graph $graph
     *
     * @return array
     *
     * @throws GeneralException
     *
     * @see IXP\Utils\Grapher\Mrtg::loadMrtgFile()
     *
     * Processing means:
     * - only returning the values for the requested period
     * - MRTG provides traffic as bytes, change to bits
     *
     */
    public function data( Graph $graph ): array
    {
        $values = [];

        if( !( $periodsecs = $this->getPeriodTime( $graph->period() ) ) ) {
            throw new GeneralException('Invalid period');
        }

        $starttime  = time() - $periodsecs;
        $endtime    = time();

        // Run through the array and pull out the values we want
        for( $i = sizeof( $this->array )-1; $i >= 0; $i-- ) {
            // process within start / end time
            if( ($this->array[ $i ][ 0 ] >= $starttime) && ($this->array[ $i ][ 0 ] <= $endtime) ) {
                $values[] = $this->array[ $i ];
            }
        }

        // convert bytes to bits
        if( $graph->category() === Graph::CATEGORY_BITS ) {
            foreach( $values as $i => $v ) {
                $values[$i][1] *= 8;
                $values[$i][2] *= 8;
                $values[$i][3] *= 8;
                $values[$i][4] *= 8;
            }
        }
        return $values;
    }

    /**
     * Accessor method for $array - the data from the MRTG file.
     *
     * @return array The data from the MRTG file
     */
    public function getArray(): array
    {
        return $this->array;
    }
}