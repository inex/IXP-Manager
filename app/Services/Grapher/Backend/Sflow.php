<?php

namespace IXP\Services\Grapher\Backend;

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

use Log;

use IXP\Contracts\Grapher\Backend as GrapherBackendContract;

use IXP\Exceptions\Services\Grapher\CannotHandleRequestException;
use IXP\Exceptions\Utils\Grapher\FileError as FileErrorException;

use IXP\Services\Grapher\Backend as GrapherBackend;
use IXP\Services\Grapher\Graph;

use IXP\Utils\Grapher\{
    Rrd  as RrdUtil
};

/**
 * Grapher Backend -> Sflow
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <Yann@islandbridgenetworks.ie>
 * @category   Ixp
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Sflow extends GrapherBackend implements GrapherBackendContract
{
    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function name(): string
    {
        return 'sflow';
    }

    /**
     * The sflow backend requires no configuration.
     *
     * {@inheritDoc}
     *
     * @return bool
     */
    public function isConfigurationRequired(): bool
    {
        return false;
    }

    /**
     * This function indicates whether this graphing engine supports single monolithic text
     *
     * @see \IXP\Contracts\Grapher::isMonolithicConfigurationSupported() for an explanation
     *
     * @return bool
     */
    public function isMonolithicConfigurationSupported(): bool
    {
        return false;
    }

    /**
     * This function indicates whether this graphing engine supports multiple files to a directory
     *
     * @see \IXP\Contracts\Grapher::isMonolithicConfigurationSupported() for an explanation
     *
     * @return bool
     */
    public function isMultiFileConfigurationSupported(): bool
    {
        return false;
    }

    /**
     * Generate the configuration file(s) for this graphing backend
     *
     * {inheritDoc}
     *
     * @param int   $type       The type of configuration to generate
     * @param array $options
     *
     * @return array
     */
    public function generateConfiguration( int $type = self::GENERATED_CONFIG_TYPE_MONOLITHIC, array $options = [] ): array
    {
        return [];
    }

    /**
     * Get a complete list of functionality that this backend supports.
     *
     * {inheritDoc}
     *
     * @return array
     */
    public static function supports(): array
    {
        $graphProtocols = Graph::PROTOCOLS;
        unset( $graphProtocols[ Graph::PROTOCOL_ALL ] );

        return [
            'vlan' => [
                'protocols'   => Graph::PROTOCOLS_REAL,
                'categories'  => [ Graph::CATEGORY_BITS => Graph::CATEGORY_BITS,
                                    Graph::CATEGORY_PACKETS => Graph::CATEGORY_PACKETS ],
                'periods'     => Graph::PERIODS,
                'types'       => Graph::TYPES,
            ],
            'vlaninterface' => [
                'protocols'   => $graphProtocols,
                'categories'  => [ Graph::CATEGORY_BITS => Graph::CATEGORY_BITS,
                                    Graph::CATEGORY_PACKETS => Graph::CATEGORY_PACKETS ],
                'periods'     => Graph::PERIODS,
                'types'       => Graph::TYPES,
            ],
            'p2p' => [
                'protocols'   => $graphProtocols,
                'categories'  => [ Graph::CATEGORY_BITS => Graph::CATEGORY_BITS,
                                    Graph::CATEGORY_PACKETS => Graph::CATEGORY_PACKETS ],
                'periods'     => Graph::PERIODS,
                'types'       => Graph::TYPES,
            ],
        ];
    }

    /**
     * Get the data points for a given graph
     *
     * {inheritDoc}
     *
     * @param Graph $graph
     *
     * @return array
     *
     * @throws
     */
    public function data( Graph $graph ): array
    {
        try {
            $rrd = new RrdUtil( $this->resolveFilePath( $graph, 'rrd' ), $graph );
            return $rrd->data();
        } catch( FileErrorException $e ) {
            Log::notice("[Grapher] {$this->name()} data(): could not load file {$this->resolveFilePath( $graph, 'rrd' )}");
            return [];
        }
    }

    /**
     * Get the path to the graphing data file (e.g. path to log or rrd file).
     *
     * {inheritDoc}
     *
     * @param Graph $graph
     *
     * @return string Path or empty string
     *
     * @throws
     */
    public function dataPath( Graph $graph ): string
    {
        return $this->resolveFilePath( $graph, 'rrd' );
    }

    /**
     * Get the PNG image for a given graph
     *
     * {inheritDoc}
     *
     * @param Graph $graph
     *
     * @return string
     *
     * @throws
     */
    public function png( Graph $graph ): string
    {
        try {
            $rrd = new RrdUtil( $this->resolveFilePath( $graph, 'rrd' ), $graph );
            return @file_get_contents( $rrd->png() );
        } catch( FileErrorException $e ) {
            Log::notice("[Grapher] {$this->name()} png(): could not load rrd file " . ( isset( $rrd ) ? $rrd->file() : '???' ) );
            return false; // FIXME check handling of this
        }
    }

    /**
     * Get the RRD file for a given graph
     *
     * {inheritDoc}
     *
     * @param Graph $graph
     *
     * @return string
     *
     * @throws
     */
    public function rrd( Graph $graph ): string
    {
        try {
            $rrd = new RrdUtil( $this->resolveFilePath( $graph, 'rrd' ), $graph );
            return $rrd->rrd();
        } catch( FileErrorException $e ) {
            Log::notice("[Grapher] {$this->name()} rrd(): could not load rrd file {$rrd->file()}");
            return false; // FIXME check handling of this
        }
    }

    /**
     * Our sflow/p2p script stores as bytes and names directories / files accordingly. This
     * function just s/bits/bytes for accessing these files.
     *
     * @param string $c
     *
     * @return string
     */
    private function translateCategory( $c ): string
    {
        if( $c === Graph::CATEGORY_BITS ) {
            return 'bytes';
        }
        return $c;
    }

    /**
     * For a given graph, return the filename where the appropriate data
     * will be found.
     *
     * @param Graph     $graph
     * @param string    $type
     *
     * @return string
     *
     * @throws
     */
    private function resolveFileName( Graph $graph, $type ): string
    {
        switch( $graph->classType() ) {
            case 'Vlan':
                /** @var Graph\Vlan $graph */
                return sprintf( "aggregate.%s.%s.vlan%05d.%s",
                    $graph->protocol(), $this->translateCategory( $graph->category() ),
                    $graph->vlan()->number, $type );
            case 'VlanInterface':
                /** @var Graph\VlanInterface $graph */
                return sprintf( "individual.%s.%s.src-%05d.%s",
                    $graph->protocol(), $this->translateCategory( $graph->category() ),
                    $graph->vlanInterface()->id, $type );
            case 'P2p':
                /** @var Graph\P2p $graph */
                return sprintf( "p2p.%s.%s.src-%05d.dst-%05d.%s",
                    $graph->protocol(), $this->translateCategory( $graph->category() ),
                    $graph->svli()->id, $graph->dvli()->id, $type );
            default:
                throw new CannotHandleRequestException("Backend asserted it could process but cannot handle graph of type: {$graph->type()}" );
        }
    }

    /**
     * For a given graph, return the path where the appropriate file
     * will be found.
     *
     * @param Graph $graph
     *
     * @return string
     *
     * @throws
     */
    private function resolveFilePath( Graph $graph, $type ): string
    {
        $config = config('grapher.backends.sflow');

        switch( $graph->classType() ) {
            case 'Vlan':
                /** @var Graph\Vlan $graph */
                return sprintf( "%s/%s/%s/aggregate/%s", $config['root'],
                    $graph->protocol(), $this->translateCategory( $graph->category() ),
                    $this->resolveFileName( $graph, $type ) );
            case 'VlanInterface':
                /** @var Graph\VlanInterface $graph */
                return sprintf( "%s/%s/%s/individual/%s", $config['root'],
                    $graph->protocol(), $this->translateCategory( $graph->category() ),
                    $this->resolveFileName( $graph, $type ) );
            case 'P2p':
                /** @var Graph\P2p $graph */
                return sprintf( "%s/%s/%s/p2p/src-%05d/%s", $config['root'],
                    $graph->protocol(), $this->translateCategory( $graph->category() ),
                    $graph->svli()->id, $this->resolveFileName( $graph, $type ) );
            default:
                throw new CannotHandleRequestException("Backend asserted it could process but cannot handle graph of type: {$graph->type()}" );
        }
    }
}