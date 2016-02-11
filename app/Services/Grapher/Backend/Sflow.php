<?php namespace IXP\Services\Grapher\Backend;

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

use IXP\Contracts\Grapher\Backend as GrapherBackendContract;
use IXP\Services\Grapher\Backend as GrapherBackend;

use IXP\Services\Grapher\Graph;

use Entities\IXP;

/**
 * Grapher Backend -> Mrtg
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (c) 2009 - 2016, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Sflow extends GrapherBackend implements GrapherBackendContract {

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function name(): string {
        return 'sflow';
    }

    /**
     * The sflow backend requires no configuration.
     *
     * {@inheritDoc}
     *
     * @return bool
     */
    public function isConfigurationRequired(): bool {
        return false;
    }

    /**
     * This function indicates whether this graphing engine supports single monolithic text
     *
     * @see IXP\Contracts\Grapher::isMonolithicConfigurationSupported() for an explanation
     * @return bool
     */
    public function isMonolithicConfigurationSupported(): bool {
        return false;
    }

    /**
     * This function indicates whether this graphing engine supports multiple files to a directory
     *
     * @see IXP\Contracts\Grapher::isMonolithicConfigurationSupported() for an explanation
     * @return bool
     */
    public function isMultiFileConfigurationSupported(): bool {
        return false;
    }

    /**
     * Generate the configuration file(s) for this graphing backend
     *
     * {inheritDoc}
     *
     * @param Entities\IXP $ixp The IXP to generate the config for (multi-IXP mode)
     * @param int $config_type The type of configuration to generate
     * @return array
     */
    public function generateConfiguration( IXP $ixp, int $type = self::GENERATED_CONFIG_TYPE_MONOLITHIC ): array
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
    public static function supports(): array {
        return [
            'ixp' => [
                'protocols'   => array_except( Graph::PROTOCOLS, Graph::PROTOCOL_ALL ),
                'categories'  => [ Graph::CATEGORY_BITS => Graph::CATEGORY_BITS,
                                    Graph::CATEGORY_PACKETS => Graph::CATEGORY_PACKETS ],
                'periods'     => Graph::PERIODS,
                'types'       => Graph::TYPES,
            ],
            'infrastructure' => [
                'protocols'   => array_except( Graph::PROTOCOLS, Graph::PROTOCOL_ALL ),
                'categories'  => [ Graph::CATEGORY_BITS => Graph::CATEGORY_BITS,
                                    Graph::CATEGORY_PACKETS => Graph::CATEGORY_PACKETS ],
                'periods'     => Graph::PERIODS,
                'types'       => Graph::TYPES,
            ]
        ];
    }


    /**
     * Get the data points for a given graph
     *
     * {inheritDoc}
     *
     * @param IXP\Services\Grapher\Graph $graph
     * @return array
     */
    public function data( Graph $graph ): array {
        return [];
    }

    /**
     * Get the PNG image for a given graph
     *
     * {inheritDoc}
     *
     * @param IXP\Services\Grapher\Graph $graph
     * @return string
     */
    public function png( Graph $graph ): string {
        return '';
    }

    /**
     * Get the RRD file for a given graph
     *
     * {inheritDoc}
     *
     * @param IXP\Services\Grapher\Graph $graph
     * @return string
     */
    public function rrd( Graph $graph ): string {
        return '';
    }


    /**
     * For a given graph, return the path where the appropriate file
     * will be found.
     *
     * @param IXP\Services\Grapher\Graph $graph
     * @return string
     */
    private function resolveFilePath( Graph $graph, $type ): string {
        $config = config('grapher.backends.sflow');

        switch( $graph->classType() ) {
            case 'IXP':
                return sprintf( "%s/ixp%03d-%s%s.%s", $config['logdir'], $graph->ixp()->getId(),
                    $graph->category(), $type == 'log' ? '' : "-{$graph->period()}", $type );
                break;

            case 'Infrastructure':
                return sprintf( "%s/ixp%03d-infra%03d-%s%s.%s", $config['logdir'], $graph->infrastructure()->getIXP()->getId(),
                    $graph->infrastructure()->getId(), $graph->category(), $type == 'log' ? '' : "-{$graph->period()}", $type );
                break;

            default:
                throw new CannotHandleRequestException("Backend asserted it could process but cannot handle graph of type: {$graph->type()}" );
        }
    }



}
