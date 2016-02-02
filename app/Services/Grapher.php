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

use IXP\Contracts\Grapher as GrapherContract;
use IXP\Exceptions\Services\Grapher\ConfigurationException;

use Config;

/**
 * Grapher Backend -> Mrtg
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (c) 2009 - 2016, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Grapher implements GrapherContract {

    /**
     * Initialised grapher backends
     * @var array
     */
    public $backends = [];


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
            throw new ConfigurationException('No graphing provider enabled (see configs/grapher.php) for ' . $backend);
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
    public function getBackend( $backend = null ) {
        $backend = $this->resolveBackend( $backend );

        if( !isset( $this->backends[$backend] ) ) {
            $backendClass = Config::get( "grapher.providers.{$backend}" );
            $this->backends[ $backend ] = new $backendClass( $app['config']['grapher']['backends'][ $backend ] );
        }
        return $this->backends[$backend];
    }


}
