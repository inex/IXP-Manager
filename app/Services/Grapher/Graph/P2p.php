<?php namespace IXP\Services\Grapher\Graph;

/*
 * Copyright (C) 2009-2016 Internet Neutral Exchange Association Limited.
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
use IXP\Services\Grapher\{Graph,Statistics};

use IXP\Exceptions\Services\Grapher\{BadBackendException,CannotHandleRequestException,ConfigurationException,ParameterException};

use Entities\VlanInterface as VlanInterfaceEntity;

use Auth, Log;

/**
 * Grapher -> P2P Graph
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (c) 2009 - 2016, Internet Neutral Exchange Association Ltd
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
     */
    public function __construct( Grapher $grapher, VlanInterfaceEntity $svli, VlanInterfaceEntity $dvli ) {
        parent::__construct( $grapher );
        $this->svli = $svli;
        $this->dvli = $dvli;
    }

    /**
     * Get the source vlan we're set to use
     * @return \Entities\Vlan
     */
    public function svli(): VlanInterfaceEntity {
        return $this->svli;
    }

    /**
     * Get the dest vlan we're set to use
     * @return \Entities\Vlan
     */
    public function dvli(): VlanInterfaceEntity {
        return $this->dvli;
    }


    /**
     * Set the source vli we should use
     * @param Entities\VlanInterface $i
     * @return \IXP\Services\Grapher Fluid interface
     * @throws \IXP\Exceptions\Services\Grapher\ParameterException
     */
    public function setSourceVlanInterface( VlanInterfaceEntity $i ): Grapher {
        if( $this->svli() && $this->svli()->getId() != $i->getId() ) {
            $this->wipe();
        }

        $this->svli = $i;
        return $this;
    }

    /**
     * Set the dest vli we should use
     * @param Entities\VlanInterface $i
     * @return \IXP\Services\Grapher Fluid interface
     * @throws \IXP\Exceptions\Services\Grapher\ParameterException
     */
    public function setDestinationVlanInterface( VlanInterfaceEntity $i ): Grapher {
        if( $this->dvli() && $this->dvli()->getId() != $i->getId() ) {
            $this->wipe();
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
     * This function controls access to the graph.
     *
     * {@inheritDoc}
     *
     * For (public) vlan aggregate graphs we pretty much allow complete access.
     *
     * @return bool
     */
    public function authorise(): bool {
        if( !Auth::check() ) {
            return $this->deny();
        }

        if( Auth::user()->isSuperUser() ) {
            return $this->allow();
        }

        if( Auth::user()->getCustomer()->getId() == $this->svli()->getVirtualInterface()->getCustomer()->getId() ) {
            return $this->allow();
        }

        Log::notice( sprintf( "[Grapher] [P2P]: user %d::%s tried to access a p2p vlan interface graph "
            . "{$this->svli()->getId()} which is not theirs", Auth::user()->getId(), Auth::user()->getUsername() )
        );
        return $this->deny();
    }

    /**
     * Generate a URL to get this graphs 'file' of a given type
     *
     * @param array $overrides Allow standard parameters to be overridden (e.g. category)
     * @return string
     */
    public function url( array $overrides = [] ): string {
        return parent::url( $overrides ) . sprintf("&id=%d",
            isset( $overrides['id']   ) ? $overrides['id']   : $this->vlanInterface()->getId()
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
     * @param int $pi The user input value
     * @return int The verified / sanitised / default value
     */
    public static function processParameterVlanInterface( int $i ): VlanInterfaceEntity {
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
     * @param int $pi The user input value
     * @return int The verified / sanitised / default value
     */
    public static function processParameterSourceVlanInterface( int $i ): VlanInterfaceEntity {
        return self::processParameterVlanInterface($i);
    }

    /**
     * Process user input for the parameter: dst vlanint
     *
     * Does a abort(404) if invalid
     *
     * @param int $pi The user input value
     * @return int The verified / sanitised / default value
     */
    public static function processParameterDestinationVlanInterface( int $i ): VlanInterfaceEntity {
        return self::processParameterVlanInterface($i);
    }
}
