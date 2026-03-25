<?php

namespace IXP\Utils\Grapher;

/*
 * Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee.
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
use IXP\Exceptions\Services\Grapher\BackendFeatureNotImplementedException;
use IXP\Exceptions\Utils\Grapher\FileError as FileErrorException;
use IXP\Services\Grapher\Graph;

use Illuminate\Support\Facades\Log;


/**
 * A class to handle RRD files
 *
 * @author Barry O'Donovan <barry@opensolutions.ie>
 * @package Grapher
 */
class MultiRrd
{
    /**
     * the absolute path for the RRD files under investigation
     */
    protected array $realfiles = [];

    /**
     * the absolute path for the local copy of the RRD file under investigation
     *
     * **WARNING:** this is deleted after processing if set!
     */
    protected array $localfiles = [];


    /**
     * Start time of RRD data (as returned by rrd_fetch)
     */
    protected ?int $start = null;

    /**
     * End time of RRD data (as returned by rrd_fetch)
     */
    protected ?int $end = null;

    /**
     * Step interval RRD data (as returned by rrd_fetch)
     */
    protected ?int $step = null;

    /**
     * Data from RRD file formatted as a MRTG Log array as returned by
     * IXP\Utils\Grapher\Mrtg
     * @var string[]
     */
    protected array $array = [];

    /**
     * Graph object under consideration
     * @var Graph
     */
    protected readonly Graph $graph;

    /**
     * Period times.
     *
     * these values are taken from mrtg/src/rateup.c
     */
    public const PERIOD_TIME = Mrtg::PERIOD_TIME;


    /**
     * Prefix for local cached files
     */
    public const LOCAL_CACHE_RRD_PREFIX = "ixp-utils-multirrd-";

    /**
     * Class constructor.
     *
     * @param  string[]  $files  The RRD log file to load for analysis
     * @param  Graph     $graph  The graph object
     *
     * @throws FileErrorException
     */
    public function __construct( array $files, Graph $graph )
    {
        $this->realfiles = $files;
        $this->graph     = $graph;
        $this->loadRrdFiles();
    }

    /**
     * Class destructor
     */
    public function __destruct()
    {
        $this->deleteLocalCopies();
    }

    /**
     * Accessor for PERIOD_TIME
     *
     * @param string $period
     * @return float
     */
    public function getPeriodTime( string $period ): float
    {
        return self::PERIOD_TIME[ $period ] ?? 0.0;
    }

    /**
     * Access for the graph object under consideration
     *
     * @return Graph
     */
    private function graph(): Graph
    {
        return $this->graph;
    }

