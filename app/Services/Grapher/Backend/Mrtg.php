<?php namespace IXP\Services\Grapher\Backend;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Entities\{
    IXP                     as IXPEntity,
    PhysicalInterface       as PhysicalInterfaceEntity,
    SwitchPort              as SwitchPortEntity,
    Switcher                as SwitcherEntity,
    VirtualInterface        as VirtualInterfaceEntity
};

use IXP\Utils\Grapher\{
    Mrtg as MrtgFile,
    Rrd  as RrdUtil
};

use D2EM, View,Log;

/**
 * Grapher Backend -> Mrtg
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
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
     * @see \IXP\Contracts\Grapher::isMonolithicConfigurationSupported() for an explanation
     * @return bool
     */
    public function isMonolithicConfigurationSupported(): bool {
        return true;
    }

    /**
     * This function indicates whether this graphing engine supports multiple files to a directory
     *
     * @see \IXP\Contracts\Grapher::isMonolithicConfigurationSupported() for an explanation
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
     * @param int $type The type of configuration to generate
     * @param array $options
     * @return array
     */
    public function generateConfiguration( int $type = self::GENERATED_CONFIG_TYPE_MONOLITHIC, array $options = [] ): array
    {
        return [
            View::make( 'services.grapher.mrtg.monolithic', [
                    'ixp'        => D2EM::getRepository( IXPEntity::class )->getDefault(),
                    'data'       => $this->getPeeringPorts( D2EM::getRepository( IXPEntity::class )->getDefault() ),
                    'snmppasswd' => config('grapher.backends.mrtg.snmppasswd'),
                ]
            )->render(),
        ];
    }

    /**
     * Utility function to slurp all peering ports from the database and arrange them in
     * arrays for genertaing Mrtg configuration files.
     *
     * The array returned is an array of arrays containing:
     *
     * * array `['pis']` of PhysicalInterfaceEntity objects indexed by their ID
     * * array `['custs']` of Customer objects indexed by their ID
     * * array `['sws']` of Switcher objects indexed by their ID
     * * array `['infras']` of Infrastructure objects indexed by their ID
     * * array `['custports']` containing an array of PhysicalInterfaceEntity IDs indexed by customer ID
     * * array `['custlags']` containing an array of PhysicalInterfaceEntity IDs contained in an array indexed
     *       by VirtualInterfaceEntity IDs in turn in an array of customer IDs:
     *       `['custlags'][$custid][$viid][]`
     * * array `['swports']` indexed by Switcher ID conataining the PhysicalInterfaceEntity IDs of peering ports
     * * array `['infraports']` indexed by Infrastructure ID conataining the PhysicalInterfaceEntity IDs of peering ports
     * * array `['ixpports']` conataining the PhysicalInterfaceEntity IDs of peering ports
     *
     *
     * @param IXPEntity $ixp The IXP to generate the config for (multi-IXP mode)
     * @return array
     */
    public function getPeeringPorts( IXPEntity $ixp ): array {
        $data = [];
        $data['ixpports']            = [];
        $data['ixpports_maxbytes']   = 0;
        $data['infras']              = [];
        $data['infraports']          = [];
        $data['infraports_maxbytes'] = [];
        $data['custs']               = [];
        $data['custports']           = [];
        $data['custlags']            = [];
        $data['sws']                 = [];
        $data['swports']             = [];
        $data['swports_maxbytes']    = [];
        $data['cbs']                 = [];
        $data['cbports']             = [];
        $data['cbbundles']           = [];


        // we need to wrap switch ports in physical interfaces for switch aggregates and, as such, we need to use unused physical interface IDs
        $maxPiID = 0;

        foreach( $ixp->getCustomers() as $c ) {

            foreach( $c->getVirtualInterfaces() as $vi ) {
                /** @var VirtualInterfaceEntity $vi*/
                // we do not include core bundle interfaces here
                if( $vi->getCoreBundle() !== false ) {
                    continue;
                }

                foreach( $vi->getPhysicalInterfaces() as $pi ) {

                    if( $pi->getId() > $maxPiID ) {
                        $maxPiID = $pi->getId();
                    }

                    // we're not multi-ixp in v4 but we'll catch non-relavent ports here
                    if( $pi->getSwitchPort()->getSwitcher()->getInfrastructure()->getIXP()->getId() != $ixp->getId() ) {
                        break 2;
                    }

                    if( !$pi->statusIsConnectedOrQuarantine() || !$pi->getSwitchPort()->getSwitcher()->getActive() ) {
                        continue;
                    }

                    $data['pis'][$pi->getId()] = $pi;

                    if( !isset( $data['custs'][ $c->getId() ] ) ) {
                            $data['custs'][ $c->getId() ] = $c;
                    }

                    if( !isset( $data['sws'][ $pi->getSwitchPort()->getSwitcher()->getId() ] ) ) {
                        $data['sws'][$pi->getSwitchPort()->getSwitcher()->getId() ] = $pi->getSwitchPort()->getSwitcher();
                        $data['swports_maxbytes'][ $pi->getSwitchPort()->getSwitcher()->getId() ] = 0;
                    }

                    if( !isset( $data['infras'][ $pi->getSwitchPort()->getSwitcher()->getInfrastructure()->getId() ] ) ) {
                        $data['infras'][ $pi->getSwitchPort()->getSwitcher()->getInfrastructure()->getId() ] = $pi->getSwitchPort()->getSwitcher()->getInfrastructure();
                        $data['infraports_maxbytes'][ $pi->getSwitchPort()->getSwitcher()->getInfrastructure()->getId() ] = 0;
                    }

                    $data['custports'][$c->getId()][] = $pi->getId();

                    if( count( $vi->getPhysicalInterfaces() ) > 1 ) {
                        $data['custlags'][$c->getId()][$vi->getId()][] = $pi->getId();
                    }

                    $data['swports'][ $pi->getSwitchPort()->getSwitcher()->getId() ][] = $pi->getId();
                    $data['infraports'][ $pi->getSwitchPort()->getSwitcher()->getInfrastructure()->getId() ][] = $pi->getId();
                    $data['ixpports'][] = $pi->getId();

                    $maxbytes = $pi->resolveDetectedSpeed() * 1000000 / 8; // Mbps * bps / to bytes
                    $data['swports_maxbytes'   ][ $pi->getSwitchPort()->getSwitcher()->getId() ] += $maxbytes;
                    $data['infraports_maxbytes'][ $pi->getSwitchPort()->getSwitcher()->getInfrastructure()->getId() ] += $maxbytes;
                    $data['ixpports_maxbytes'] += $maxbytes;
                }
            }
        }

        // include core switch ports.
        // This is a slight hack as the template requires PhysicalInterfaces so we wrap core SwitchPorts in temporary PhyInts.
        $cbseen = [];
        foreach( $ixp->getInfrastructures() as $infra ) {
            foreach( $infra->getSwitchers() as $switch ) {
                /** @var SwitcherEntity $switch */

                if( !$switch->getActive() ) {
                    continue;
                }

                if( !isset( $data['sws'][ $switch->getId() ] ) ) {
                    $data['sws'][$switch->getId() ] = $switch;
                }

                // Handle Core Bundles
                foreach( $switch->getCoreBundles() as $cb ) {
                    // because we iterate through each switch, we see each $cb twice
                    if (isset( $cbseen[ $cb->getId() ] )) {
                        continue;
                    }
                    $cbseen[ $cb->getId() ] = 1;

                    if( !isset( $data['cbs'][ $cb->getId() ] ) ) {
                        $data['cbs'][ $cb->getId() ] = $cb;
                    }

                    foreach( $cb->getCoreLinks() as $cl ) {
                        foreach( array ( 'sidea', 'sideb' ) as $side ) {

                            $pi = ( $side == 'sidea' ) ?
                                $cl->getCoreInterfaceSideA()->getPhysicalInterface() :
                                $cl->getCoreInterfaceSideB()->getPhysicalInterface();
                            $data['cbports'][$cb->getId()][$cl->getId()][$side] = $pi->getId();

                            if( !isset( $data['pis'][$pi->getId()] ) ) {
                                $data['pis'][$pi->getId()] = $pi;
                            }

                            if( $pi->getId() > $maxPiID ) {
                                $maxPiID = $pi->getId();
                            }

                            $data['cbbundles'][$cb->getId()][$side][] = $pi->getId();
                        }
                    }
                }

                foreach( $switch->getPorts() as $sp ) {
                    /** @var SwitchPortEntity $sp */
                    if( $sp->isTypeCore() ) {
                        // this needs to be wrapped in a physical interface for the template
                        $pi = $this->wrapSwitchPortInPhysicalInterface( $sp, ++$maxPiID );
                        $data['pis'][$pi->getId()] = $pi;
                        $data['swports'][$switch->getId()][] = $pi->getId();

                        if( !isset( $data['swports_maxbytes'][$switch->getId()] ) ) {
                            $data['swports_maxbytes'][$switch->getId()] = 0;
                        }

                        $data['swports_maxbytes'][$switch->getId()] += ( ( $pi->resolveDetectedSpeed() > 0 ) ? $pi->resolveDetectedSpeed() : 1 ) * 1000000 / 8;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Wrap a switchport in a temporary PhysicalInterface.
     *
     * @see getPeeringPorts() for usage
     *
     * @param SwitchPortEntity $sp
     * @param int $id The ID to set in the physical interface
     *
     * @return PhysicalInterfaceEntity
     */
    public function wrapSwitchPortInPhysicalInterface( SwitchPortEntity $sp, int $id ): PhysicalInterfaceEntity {
        $pi = new PhysicalInterfaceEntity;
        $pi->setId( $id );
        $pi->setSwitchPort($sp);
        $pi->setSpeed( $sp->getIfHighSpeed() );
        return $pi;
    }

    /**
     * Get a complete list of functionality that this backend supports.
     *
     * {inheritDoc}
     *
     * @return array
     */
    public static function supports(): array {
        $rrd = config('grapher.backends.mrtg.dbtype') == 'rrd';

        $graphTypes = Graph::TYPES;
        unset( $graphTypes[ Graph::TYPE_RRD ] );

        return [
            'ixp' => [
                'protocols'   => [ Graph::PROTOCOL_ALL => Graph::PROTOCOL_ALL ],
                'categories'  => [ Graph::CATEGORY_BITS => Graph::CATEGORY_BITS,
                                    Graph::CATEGORY_PACKETS => Graph::CATEGORY_PACKETS ],
                'periods'     => Graph::PERIODS,
                'types'       => $rrd ? Graph::TYPES : $graphTypes,
            ],
            'infrastructure' => [
                'protocols'   => [ Graph::PROTOCOL_ALL => Graph::PROTOCOL_ALL ],
                'categories'  => [ Graph::CATEGORY_BITS => Graph::CATEGORY_BITS,
                                    Graph::CATEGORY_PACKETS => Graph::CATEGORY_PACKETS ],
                'periods'     => Graph::PERIODS,
                'types'       => $rrd ? Graph::TYPES : $graphTypes,
            ],
            'switcher' => [
                'protocols'   => [ Graph::PROTOCOL_ALL => Graph::PROTOCOL_ALL ],
                'categories'  => [ Graph::CATEGORY_BITS => Graph::CATEGORY_BITS,
                                    Graph::CATEGORY_PACKETS => Graph::CATEGORY_PACKETS ],
                'periods'     => Graph::PERIODS,
                'types'       => $rrd ? Graph::TYPES : $graphTypes,
            ],
            'trunk' => [
                'protocols'   => [ Graph::PROTOCOL_ALL => Graph::PROTOCOL_ALL ],
                'categories'  => [ Graph::CATEGORY_BITS => Graph::CATEGORY_BITS ],
                'periods'     => Graph::PERIODS,
                'types'       => $rrd ? Graph::TYPES : $graphTypes,
            ],
            'physicalinterface' => [
                'protocols'   => [ Graph::PROTOCOL_ALL => Graph::PROTOCOL_ALL ],
                'categories'  => Graph::CATEGORIES,
                'periods'     => Graph::PERIODS,
                'types'       => $rrd ? Graph::TYPES : $graphTypes,
            ],
            'virtualinterface' => [
                'protocols'   => [ Graph::PROTOCOL_ALL => Graph::PROTOCOL_ALL ],
                'categories'  => Graph::CATEGORIES,
                'periods'     => Graph::PERIODS,
                'types'       => $rrd ? Graph::TYPES : $graphTypes,
            ],
            'customer' => [
                'protocols'   => [ Graph::PROTOCOL_ALL => Graph::PROTOCOL_ALL ],
                'categories'  => Graph::CATEGORIES,
                'periods'     => Graph::PERIODS,
                'types'       => $rrd ? Graph::TYPES : $graphTypes,
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
    public function data( Graph $graph ): array {
        try {
            if( config('grapher.backends.mrtg.dbtype') == 'log' ) {
                $mrtg = new MrtgFile( $this->resolveFilePath( $graph, 'log' ) );
                return $mrtg->data( $graph );
            } else {
                $rrd = new RrdUtil( $this->resolveFilePath( $graph, 'rrd' ), $graph );
                return $rrd->data();
            }
        } catch( FileErrorException $e ) {
            Log::notice("[Grapher] {$this->name()} data(): could not load file {$this->resolveFilePath( $graph, config('grapher.backends.mrtg.dbtype') )}");
            return [];
        }
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
    public function png( Graph $graph ): string {

        try {
            if( config('grapher.backends.mrtg.dbtype') == 'log' ) {
                if( ( $img = @file_get_contents( $this->resolveFilePath( $graph, 'png' ) ) ) === false ) {
                    // couldn't load the image so return a placeholder
                    Log::notice( "[Grapher] {$this->name()} png(): could not load file {$this->resolveFilePath( $graph, 'png' )}" );
                    return @file_get_contents( public_path() . "/images/image-missing.png" );
                }
                return $img;
            } else {
                $rrd = new RrdUtil( $this->resolveFilePath( $graph, 'rrd' ), $graph );
                return @file_get_contents( $rrd->png() );
            }
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
    public function rrd( Graph $graph ): string {
        try {
            if( config('grapher.backends.mrtg.dbtype') == 'log' ) {
                return '';
            } else {
                return file_get_contents( $this->resolveFilePath( $graph, 'rrd' ) );
            }
        } catch( FileErrorException $e ) {
            Log::notice("[Grapher] {$this->name()} rrd(): could not load file {$this->resolveFilePath( $graph, 'rrd' )}");
            return '';
        }
    }

    /**
     * Get the path to the graphing data file (e.g. path to log or rrd file).
     *
     * {inheritDoc}
     *
     * @param Graph $graph
     * @return string Path or empty string
     * @throws CannotHandleRequestException
     */
    public function dataPath( Graph $graph ): string {
        try {
            if( config( 'grapher.backends.mrtg.dbtype' ) == 'log' ) {
                return $this->resolveFilePath( $graph, 'log' );
            } else {
                return $this->resolveFilePath( $graph, 'rrd' );
            }
        } catch( CannotHandleRequestException $e ) {
            return '';
        }
    }


    /**
     * For larger IXPs, allow sharding of directories over 16 possible base directories
     *
     * @param int $id The customer entity id
     *
     * @return string shared path -> e.g. 18 -> 18 % 16 = 2 / 00016 -> 2/00016
     */
    private function shardMemberDir( int $id ): string {
        return sprintf( "%x/%05d", $id % 16, $id );
    }

    /**
     * For a given graph, return the path where the appropriate log file
     * will be found.
     *
     * @param Graph $graph
     * @param string $type
     *
     * @return string
     *
     * @throws
     */
    public function resolveFilePath( Graph $graph, string $type ): string {
        $config = config('grapher.backends.mrtg');

        $loggyType = $type == 'rrd' || $type == 'log';

        switch( $graph->classType() ) {
            case 'IXP':
                /** @var Graph\IXP $graph */
                return sprintf( "%s/ixp/ixp%03d-%s%s.%s", $config['logdir'], $graph->ixp()->getId(),
                    $graph->category(), $loggyType ? '' : "-{$graph->period()}", $type );
                break;

            case 'Infrastructure':
                /** @var Graph\Infrastructure $graph */
                return sprintf( "%s/infras/%03d/ixp%03d-infra%03d-%s%s.%s", $config['logdir'],
                    $graph->infrastructure()->getId(), $graph->infrastructure()->getIXP()->getId(),
                    $graph->infrastructure()->getId(), $graph->category(), $loggyType ? '' : "-{$graph->period()}", $type );
                break;

            case 'Switcher':
                /** @var Graph\Switcher $graph */
                return sprintf( "%s/switches/%03d/switch-aggregate-%05d-%s%s.%s", $config['logdir'],
                    $graph->switch()->getId(), $graph->switch()->getId(),
                    $graph->category(), $loggyType ? '' : "-{$graph->period()}", $type );
                break;

            case 'Trunk':
                /** @var Graph\Trunk $graph */
                return sprintf( "%s/trunks/%s%s.%s", $config['logdir'], $graph->trunkname(),
                    $loggyType ? '' : "-{$graph->period()}", $type );
                break;

            case 'PhysicalInterface':
                /** @var Graph\PhysicalInterface $graph */
                return sprintf( "%s/members/%s/ints/%s-%s%s.%s", $config['logdir'],
                    $this->shardMemberDir( $graph->physicalInterface()->getVirtualInterface()->getCustomer()->getId() ),
                    $graph->identifier(), $graph->category(),
                    $loggyType ? '' : "-{$graph->period()}", $type );
                break;

            case 'VirtualInterface':
                /** @var Graph\VirtualInterface $graph */
                return sprintf( "%s/members/%s/lags/%s-%s%s.%s", $config['logdir'],
                    $this->shardMemberDir( $graph->virtualInterface()->getCustomer()->getId() ),
                    $graph->identifier(), $graph->category(),
                    $loggyType ? '' : "-{$graph->period()}", $type );
                break;

            case 'Customer':
                /** @var Graph\Customer $graph */
                return sprintf( "%s/members/%s/%s-%s%s.%s", $config['logdir'],
                    $this->shardMemberDir( $graph->customer()->getId() ),
                    $graph->identifier(), $graph->category(),
                    $loggyType ? '' : "-{$graph->period()}", $type );
                break;


            default:
                throw new CannotHandleRequestException("Backend asserted it could process but cannot handle graph of type: {$graph->type()}" );
        }
    }
}
