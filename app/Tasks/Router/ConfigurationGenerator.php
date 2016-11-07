<?php

declare(strict_types=1);
namespace IXP\Tasks\Router;

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

use Entities\Vlan;
use Illuminate\Contracts\View\View as ViewContract;
use IXP\Exceptions\GeneralException;
use View;

/**
 * ConfigurationGenerator
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Tasks
 * @package    IXP\Tasks\Router
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ConfigurationGenerator
{
    /**
     * Handle - key for router in config/routers.php
     * @var string
     */
    private $handle = null;

    /**
     * Router details array.
     *
     * See config/routers.php
     *
     * @var array
     */
    private $router = null;

    public function __construct( string $handle ) {
        $this->setRouterByHandle( $handle );
        $this->setHandle( $handle );

        // make sure the template exists or there's no point continuing:
        if( !isset( $this->router()['template'] ) ) {
            throw new GeneralException( "Template not set in router settings" );
        }

        $template = preg_replace( '/[^\da-z_\-\/]/i', '', $this->router()['template'] );
        if( $template[0] == '/' ) {
            $template = substr( $template, 1 );
        }

        if( !View::exists( $template ) ) {
            throw new GeneralException( "Template does not exist" );
        }

    }

    /**
     * Set the router handle string
     *
     * @param string $handle Router handle
     * @return IXP\Tasks\Router\ConfigurationGenerator
     */
    public function setHandle( string $handle ): ConfigurationGenerator {
        $this->handle = $handle;
        return $this;
    }

    /**
     * Get the router handle
     *
     * @return string
     */
    public function handle(): string {
        return $this->handle;
    }

    /**
     * Set the router options array
     *
     * @throws IXP\Exceptions\GeneralException
     * @param array $router Router details (see config/routers.php)
     * @return IXP\Tasks\Router\ConfigurationGenerator
     */
    public function setRouter( array $router ): ConfigurationGenerator {
        $this->router = $router;
        return $this;
    }

    /**
     * Get the router options array
     *
     * @return array
     */
    public function router(): array {
        return $this->router;
    }

    /**
     * Set (and validate) the router by handle
     *
     * @throws IXP\Exceptions\GeneralException
     * @param string $handle Router handle to generate configuration for
     * @return IXP\Tasks\Router\ConfigurationGenerator
     */
    public function setRouterByHandle( string $handle ): ConfigurationGenerator {
        // handle existance should be validated up the chain and errored appropriately (api, cli) - but belt'n'braces:
        if( ! ( $router = config( 'routers.'.$handle, false ) ) ) {
            throw new GeneralException( "Router handle does not exist: " . $handle );
        }

        return $this->setRouter( config( 'routers.'.$handle ) );
    }

    /**
     * Generate and return the configuration
     *
     * @throws IXP\Exceptions\GeneralException
     * @return Illuminate\Contracts\View The configuration
     */
    public function render(): ViewContract {

        // does the VLAN exist?
        if( !isset($this->router['vlan_id']) || !( $vlan = d2r('Vlan')->find( $this->router()['vlan_id'] ) ) ) {
            throw new GeneralException( "Invalid/missing vlan_id in router object" );
        }

        $ints = $this->sanitiseVlanInterfaces($vlan);

        return view( $this->router()['template'] )->with(
            ['handle' => $this->handle(), 'ints' => $ints, 'router' => $this->router(), 'vlan' => $vlan]
        );
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
     * @param Vlan $vlan
     * @return array As defined above
     */
    private function sanitiseVlanInterfaces( Vlan $vlan ): array {

        $ints = d2r( 'VlanInterface' )->getForProto( $vlan, $this->router()['protocol'] ?? 4, false,
            ( ( $this->router()['quarantine'] ?? false ) ? \Entities\PhysicalInterface::STATUS_QUARANTINE : \Entities\PhysicalInterface::STATUS_CONNECTED )
        );

        $newints = [];

        foreach( $ints as $int )
        {
            if( !$int['enabled'] ) {
                continue;
            }

            if( ( $this->router()['type'] ?? 'RS' ) == 'RS' && !$int['rsclient'] ) {
                continue;
            }

            // Due the the way we format the SQL query to join with physical
            // interfaces (of which there may be multiple per VLAN interface),
            // we need to weed out duplicates
            if( isset( $newints[ $int['address'] ] ) ) {
                continue;
            }

            // don't need this:
            unset( $int['enabled'] );

            $int['fvliid'] = sprintf( '%04d', $int['vliid'] );

            if( $int['maxbgpprefix'] && $int['maxbgpprefix'] > $int['gmaxprefixes'] ) {
                $int['maxprefixes'] = $int['maxbgpprefix'];
            } else {
                $int['maxprefixes'] = $int['gmaxprefixes'];
            }

            if( !$int['maxprefixes'] ) {
                $int['maxprefixes'] = 250;
            }

            unset( $int['gmaxprefixes'] );
            unset( $int['maxbgpprefix'] );

            if( ( $this->router()['protocol'] ?? 4 ) == 6 && $int['peeringmacrov6'] ) {
                $int['peeringmacro'] = $int['peeringmacrov6'];
            }

            if( !$int['peeringmacro'] ) {
                $int['peeringmacro'] = 'AS' . $int['autsys'];
            }

            unset( $int['peeringmacrov6'] );

            if( !$int['bgpmd5secret'] ) {
                $int['bgpmd5secret'] = false;
            }

            if( $int['irrdbfilter'] ) {
                $int['irrdbfilter_prefixes'] = d2r( 'IrrdbPrefix' )->getForCustomerAndProtocol( $int[ 'cid' ], $this->router()['protocol'] ?? 4, true );
                $int['irrdbfilter_asns'    ] = d2r( 'IrrdbAsn'    )->getForCustomerAndProtocol( $int[ 'cid' ], $this->router()['protocol'] ?? 4, true );
            }

            $newints[ $int['address'] ] = $int;
        }

        return $newints;
    }
}
