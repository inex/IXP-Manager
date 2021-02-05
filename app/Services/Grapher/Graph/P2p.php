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
    User,
    VlanInterface as VlanInterfaceModel
};

use IXP\Services\Grapher;
use IXP\Services\Grapher\{Graph};

/**
 * Grapher -> P2P Graph
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class P2p extends Graph
{
    /**
     * Source VlanInterface to graph
     *
     * @var VlanInterfaceModel
     */
    private $svli = null;

    /**
     * Destination VlanInterface to graph
     *
     * @var VlanInterfaceModel
     */
    private $dvli = null;

    /**
     * Constructor
     *
     * @param Grapher $grapher
     * @param VlanInterfaceModel $svli
     * @param VlanInterfaceModel $dvli
     */
    public function __construct( Grapher $grapher, VlanInterfaceModel $svli, VlanInterfaceModel $dvli ) {
        parent::__construct( $grapher );
        $this->svli = $svli;
        $this->dvli = $dvli;
    }

    /**
     * Get the source vlan we're set to use
     *
     * @return VlanInterfaceModel
     */
    public function svli(): VlanInterfaceModel
    {
        return $this->svli;
    }

    /**
     * Get the dest vlan we're set to use
     *
     * @return VlanInterfaceModel
     */
    public function dvli(): VlanInterfaceModel
    {
        return $this->dvli;
    }

    /**
     * Set the source vli we should use
     *
     * @param VlanInterfaceModel $vli
     *
     * @return P2p Fluid interface
     */
    public function setSourceVlanInterface( VlanInterfaceModel $vli ): P2p
    {
        if( $this->svli() && $this->svli()->id !== $vli->id ) {
            $this->wipe();
        }

        $this->svli = $vli;
        return $this;
    }

    /**
     * Set the dest vli we should use
     *
     * @param VlanInterfaceModel    $vli
     * @param bool                  $wipe Graph settings are wiped by default when the dvli changes
     *
     * @return P2p Fluid interface
     */
    public function setDestinationVlanInterface( VlanInterfaceModel $vli, bool $wipe = true ): P2p
    {
        if( $this->dvli() && $this->dvli()->id !== $vli->id ) {
            if( $wipe ) { $this->wipe(); }
        }

        $this->dvli = $vli;
        return $this;
    }

    /**
     * The name of a graph (e.g. member name, IXP name, etc)
     * @return string
     */
    public function name(): string
    {
        return sprintf( "P2P :: %s - %s :: %s",
            $this->svli()->virtualInterface->customer->abbreviatedName,
            $this->dvli()->virtualInterface->customer->abbreviatedName,
            $this->dvli()->vlan->name
        );
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
        return sprintf( "p2p-svli%05d-dvli%05d", $this->svli()->id, $this->dvli()->id );
    }

    /**
     * Utility function to determine if the currently logged in user can access 'all customer's p2p' graphs
     *
     * @return bool
     */
    public static function authorisedForAllCustomers(): bool
    {
        if( Auth::check() && Auth::getUser()->isSuperUser() ) {
            return true;
        }

        if( !Auth::check() && is_numeric( config( 'grapher.access.p2p' ) ) && config( 'grapher.access.p2p' ) === User::AUTH_PUBLIC ) {
            return true;
        }

        return Auth::check() && is_numeric( config( 'grapher.access.p2p' ) ) && Auth::getUser()->privs() >= config( 'grapher.access.p2p' );
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
        // NB: see above authorisedForAllCustomers()
        if( is_numeric( config( 'grapher.access.p2p' ) ) && config( 'grapher.access.p2p' ) === User::AUTH_PUBLIC ) {
            return $this->allow();
        }

        if( !Auth::check() ) {
            $this->deny();
            return false;
        }

        if( Auth::getUser()->isSuperUser() ) {
            return $this->allow();
        }

        if( Auth::getUser()->custid === $this->svli()->virtualInterface->customer->id ) {
            return $this->allow();
        }

        if( config( 'grapher.access.p2p' ) !== 'own_graphs_only'
            && is_numeric( config( 'grapher.access.p2p' ) )
            && Auth::getUser()->privs() >= (int)config( 'grapher.access.p2p' )
        ) {
            return $this->allow();
        }

        Log::notice( sprintf( "[Grapher] [Customer]: user %d::%s tried to access a customer p2p vli graph "
                . "{$this->svli()->id} with dvli {$this->dvli()->id} which is not theirs", Auth::id(), Auth::getUser()->username )
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
        return parent::url( $overrides ) . sprintf("&svli=%d&dvli=%d",
            $overrides['svli']   ?? $this->svli()->id,
            $overrides['dvli']   ?? $this->dvli()->id
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
        $p['svli'] = $this->svli()->id;
        $p['dvli'] = $this->dvli()->id;
        return $p;
    }

    /**
     * Process user input for the parameter: vlanint
     *
     * Does a abort(404) if invalid
     *
     * @param int $vli The user input value
     *
     * @return VlanInterfaceModel The verified / sanitised / default value
     */
    public static function processParameterVlanInterface( int $vli ): VlanInterfaceModel
    {
        return VlanInterfaceModel::findOrFail( $vli );
    }

    /**
     * Process user input for the parameter: srv vlanint
     *
     * Does a abort(404) if invalid
     *
     * @param int $vli The user input value
     *
     * @return VlanInterfaceModel The verified / sanitised / default value
     */
    public static function processParameterSourceVlanInterface( int $vli ): VlanInterfaceModel
    {
        return self::processParameterVlanInterface( $vli );
    }

    /**
     * Process user input for the parameter: dst vlanint
     *
     * Does a abort(404) if invalid
     *
     * @param int $vli The user input value
     *
     * @return VlanInterfaceModel The verified / sanitised / default value
     */
    public static function processParameterDestinationVlanInterface( int $vli ): VlanInterfaceModel {
        return self::processParameterVlanInterface( $vli );
    }
}