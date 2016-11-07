<?php

namespace IXP\Utils;


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

 use IXP\Exceptions\ConfigurationException;
 use IXP\Exceptions\Utils\RouterException;
use View;

/**
 * A class to proxy access to config/routers.php. Eventually this will serve
 * as a template for an interface / contract to a database backed version of
 * that configuration file.
 *
 * @author Barry O'Donovan <barry@islandbridgenetworks.ie>
 */
class Router
{
    /**
     * Router details array.
     *
     * See config/routers.php
     *
     * @var array
     */
    private $router = null;

    /**
     * Handle - key for router in config/routers.php
     * @var string
     */
    private $handle = null;




    public function __construct( $handle ) {
        $this->setRouterByHandle( $handle );
        $this->setHandle( $handle );
        return $this;
    }

    /**
     * @throws IXP\Exceptions\Utils\RouterException
     */
    public function checkTemplate() {
        // make sure the template exists or there's no point continuing:
        if( !isset( $this->router()['template'] ) ) {
            throw new RouterException( "Template not set in router settings" );
        }

        $template = preg_replace( '/[^\da-z_\-\/]/i', '', $this->template() );
        if( $template[0] == '/' ) {
            $template = substr( $template, 1 );
        }

        if( !View::exists( $template ) ) {
            throw new RouterException( "Template does not exist" );
        }
    }
    
    /**
     * Set the router options array
     *
     * @throws IXP\Exceptions\GeneralException
     * @param array $router Router details (see config/routers.php)
     * @return IXP\Tasks\Router\ConfigurationGenerator
     */
    public function setRouter( array $router ): Router {
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
    public function setRouterByHandle( string $handle ): Router {
        // handle existance should be validated up the chain and errored appropriately (api, cli) - but belt'n'braces:
        if( ! ( $router = config( 'routers.'.$handle, false ) ) ) {
            throw new RouterException( "Router handle does not exist: " . $handle );
        }

        return $this->setRouter( config( 'routers.'.$handle ) );
    }

    /**
     * Set the router handle string
     *
     * @param string $handle Router handle
     * @return IXP\Tasks\Router\ConfigurationGenerator
     */
    public function setHandle( string $handle ): Router {
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
     * Get the router's VLAN ID
     *
     * @return int
     */
    public function vlanId(): int {
        if( !isset( $this->router()['vlan_id'] ) ) {
            throw new ConfigurationException();
        }
        return intval( $this->router()['vlan_id'] );
    }

    /**
     * Get the router protocol
     *
     * @return int
     */
    public function protocol(): int {
        if( !isset( $this->router()['protocol'] ) || !in_array( $this->router()['protocol'], [4,6] ) ) {
            throw new ConfigurationException();
        }
        return intval( $this->router()['protocol'] );
    }

    /**
     * Get the router handle
     *
     * @return bool
     */
    public function quarantine(): bool {
        return boolval( $this->router()['quarantine'] ?? false );
    }

    /**
     * Get the router handle
     *
     * @return string
     */
    public function type(): string {
        if( !isset( $this->router()['type'] ) || !in_array( $this->router()['type'], ['AS112', 'RC', 'RS' ] ) ) {
            throw new ConfigurationException();
        }
        return $this->router()['type'];
    }

    /**
     * Get the router template
     *
     * @return string
     */
    public function template(): string {
        if( !isset( $this->router()['template'] ) ) {
            throw new ConfigurationException();
        }
        
        return $this->router()['template'];
    }

    /**
     * Get the router ASN
     *
     * @return int
     */
    public function asn(): int {
        if( !isset( $this->router()['asn'] ) ) {
            throw new ConfigurationException();
        }
        return intval( $this->router()['asn'] );
    }

    /**
     * Get the router template
     *
     * @return string
     */
    public function peeringIp(): string {
        if( !isset( $this->router()['peering_ip'] ) ) {
            throw new ConfigurationException();
        }
        return $this->router()['peering_ip'];
    }

    /**
     * Get the router template
     *
     * @return string
     */
    public function routerId(): string {
        if( !isset( $this->router()['router_id'] ) ) {
            throw new ConfigurationException();
        }
        return $this->router()['router_id'];
    }

    /**
     * Are BGP large communities enabled?
     *
     * @return bool
     */
    public function bgpLargeCommunities(): bool {
        return boolval( $this->router()['bgp_lc'] ?? false );
    }


}
