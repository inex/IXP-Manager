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

use IXP\Services\Grapher;
use IXP\Services\Grapher\{Graph};

use IXP\Models\{
    Customer,
    User,
    VirtualInterface as VirtualInterfaceModel
};

/**
 * Grapher -> VirtualInterface Graph (LAGs)
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class VirtualInterface extends Graph
{
    /**
     * VirtualInterface to graph
     *
     * @var VirtualInterfaceModel
     */
    private $virtint = null;

    /**
     * Constructor
     *
     * @param Grapher               $grapher
     * @param VirtualInterfaceModel $vi
     */
    public function __construct( Grapher $grapher, VirtualInterfaceModel $vi )
    {
        parent::__construct( $grapher );
        $this->virtint = $vi;
    }

    /**
     * Get the vlan we're set to use
     *
     * @return VirtualInterfaceModel
     */
    public function virtualInterface(): VirtualInterfaceModel
    {
        return $this->virtint;
    }

    /**
     * Get the customer owning this virtual interface
     *
     * @return Customer
     */
    public function customer(): Customer
    {
        return $this->virtint->customer;
    }

    /**
     * Set the interface we should use
     *
     * @param VirtualInterfaceModel $vi
     *
     * @return VirtualInterface Fluid interface
     */
    public function setVirtualInterface( VirtualInterfaceModel $vi ): VirtualInterface
    {
        if( $this->virtualInterface() && $this->virtualInterface()->id !== $vi->id ) {
            $this->wipe();
        }

        $this->virtint = $vi;
        return $this;
    }

    /**
     * The name of a graph (e.g. member name, IXP name, etc)
     *
     * @return string
     */
    public function name(): string
    {
        $pi = $this->virtualInterface()->physicalInterfaces[ 0 ];
        return "LAG over ports on " . $pi->switchPort->switcher->name;
    }

    /**
     * A unique identifier for this 'graph type'
     *
     * E.g. for an IXP, it might be ixpxxx where xxx is the database id
     * @return string
     */
    public function identifier(): string
    {
        return sprintf( "vi%05d", $this->virtualInterface()->id );
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

        if( Auth::getUser()->custid === $this->virtualInterface()->customer->id ) {
            return $this->allow();
        }

        if( config( 'grapher.access.customer' ) !== 'own_graphs_only'
            && is_numeric( config( 'grapher.access.customer' ) )
            && Auth::getUser()->privs() >= config( 'grapher.access.customer' )
        ) {
            return $this->allow();
        }

        Log::notice( sprintf( "[Grapher] [VirtualInterface]: user %d::%s tried to access a virtual interface graph "
                . "{$this->virtualInterface()->id} which is not theirs", Auth::id(), Auth::getUser()->username )
        );

        $this->deny();
        return false;
    }

    /**
     * Generate a URL to get this graphs 'file' of a given type
     *
     * @param array $overrides Allow standard parameters to be overridden (e.g. category)
     * @return string
     */
    public function url( array $overrides = [] ): string
    {
        return parent::url( $overrides ) . sprintf("&id=%d",
                $overrides[ 'id' ] ?? $this->virtualInterface()->id
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
        $p['id']    = $this->virtualInterface()->id;
        return $p;
    }

    /**
     * Process user input for the parameter: virtint
     *
     * Does a abort(404) if invalid
     *
     * @param int $vi The user input value
     *
     * @return VirtualInterfaceModel The verified / sanitised / default value
     */
    public static function processParameterVirtualInterface( int $vi ): VirtualInterfaceModel
    {
        return VirtualInterfaceModel::findOrFail( $vi );
    }
}