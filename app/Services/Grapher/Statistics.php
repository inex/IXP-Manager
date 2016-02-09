<?php namespace IXP\Services\Grapher;

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

use IXP\Services\Grapher;
use IXP\Services\Grapher\Graph;

/**
 * Grapher -> Statistics of the given graph
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (c) 2009 - 2016, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Statistics {

    /**
     * Total packets/bits in
     * @var int
     */
    private $totalIn;

    /**
     * Total packets/bits out
     * @var int
     */
    private $totalOut;

    /**
     * Current packets/bits in
     * @var int
     */
    private $curIn;

    /**
     * Current packets/bits out
     * @var int
     */
    private $curOut;

    /**
     * Average packets/bits in
     * @var int
     */
    private $averageIn;

    /**
     * Average packets/bits out
     * @var int
     */
    private $averageOut;

    /**
     * Max packets/bits in
     * @var int
     */
    private $maxIn;

    /**
     * Max packets/bits out
     * @var int
     */
    private $maxOut;

    /**
     * Graph under consideration
     * @var IXP\Services\Grapher\Graph
     */
    private $graph;


    /**
     * Constructor
     */
    public function __construct( Graph $g ) {
        $this->graph = $g;
        $this->process();
    }

    /**
     * Access for the graph object under consideration
     * @return IXP\Services\Grapher\Graph
     */
    public function graph(): Graph {
        return $this->graph;
    }

    /**
     * Access for the graph object under consideration
     *
     * Private access as this should be accessed publically
     * via the Graph object (where it is also cached).
     *
     * @return IXP\Services\Grapher\Graph
     */
    private function data(): array {
        return $this->graph()->data();
    }

    function process() {
        $maxIn = 0;
        $maxOut = 0;
        $totalIn = 0;
        $totalOut = 0;
        $intLastTime = 0;
        $intTime = 0;

        $gotrealstartdate = false;
        $curenddate = false;

        foreach( $this->data() as $i => $v ) {
            $curenddate = $v[ 0 ];

            if( !$gotrealstartdate && $v[ 0 ] ) {
                $starttime = $v[ 0 ];
                $gotrealstartdate = true;
            }

            list( $intTime, $avgratein, $avgrateout, $peakratein, $peakrateout ) = $v;

            if( $peakratein > $maxIn ) {
                $maxIn = $peakratein;
            }
            if( $peakrateout > $maxOut ) {
                $maxOut = $peakrateout;
            }

            if( $intLastTime == 0 ) {
                $intLastTime = $intTime;
            }
            else {
                $intRange    = $intTime - $intLastTime;
                $totalIn    += ( $intRange * $avgratein );
                $totalOut   += ( $intRange * $avgrateout );
                $intLastTime = $intTime;
            }
        }

        $endtime   = $curenddate;
        $totalTime = $endtime - $starttime;

        $curIn  = isset( $avgratein )  ? $avgratein  : 0.0;
        $curOut = isset( $avgrateout ) ? $avgrateout : 0.0;

        $this->setTotalIn(    floor( $totalIn  )                )
            ->setTotalOut(    floor( $totalOut )                )
            ->setCurrentIn(   (float)$curIn                     )
            ->setCurrentOut(  (float)$curOut                    )
            ->setMaxIn(       (float)$maxIn                     )
            ->setMaxOut(      (float)$maxOut                    )
            ->setAverageIn(   (float)( $totalIn  / $totalTime ) )
            ->setAverageOut(  (float)( $totalOut / $totalTime ) );
    }

    /**
     * Scale function
     *
     * This function will scale a number to (for example for traffic
     * measured in bits/second) to Kbps, Mbps, Gbps or Tbps.
     *
     * Valid string formats ($strFormats) and what they return are:
     *    bytes               => Bytes, KBytes, MBytes, GBytes, TBytes
     *    pkts / errs / discs => pps, Kpps, Mpps, Gpps, Tpps
     *    bits / *            => bits, Kbits, Mbits, Gbits, Tbits
     *
     * Valid return types ($format) are:
     *    0 => fully formatted and scaled value. E.g.  12,354.235 Tbits
     *    1 => scaled value without string. E.g. 12,354.235
     *    2 => just the string. E.g. Tbits
     *
     * @param float  $v          The value to scale
     * @param string $format     The format to sue (as above: bytes / pkts / errs / etc )
     * @param int    $decs       Number of decimals after the decimal point. Defaults to 3.
     * @param int    $returnType Type of string to return. Valid values are listed above. Defaults to 0.
     * @return string            Scaled / formatted number / type.
     */
    public static function scale( float $v, string $format, int $decs = 3, int $returnType = 0 ): string {
        if( $format == "bytes" ) {
            $formats = [
                "Bytes", "KBytes", "MBytes", "GBytes", "TBytes"
            ];
        } else if( in_array( $format, [ 'pkts', 'errs', 'discs' ] ) ) {
            $formats = [
                "pps", "Kpps", "Mpps", "Gpps", "Tpps"
            ];
        } else {
            $formats = [
                "bits", "Kbits", "Mbits", "Gbits", "Tbits"
            ];
        }

        for( $i = 0; $i < sizeof( $formats ); $i++ )
        {
            if( ( $v / 1000.0 < 1.0 ) || ( sizeof( $formats ) == $i + 1 ) ) {
                if( $returnType == 0 )
                    return number_format( $v, $decs ) . " " . $formats[$i];
                elseif( $returnType == 1 )
                    return number_format( $v, $decs );
                else
                    return $formats[$i];
            } else {
                $v /= 1000.0;
            }
        }
    }



    /**
     * Set statistics value
     * @param float $v
     * @return IXP\Services\Grapher\Statistics (for fluid interface)
     */
    public function setTotalIn( float $v ): Statistics {
        $this->totalIn = $v;
        return $this;
    }

    /**
     * Set statistics value
     * @param float $v
     * @return IXP\Services\Grapher\Statistics (for fluid interface)
     */
    public function setTotalOut( float $v ): Statistics {
        $this->totalOut = $v;
        return $this;
    }

    /**
     * Set statistics value
     * @param float $v
     * @return IXP\Services\Grapher\Statistics (for fluid interface)
     */
    public function setCurrentIn( float $v ): Statistics {
        $this->curIn = $v;
        return $this;
    }

    /**
     * Set statistics value
     * @param float $v
     * @return IXP\Services\Grapher\Statistics (for fluid interface)
     */
    public function setCurrentOut( float $v ): Statistics {
        $this->curOut = $v;
        return $this;
    }

    /**
     * Set statistics value
     * @param float $v
     * @return IXP\Services\Grapher\Statistics (for fluid interface)
     */
    public function setAverageIn( float $v ): Statistics {
        $this->averageIn = $v;
        return $this;
    }

    /**
     * Set statistics value
     * @param float $v
     * @return IXP\Services\Grapher\Statistics (for fluid interface)
     */
    public function setAverageOut( float $v ): Statistics {
        $this->averageOut = $v;
        return $this;
    }

    /**
     * Set statistics value
     * @param float $v
     * @return IXP\Services\Grapher\Statistics (for fluid interface)
     */
    public function setMaxIn( float $v ): Statistics {
        $this->maxIn = $v;
        return $this;
    }

    /**
     * Set statistics value
     * @param float $v
     * @return IXP\Services\Grapher\Statistics (for fluid interface)
     */
    public function setMaxOut( float $v ): Statistics {
        $this->maxOut = $v;
        return $this;
    }

    /**
     * Get statistics value
     * @return float
     */
    public function totalIn(): float {
        return $this->totalIn;
    }

    /**
     * Get statistics value
     * @return float
     */
    public function totalOut(): float {
        return $this->totalOut;
    }

    /**
     * Get statistics value
     * @return float
     */
    public function curIn(): float {
        return $this->curIn;
    }

    /**
     * Get statistics value
     * @return float
     */
    public function curOut(): float {
        return $this->curOut;
    }

    /**
     * Get statistics value
     * @return float
     */
    public function averageIn(): float {
        return $this->averageIn;
    }

    /**
     * Get statistics value
     * @return float
     */
    public function averageOut(): float {
        return $this->averageOut;
    }

    /**
     * Get statistics value
     * @return float
     */
    public function maxIn(): float {
        return $this->maxIn;
    }

    /**
     * Get statistics value
     * @return float
     */
    public function maxOut(): float {
        return $this->maxOut;
    }

    /**
     * Get all defined stats as an associative array
     * @return array
     */
    public function all(): array {
        return [
            'totalIn'     => $this->totalIn(),
            'totalOut'    => $this->totalOut(),
            'curIn'       => $this->curIn(),
            'curOut'      => $this->curOut(),
            'averageIn'   => $this->averageIn(),
            'averageOut'  => $this->averageOut(),
            'maxIn'       => $this->maxIn(),
            'maxOut'      => $this->maxOut(),
        ];
    }

}
