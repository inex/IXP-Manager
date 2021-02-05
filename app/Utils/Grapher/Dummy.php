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

use IXP\Exceptions\Services\Grapher\GeneralException;

use IXP\Services\Grapher\Graph;

/**
 * A class to handle **dummy** Mrtg log files
 *
 * @author     Nick Hilliard <nick@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Utils\Grapher
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Dummy extends Mrtg
{
    /**
     * Class constructor.
     *
     * @param string $file The MRTG log file to load for analysis
     */
    public function __construct( string $file )
    {
        parent::__construct( $file );
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

        $starttime  = $this->array[0][0] - $periodsecs;
        $endtime    = $this->array[0][0];

        // Run through the array and pull out the values we want
        for( $i = sizeof( $this->array )-1; $i >= 0; $i-- ) {
            // process within start / end time
            if( ($this->array[ $i ][ 0 ] >= $starttime) && ($this->array[ $i ][ 0 ] <= $endtime) ) {
                $values[] = $this->array[ $i ]; //(int)floor( $this->array[ $i ] * ( 1.0 + ( (float)mt_rand( 1, 20 ) / 100.0 ) ) );
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
        } else if( in_array( $graph->category(), [ Graph::CATEGORY_ERRORS, Graph::CATEGORY_DISCARDS ], false ) ) {
            foreach( $values as $i => $v ) {
                $values[$i][1] *= (int)floor( ( mt_rand( 0, 10 ) / 1000.0 ) );
                $values[$i][2] *= (int)floor( ( mt_rand( 0, 10 ) / 1000.0 ) );
                $values[$i][3]  = (int)floor( $values[$i][1] * 0.1 );
                $values[$i][4]  = (int)floor( $values[$i][2] * 0.1 );
            }
        }
        return $values;
    }
}