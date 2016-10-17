<?php

namespace IXP\Utils\Grapher;

use IXP\Exceptions\Utils\Grapher\FileError as FileErrorException;

/*
 * Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee.
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
     * Graph object under consideration
     * @var IXP\Services\Grapher\Graph
     */
    protected $graph = null;

    /**
     * Period times.
     *
     * these values are taken from mrtg/src/rateup.c
     */
    const PERIOD_TIME = Mrtg::PERIOD_TIME;


    /**
     * Prefix for local cached files
     */
    const LOCAL_CACHE_RRD_PREFIX = "ixp-utils-rrd-";

    /**
     * Class constructor.
     *
     * @param $file The MRTG log file to load for analysis
     */
    function __construct( string $file, Graph $graph )
    {
        $this->realfile  = $file;
        $this->graph     = $graph;
        $this->loadRrdFile();
    }

    /**
     * Class destructor
     */
    public function __destruct() {
        $this->deleteLocalCopies();
    }

    /**
     * Get the full path to the RRD file being used
     *
     * Could be the original "real file" or a local copy
     *
     * @return string
     */
    public function file(): string {
        return $this->file;
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
     * Access for the graph object under consideration
     * @return IXP\Services\Grapher\Graph
     */
    private function graph(): Graph {
        return $this->graph;
    }

    /**
     * Get the local directory name.
     *
     * @see getLocalCopy() for detals
     * @see getLocalFilename() for detals
     *
     * @return string The full path to the local directory
     * @throws IXP\Exceptions\Utils\Grapher\FileError
     */
    private function getLocalDirectory() {
        // use Laravel's storage if possible
        if( !function_exists( 'storage_path' ) ) {
            throw new FileErrorException("Could not identify storage directory - fn storage_path() not defined");
        }

        $dir = storage_path() . '/grapher';
        if ( !file_exists( $dir ) ) {
            if( !@mkdir( $dir, 0770, true ) ) {
                throw new FileErrorException("Could not create local RRD storage directory");
            }
        }
        return $dir;
    }

    /**
     * Get the local file name.
     *
     * @see getLocalCopy() for detals
     *
     * @return string The full path to the local copy
     * @throws IXP\Exceptions\Utils\Grapher\FileError
     */
    private function getLocalFilename( $ext = 'rrd' ) {
        return "{$this->getLocalDirectory()}/" . self::LOCAL_CACHE_RRD_PREFIX . "{$this->graph()->key()}.{$ext}";
    }

    /**
     * If necessary, copy a remote rrd to a local file (temporarily)
     *
     * Most PHP file functions support URI's such as http://.
     *
     * Naturally, rrd_* is an exception :-(
     *
     * This function creates a local copy.
     *
     * @see removeLocalCopies()
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

        $this->file = $this->localfile = $this->getLocalFilename();

        // does the local file exist and is it less than 5mins old?
        if( !file_exists($this->localfile) || !( time() - filemtime($this->localfile) < 300 ) ) {
            if( !( ( $r = @file_get_contents( $this->realfile ) ) && @file_put_contents( $this->localfile, $r ) ) ) {
                throw new FileErrorException("Could not create local RRD copy");
            }
        }

        return $this->localfile;
    }

    /**
     * If necessary, delete local copies of a remote rrds
     *
     * @see getLocalCopy() for an explanation
     */
    private function deleteLocalCopies() {
        $files = glob( $this->getLocalDirectory() . "/" . self::LOCAL_CACHE_RRD_PREFIX . "*" );
        $now   = time();

        foreach( $files as $file ) {
            if( is_file($file) && ( $now - filemtime( $file ) >= 300 ) ) {
                @unlink($file);
            }
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
     * Get the RRD file
     * @throws IXP\Exceptions\Utils\Grapher\FileError
     */
    protected function rrd() {
        if( !( $rrd = @file_get_contents($this->file) ) ) {
            throw new FileErrorException("Could not read RRD file [{$this->file}]");
        }
        return $rrd;
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
     * @return array
     * @throws IXP\Exceptions\Utils\Grapher\FileError
     */
    public function data(): array {

        $rrd = rrd_fetch( $this->file, [
            'AVERAGE',
            '--start', time() - self::PERIOD_TIME[ $this->graph()->period() ]
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

        $isBits = ( $this->graph()->category() == Graph::CATEGORY_BITS );

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
     * From the RRD file, process and return a png
     *
     * @throws IXP\Exceptions\Utils\Grapher\FileError
     */
    public function png(): string {

        // // does the local file exist and is it less than 5mins old?
        if( file_exists($this->getLocalFilename('png')) && ( time() - filemtime($this->localfile) < 300 ) ) {
            return $this->getLocalFilename('png');
        }

        $separated_maxima = ( self::PERIOD_TIME[ $this->graph()->period() ] > 60*60*24*2) ? true : false;

        $options = [
            '--width=600',
            '--height=150',
            '--slope-mode',
            '--start', time() - self::PERIOD_TIME[ $this->graph()->period() ],
            '--lower-limit=0',
            '--title=' . $this->graph()->title(),
            '--vertical-label=' . $this->graph()->category() . ' / second',
            '--watermark=' . $this->graph()->watermark(),

            'DEF:a='.$this->file().':traffic_in:AVERAGE',
            'DEF:b='.$this->file().':traffic_in:MAX',
            'DEF:c='.$this->file().':traffic_out:AVERAGE',
            'DEF:d='.$this->file().':traffic_out:MAX',

            'CDEF:cdefa=a,' . ( $this->graph()->category() == Graph::CATEGORY_BITS ? '8' : '1' ) . ',*',
            'CDEF:cdefb=b,' . ( $this->graph()->category() == Graph::CATEGORY_BITS ? '8' : '1' ) . ',*',
            'CDEF:cdefc=c,' . ( $this->graph()->category() == Graph::CATEGORY_BITS ? '8' : '1' ) . ',*',
            'CDEF:cdefd=d,' . ( $this->graph()->category() == Graph::CATEGORY_BITS ? '8' : '1' ) . ',*',

            'VDEF:last_in=cdefa,LAST',
            'VDEF:last_out=cdefc,LAST',
            'VDEF:max_in=cdefb,MAXIMUM',
            'VDEF:max_out=cdefd,MAXIMUM',
            'VDEF:avg_in=cdefb,AVERAGE',
            'VDEF:avg_out=cdefd,AVERAGE',
        ];

        if( $separated_maxima ) {
            $options[] = 'LINE2:cdefb#ff00ff:Peak Traffic In ';
            $options[] = 'GPRINT:max_in:\tMax\\:%8.2lf%s\l';
            $options[] = 'AREA:cdefd#006600:Peak Traffic Out';
            $options[] = 'GPRINT:max_out:\tMax\\:%8.2lf%s\l';
            $options[] = 'COMMENT:\s';
        }

        $avg_label = $separated_maxima ? 'Avg. ' : '';

        $options[] = 'LINE1:cdefa#002A97FF:Average In ';
        if( !$separated_maxima ) {
            $options[] = 'GPRINT:max_in:\tMax\\:%8.2lf%s';
        }
        $options[] = 'GPRINT:avg_in:\tAvg\\:%8.2lf%s';
        $options[] = 'GPRINT:last_in:\tCur\\:%8.2lf%s\l';

        $options[] = 'AREA:cdefc#00CF00:Average Out';
        if( !$separated_maxima ) {
            $options[] = 'GPRINT:max_out:\tMax\\:%8.2lf%s';
        }
        $options[] = 'GPRINT:avg_out:\tAvg\\:%8.2lf%s';
        $options[] = 'GPRINT:last_out:\tCur\\:%8.2lf%s\l';

        $options[] = 'COMMENT:\s';

        $png = rrd_graph( $this->getLocalFilename('png'), $options );

        if( $png === false ) {
            throw new FileErrorException("Could not open/create RRD/PNG file");
        }
        return $this->getLocalFilename('png');
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
