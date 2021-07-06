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

use IXP\Contracts\Grapher\Backend as GrapherBackendContract;

use IXP\Services\Grapher\{
    Backend as GrapherBackend,
    Graph
};

use IXP\Services\Grapher\Graph\Latency as LatencyGraph;

use IXP\Exceptions\Services\Grapher\CannotHandleRequestException;

use IXP\Utils\Grapher\Dummy as DummyFile;

/**
 * Grapher Backend -> Dummy
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Dummy extends GrapherBackend implements GrapherBackendContract
{
    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function name(): string
    {
        return 'dummy';
    }

    /**
     * The dummy backend required no configuration.
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
     * @see Dummy::isMonolithicConfigurationSupported() for an explanation
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
     * @see Dummy::isMonolithicConfigurationSupported() for an explanation
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
     * @param int               $type   The type of configuration to generate
     * @param array             $options
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
        return [
            'ixp' => [
                'protocols'   => Graph::PROTOCOLS,
                'categories'  => Graph::CATEGORIES,
                'periods'     => Graph::PERIODS,
                'types'       => Graph::TYPES
            ],
            'infrastructure' => [
                'protocols'   => Graph::PROTOCOLS,
                'categories'  => Graph::CATEGORIES,
                'periods'     => Graph::PERIODS,
                'types'       => Graph::TYPES
            ],
            'vlan' => [
                'protocols'   => Graph::PROTOCOLS,
                'categories'  => Graph::CATEGORIES,
                'periods'     => Graph::PERIODS,
                'types'       => Graph::TYPES
            ],
            'trunk' => [
                'protocols'   => Graph::PROTOCOLS,
                'categories'  => Graph::CATEGORIES,
                'periods'     => Graph::PERIODS,
                'types'       => Graph::TYPES
            ],
            'switcher' => [
                'protocols'   => Graph::PROTOCOLS,
                'categories'  => Graph::CATEGORIES,
                'periods'     => Graph::PERIODS,
                'types'       => Graph::TYPES
            ],
            'physicalinterface' => [
                'protocols'   => Graph::PROTOCOLS,
                'categories'  => Graph::CATEGORIES,
                'periods'     => Graph::PERIODS,
                'types'       => Graph::TYPES
            ],
            'virtualinterface' => [
                'protocols'   => Graph::PROTOCOLS,
                'categories'  => Graph::CATEGORIES,
                'periods'     => Graph::PERIODS,
                'types'       => Graph::TYPES
            ],
            'customer' => [
                'protocols'   => Graph::PROTOCOLS,
                'categories'  => Graph::CATEGORIES,
                'periods'     => Graph::PERIODS,
                'types'       => Graph::TYPES
            ],
            'vlaninterface' => [
                'protocols'   => Graph::PROTOCOLS,
                'categories'  => Graph::CATEGORIES,
                'periods'     => Graph::PERIODS,
                'types'       => Graph::TYPES
            ],
            'latency' => [
                'protocols'   => Graph::PROTOCOLS_REAL,
                'categories'  => Graph::CATEGORIES,
                'periods'     => LatencyGraph::PERIODS,
                'types'       => [ Graph::TYPE_PNG => Graph::TYPE_PNG ],
            ],
            'p2p' => [
                'protocols'   => Graph::PROTOCOLS,
                'categories'  => Graph::CATEGORIES,
                'periods'     => Graph::PERIODS,
                'types'       => Graph::TYPES
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
        $dummy = new DummyFile( $this->resolveFilePath( $graph, 'log' ) );
        return $dummy->data( $graph );
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
        return @file_get_contents( $this->resolveFilePath( $graph, 'png' ) );
    }

    /**
     * Get the RRD file for a given graph
     *
     * {inheritDoc}
     *
     * @param Graph $graph
     *
     * @return string
     */
    public function rrd( Graph $graph ): string
    {
        return '';
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
     * @throws CannotHandleRequestException
     */
    public function dataPath( Graph $graph ): string
    {
        return $this->resolveFilePath( $graph, 'log' );
    }

    /**
     * For a given graph, return the path where the appropriate log file
     * will be found.
     *
     * @param Graph     $graph
     * @param string    $type
     *
     * @return string
     *
     * @throws
     */
    private function resolveFilePath( Graph $graph, $type ): string
    {
        $config = config('grapher.backends.dummy');

        switch( $graph->classType() ) {
            default:
                $file = sprintf( "%s/dummy%s.%s", $config['logdir'], $type === 'log' ? '' : "-{$graph->period()}", $type );
                if( !file_exists( $file ) ) {
                    throw new CannotHandleRequestException("Backend asserted it could process but cannot handle graph of type: {$graph->type()}" );
                }
                return $file;
            break;
        }
    }
}