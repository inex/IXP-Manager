<?php

namespace IXP\Services;

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

use Cache, Config;

use IXP\Exceptions\Services\Grapher\{
        BadBackendException,
        ConfigurationException,
        GraphCannotBeProcessedException
};

use Closure;
use Illuminate\Contracts\Cache\Repository;
use IXP\Contracts\Grapher\Backend as BackendContract;

use IXP\Models\{Customer,
    CoreBundle,
    Infrastructure,
    Location,
    PhysicalInterface,
    VirtualInterface,
    VlanInterface,
    Switcher,
    Vlan};

use IXP\Services\Grapher\Graph;

use IXP\Services\Grapher\Graph\{
    IXP               as IXPGraph,
    Infrastructure    as InfrastructureGraph,
    Vlan              as VlanGraph,
    Location          as LocationGraph,
    Switcher          as SwitchGraph,
    Trunk             as TrunkGraph,
    CoreBundle        as CoreBundleGraph,
    PhysicalInterface as PhysIntGraph,  // member physical port
    VirtualInterface  as VirtIntGraph,  // member LAG
    Customer          as CustomerGraph, // member agg over all physical ports
    VlanInterface     as VlanIntGraph,  // member VLAN interface
    P2p               as P2pGraph,
    Latency           as LatencyGraph
};

