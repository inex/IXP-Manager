<?php namespace IXP\Services\Grapher\Graph;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use IXP\Services\Grapher;
use IXP\Services\Grapher\{Graph};

use Entities\Customer as CustomerEntity;
use Entities\User as UserEntity;
use Entities\VirtualInterface as VirtualInterfaceEntity;

use Auth, Log;

/**
 * Grapher -> VirtualInterface Graph (LAGs)
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class VirtualInterface extends Graph {

    /**
     * VirtualInterface to graph
     * @var \Entities\VirtualInterface
     */
    private $virtint = null;


    /**
     * Constructor
     * @param Grapher $grapher
     * @param VirtualInterfaceEntity $i
     */
    public function __construct( Grapher $grapher, VirtualInterfaceEntity $i ) {
        parent::__construct( $grapher );
        $this->virtint = $i;
    }

    /**
     * Get the vlan we're set to use
     * @return VirtualInterfaceEntity
     */
    public function virtualInterface(): VirtualInterfaceEntity {
        return $this->virtint;
    }

    /**
     * Get the customer owning this virtual interface
     * @return CustomerEntity
     */
    public function customer(): CustomerEntity {
        return $this->virtint->getCustomer();
    }

    /**
     * Set the interface we should use
     * @param VirtualInterfaceEntity $i
     * @return VirtualInterface Fluid interface
     */
    public function setVirtualInterface( VirtualInterfaceEntity $i ): VirtualInterface {
        if( $this->virtualInterface() && $this->virtualInterface()->getId() != $i->getId() ) {
            $this->wipe();
        }

        $this->virtint = $i;
        return $this;
    }

    /**
     * The name of a graph (e.g. member name, IXP name, etc)
     * @return string
     */
    public function name(): string {
        return "LAG over ports on " . $this->virtualInterface()->getPhysicalInterfaces()[0]->getSwitchPort()->getSwitcher()->getName();
    }

    /**
     * A unique identifier for this 'graph type'
     *
     * E.g. for an IXP, it might be ixpxxx where xxx is the database id
     * @return string
     */
    public function identifier(): string {
        return sprintf( "vi%05d", $this->virtualInterface()->getId() );
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
    public function authorise(): bool {
        if( is_numeric( config( 'grapher.access.customer' ) ) && config( 'grapher.access.customer' ) == UserEntity::AUTH_PUBLIC ) {
            return $this->allow();
        }

        if( !Auth::check() ) {
            $this->deny();
            return false;
        }

        if( Auth::user()->isSuperUser() ) {
            return $this->allow();
        }

        if( Auth::user()->getCustomer()->getId() == $this->virtualInterface()->getCustomer()->getId() ) {
            return $this->allow();
        }

        if( config( 'grapher.access.customer' ) != 'own_graphs_only'
            && is_numeric( config( 'grapher.access.customer' ) )
            && Auth::user()->getPrivs() >= config( 'grapher.access.customer' )
        ) {
            return $this->allow();
        }

        Log::notice( sprintf( "[Grapher] [VirtualInterface]: user %d::%s tried to access a virtual interface graph "
                . "{$this->virtualInterface()->getId()} which is not theirs", Auth::user()->getId(), Auth::user()->getUsername() )
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
    public function url( array $overrides = [] ): string {
        return parent::url( $overrides ) . sprintf("&id=%d",
            isset( $overrides['id']   ) ? $overrides['id']   : $this->virtualInterface()->getId()
        );
    }

    /**
     * Get parameters in bulk as associative array
     *
     * Extends base function
     *
     * @return array $params
     */
    public function getParamsAsArray(): array {
        $p = parent::getParamsAsArray();
        $p['id'] = $this->virtualInterface()->getId();
        return $p;
    }


    /**
     * Process user input for the parameter: virtint
     *
     * Does a abort(404) if invalid
     *
     * @param int $i The user input value
     * @return VirtualInterfaceEntity The verified / sanitised / default value
     */
    public static function processParameterVirtualInterface( int $i ): VirtualInterfaceEntity {
        $virtint = null;
        if( !$i || !( $virtint = d2r( 'VirtualInterface' )->find( $i ) ) ) {
            abort(404);
        }
        return $virtint;
    }

}
