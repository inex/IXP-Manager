<?php

/*
 * Copyright (C) 2009-2013 Internet Neutral Exchange Association Limited.
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
 * Controller: Router CLI Actions (such as collectors and servers)
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (c) 2009 - 2013, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class RouterCliController extends IXP_Controller_CliAction
{
    /**
     * Action to generate a route collector configuration
     */
    public function genCollectorConfAction()
    {
        $this->view->vlan = $vlan = $this->cliResolveVlanId();

        $target = $this->cliResolveTarget(
            isset( $this->_options['router']['collector']['conf']['target'] )
                ? $this->_options['router']['collector']['conf']['target']
                : false
        );

        $this->view->asn = $this->cliResolveASN(
                isset( $this->_options['router']['collector']['conf']['asn'] )
                ? $this->_options['router']['collector']['conf']['asn']
                : false
        );

        $this->collectorConfSanityCheck( $vlan );

        $this->view->proto = $proto = $this->cliResolveProtocol( false );

        if( !$proto || $proto == 4 )
            $this->view->v4ints = $this->sanitiseVlanInterfaces( $vlan, 4 );

        if( !$proto || $proto == 6 )
            $this->view->v6ints = $this->sanitiseVlanInterfaces( $vlan, 6 );

        if( isset( $this->_options['router']['collector']['conf']['dstpath'] ) )
        {
            if( !$this->writeConfig( $this->_options['router']['collector']['conf']['dstpath'] . "/rc-{$vlan->getId()}.conf",
                    $this->view->render( "router-cli/collector/{$target}/index.cfg" ) ) )
            {
                fwrite( STDERR, "Error: could not save configuration data\n" );
            }
        }
        else
            echo $this->view->render( "router-cli/collector/{$target}/index.cfg" );
    }


    /**
     * Action to generate an AS112 router configuration
     */
    public function genAs112ConfAction()
    {
        $this->view->vlan = $vlan = $this->cliResolveVlanId();

        $target = $this->cliResolveTarget(
            isset( $this->_options['router']['as112']['conf']['target'] )
                ? $this->_options['router']['as112']['conf']['target']
                : false
        );

        if( $this->getParam( 'rc', false ) )
        {
            $this->view->routeCollectors   = $vlan->getRouteCollectors( \Entities\Vlan::PROTOCOL_IPv4 );
            $this->view->routeCollectorASN = $this->getParam( 'rcasn', 65500 );
        }

        $this->view->v4ints = $this->sanitiseVlanInterfaces( $vlan, 4 );

        if( isset( $this->_options['router']['as112']['conf']['dstpath'] ) )
        {
            if( !$this->writeConfig( $this->_options['router']['as112']['conf']['dstpath'] . "/as112-{$vlan->getId()}.conf",
                    $this->view->render( "router-cli/as112/{$target}/index.cfg" ) ) )
            {
                fwrite( STDERR, "Error: could not save configuration data\n" );
            }
        }
        else
            echo $this->view->render( "router-cli/as112/{$target}/index.cfg" );
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

    /**
     * Utility function to get and return active VLAN interfaces on the requested protocol
     * suitable for route collector configuration.
     *
     * Sample return:
     *
     *     [
     *         [cid] => 999
     *         [cname] => Customer Name
     *         [cshortname] => shortname
     *         [autsys] => 65000
     *         [peeringmacro] => QWE       // or AS65500 if not defined
     *         [vliid] => 159
     *         [address] => 192.0.2.123
     *         [bgpmd5secret] => qwertyui  // or false
     *         [as112client] => 1          // if the member is an as112 client or not
     *         [rsclient] => 1             // if the member is a route server client or not
     *         [maxprefixes] => 20
     *     ]
     *
     * @param \Entities\Vlan $vlan
     * @param int $proto
     * @return array As defined above
     */
    private function sanitiseVlanInterfaces( $vlan, $proto )
    {
        $ints = $this->getD2R( '\\Entities\\VlanInterface' )->getForProto( $vlan, $proto, false );
        $newints = [];

        foreach( $ints as $int )
        {
            if( !$int['enabled'] )
                continue;

            // Due the the way we format the SQL query to join with physical
            // interfaces (of which there may be multiple per VLAN interface),
            // we need to weed out duplicates
            if( isset( $newints[ $int['address'] ] ) )
                continue;

            unset( $int['enabled'] );

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
}

