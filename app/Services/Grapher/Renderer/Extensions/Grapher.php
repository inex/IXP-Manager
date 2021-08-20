<?php

namespace IXP\Services\Grapher\Renderer\Extensions;

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

use Foil\Contracts\ExtensionInterface;

use IXP\Services\Grapher\Graph\{
    Customer as CustomerGraph,
    Latency  as LatencyGraph,
    P2p      as P2pGraph
};

/**
 * Grapher -> Renderer view extensions
 *
 * See: http://www.foilphp.it/docs/EXTENDING/CUSTOM-EXTENSIONS.html
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Grapher implements ExtensionInterface
{
    
    private $args;

    public function setup(array $args = [])
    {
        $this->args = $args;
    }

    public function provideFilters()
    {
       return [];
    }
    
    public function provideFunctions()
    {
        return [
          'grapher' => [$this, 'getObject']
        ];
    }

    public function getObject(): Grapher
    {
       return $this;
    }

    /**
     * Escape SNMP communities so they are suitable for use in Mrtg config files.
     *
     * As per: https://oss.oetiker.ch/mrtg/doc/mrtg-reference.en.html
     *
     * > If your community contains a "@" or a " " these characters must be escaped with a "\".
     *
     * @param string $c The SNMP community to be escaped
     *
     * @return string The escaped community
     */
    public function escapeCommunityForMrtg( string $c ): string
    {
        return str_replace( '@', '\@', str_replace(' ', '\\ ', $c ) );
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
    *
    * @return string            Scaled / formatted number / type.
    */
    public function scale( float $v, string $format, int $decs = 3, int $returnType = 0 ): string
    {
        if( $format === "bytes" ) {
            $formats = [
                "Bytes", "KBytes", "MBytes", "GBytes", "TBytes"
            ];
        } else if( in_array( $format, [ 'pkts', 'errs', 'discs', 'bcasts' ] ) ) {
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
            if( ( $v / 1000.0 < 1.0 ) || ( sizeof( $formats ) === $i + 1 ) ) {
                if( $returnType == 0 )
                    return number_format( $v, $decs ) . "&nbsp;" . $formats[$i];
                elseif( $returnType == 1 )
                    return number_format( $v, $decs );
                else
                    return $formats[$i];
            } else {
                $v /= 1000.0;
            }
        }

        // should not get here:
        return '';
    }

    /**
     * Utility function to determine if the currently logged in user can access 'all customer' graphs
     *
     * @return bool
     */
    public function canAccessAllCustomerGraphs(): bool
    {
        return CustomerGraph::authorisedForAllCustomers();
    }

    /**
     * Utility function to determine if the currently logged in user can access 'all customers' p2p' graphs
     *
     * @return bool
     */
    public function canAccessAllCustomerP2pGraphs(): bool
    {
        return P2pGraph::authorisedForAllCustomers();
    }

    /**
     * Utility function to determine if the currently logged in user can access 'all customers' latency' graphs
     *
     * @return bool
     */
    public function canAccessAllCustomerLatencyGraphs(): bool
    {
        return LatencyGraph::authorisedForAllCustomers();
    }
}