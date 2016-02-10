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

use Entities\Infrastructure as InfrastructureEntity;

/**
 * Grapher -> Infrastructure Graph
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (c) 2009 - 2016, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Infrastructure extends Graph {

    /**
     * Infrastructure to graph
     * @var \Entities\IXP
     */
    private $infrastructure = null;


    /**
     * Constructor
     */
    public function __construct( Grapher $grapher, InfrastructureEntity $i ) {
        parent::__construct( $grapher );
        $this->infrastructure = $i;
    }

    /**
     * Get the infrastructure we're set to use
     * @return \Entities\Infrastructure
     */
    public function infrastructure(): InfrastructureEntity {
        return $this->infrastructure;
    }

    /**
     * Set the infrastructure we should use
     * @param Entities\Infrastructure $infra
     * @return \IXP\Services\Grapher Fluid interface
     * @throws \IXP\Exceptions\Services\Grapher\ParameterException
     */
    public function setInfrastructure( InfrastructureEntity $infra ): Grapher {
        if( $this->infrastructure() && $this->infrastructure()->getId() != $infra->getId() ) {
            $this->wipe();
        }

        $this->infrastructure = $infra;
        return $this;
    }

    /**
     * The name of a graph (e.g. member name, IXP name, etc)
     * @return string
     */
    public function name(): string {
        return $this->infrastructure()->getName();
    }

    /**
     * A unique identifier for this 'graph type'
     *
     * E.g. for an IXP, it might be ixpxxx where xxx is the database id
     * @return string
     */
    public function identifier(): string {
        return sprintf( "infrastructure%03d", $this->infrastructure()->getId() );
    }

    /**
     * Process user input for the parameter: ixp
     *
     * Note that this function just sets the default if the input is invalid.
     * If you want to force an exception in such cases, use setIXP()
     *
     * @param int $v The user input value
     * @return int The verified / sanitised / default value
     */
    public static function processParameterInfrastructure( int $v ): InfrastructureEntity {
        if( !( $infra = d2r( 'Infrastructure' )->find( $v ) ) ) {
            $infra = d2r( 'IXP' )->getDefault()->getInfrastructures()[0];
        }
        return $infra;
    }


}
