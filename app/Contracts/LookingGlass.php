<?php

namespace IXP\Contracts;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use IXP\Models\Router;

 /**
  * LookingGlassContract Contract - any concrete implementation of a LookingGlassContract
  * provider must implement this interface
  *
  * @see        http://laravel.com/docs/5.0/contracts
  * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
  * @category   LookingGlassContract
  * @package    IXP\Contracts
  * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
  */
interface LookingGlass
{
    /**
     * Set the router object
     *
     * @param Router $r
     *
     * @return LookingGlass For fluent interfaces
     */
    public function setRouter( Router $r ): LookingGlass;

    /**
     * Get the router object
     *
     * @return Router
     */
    public function router(): Router;

    /**
     * Get BGP Summary information as JSON
     *
     * Response must use equivalent structure as Bird's Eye:
     *     https://github.com/inex/birdseye/
     *
     * @return string
     */
    public function bgpSummary(): string;

    /**
     * Get the router status information as JSON
     *
     * Response must use equivalent structure as Bird's Eye:
     *     https://github.com/inex/birdseye/
     *
     * @return string
     */
    public function status(): string;

    /**
     * Get internal symbols.
     *
     * Particularly we're interested in route tables / vrfs and protocols.
     *
     * @return string
     */
    public function symbols(): string;

    /**
     * Get routes for a named routing table (aka. vrf)
     *
     * @param string $table Table name
     *
     * @return string
     */
    public function routesForTable( string $table ): string;

    /**
     * Get routes learnt from named protocol (e.g. BGP session)
     *
     * @param string $protocol Protocol name
     *
     * @return string
     */
    public function routesForProtocol( string $protocol ): string;
    
    /**
     * Get routes exported to named protocol (e.g. BGP session)
     *
     * @param string $protocol Protocol name
     *
     * @return string
     */
    public function routesForExport( string $protocol ): string;

    /**
     * Get details for a specific route as received by a protocol
     *
     * @param string    $protocol   Protocol name
     * @param string    $network    The route to lookup
     * @param int       $mask       The mask of the route to look up
     *
     * @return string
     */
    public function protocolRoute( string $protocol, string $network, int $mask ): string;

    /**
     * Get details for a specific route in a named table (vrf)
     *
     * @param string    $table      Table name
     * @param string    $network    The route to lookup
     * @param int       $mask       The mask of the route to look up
     *
     * @return string
     */
    public function protocolTable( string $table, string $network, int $mask ): string;

    /**
     * Get wildcard large communities in protocol table of form ( x, y, * )
     *
     * @param string    $protocol Protocol name
     * @param int       $x
     * @param int       $y
     *
     * @return string
     */
    public function routesProtocolLargeCommunityWildXYRoutes( string $protocol, int $x, int $y ): string;
}