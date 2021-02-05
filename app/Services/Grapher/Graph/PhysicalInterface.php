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

use Auth, Log;

use IXP\Models\{
    Customer,
    PhysicalInterface as PhysicalInterfaceModel,
    User
};

use IXP\Services\Grapher;
use IXP\Services\Grapher\{Graph};

/**
 * Grapher -> PhysicalInterface Graph
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PhysicalInterface extends Graph
{
    /**
     * PhysicalInterface to graph
     *
     * @var PhysicalInterfaceModel
     */
    private $physint = null;

    /**
     * Constructor
     *
     * @param Grapher $grapher
     * @param PhysicalInterfaceModel $pi
     */
    public function __construct( Grapher $grapher, PhysicalInterfaceModel $pi )
    {
        parent::__construct( $grapher );
        $this->physint = $pi;
    }

    /**
     * Get the vlan we're set to use
     *
     * @return PhysicalInterfaceModel
     */
    public function physicalInterface(): PhysicalInterfaceModel
    {
        return $this->physint;
    }

    /**
     * Get the customer owning this virtual interface
     *
     * @return Customer
     */
    public function customer(): Customer
    {
        return $this->physint->virtualInterface->customer;
    }

    /**
     * Set the physint we should use
     *
     * @param PhysicalInterfaceModel $pi
     *
     * @return Graph Fluid interface
     */
    public function setPhysicalInterface( PhysicalInterfaceModel $pi ): Graph
    {
        if( $this->physicalInterface() && $this->physicalInterface()->id !== $pi->id ) {
            $this->wipe();
        }

        $this->physint = $pi;
        return $this;
    }

    /**
     * The name of a graph (e.g. member name, IXP name, etc)
     *
     * @return string
     */
    public function name(): string
    {
        return $this->physicalInterface()->switchPort->name;
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
        return sprintf( "pi%05d", $this->physicalInterface()->id );
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
        if( is_numeric( config( 'grapher.access.customer' ) ) && config( 'grapher.access.customer' ) === User::AUTH_PUBLIC ) {
            return $this->allow();
        }

        if( !Auth::check() ) {
            $this->deny();
            return false;
        }

        if( Auth::getUser()->isSuperUser() ) {
            return $this->allow();
        }

        if( Auth::getUser()->custid === $this->physicalInterface()->virtualInterface->customer->id ) {
            return $this->allow();
        }

        if( config( 'grapher.access.customer' ) !== 'own_graphs_only'
            && is_numeric( config( 'grapher.access.customer' ) )
            && Auth::getUser()->privs >= config( 'grapher.access.customer' )
        ) {
            return $this->allow();
        }

        Log::notice( sprintf( "[Grapher] [PhysicalInterface]: user %d::%s tried to access a physical interface graph "
                . "{$this->physicalInterface()->id} which is not theirs", Auth::id(), Auth::getUser()->username )
        );

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
                $overrides[ 'id' ] ?? $this->physicalInterface()->id
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
        $p['id']    = $this->physicalInterface()->id;
        return $p;
    }

    /**
     * Process user input for the parameter: physint
     *
     * Does a abort(404) if invalid
     *
     * @param int $pi The user input value
     *
     * @return PhysicalInterfaceModel The verified / sanitised / default value
     */
    public static function processParameterPhysicalInterface( int $pi ): PhysicalInterfaceModel
    {
        return PhysicalInterfaceModel::findOrFail( $pi );
    }
}