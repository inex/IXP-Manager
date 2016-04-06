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

use Illuminate\Cache\Repository as CacheRepository;

use IXP\Exceptions\Services\Grapher\{
        BadBackendException,
        CannotHandleRequestException,
        ConfigurationException,
        ParameterException,
        GraphCannotBeProcessedException
};

use IXP\Services\Grapher\Graph;

use IXP\Services\Grapher\Graph\{
    IXP            as IXPGraph,
    Infrastructure as InfrastructureGraph,
    Vlan           as VlanGraph,
    Switcher       as SwitchGraph
};

use IXP\Contracts\Grapher\Backend as BackendContract;

use Cache;
use Config;
use D2EM;

use Entities\{IXP,Infrastructure,Vlan,Switcher};

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
     * Is the cache enabled?
     * @var bool
     */
    private $cacheEnabled = false;

    /**
     * Is the cache enabled?
     * @var bool
     */
    private $cacheLifetime = 5;

    /**
     * The cache store
     * @var Illuminate\Contracts\Cache\Store
     */
    private $cache = null;


    /**
     * Constructor
     */
    public function __construct() {
        $this->setupCache();
    }

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
     * @return \IXP\Contracts\Grapher\Backend
     */
    public function backend( $backend = null ): BackendContract {
        $backend = $this->resolveBackend( $backend );
        $backendClass = Config::get( "grapher.providers.{$backend}" );
        return new $backendClass( $app['config']['grapher']['backends'][ $backend ] );
    }

    /**
     * Return the required grapher for the specified graph
     *
     * @param IXP\Services\Grapher\Graph $graph
     * @param string $backends Limit search to specified backends
     * @return IXP\Contracts\Grapher\Backend
     * @throws IXP\Exceptions\Services\Grapher\ConfigurationException, IXP\Exceptions\Services\Grapher\GraphCannotBeProcessedException
     */
    public function backendForGraph( Graph $graph, array $backends = [] ): BackendContract {
        if( !count( $backends ) ) {
            $backends = config('grapher.backend');
        }

        if( !count( $backends ) ) {
            throw new ConfigurationException( 'No graphing backend supplied or configured (see configs/grapher.php)' );
        }

        foreach( $backends as $backend ) {
            if( ( $b = $this->backend( $backend ) )->canProcess( $graph ) ) {
                return $b;
            }
        }

        throw new GraphCannotBeProcessedException('No backend available to process this graph');
    }

    /**
     * Return the available grapher backends for the specified graph
     *
     * @param IXP\Services\Grapher\Graph $graph
     * @return IXP\Contracts\Grapher\Backend[]
     * @throws IXP\Exceptions\Services\Grapher\ConfigurationException
     */
    public function backendsForGraph( Graph $graph ): array {
        if( !count( config('grapher.backend') ) ) {
            throw new ConfigurationException( 'No graphing backend supplied or configured (see configs/grapher.php)' );
        }

        $backends = [];
        foreach( config('grapher.backend') as $backend ) {
            if( ( $b = $this->backend( $backend ) )->canProcess( $graph ) ) {
                $backends[] = $b;
            }
        }
        return $backends;
    }

    /**
     * Iterate over all configured backends and provide a complete array of what
     * graph types are supported
     * @return array
     */
    public function supports(): array {
        $s = [];

        foreach( config('grapher.backend') as $backend ) {
            $backendClass = Config::get( "grapher.providers.{$backend}" );
            $s = array_replace_recursive( $s, $backendClass::supports() );
        }

        return $s;
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
    public function infrastructure( Infrastructure $infra ): InfrastructureGraph {
        return new InfrastructureGraph( $this, $infra );
    }

    /**
     * Get an instance of an vlan graph
     * @param Entities\Vlan $vlan
     * @return IXP\Services\Grapher\Graph\Vlan
     */
    public function vlan( Vlan $vlan ): VlanGraph {
        return new VlanGraph( $this, $vlan );
    }

    /**
     * Get an instance of a switch graph
     * @param Entities\Switcher $switch
     * @return IXP\Services\Grapher\Graph\Switcher
     */
    public function switch( Switcher $switch ): SwitchGraph {
        return new SwitchGraph( $this, $switch );
    }




    /**
     * initialise the cache
     * @return void
     */
    private function setupCache() {
        if( config('grapher.cache.enabled', false ) ) {
            $this->cacheEnabled = true;
            $this->cacheLifetime = config('grapher.cache.lifetime', 5 );
            $this->cache = Cache::store( config('grapher.cache.store' ) );
        } else {
            $this->cacheEnabled = false;
        }
    }

    /**
     * Is the cache enabled?
     * @return bool
     */
    public function cacheEnabled(): bool {
        return (bool)$this->cacheEnabled;
    }

    /**
     * How long do we cache entries for?
     * @return int (minutes)
     */
    public function cacheLifetime(): int {
        return (int)$this->cacheLifetime;
    }


    /**
     * Get the cache repository
     * @return Illuminate\Cache\Repository
     */
    public function cacheRepository(): CacheRepository {
        return $this->cache;
    }

    /**
     * If the cache is enabled, return a previously cached item or else update / set it
     *
     * See Laravel's Cache::remember() function
     *
     * @param string $key
     * @param Closure $fn Callback to populate the cache
     * @return mixed
     */
    public function remember( $key, $fn ) {
        if( $this->cacheEnabled() ) {
            return $this->cacheRepository()->remember( $key, $this->cacheLifetime(), $fn );
        } else {
            return $fn();
        }
    }

}
