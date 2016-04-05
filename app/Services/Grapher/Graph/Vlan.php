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

use Entities\Vlan as VlanEntity;

use Auth;

/**
 * Grapher -> Vlan Graph
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (c) 2009 - 2016, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Vlan extends Graph {

    /**
     * Vlan to graph
     * @var \Entities\Vlan
     */
    private $vlan = null;


    /**
     * Constructor
     */
    public function __construct( Grapher $grapher, VlanEntity $v ) {
        parent::__construct( $grapher );
        $this->vlan = $v;
    }

    /**
     * Get the vlan we're set to use
     * @return \Entities\Vlan
     */
    public function vlan(): VlanEntity {
        return $this->vlan;
    }

    /**
     * Set the vlan we should use
     * @param Entities\Vlan $vlan
     * @return \IXP\Services\Grapher Fluid interface
     * @throws \IXP\Exceptions\Services\Grapher\ParameterException
     */
    public function setVlan( VlanEntity $vlan ): Grapher {
        if( $this->vlan() && $this->vlan()->getId() != $vlan->getId() ) {
            $this->wipe();
        }

        $this->vlan = $vlan;
        return $this;
    }

    /**
     * The name of a graph (e.g. member name, IXP name, etc)
     * @return string
     */
    public function name(): string {
        return $this->vlan()->getName();
    }

    /**
     * A unique identifier for this 'graph type'
     *
     * E.g. for an IXP, it might be ixpxxx where xxx is the database id
     * @return string
     */
    public function identifier(): string {
        return sprintf( "vlan%05d", $this->vlan()->getId() );
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
        if( Auth::check() && Auth::user()->isSuperUser() ) {
            return $this->allow();
        }

        if( $this->vlan()->getPrivate() ) {
            // FIXME
            return $this->deny();
        }

        return $this->allow();
    }

    /**
     * Generate a URL to get this graphs 'file' of a given type
     *
     * @param array $overrides Allow standard parameters to be overridden (e.g. category)
     * @return string
     */
    public function url( array $overrides = [] ): string {
        return parent::url( $overrides ) . sprintf("&id=%d",
            isset( $overrides['id']   ) ? $overrides['id']   : $this->vlan()->getId()
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
        $p['id'] = $this->vlan()->getId();
        return $p;
    }


    /**
     * Process user input for the parameter: vlan
     *
     * Does a abort(404) if invalid
     *
     * @param int $v The user input value
     * @return int The verified / sanitised / default value
     */
    public static function processParameterVlan( int $v ): VlanEntity {
        if( !$v || !( $vlan = d2r( 'Vlan' )->find( $v ) ) ) {
            abort(404);
        }
        return $vlan;
    }


}
