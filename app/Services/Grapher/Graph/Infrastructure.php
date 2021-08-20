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

use IXP\Services\Grapher;
use IXP\Services\Grapher\{Graph};

use IXP\Models\{
    Infrastructure as InfrastructureModel,
    User
};

/**
 * Grapher -> Infrastructure Graph
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Infrastructure extends Graph
{
    /**
     * Infrastructure to graph
     *
     * @var InfrastructureModel
     */
    private $infrastructure = null;

    /**
     * Constructor
     *
     * @param Grapher $grapher
     *
     * @param InfrastructureModel $i
     */
    public function __construct( Grapher $grapher, InfrastructureModel $i )
    {
        parent::__construct( $grapher );
        $this->infrastructure = $i;
    }

    /**
     * Get the infrastructure we're set to use
     *
     * @return InfrastructureModel
     */
    public function infrastructure(): InfrastructureModel
    {
        return $this->infrastructure;
    }

    /**
     * Set the infrastructure we should use
     *
     * @param InfrastructureModel $infra
     *
     * @return Infrastructure Fluid interface
     */
    public function setInfrastructure( InfrastructureModel $infra ): Infrastructure
    {
        if( $this->infrastructure() && $this->infrastructure()->id !== $infra->id ) {
            $this->wipe();
        }

        $this->infrastructure = $infra;
        return $this;
    }

    /**
     * The name of a graph (e.g. member name, IXP name, etc)
     *
     * @return string
     */
    public function name(): string
    {
        return $this->infrastructure()->name;
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
        return sprintf( "infrastructure%03d", $this->infrastructure()->id );
    }

    /**
     * This function controls access to the graph.
     *
     * {@inheritDoc}
     *
     * For infrastructure aggregate graphs we pretty much allow complete access.
     *
     * @return bool
     */
    public function authorise(): bool
    {
        if( Auth::check() && Auth::getUser()->isSuperUser() ) {
            return $this->allow();
        }

        if( in_array( $this->category(), [ self::CATEGORY_ERRORS, self::CATEGORY_DISCARDS ], true ) ) {
            $this->deny();
            return false;
        }

        if( is_numeric( config( 'grapher.access.infrastructure' ) ) && (int)config( 'grapher.access.infrastructure' ) === User::AUTH_PUBLIC ) {
            return $this->allow();
        }

        if( Auth::check() && is_numeric( config( 'grapher.access.infrastructure' ) ) && Auth::getUser()->privs() >= config( 'grapher.access.infrastructure' ) ) {
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
        return parent::url( $overrides ) . sprintf("&id=%d",
                $overrides[ 'id' ] ?? $this->infrastructure()->id
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
        $p['id'] = $this->infrastructure()->id;
        return $p;
    }

    /**
     * Process user input for the parameter: ixp
     *
     * Note that this function just sets the default if the input is invalid.
     * If you want to force an exception in such cases, use setIXP()
     *
     * @param int $v The user input value
     *
     * @return InfrastructureModel The verified / sanitised / default value
     */
    public static function processParameterInfrastructure( int $v ): InfrastructureModel
    {
        return InfrastructureModel::findOrFail( $v );
    }
}