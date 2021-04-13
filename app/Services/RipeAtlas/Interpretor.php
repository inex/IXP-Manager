<?php

namespace IXP\Services\RipeAtlas;

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

use IXP\Models\{
    AtlasMeasurement,
    AtlasResult,
    Customer
};

/**
 * RipeAtlas Interpretor
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Services\RipeAtlas
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Interpretor
{
    /**
     * Basic Interpretor
     * FIXME?: Has to be adapted for ripe atlas
     *
     * @param  AtlasMeasurement  $atlasMeasurement
     *
     * @return AtlasResult
     *
     * @throws
     */
    public function interpret( AtlasMeasurement $atlasMeasurement ): AtlasResult
    {
        $atlasRun = $atlasMeasurement->atlasRun;

        // what is the source network's peering addresses?
        $srcAddrs = Customer::addressesForVlan( $atlasRun->vlan_id, $atlasMeasurement->cust_source, $atlasRun->protocol );
        $dstAddrs = Customer::addressesForVlan( $atlasRun->vlan_id, $atlasMeasurement->cust_dest,  $atlasRun->protocol );

        // traceroute from different routers can use the ingress or egress path so we merge these:
        $allLanAddrs = array_merge( $dstAddrs->toArray(), $srcAddrs->toArray() );

        $atlas_data = json_decode( $atlasMeasurement->atlas_data, false, 512, JSON_THROW_ON_ERROR);

        $path = $this->parsePath( $atlas_data );

        $viaLan = $this->queryPassesThrough( $path, $allLanAddrs );

        //dd( $viaLan, $path);

        $ar = new AtlasResult;

        if( $viaLan ) {
            $ar->routing = 'IXP_LAN_SYM';
        } else {
            //$viaIx = $this->queryPassesThrough( $path, $allIxpAddrs );
            //
            //            if( ( $viaIxpOut && $viaIxpIn ) || ( $viaIxpOut && $viaLanIn ) || ( $viaLanOut && $viaIxpIn ) ) {
            //                $ar->setAttribute( 'routing', 'IXP_SYM' );
            //            } else if( !$viaIxpOut && $viaIxpIn ) {
            //                $ar->setAttribute( 'routing', 'IXP_ASYM_OUT' );
            //            } else if( $viaIxpOut && !$viaIxpIn ) {
            //                $ar->setAttribute( 'routing', 'IXP_ASYM_IN' );
            //            } else {
            //                $ar->setAttribute( 'routing', 'NON_IXP' );
            //            }
        }

        $ar->path = serialize( $path );

        $ar->save();

        return $ar;
    }

    /**
     * Take a RIPE Atles traceroute result and extract the path
     *
     * NB: FIXME?: Assumes no ECMP... takes only one IP per hop.
     *
     * @param array $tracert Raw RIPE Atles JSON result as PHP
     *
     * @return array The path
     */
    private function parsePath( array $tracert ): array
    {
        $path = [
            'hops' => [],
            'ixpx' => [],  // point of intersection with IXP
        ];

        foreach( $tracert[0]->result as $hop ) {
            // three iterations means each hop has three results:
            $results = [];
            foreach( $hop->result as $result ) {
                if( !isset( $result->from ) ) {
                    continue;
                }

                if( in_array($result->from, $results, true ) ) {
                    continue;
                }

                $results[] = $result->from;
            }

            if( count($results) ) {
                $path['hops'][] = $results;
            } else {
                $path['hops'][] = [ '*' ];
            }
        }

        return $path;
    }

    /**
     * For a given path of IP addresses, see if another list of addresses appears in the path
     *
     * @param array $path Path of IP addresses
     * @param array $addrs List of addresses to find in $path
     *
     * @return bool
     */
    private function queryPassesThrough( array &$path, array $addrs )
    {

        foreach( $path[ 'hops' ] as $ipset ) {
            foreach( $ipset as $ip ) {
                if( in_array( $ip, $addrs ) ) {
                    $path['ixpx'][] = $ip;
                }
            }
        }

        return count( $path[ 'ixpx' ] );
    }
}