    /**
     * Get the local directory name.
     *
     * @see getLocalCopy() for detals
     * @see getLocalFilename() for detals
     *
     * @return string The full path to the local directory
     *
     * @throws FileErrorException
     */
    private function getLocalDirectory(): string
    {
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
     * @param  string $ext The extension
     *
     * @return string The full path to the local copy
     *
     * @throws FileErrorException
     */
    private function getLocalFilename( $file, ?string $exten = null ): string
    {
        $parts = explode( '/', $file );
        $name  = $parts[ array_key_last( $parts ) ];

        return "{$this->getLocalDirectory()}/" . self::LOCAL_CACHE_RRD_PREFIX . "$name." . ($exten ?? '' );
    }

    /**
     * If necessary, copy a remote rrd to a local file (temporarily)
     *
     * Most PHP file functions support URI's such as http://.
     *
     * Naturally, rrd_* is an exception :-(
     *
     * This function creates a local copy of the remote file.
     *
     * If the remote file cannot be found, or if the requested file is
     * local but does not exist, false is returned instead.
     *
     * This deviates from the other RRD class, as there are more transient
     * reasons that a file might not exist in this case.
     *
     * @see removeLocalCopies()
     *
     * @return string|false The full path to the local copy, or false if it does not exist.
     *
     * @throws FileErrorException
     */
    private function getLocalCopy( string $file ): string|false
    {
        // if it's already local, just return it, if it exists.
        if( !str_starts_with( $file, 'http' ) ) {
            if( !file_exists( $file ) ) {
                return false;
            }
            return $file;
        }

        $localname = $this->getLocalFilename( $file );

        // does the local file exist and is it less than 5mins old?
        if( !file_exists($localname) || !( time() - filemtime($localname) < 300 ) ) {
            if( !( $r = @file_get_contents( $file ) ) ) {
                return false;
            }
            if( !( @file_put_contents( $localname, $r ) ) ) {
                throw new FileErrorException("Could not create local RRD copy");
            }
        }

        return $localname;
    }

    public function localfiles(): array
    {
        return $this->localfiles;
    }

    /**
     * If necessary, delete local copies of a remote rrds
     *
     * @see getLocalCopy() for an explanation
     */
    private function deleteLocalCopies(): void
    {
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
     *
     * @throws FileErrorException
     */
    protected function loadRrdFiles(): void
    {
        $missing = [];
        // we need to allow for remote files but php's rrd_* functions don't support this
        foreach( $this->realfiles as $file ) {
            if( ( $localCopy = $this->getLocalCopy( $file ) ) ) {
                $this->localfiles[] = $localCopy;
            } else {
                $missing[] = $file;
            }
        }

        if (count($missing)) {
            Log::notice("Some remote RRD files could not be found: " . implode(", ", $missing));
        }

        if (count($this->localfiles) === 0) {
            throw new FileErrorException("No local files found after loading.");
        }
    }

    /**
     * Get the RRD file
     *
     * @throws BackendFeatureNotImplementedException
     */
    public function rrd()
    {
        throw new BackendFeatureNotImplementedException();
    }

    /**
     * The MRTG files have different data source names than the sflow files.
     *
     * This returns the appropriate keys to use when indexing arrays.
     *
     * @return array
     */
    private function getIndexKeys(): array
    {
        if( $this->graph()->backend()->name() === 'mrtg' ) {
            return [ 'ds0', 'ds1' ];  // in out
        }

        return [ 'traffic_in', 'traffic_out' ];
    }

    /**
     * From the RRD file, process the data and return it in the same format
     * as an MRTG log file.
     *
     * @see \IXP\Utils\Grapher\Mrtg::loadMrtgFile()
     *
     * Processing means:
     * - only returning the values for the requested period
     * - MRTG/RRD provides traffic as bytes, change to bits
     *
     * @return array
     *
     */
    public function data(): array
    {
        throw new BackendFeatureNotImplementedException();
    }

    /**
     * From the RRD file, process and return a png
     *
     * @throws FileErrorException
     */
    public function png(): string
    {
        $separated_maxima = self::PERIOD_TIME[ $this->graph()->period() ] > 60*60*24*2;

        [ $indexIn, $indexOut ] = $this->getIndexKeys();

        $options = [
            '--width=600',
            '--height=150',
            '--slope-mode',
            '--start', time() - self::PERIOD_TIME[ $this->graph()->period() ],
            '--lower-limit=0',
            '--title=' . $this->graph()->title(),
            '--vertical-label=' . $this->graph()->category() . ' / second',
            '--watermark=' . $this->graph()->watermark(),
            '--font=TITLE:10:Times',
            '--font=WATERMARK:8:Times',
            '--font=LEGEND:8:Courier',
            '--font=AXIS:6:Courier',
            '--font=UNIT:6:Courier',
        ];

        $cnt = 0;
        $a = $b = $c = $d = '';
        foreach( $this->localfiles as $f ) {
            $options[] = "DEF:a{$cnt}={$f}:{$indexIn}:AVERAGE";
            $options[] = "DEF:b{$cnt}={$f}:{$indexIn}:MAX";
            $options[] = "DEF:c{$cnt}={$f}:{$indexOut}:AVERAGE";
            $options[] = "DEF:d{$cnt}={$f}:{$indexOut}:MAX";

            $a .= "a{$cnt},";
            $b .= "b{$cnt},";
            $c .= "c{$cnt},";
            $d .= "d{$cnt},";
            $cnt++;
        }

        $options[] = "CDEF:atotal={$a}" . str_repeat( '+,', $cnt-2 ) . '+';
        $options[] = "CDEF:btotal={$b}" . str_repeat( '+,', $cnt-2 ) . '+';
        $options[] = "CDEF:ctotal={$c}" . str_repeat( '+,', $cnt-2 ) . '+';
        $options[] = "CDEF:dtotal={$d}" . str_repeat( '+,', $cnt-2 ) . '+';

        $options[] = "CDEF:cdefa=atotal," . ( $this->graph()->category() === Graph::CATEGORY_BITS ? '8' : '1' ) . ',*';
        $options[] = "CDEF:cdefb=btotal," . ( $this->graph()->category() === Graph::CATEGORY_BITS ? '8' : '1' ) . ',*';
        $options[] = "CDEF:cdefc=ctotal," . ( $this->graph()->category() === Graph::CATEGORY_BITS ? '8' : '1' ) . ',*';
        $options[] = "CDEF:cdefd=dtotal," . ( $this->graph()->category() === Graph::CATEGORY_BITS ? '8' : '1' ) . ',*';

        $options[] = 'VDEF:last_in=cdefa,LAST';
        $options[] = 'VDEF:last_out=cdefc,LAST';
        $options[] = 'VDEF:max_in=cdefb,MAXIMUM';
        $options[] = 'VDEF:max_out=cdefd,MAXIMUM';
        $options[] = 'VDEF:avg_in=cdefb,AVERAGE';
        $options[] = 'VDEF:avg_out=cdefd,AVERAGE';

        $options[] = 'COMMENT:Out';

        if( $separated_maxima ) {
            $options[] = 'AREA:cdefd#006600:Peak';
            $options[] = 'GPRINT:max_out:%6.2lf%s\t';
            $options[] = 'AREA:cdefc#00CF00:Avg';
        } else {
            $options[] = 'AREA:cdefc#00CF00:Max';
            $options[] = 'GPRINT:max_out:%6.2lf%s\t';
            $options[] = 'COMMENT:Avg';
        }
        $options[] = 'GPRINT:avg_out:%6.2lf%s';
        $options[] = 'GPRINT:last_out:\tCur\\: %6.2lf%s\l';

        $options[] = 'COMMENT:In ';

        if( $separated_maxima ) {
            $options[] = 'LINE1:cdefb#ff00ff:Peak';
            $options[] = 'GPRINT:max_in:%6.2lf%s\t';
            $options[] = 'LINE2:cdefa#002A97FF:Avg';
        } else {
            $options[] = 'LINE2:cdefa#002A97FF:Max';
            $options[] = 'GPRINT:max_in:%6.2lf%s\t';
            $options[] = 'COMMENT:Avg';
        }
        $options[] = 'GPRINT:avg_in:%6.2lf%s';
        $options[] = 'GPRINT:last_in:\tCur\\: %6.2lf%s\l';

        $options[] = 'COMMENT:\s';

        $png = rrd_graph( $this->getLocalFilename( $this->graph->identifier(), 'png'), $options );

        if( $png === false ) {
            throw new FileErrorException("Could not open/create RRD/PNG file");
        }

        return $this->getLocalFilename( $this->graph->identifier(), 'png');
    }

    /**
     * Accessor method for $array - the data from the MRTG file.
     *
     * @return array The data from the MRTG file
     */
    public function getArray(): array
    {
        throw new BackendFeatureNotImplementedException();
    }
}