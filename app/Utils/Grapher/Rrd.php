<?php

namespace IXP\Utils\Grapher;

use IXP\Exceptions\Utils\Grapher\FileError as FileErrorException;

/*
 * Copyright (C) 2009-2016 Internet Neutral Exchange Association Limited.
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

use IXP\Services\Grapher\Graph;
use IXP\Exceptions\Services\Grapher\GeneralException;

/**
 * A class to handle RRD files
 *
 * @author Barry O'Donovan <barry@opensolutions.ie>
 * @package Grapher
 */
class Rrd
{
    /**
     * the absolute path for the RRD file under investigation
     * @var string
     */
    protected $realfile = null;

    /**
     * the absolute path for the local copy of the RRD file under investigation
     *
     * **WARNING:** this is deleted after processing if set!
     *
     * @var string
     */
    protected $localfile = null;

    /**
     * the absolute path to the file we're actually using (real / local)
     *
     * @var string
     */
    protected $file = null;


    /**
     * Start time of RRD data (as returned by rrd_fetch)
     * @var int
     */
    protected $start = null;

    /**
     * End time of RRD data (as returned by rrd_fetch)
     * @var int
     */
    protected $end = null;

    /**
     * Step interval RRD data (as returned by rrd_fetch)
     * @var int
     */
    protected $step = null;

    /**
     * Data from RRD file formatted as a MRTG Log array as returned by
     * IXP\Utils\Grapher\Mrtg
     *
     * @var array
     */
    protected $array = null;

    /**
     * Period times.
     *
     * these values are taken from mrtg/src/rateup.c
     */
    const PERIOD_TIME = Mrtg::PERIOD_TIME;



    /**
     * Class constructor.
     *
     * @param $file The MRTG log file to load for analysis
     */
    function __construct( string $file )
    {
        $this->realfile  = $file;
        $this->loadRrdFile();
    }

    /**
     * Class destructor
     */
    public function __destruct() {
        $this->deleteLocalCopy();
    }

    /**
     * Accessor for PERIOD_TIME
     * @param string
     * @return float
     */
    public function getPeriodTime( $period ): float {
        if( isset( self::PERIOD_TIME[ $period ] ) )
            return self::PERIOD_TIME[ $period ];
        else
            return 0.0;
    }


    /**
     * If necessary, copy a remote rrd to a local file (temporarily)
     *
     * Most PHP file functions support URI's such as http://.
     *
     * Naturally, rrd_* is an exception :-(
     *
     * This function creates a local copy if necessary and its mate,
     * removeLocalCopy() deletes it afterwords.
     *
     * @return string The full path to the local copy
     * @throws IXP\Exceptions\Utils\Grapher\FileError
     */
    private function getLocalCopy(): string {
        // if it's already local, just return
        if( substr( $this->realfile, 0, 4 ) != 'http' ) {
            $this->file = $this->realfile;
            return $this->file;
        }

        // use Laravel's storage if possible
        if( function_exists( 'storage_path' ) ) {
            $dir = storage_path() . '/grapher';
            if ( !file_exists( $dir ) ) {
                if( !@mkdir( $dir, 0770, true ) ) {
                    throw new FileErrorException("Could not create local RRD storage directory");
                }
            }
            $this->localfile = tempnam( $dir, 'utils-rrd-' );
        } else {
            $this->localfile = tempnam( $dir, 'ixp-manager-grapher-utils-rrd-' );
        }

        if( !( ( $r = @file_get_contents( $this->realfile ) ) && @file_put_contents( $this->localfile, $r ) ) ) {
            throw new FileErrorException("Could not create local RRD copy");
        }
        $this->file = $this->localfile;
        return $this->localfile;
    }

    /**
     * If necessary, delete a local copy of a remote rrd
     *
     * @see getLocalCopy() for an explanation
     */
    private function deleteLocalCopy() {
        if( $this->localfile !== null ) {
            @unlink( $this->localfile );
        }
    }

    /**
     * Prepare RRD file
     * @throws IXP\Exceptions\Utils\Grapher\FileError
     */
    protected function loadRrdFile() {

        // we need to allow for remote files but php's rrd_* functions don't support this
        $fname = $this->getLocalCopy();
    }

    /**
     * From the RRD file, process the data and return it in the same format
     * as an MRTG log file.
     *
     * @see IXP\Utils\Grapher\Mrtg::loadMrtgFile()
     *
     * Processing means:
     * - only returning the values for the requested period
     * - MRTG/RRD provides traffic as bytes, change to bits
     *
     * @param IXP\Services\Grapher\Graph $graph
     * @return array
     * @throws IXP\Exceptions\Utils\Grapher\FileError
     */
    public function data( Graph $graph ): array {

        $rrd = rrd_fetch( $this->file, [
            'AVERAGE',
            '--start', time() - self::PERIOD_TIME[ $graph->period() ]
        ]);

        if( $rrd === false || !is_array( $rrd ) ) {
            throw new FileErrorException("Could not open RRD file");
        }

        $this->start = $rrd['start'];
        $this->end   = $rrd['end'];
        $this->step  = $rrd['step'];

        // we want newest first, so iterate in reverse
        $tin = array_reverse( $rrd['data']['traffic_in'], true );

        $values  = [];

        $isBits = ( $graph->category() == Graph::CATEGORY_BITS );

        $i = 0;
        foreach( $tin as $ts => $v ) {
            if( is_numeric( $v ) && is_numeric( $rrd['data']['traffic_out'][$ts] ) ) {

                // first couple are often blank
                if( $ts > time() - $this->step ) {
                    continue;
                }

                $values[$i] = [ (int)$ts, (int)$v, (int)$rrd['data']['traffic_out'][$ts], (int)$v, (int)$rrd['data']['traffic_out'][$ts] ];

                if( $isBits ) {
                    $values[$i][1] *= 8;
                    $values[$i][2] *= 8;
                    $values[$i][3] *= 8;
                    $values[$i][4] *= 8;
                }

                $i++;
            }
        }

        return $values;
    }



    /**
     * Accessor method for $array - the data from the MRTG file.
     *
     * @return array The data from the MRTG file
     */
    public function getArray()
    {
        return $this->array;
    }

}
