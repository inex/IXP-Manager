<?php

namespace IXP\Services\LookingGlass;

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

use IXP\Contracts\LookingGlass as LookingGlassContract;

use IXP\Models\Router;

/**
 * LookingGlass Backend -> Bird's Eye
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Services\LookingGlass
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class BirdsEye implements LookingGlassContract
{
    /**
     * Instance of a router object representing the looking glass target
     *
     * @var Router
     */
    private $router;

    /**
     * Is caching enabled?
     *
     * @var bool
     */
    private $cacheEnabled = true;

    /**
     * Constructor
     * @param Router $r
     */
    public function __construct( Router $r )
    {
        $this->setRouter( $r );
    }

    /**
     * Enable / disable caching
     *
     * @param bool
     *
     * @return BirdsEye
     */
    public function setCacheEnabled( bool $b ): Birdseye
    {
        $this->cacheEnabled = $b;
        return $this;
    }

    /**
     * Is caching enabled?
     *
     * @return bool
     */
    public function cacheEnabled(): bool
    {
        return $this->cacheEnabled;
    }

    /**
     * Set the router object
     *
     * @param Router $r
     *
     * @return BirdsEye For fluent interfaces
     */
    public function setRouter( Router $r ): LookingGlassContract
    {
        $this->router = $r;
        return $this;
    }

    /**
     * Get the router object
     *
     * @return Router
     */
    public function router(): Router
    {
        return $this->router;
    }

    /**
     * Make the API call
     *
     * @param string $cmd
     *
     * @return string
     */
    private function apiCall( string $cmd ): string
    {
        $ret = @file_get_contents( $this->router()->api . '/' . $cmd . ( $this->cacheEnabled ? '' : '?use_cache=0' ) );

        return $ret ?: "";
    }

    /**
     * Get BGP Summary information as JSON
     *
     * @return string
     */
    public function bgpSummary(): string
    {
        return $this->apiCall( 'protocols/bgp' );
    }

    /**
     * Get the router's status as JSON
     *
     * @return string
     */
    public function status(): string
    {
        return $this->apiCall( 'status' );
    }

    /**
     * Get internal symbols.
     *
     * Particularly we're interested in route tables / vrfs and protocols.
     *
     * @return string
     */
    public function symbols(): string
    {
        return $this->apiCall( 'symbols' );
    }

    /**
     * Get routes for a named routing table (aka. vrf)
     * @param string $table Table name
     * @return string
     */
    public function routesForTable( string $table ): string
    {
        return $this->apiCall( 'routes/table/' . urlencode( $table ) );
    }

    /**
     * Get routes learnt from named protocol (e.g. BGP session)
     *
     * @param string $protocol Protocol name
     *
     * @return string
     */
    public function routesForProtocol( string $protocol ): string
    {
        return $this->apiCall( 'routes/protocol/' . urlencode( $protocol ) );
    }

    /**
     * Get routes exported to named protocol (e.g. BGP session)
     *
     * @param string $protocol Protocol name
     *
     * @return string
     */
    public function routesForExport(string $protocol): string
    {
        return $this->apiCall( 'routes/export/' . urlencode( $protocol ) );
    }

    /**
     * Get details for a specific route as received by a protocol
     *
     * @param string    $protocol   Protocol name
     * @param string    $network    The route to lookup
     * @param int       $mask       The mask of the route to look up
     *
     * @return string
     */
    public function protocolRoute( string $protocol,string $network,int $mask ): string
    {
        return $this->apiCall( 'route/' . urlencode($network . '/' . $mask ) . '/protocol/' . urlencode( $protocol ) );
    }

    /**
     * Get details for a specific route in a named table (vrf)
     *
     * @param string    $table      Table name
     * @param string    $network    The route to lookup
     * @param int       $mask       The mask of the route to look up
     *
     * @return string
     */
    public function protocolTable( string $table,string $network,int $mask ): string
    {
        return $this->apiCall( 'route/' . urlencode($network . '/' . $mask ) . '/table/' . urlencode( $table ) );
    }

    /**
     * Get details for a specific route in a named protocol export
     *
     * @param string    $protocol   Protocol name
     * @param string    $network    The route to lookup
     * @param int       $mask       The mask of the route to look up
     *
     * @return string
     */
    public function exportRoute( string $protocol, string $network, int $mask ): string
    {
        return $this->apiCall( 'route/' . urlencode($network . '/' . $mask ) . '/export/' . urlencode( $protocol ) );
    }

    /**
     * Get wildcard large communities in protocol table of form ( x, y, * )
     *
     * @param string    $protocol Protocol name
     * @param int       $x
     * @param int       $y
     *
     * @return string
     */
    public function routesProtocolLargeCommunityWildXYRoutes( string $protocol, int $x, int $y ): string
    {
        return $this->apiCall( 'routes/lc-zwild/protocol/' . urlencode( $protocol ) . '/' . $x . '/' . $y );
    }
}