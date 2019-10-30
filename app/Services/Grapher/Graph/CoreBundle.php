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
    CoreBundle as CoreBundleEntity,
    User       as UserEntity
};

use Auth, Log;

/**
 * Grapher -> Customer Graph (LAGs)
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CoreBundle extends Graph {

    /**
     * CoreBundle to graph
     * @var \Entities\CoreBundle
     */
    private $cb = null;


    /**
     * Constructor
     * @param Grapher $grapher
     * @param CoreBundleEntity $cb
     */
    public function __construct( Grapher $grapher, CoreBundleEntity $cb ) {
        parent::__construct( $grapher );
        $this->cb = $cb;
    }

    /**
     * Get the CoreBundle we're set to use
     * @return CoreBundleEntity
     */
    public function coreBundle(): CoreBundleEntity {
        return $this->cb;
    }

    /**
     * Set the CoreBundleEntity we should use
     * @param CoreBundleEntity $cb
     * @return CoreBundle Fluid interface
     */
    public function setCoreBundle( CoreBundleEntity $cb ): CoreBundle {
        if( $this->coreBundle() && $this->coreBundle()->getId() != $cb->getId() ) {
            $this->wipe();
        }

        $this->cb = $cb;
        return $this;
    }

    /**
     * The name of a graph (e.g. member name, IXP name, etc)
     * @return string
     */
    public function name(): string {
        return $this->coreBundle()->getGraphTitle();
    }

    /**
     * A unique identifier for this 'graph type'
     *
     * E.g. for an IXP, it might be ixpxxx where xxx is the database id
     * @return string
     */
    public function identifier(): string {
        return sprintf( "cb-%05d", $this->coreBundle()->getId() );
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

        if( !Auth::check() && is_numeric( config( 'grapher.access.trunk' ) ) && config( 'grapher.access.trunk' ) == UserEntity::AUTH_PUBLIC ) {
            return $this->allow();
        }

        if( Auth::check() && is_numeric( config( 'grapher.access.trunk' ) ) && Auth::user()->getPrivs() >= config( 'grapher.access.trunk' ) ) {
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
            isset( $overrides['id']   ) ? $overrides['id']   : $this->coreBundle()->getId()
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
        $p['id'] = $this->coreBundle()->getId();
        return $p;
    }


    /**
     * Process user input for the parameter: cb
     *
     * Does a abort(404) if invalid
     *
     * @param int $i The user input value
     * @return CoreBundleEntity The verified / sanitised / default value
     */
    public static function processParameterCoreBundle( int $i ): CoreBundleEntity {
        $cb = null;
        if( !$i || !( $cb = d2r( 'CoreBundle' )->find( $i ) ) ) {
            abort(404);
        }

        return $cb;
    }

}
