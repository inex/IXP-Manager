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

use Entities\{
    User as UserEntity,
    VlanInterface as VlanInterfaceEntity
};

use Auth, Log;

/**
 * Grapher -> P2P Graph
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class P2p extends Graph {

    /**
     * Source VlanInterface to graph
     * @var \Entities\VlanInterface
     */
    private $svli = null;

    /**
     * Destination VlanInterface to graph
     * @var \Entities\VlanInterface
     */
    private $dvli = null;


    /**
     * Constructor
     * @param Grapher $grapher
     * @param VlanInterfaceEntity $svli
     * @param VlanInterfaceEntity $dvli
     */
    public function __construct( Grapher $grapher, VlanInterfaceEntity $svli, VlanInterfaceEntity $dvli ) {
        parent::__construct( $grapher );
        $this->svli = $svli;
        $this->dvli = $dvli;
    }

    /**
     * Get the source vlan we're set to use
     * @return VlanInterfaceEntity
     */
    public function svli(): VlanInterfaceEntity {
        return $this->svli;
    }

    /**
     * Get the dest vlan we're set to use
     * @return VlanInterfaceEntity
     */
    public function dvli(): VlanInterfaceEntity {
        return $this->dvli;
    }


    /**
     * Set the source vli we should use
     * @param VlanInterfaceEntity $i
     * @return P2p Fluid interface
     */
    public function setSourceVlanInterface( VlanInterfaceEntity $i ): P2p {
        if( $this->svli() && $this->svli()->getId() != $i->getId() ) {
            $this->wipe();
        }

        $this->svli = $i;
        return $this;
    }

    /**
     * Set the dest vli we should use
     * @param VlanInterfaceEntity $i
     * @param bool $wipe Graph settings are wiped by default when the dvli changes
     * @return P2p Fluid interface
     */
    public function setDestinationVlanInterface( VlanInterfaceEntity $i, bool $wipe = true ): P2p {
        if( $this->dvli() && $this->dvli()->getId() != $i->getId() ) {
            if( $wipe ) { $this->wipe(); }
        }

        $this->dvli = $i;
        return $this;
    }

    /**
     * The name of a graph (e.g. member name, IXP name, etc)
     * @return string
     */
    public function name(): string {
        return sprintf( "P2P :: %s - %s :: %s",
            $this->svli()->getVirtualInterface()->getCustomer()->getAbbreviatedName(),
            $this->dvli()->getVirtualInterface()->getCustomer()->getAbbreviatedName(),
            $this->dvli()->getVlan()->getName()
        );
    }

    /**
     * A unique identifier for this 'graph type'
     *
     * E.g. for an IXP, it might be ixpxxx where xxx is the database id
     * @return string
     */
    public function identifier(): string {
        return sprintf( "p2p-svli%05d-dvli%05d", $this->svli()->getId(), $this->dvli()->getId() );
    }


    /**
     * Utility function to determine if the currently logged in user can access 'all customer's p2p' graphs
     *
     * @return bool
     */
    public static function authorisedForAllCustomers(): bool {
        if( Auth::check() && Auth::user()->isSuperUser() ) {
            return true;
        }

        if( !Auth::check() && is_numeric( config( 'grapher.access.p2p' ) ) && config( 'grapher.access.p2p' ) == UserEntity::AUTH_PUBLIC ) {
            return true;
        }

        return Auth::check() && is_numeric( config( 'grapher.access.p2p' ) ) && Auth::user()->getPrivs() >= config( 'grapher.access.p2p' );
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

        // NB: see above authorisedForAllCustomers()

        if( is_numeric( config( 'grapher.access.p2p' ) ) && config( 'grapher.access.p2p' ) == UserEntity::AUTH_PUBLIC ) {
            return $this->allow();
        }

        if( !Auth::check() ) {
            $this->deny();
            return false;
        }

        if( Auth::user()->isSuperUser() ) {
            return $this->allow();
        }

        if( Auth::user()->getCustomer()->getId() == $this->svli()->getVirtualInterface()->getCustomer()->getId() ) {
            return $this->allow();
        }

        if( config( 'grapher.access.p2p' ) != 'own_graphs_only'
            && is_numeric( config( 'grapher.access.p2p' ) )
            && Auth::user()->getPrivs() >= config( 'grapher.access.p2p' )
        ) {
            return $this->allow();
        }

        Log::notice( sprintf( "[Grapher] [Customer]: user %d::%s tried to access a customer p2p vli graph "
                . "{$this->svli()->getId()} with dvli {$this->dvli()->getId()} which is not theirs", Auth::user()->getId(), Auth::user()->getUsername() )
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
        return parent::url( $overrides ) . sprintf("&svli=%d&dvli=%d",
            $overrides['svli']   ?? $this->svli()->getId(),
            $overrides['dvli']   ?? $this->dvli()->getId()
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
        $p['svli'] = $this->svli()->getId();
        $p['dvli'] = $this->dvli()->getId();
        return $p;
    }


    /**
     * Process user input for the parameter: vlanint
     *
     * Does a abort(404) if invalid
     *
     * @param int $i The user input value
     * @return VlanInterfaceEntity The verified / sanitised / default value
     */
    public static function processParameterVlanInterface( int $i ): VlanInterfaceEntity {
        $vlanint = null;
        if( !$i || !( $vlanint = d2r( 'VlanInterface' )->find( $i ) ) ) {
            abort(404);
        }
        return $vlanint;
    }

    /**
     * Process user input for the parameter: srv vlanint
     *
     * Does a abort(404) if invalid
     *
     * @param int $i The user input value
     * @return VlanInterfaceEntity The verified / sanitised / default value
     */
    public static function processParameterSourceVlanInterface( int $i ): VlanInterfaceEntity {
        return self::processParameterVlanInterface($i);
    }

    /**
     * Process user input for the parameter: dst vlanint
     *
     * Does a abort(404) if invalid
     *
     * @param int $i The user input value
     * @return VlanInterfaceEntity The verified / sanitised / default value
     */
    public static function processParameterDestinationVlanInterface( int $i ): VlanInterfaceEntity {
        return self::processParameterVlanInterface($i);
    }
}