/**
 * Grapher Backend -> Mrtg
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Grapher
{
    /**
     * Is the cache enabled?
     *
     * @var bool
     */
    private $cacheEnabled = false;

    /**
     * Is the cache enabled?
     *
     * @var bool
     */
    private $cacheLifetime = 300;

    /**
     * Constructor
     */
    public function __construct()
    {
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
     * @param string|null $backend |null
     *
     * @return string
     *
     * @throws
     */
    public function resolveBackend( string $backend = null ): string
    {
        $config = config('grapher.backend');

        if( $backend === null ) {
            if( is_array( $config ) && count( $config ) ) {
                $backend = config('grapher.backend')[0];
            } else {
                throw new ConfigurationException( 'No graphing backend supplied or configured (see configs/grapher.php)' );
            }
        }

        if( !is_array( $config ) || !in_array( $backend, $config, true ) ) {
            throw new BadBackendException( 'No graphing provider enabled (see configs/grapher.php) for ' . $backend );
        }

        return $backend;
    }

    /**
     * Return the required grapher for the specified backend
     *
     * If the backend is not specified, it is resolved via `resolveBackend()`.
     * @param string|null $backend A specific backend to return. If not specified, we use command line arguments
     *
     * @return BackendContract
     *
     * @throws
     *
     * @see \IXP\Console\Commands\Grapher\GrapherCommand::resolveBackend()
     *
     */
    public function backend( $backend = null ): BackendContract
    {
        $backend = $this->resolveBackend( $backend );
        $backendClass = Config::get( "grapher.providers.{$backend}" );
        return new $backendClass( config('grapher.backends')[ $backend ] );
    }

    /**
     * Return the required grapher for the specified graph
     *
     * @param Graph $graph
     *
     * @param array|string $backends Limit search to specified backends
     *
     * @return BackendContract
     *
     * @throws
     */
    public function backendForGraph( Graph $graph, array $backends = [] ): BackendContract
    {
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
     * @param Graph $graph
     *
     * @return BackendContract[]
     *
     * @throws
     */
    public function backendsForGraph( Graph $graph ): array
    {
        $config = config('grapher.backend');
        if( !is_array( $config ) || !count( $config ) ) {
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
     *
     * @return array
     */
    public function supports(): array
    {
        $s = [];

        foreach( config('grapher.backend') as $backend ) {
            $backendClass = Config::get( "grapher.providers.{$backend}" );
            $s = array_replace_recursive( $s, $backendClass::supports() );
        }

        return $s;
    }

    /**
     * Get an instance of an IXP graph
     *
     * @return IXPGraph
     */
    public function ixp(): IXPGraph
    {
        return new IXPGraph( $this );
    }

    /**
     * Get an instance of an infrastructure graph
     *
     * @param Infrastructure $infra
     *
     * @return InfrastructureGraph
     */
    public function infrastructure( Infrastructure $infra ): InfrastructureGraph
    {
        return new InfrastructureGraph( $this, $infra );
    }

    /**
     * Get an instance of an vlan graph
     *
     * @param Vlan $vlan
     *
     * @return VlanGraph
     */
    public function vlan( Vlan $vlan ): VlanGraph
    {
        return new VlanGraph( $this, $vlan );
    }

    /**
     * Get an instance of a location graph
     *
     * @param Location $location
     *
     * @return LocationGraph
     */
    public function location( Location $location ): LocationGraph
    {
        return new LocationGraph( $this, $location );
    }

    /**
     * Get an instance of a switch graph
     *
     * @param Switcher $switch
     *
     * @return SwitchGraph
     */
    public function switch( Switcher $switch ): SwitchGraph
    {
        return new SwitchGraph( $this, $switch );
    }

    /**
     * Get an instance of a trunk graph
     *
     * @param string $trunkname
     *
     * @return TrunkGraph
     *
     * @throws
     */
    public function trunk( string $trunkname ): TrunkGraph
    {
        return new TrunkGraph( $this, $trunkname );
    }

    /**
     * Get an instance of a customer aggregate graph
     *
     * @param Customer $c
     *
     * @return CustomerGraph
     */
    public function customer( Customer $c ): CustomerGraph
    {
        return new CustomerGraph( $this, $c );
    }

    /**
     * Get an instance of a physint graph
     *
     * @param PhysicalInterface $int
     *
     * @return PhysIntGraph
     */
    public function physint( PhysicalInterface $int ): PhysIntGraph
    {
        return new PhysIntGraph( $this, $int );
    }

    /**
     * Get an instance of a virtint graph
     *
     * @param VirtualInterface $int
     *
     * @return Graph
     */
    public function virtint( VirtualInterface $int ): Graph
    {
        // if there is only one physint, then the user really wants that:
        if( $int->physicalInterfaces->count()  === 1 ) {
            return $this->physint( $int->physicalInterfaces->first() );
        }
        return new VirtIntGraph( $this, $int );
    }

    /**
     * Get an instance of a vlanint graph
     *
     * @param VlanInterface $int
     *
     * @return VlanIntGraph
     */
    public function vlanint( VlanInterface $int ): VlanIntGraph
    {
        return new VlanIntGraph( $this, $int );
    }

    /**
     * Get an instance of a CoreBundle aggregate graph
     *
     * @param CoreBundle    $cb
     * @param string        $side
     *
     * @return CoreBundleGraph
     */
    public function coreBundle( CoreBundle $cb, string $side = 'a' ): CoreBundleGraph
    {
        return new CoreBundleGraph( $this, $cb, $side );
    }

    /**
     * Get an instance of a p2p graph
     *
     * @param VlanInterface $svli
     * @param VlanInterface $dvli
     *
     * @return P2pGraph
     */
    public function p2p( VlanInterface $svli, VlanInterface $dvli ): P2pGraph
    {
        return new P2pGraph( $this, $svli, $dvli );
    }

    /**
     * Get an instance of a latency graph
     *
     * @param VlanInterface $vli
     *
     * @return LatencyGraph
     *
     * @throws
     */
    public function latency( VlanInterface $vli ): LatencyGraph
    {
        return new LatencyGraph( $this, $vli );
    }

    /**
     * initialise the cache
     *
     * @return void
     */
    private function setupCache(): void
    {
        if( config('grapher.cache.enabled', false ) ) {
            $this->cacheEnabled     = true;
            $this->cacheLifetime    = config('grapher.cache.lifetime', 5 );
        } else {
            $this->cacheEnabled = false;
        }
    }

    /**
     * Is the cache enabled?
     * @return bool
     */
    public function cacheEnabled(): bool
    {
        return (bool)$this->cacheEnabled;
    }

    /**
     * Manually disable the cache
     */
    public function disableCache(): void
    {
        $this->cacheEnabled = false;
    }

    /**
     * How long do we cache entries for?
     *
     * @return int (minutes)
     */
    public function cacheLifetime(): int
    {
        return (int)$this->cacheLifetime;
    }

    /**
     * Get the cache repository
     *
     * @return Repository
     */
    public function cacheRepository(): Repository
    {
        return Cache::store( config('grapher.cache.store' ) );
    }

    /**
     * If the cache is enabled, return a previously cached item or else update / set it
     *
     * See Laravel's Cache::remember() function
     *
     * @param string    $key
     * @param Closure   $fn Callback to populate the cache
     *
     * @return mixed
     */
    public function remember( string $key, Closure $fn )
    {
        if( $this->cacheEnabled() ) {
            return $this->cacheRepository()->remember( $key, $this->cacheLifetime(), $fn );
        }
        return $fn();
    }
}