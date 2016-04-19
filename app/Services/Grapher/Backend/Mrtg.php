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

use IXP\Exceptions\Services\Grapher\CannotHandleRequestException;
use IXP\Exceptions\Utils\Grapher\FileError as FileErrorException;

use Entities\{IXP,Switcher,SwitchPort};
use IXP\Utils\Grapher\Mrtg as MrtgFile;

use View,Log;

/**
 * Grapher Backend -> Mrtg
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (c) 2009 - 2016, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Mrtg extends GrapherBackend implements GrapherBackendContract {

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function name(): string {
        return 'mrtg';
    }

    /**
     * The dummy backend requires no configuration.
     *
     * {@inheritDoc}
     *
     * @return bool
     */
    public function isConfigurationRequired(): bool {
        return true;
    }

    /**
     * This function indicates whether this graphing engine supports single monolithic text
     *
     * @see IXP\Contracts\Grapher::isMonolithicConfigurationSupported() for an explanation
     * @return bool
     */
    public function isMonolithicConfigurationSupported(): bool {
        return true;
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
        return [
            View::make( 'services.grapher.mrtg.monolithic',
                [
                    'ixp'       => $ixp,
                    'data'      => $this->getPeeringPorts( $ixp )
                ]
            )->render()
        ];
    }

    /**
     * Utility function to slurp all peering ports from the database and arrange them in
     * arrays for genertaing Mrtg configuration files.
     *
     * The array returned is an array of arrays containing:
     *
     * * array `['pis']` of PhysicalInterface objects indexed by their ID
     * * array `['custs']` of Customer objects indexed by their ID
     * * array `['sws']` of Switcher objects indexed by their ID
     * * array `['infras']` of Infrastructure objects indexed by their ID
     * * array `['custports']` containing an array of PhysicalInterface IDs indexed by customer ID
     * * array `['custlags']` containing an array of PhysicalInterface IDs contained in an array indexed
     *       by VirtualInterface IDs in turn in an array of customer IDs:
     *       `['custlags'][$custid][$viid][]`
     * * array `['swports']` indexed by Switcher ID conataining the PhysicalInterface IDs of peering ports
     * * array `['infraports']` indexed by Infrastructure ID conataining the PhysicalInterface IDs of peering ports
     * * array `['ixpports']` conataining the PhysicalInterface IDs of peering ports
     *
     *
     * @param Entities\IXP $ixp The IXP to generate the config for (multi-IXP mode)
     * @return array
     */
    public function getPeeringPorts( IXP $ixp ): array {
        $data = [];

        foreach( $ixp->getCustomers() as $c ) {

            foreach( $c->getVirtualInterfaces() as $vi ) {

                foreach( $vi->getPhysicalInterfaces() as $pi ) {

                    // we're not multi-ixp in v4 but we'll catch non-relavent ports here
                    if( $pi->getSwitchPort()->getSwitcher()->getInfrastructure()->getIXP()->getId() != $ixp->getId() ) {
                        break 2;
                    }

                    $data['pis'][$pi->getId()] = $pi;

                    if( !isset( $data['custs'][ $c->getId() ] ) ) {
                            $data['custs'][ $c->getId() ] = $c;
                    }

                    if( !isset( $data['sws'][ $pi->getSwitchPort()->getSwitcher()->getId() ] ) ) {
                        $data['sws'][$pi->getSwitchPort()->getSwitcher()->getId() ] = $pi->getSwitchPort()->getSwitcher();
                    }

                    if( !isset( $data['infras'][ $pi->getSwitchPort()->getSwitcher()->getInfrastructure()->getId() ] ) ) {
                        $data['infras'][ $pi->getSwitchPort()->getSwitcher()->getInfrastructure()->getId() ] = $pi->getSwitchPort()->getSwitcher()->getInfrastructure();
                    }

                    $data['custports'][$c->getId()][] = $pi->getId();

                    if( count( $vi->getPhysicalInterfaces() ) > 1 ) {
                        $data['custlags'][$c->getId()][$vi->getId()][] = $pi->getId();
                    }

                    if( $pi->statusIsConnected() ) {
                        $data['swports'][ $pi->getSwitchPort()->getSwitcher()->getId() ][] = $pi->getId();
                        $data['infraports'][ $pi->getSwitchPort()->getSwitcher()->getInfrastructure()->getId() ][] = $pi->getId();
                        $data['ixpports'][] = $pi->getId();
                    }

                }
            }
        }

        return $data;
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
                'protocols'   => [ Graph::PROTOCOL_ALL => Graph::PROTOCOL_ALL ],
                'categories'  => [ Graph::CATEGORY_BITS => Graph::CATEGORY_BITS,
                                    Graph::CATEGORY_PACKETS => Graph::CATEGORY_PACKETS ],
                'periods'     => Graph::PERIODS,
                'types'       => array_except( Graph::TYPES, Graph::TYPE_RRD )
            ],
            'infrastructure' => [
                'protocols'   => [ Graph::PROTOCOL_ALL => Graph::PROTOCOL_ALL ],
                'categories'  => [ Graph::CATEGORY_BITS => Graph::CATEGORY_BITS,
                                    Graph::CATEGORY_PACKETS => Graph::CATEGORY_PACKETS ],
                'periods'     => Graph::PERIODS,
                'types'       => array_except( Graph::TYPES, Graph::TYPE_RRD )
            ],
            'switcher' => [
                'protocols'   => [ Graph::PROTOCOL_ALL => Graph::PROTOCOL_ALL ],
                'categories'  => [ Graph::CATEGORY_BITS => Graph::CATEGORY_BITS,
                                    Graph::CATEGORY_PACKETS => Graph::CATEGORY_PACKETS ],
                'periods'     => Graph::PERIODS,
                'types'       => array_except( Graph::TYPES, Graph::TYPE_RRD )
            ],
            'physicalinterface' => [
                'protocols'   => [ Graph::PROTOCOL_ALL => Graph::PROTOCOL_ALL ],
                'categories'  => Graph::CATEGORIES,
                'periods'     => Graph::PERIODS,
                'types'       => array_except( Graph::TYPES, Graph::TYPE_RRD )
            ],
            'virtualinterface' => [
                'protocols'   => [ Graph::PROTOCOL_ALL => Graph::PROTOCOL_ALL ],
                'categories'  => Graph::CATEGORIES,
                'periods'     => Graph::PERIODS,
                'types'       => array_except( Graph::TYPES, Graph::TYPE_RRD )
            ],
            'customer' => [
                'protocols'   => [ Graph::PROTOCOL_ALL => Graph::PROTOCOL_ALL ],
                'categories'  => Graph::CATEGORIES,
                'periods'     => Graph::PERIODS,
                'types'       => array_except( Graph::TYPES, Graph::TYPE_RRD )
            ],
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
        try {
            $mrtg = new MrtgFile( $this->resolveFilePath( $graph, 'log' ) );
        } catch( FileErrorException $e ) {
            Log::notice("[Grapher] {$this->name()} data(): could not load file {$this->resolveFilePath( $graph, 'log' )}");
            return [];
        }

        return $mrtg->data( $graph );
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
        if( ( $img = @file_get_contents( $this->resolveFilePath( $graph, 'png' ) ) ) === false ){
            // couldn't load the image so return a placeholder
            Log::notice("[Grapher] {$this->name()} png(): could not load file {$this->resolveFilePath( $graph, 'png' )}");
            return @file_get_contents( public_path() . "/images/image-missing.png" );
        }

        return $img;
    }

    /**
     * For larger IXPs, allow sharding of directories over 16 possible base directories
     * @param int $id The customer entity id
     * @return string shared path -> e.g. 18 -> 18 % 16 = 2 / 00016 -> 2/00016
     */
    private function shardMemberDir( int $id ): string {
        return sprintf( "%x/%05d", $id % 16, $id );
    }

    /**
     * For a given graph, return the path where the appropriate log file
     * will be found.
     *
     * @param IXP\Services\Grapher\Graph $graph
     * @return string
     */
    public function resolveFilePath( Graph $graph, $type ): string {
        $config = config('grapher.backends.mrtg');

        switch( $graph->classType() ) {
            case 'IXP':
                return sprintf( "%s/ixp/ixp%03d-%s%s.%s", $config['logdir'], $graph->ixp()->getId(),
                    $graph->category(), $type == 'log' ? '' : "-{$graph->period()}", $type );
                break;

            case 'Infrastructure':
                return sprintf( "%s/infras/%03d/ixp%03d-infra%03d-%s%s.%s", $config['logdir'],
                    $graph->infrastructure()->getId(), $graph->infrastructure()->getIXP()->getId(),
                    $graph->infrastructure()->getId(), $graph->category(), $type == 'log' ? '' : "-{$graph->period()}", $type );
                break;

            case 'Switcher':
                return sprintf( "%s/switches/%03d/switch-aggregate-%05d-%s%s.%s", $config['logdir'],
                    $graph->switch()->getId(), $graph->switch()->getId(),
                    $graph->category(), $type == 'log' ? '' : "-{$graph->period()}", $type );
                break;

            case 'PhysicalInterface':
                return sprintf( "%s/members/%s/ints/%s-%s%s.%s", $config['logdir'],
                    $this->shardMemberDir( $graph->physicalInterface()->getVirtualInterface()->getCustomer()->getId() ),
                    $graph->identifier(), $graph->category(),
                    $type == 'log' ? '' : "-{$graph->period()}", $type );
                break;

            case 'VirtualInterface':
                return sprintf( "%s/members/%s/lags/%s-%s%s.%s", $config['logdir'],
                    $this->shardMemberDir( $graph->virtualInterface()->getCustomer()->getId() ),
                    $graph->identifier(), $graph->category(),
                    $type == 'log' ? '' : "-{$graph->period()}", $type );
                break;

            case 'Customer':
                return sprintf( "%s/members/%s/%s-%s%s.%s", $config['logdir'],
                    $this->shardMemberDir( $graph->customer()->getId() ),
                    $graph->identifier(), $graph->category(),
                    $type == 'log' ? '' : "-{$graph->period()}", $type );
                break;


            default:
                throw new CannotHandleRequestException("Backend asserted it could process but cannot handle graph of type: {$graph->type()}" );
        }
    }



}
