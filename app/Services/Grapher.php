<?php

namespace IXP\Services;

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

use IXP\Exceptions\Services\Grapher\{
        BadBackendException,
        CannotHandleRequestException,
        ConfigurationException,
        ParameterException,
        GraphCannotBeProcessedException
};

use IXP\Services\Grapher\Graph;
use IXP\Services\Grapher\Graph\IXP as IXPGraph;
use IXP\Services\Grapher\Graph\Infrastructure as InfrastructureGraph;

use IXP\Contracts\Grapher\Backend as BackendContract;

use Config;
use D2EM;

use Entities\{IXP,Infrastructure};

/**
 * Grapher Backend -> Mrtg
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (c) 2009 - 2016, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Grapher {

    /**
     * As we allow multiple graphing backends, we need to resolve
     * which one we're meant to use here.
     *
     * The order of resolution is:
     *
     * 1. As specified in the `$backend` parameter if not null
     * 2. First backend in `configs/grapher.php` `backend` element.
     *
     * @param string $backend|null
     * @return string
     */
    public function resolveBackend( string $backend = null ): string {
        if( $backend === null ) {
            if( count( config('grapher.backend') ) ) {
                $backend = config('grapher.backend')[0];
            } else {
                throw new ConfigurationException( 'No graphing backend supplied or configured (see configs/grapher.php)' );
            }
        }

        if( !in_array($backend,config('grapher.backend') ) ) {
            throw new BadBackendException( 'No graphing provider enabled (see configs/grapher.php) for ' . $backend );
        }

        return $backend;
    }

    /**
     * Return the required grapher for the specified backend
     *
     * If the backend is not specified, it is resolved via `resolveBackend()`.
     * @see IXP\Console\Commands\Grapher\GrapherCommand::resolveBackend()
     *
     * @param string|null $backend A specific backend to return. If not specified, we use command line arguments
     * @return \IXP\Contracts\Grapher
     */
    public function backend( $backend = null ) {
        $backend = $this->resolveBackend( $backend );
        $backendClass = Config::get( "grapher.providers.{$backend}" );
        return new $backendClass( $app['config']['grapher']['backends'][ $backend ] );
    }

    /**
     * Return the required grapher for the specified graph
     *
     * @param IXP\Services\Grapher\Graph $graph
     * @return IXP\Contracts\Grapher\Backend
     * @throws IXP\Exceptions\Services\Grapher\ConfigurationException, IXP\Exceptions\Services\Grapher\GraphCannotBeProcessedException
     */
    public function backendForGraph( Graph $graph ): BackendContract {
        if( !count( config('grapher.backend') ) ) {
            throw new ConfigurationException( 'No graphing backend supplied or configured (see configs/grapher.php)' );
        }

        foreach( config('grapher.backend') as $backend ) {
            if( ( $b = $this->backend( $backend ) )->canProcess( $graph ) ) {
                return $b;
            }
        }

        throw new GraphCannotBeProcessedException('No backend available to process this graph');
    }

    /**
     * Get an instance of an IXP graph
     * @param Entities\IXP $ixp
     * @return IXP\Services\Grapher\Graph\IXP
     */
    public function ixp( IXP $ixp ): IXPGraph {
        return new IXPGraph( $this, $ixp );
    }

    /**
     * Get an instance of an infrastructure graph
     * @param Entities\Infrastructure $infra
     * @return IXP\Services\Grapher\Graph\Infrastructure
     */
    public function infrastructure( Infrastructure $i ): InfrastructureGraph {
        return new InfrastructureGraph( $this, $i );
    }



}
