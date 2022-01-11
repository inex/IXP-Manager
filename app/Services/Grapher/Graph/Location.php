<?php

namespace IXP\Services\Grapher\Graph;

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

use Auth;

use IXP\Models\{
    Location as LocationModel,
    User
};

use IXP\Services\Grapher;
use IXP\Services\Grapher\{Graph};

/**
 * Grapher -> Location Graph
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Location extends Graph
{
    /**
     * Switcher to graph
     *
     * @var LocationModel
     */
    private $location = null;

    /**
     * Constructor
     *
     * @param Grapher       $grapher
     * @param LocationModel $l
     */
    public function __construct( Grapher $grapher, LocationModel $l )
    {
        parent::__construct( $grapher );
        $this->location = $l;
    }

    /**
     * Get the switch we're set to use
     *
     * @return LocationModel
     */
    public function location(): LocationModel
    {
        return $this->location;
    }

    /**
     * Set the switch we should use
     *
     * @param LocationModel $switch
     *
     * @return Location Fluid interface
     */
    public function setLocation( LocationModel $location ): Location
    {
        if( $this->location() && $this->location()->id !== $location->id ) {
            $this->wipe();
        }

        $this->location = $location;
        return $this;
    }

    /**
     * The name of a graph (e.g. member name, IXP name, etc)
     *
     * @return string
     */
    public function name(): string
    {
        return $this->location()->name;
    }

    /**
     * A unique identifier for this 'graph type'
     *
     * E.g. for an IXP, it might be ixpxxx where xxx is the database id
     *
     * @return string
     */
    public function identifier(): string
    {
        return sprintf( "location%05d", $this->location()->id );
    }

    /**
     * This function controls access to the graph.
     *
     * {@inheritDoc}
     *
     * For (public) vlan aggregate graphs we pretty much allow complete access.
     *
     * @return bool
     */
    public function authorise(): bool
    {
        if( Auth::check() && Auth::getUser()->isSuperUser() ) {
            return $this->allow();
        }

        if( is_numeric( config( 'grapher.access.location' ) ) && (int)config( 'grapher.access.location' ) === User::AUTH_PUBLIC ) {
            return $this->allow();
        }

        if( Auth::check() && is_numeric( config( 'grapher.access.location' ) ) && Auth::getUser()->privs() >= (int)config( 'grapher.access.location' ) ) {
            return $this->allow();
        }

        $this->deny();
        return false;
    }

    /**
     * Generate a URL to get this graphs 'file' of a given type
     *
     * @param array $overrides Allow standard parameters to be overridden (e.g. category)
     *
     * @return string
     */
    public function url( array $overrides = [] ): string
    {
        return parent::url( $overrides ) . sprintf("&id=%d",
                $overrides[ 'id' ] ?? $this->location()->id
        );
    }

    /**
     * Get parameters in bulk as associative array
     *
     * Extends base function
     *
     * @return array $params
     */
    public function getParamsAsArray(): array
    {
        $p = parent::getParamsAsArray();
        $p['id'] = $this->location()->id;
        return $p;
    }

    /**
     * Process user input for the parameter: switch
     *
     * Does a abort(404) if invalid
     *
     * @param int $l The user input value
     *
     * @return LocationModel The verified / sanitised / default value
     */
    public static function processParameterLocation( int $l ): LocationModel
    {
        return LocationModel::findOrFail( $l );
    }
}