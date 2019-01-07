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

use Entities\Switcher as SwitchEntity;
use Entities\User as UserEntity;

use Auth;

/**
 * Grapher -> Switch Graph
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Switcher extends Graph {

    /**
     * Vlan to graph
     * @var \Entities\Vlan
     */
    private $switch = null;


    /**
     * Constructor
     * @param Grapher $grapher
     * @param SwitchEntity $s
     */
    public function __construct( Grapher $grapher, SwitchEntity $s ) {
        parent::__construct( $grapher );
        $this->switch = $s;
    }

    /**
     * Get the switch we're set to use
     * @return \Entities\Switcher
     */
    public function switch(): SwitchEntity {
        return $this->switch;
    }

    /**
     * Set the switch we should use
     * @param SwitchEntity $switch
     * @return Switcher Fluid interface
     */
    public function setSwitch( SwitchEntity $switch ): Switcher {
        if( $this->switch() && $this->switch()->getId() != $switch->getId() ) {
            $this->wipe();
        }

        $this->switch = $switch;
        return $this;
    }

    /**
     * The name of a graph (e.g. member name, IXP name, etc)
     * @return string
     */
    public function name(): string {
        return $this->switch()->getName();
    }

    /**
     * A unique identifier for this 'graph type'
     *
     * E.g. for an IXP, it might be ixpxxx where xxx is the database id
     * @return string
     */
    public function identifier(): string {
        return sprintf( "switch%05d", $this->switch()->getId() );
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

        if( is_numeric( config( 'grapher.access.switch' ) ) && config( 'grapher.access.switch' ) == UserEntity::AUTH_PUBLIC ) {
            return $this->allow();
        }

        if( Auth::check() && is_numeric( config( 'grapher.access.switch' ) ) && Auth::user()->getPrivs() >= config( 'grapher.access.switch' ) ) {
            return $this->allow();
        }

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
            isset( $overrides['id']   ) ? $overrides['id']   : $this->switch()->getId()
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
        $p['id'] = $this->switch()->getId();
        return $p;
    }


    /**
     * Process user input for the parameter: switch
     *
     * Does a abort(404) if invalid
     *
     * @param int $s The user input value
     * @return SwitchEntity The verified / sanitised / default value
     */
    public static function processParameterSwitch( int $s ): SwitchEntity {
        $switch = null;
        if( !$s || !( $switch = d2r( 'Switcher' )->find( $s ) ) ) {
            abort(404);
        }
        return $switch;
    }


}
