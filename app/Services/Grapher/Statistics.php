<?php namespace IXP\Services\Grapher;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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

/**
 * Grapher -> Statistics of the given graph
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
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
     * @var Graph
     */
    private $graph;


    /**
     * Constructor
     * @param Graph $g
     */
    public function __construct( Graph $g ) {
        $this->graph = $g;
        $this->process();
    }

    /**
     * Access for the graph object under consideration
     * @return Graph
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
     * @return array
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

        $data = $this->data();

        if( is_array( $data ) && isset( $data[0][0] ) ) {
            $curenddate = false;
            $starttime  = $this->data()[0][0];

            foreach( $data as $i => $v ) {
                $curenddate = $v[ 0 ];

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
                    $intRange    = ($intTime > $intLastTime) ? $intTime - $intLastTime : $intLastTime - $intTime;
                    $totalIn    += ( $intRange * $avgratein );
                    $totalOut   += ( $intRange * $avgrateout );
                    $intLastTime = $intTime;
                }
            }

            $endtime   = $curenddate;
            $totalTime = $endtime - $starttime;
        }

        $curIn     = isset( $avgratein )               ? $avgratein  : 0.0;
        $curOut    = isset( $avgrateout )              ? $avgrateout : 0.0;
        $totalTime = isset( $totalTime ) && $totalTime ? $totalTime  : 1;

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
     * Set statistics value
     * @param float $v
     * @return Statistics (for fluid interface)
     */
    public function setTotalIn( float $v ): Statistics {
        $this->totalIn = $v;
        return $this;
    }

    /**
     * Set statistics value
     * @param float $v
     * @return Statistics (for fluid interface)
     */
    public function setTotalOut( float $v ): Statistics {
        $this->totalOut = $v;
        return $this;
    }

    /**
     * Set statistics value
     * @param float $v
     * @return Statistics (for fluid interface)
     */
    public function setCurrentIn( float $v ): Statistics {
        $this->curIn = $v;
        return $this;
    }

    /**
     * Set statistics value
     * @param float $v
     * @return Statistics (for fluid interface)
     */
    public function setCurrentOut( float $v ): Statistics {
        $this->curOut = $v;
        return $this;
    }

    /**
     * Set statistics value
     * @param float $v
     * @return Statistics (for fluid interface)
     */
    public function setAverageIn( float $v ): Statistics {
        $this->averageIn = $v;
        return $this;
    }

    /**
     * Set statistics value
     * @param float $v
     * @return Statistics (for fluid interface)
     */
    public function setAverageOut( float $v ): Statistics {
        $this->averageOut = $v;
        return $this;
    }

    /**
     * Set statistics value
     * @param float $v
     * @return Statistics (for fluid interface)
     */
    public function setMaxIn( float $v ): Statistics {
        $this->maxIn = $v;
        return $this;
    }

    /**
     * Set statistics value
     * @param float $v
     * @return Statistics (for fluid interface)
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
            'totalin'     => $this->totalIn(),
            'totalout'    => $this->totalOut(),
            'curin'       => $this->curIn(),
            'curout'      => $this->curOut(),
            'averagein'   => $this->averageIn(),
            'averageout'  => $this->averageOut(),
            'maxin'       => $this->maxIn(),
            'maxout'      => $this->maxOut(),
        ];
    }

}
