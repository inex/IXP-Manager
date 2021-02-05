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

use IXP\Exceptions\Services\Grapher\{ParameterException};

use IXP\Models\User;

use IXP\Services\Grapher;
use IXP\Services\Grapher\{Graph};

/**
 * Grapher -> Switch Graph
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Trunk extends Graph
{
    /**
     * Trunk to graph
     *
     * @var string
     */
    private $trunkname = null;

    /**
     * Constructor
     *
     * @param Grapher $grapher
     * @param string $n
     *
     * @throws
     */
    public function __construct( Grapher $grapher, string $n )
    {
        parent::__construct( $grapher );
        $this->setTrunkname($n);
    }

    /**
     * Get the trunk name we're meant to graph
     *
     * @return string
     */
    public function trunkname(): string
    {
        return $this->trunkname;
    }

    /**
     * Set the trunk we should use
     *
     * @param string $n
     *
     * @return Trunk Fluid interface
     *
     * @throws
     */
    public function setTrunkname( string $n ): Trunk
    {
        if( !is_array( config('grapher.backends.mrtg.trunks.'.$n) ) ) {
            throw new ParameterException("Invalid trunk name in constructor");
        }
        
        $this->trunkname = $n;
        return $this;
    }

    /**
     * The name of a graph (e.g. member name, IXP name, etc)
     * @return string
     */
    public function name(): string
    {
        return config('grapher.backends.mrtg.trunks.' . $this->trunkname().'.title' );
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
        return $this->trunkname();
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

        if( is_numeric( config( 'grapher.access.trunk' ) ) && (int)config( 'grapher.access.trunk' ) === User::AUTH_PUBLIC ) {
            return $this->allow();
        }

        if( Auth::check() && is_numeric( config( 'grapher.access.trunk' ) ) && Auth::getUser()->privs() >= config( 'grapher.access.trunk' ) ) {
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
        return parent::url( $overrides ) . sprintf("&id=%s",
                $overrides[ 'id' ] ?? $this->trunkname()
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
        $p          = parent::getParamsAsArray();
        $p['id']    = $this->trunkname();
        return $p;
    }

    /**
     * Process user input for the parameter: switch
     *
     * Does a abort(404) if invalid
     *
     * @param string $n The user input value
     *
     * @return string The verified / sanitised / default value
     */
    public static function processParameterTrunkname( string $n ): string
    {
        if( !is_array( config('grapher.backends.mrtg.trunks.'.$n) ) ) {
            abort(404);
        }
        return $n;
    }
}