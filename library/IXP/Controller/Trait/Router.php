<?php

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


/**
 * A trait of common router functions (mainly config generation)
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller_Trait
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
trait IXP_Controller_Trait_Router
{
    /**
     * Common generation tasks for route collectors shared between CLI and API interfaces.
     *
     * @param \Entities\Vlan $vlan The VLAN object
     * @param int $proto 4/6
     * @param string $target The directory containing the Smarty templates
     * @param bool $quarantine If true, select only interfaces in the quaratine lan
     * @return string The configuration
     */
    protected function generateCollectorConfiguration( $vlan, $proto, $target, $quarantine = false )
    {
        if( !$proto || $proto == 4 )
            $this->view->v4ints = $this->sanitiseVlanInterfaces( $vlan, 4, false, $quarantine );

        if( !$proto || $proto == 6 )
            $this->view->v6ints = $this->sanitiseVlanInterfaces( $vlan, 6, false, $quarantine );

        return $this->view->render( "router-cli/collector/{$target}/index.cfg" );
    }

    /**
     * Utility function to get and return active VLAN interfaces on the requested protocol
     * suitable for route collector / server configuration.
     *
     * Sample return:
     *
     *     [
     *         [cid] => 999
     *         [cname] => Customer Name
     *         [cshortname] => shortname
     *         [autsys] => 65000
     *         [peeringmacro] => QWE              // or AS65500 if not defined
     *         [vliid] => 159
     *         [fvliid] => 00159                  // formatted %05d
     *         [address] => 192.0.2.123
     *         [bgpmd5secret] => qwertyui         // or false
     *         [as112client] => 1                 // if the member is an as112 client or not
     *         [rsclient] => 1                    // if the member is a route server client or not
     *         [maxprefixes] => 20
     *         [irrdbfilter] => 0/1               // if IRRDB filtering should be applied
     *         [location_name] => Interxion DUB1
     *         [location_shortname] => IX-DUB1
     *         [location_tag] => ix1
     *     ]
     *
     * @param \Entities\Vlan $vlan
     * @param int $proto
     * @param bool $rsclient Find VLAN interfaces for route server config
     * @param bool $quarantine Use interfaces in quarantine rather than production
     * @return array As defined above
     */
    private function sanitiseVlanInterfaces( $vlan, $proto, $rsclient = false, $quarantine = false )
    {
        $ints = $this->getD2R( '\\Entities\\VlanInterface' )->getForProto( $vlan, $proto, false,
            ( $quarantine ? \Entities\PhysicalInterface::STATUS_QUARANTINE : \Entities\PhysicalInterface::STATUS_CONNECTED )
        );

        $newints = [];

        foreach( $ints as $int )
        {
            if( !$int['enabled'] )
                continue;

            if( $rsclient && !$int['rsclient'] )
                continue;

            // Due the the way we format the SQL query to join with physical
            // interfaces (of which there may be multiple per VLAN interface),
            // we need to weed out duplicates
            if( isset( $newints[ $int['address'] ] ) )
                continue;

            unset( $int['enabled'] );

            $int['fvliid'] = sprintf( '%04d', $int['vliid'] );

            if( $int['maxbgpprefix'] && $int['maxbgpprefix'] > $int['gmaxprefixes'] )
                $int['maxprefixes'] = $int['maxbgpprefix'];
            else
                $int['maxprefixes'] = $int['gmaxprefixes'];

            if( !$int['maxprefixes'] )
                $int['maxprefixes'] = 20;

            unset( $int['gmaxprefixes'] );
            unset( $int['maxbgpprefix'] );

            if( $proto == 6 && $int['peeringmacrov6'] )
                $int['peeringmacro'] = $int['peeringmacrov6'];

            if( !$int['peeringmacro'] )
                $int['peeringmacro'] = 'AS' . $int['autsys'];

            unset( $int['peeringmacrov6'] );

            if( !$int['bgpmd5secret'] )
                $int['bgpmd5secret'] = false;

            $newints[ $int['address'] ] = $int;
        }

        return $newints;
    }

    /**
     * The collector configuration expects some data to be available. This function
     * gathers and checks that data.
     *
     */
    private function collectorConfSanityCheck( $vlan )
    {
        /*
         // get the available reoute collectors and set the IP of the first as
        // the route collector router ID.
        $collectors = $vlan->getRouteCollectors( \Entities\Vlan::PROTOCOL_IPv4 );

        if( !is_array( $collectors ) || !count( $collectors ) )
        {
        die(
            "ERROR: Not IPv4 route collectors defined in the VLANs network information table\n"
            . "    See: https://github.com/inex/IXP-Manager/wiki/Network-Information\n"
        );
        }

        $this->view->routerId = $collectors[0];
        */

        /*
         if( !isset( $this->_options['router']['collector']['conf']['asn'] ) )
            die( "ERROR: No route collector ASN configured in application.ini\n");
        */
    }


}

