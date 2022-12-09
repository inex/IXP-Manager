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

use Log, View;

use IXP\Contracts\Grapher\Backend as GrapherBackendContract;

use IXP\Exceptions\Services\Grapher\CannotHandleRequestException;
use IXP\Exceptions\Utils\Grapher\FileError as FileErrorException;

use IXP\Models\{
    Customer,
    Infrastructure,
    PhysicalInterface,
    SwitchPort
};

use IXP\Services\Grapher\Backend as GrapherBackend;
use IXP\Services\Grapher\Graph;

use IXP\Utils\Grapher\{
    Mrtg as MrtgFile,
    Rrd  as RrdUtil
};

/**
 * Grapher Backend -> Mrtg
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Mrtg extends GrapherBackend implements GrapherBackendContract
{
    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function name(): string
    {
        return 'mrtg';
    }

    /**
     * The dummy backend requires no configuration.
     *
     * {@inheritDoc}
     *
     * @return bool
     */
    public function isConfigurationRequired(): bool
    {
        return true;
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
        return true;
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
        return [
            View::make( 'services.grapher.mrtg.monolithic', [
                    'data'       => $this->getPeeringPorts(),
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
     * * array `['locs']` of Location objects indexed by their ID
     * * array `['infras']` of Infrastructure objects indexed by their ID
     * * array `['custports']` containing an array of PhysicalInterfaceEntity IDs indexed by customer ID
     * * array `['custlags']` containing an array of PhysicalInterfaceEntity IDs contained in an array indexed
     *       by VirtualInterfaceEntity IDs in turn in an array of customer IDs:
     *       `['custlags'][$custid][$viid][]`
     * * array `['swports']` indexed by Switcher ID conataining the PhysicalInterfaceEntity IDs of peering ports
     * * array `['infraports']` indexed by Infrastructure ID conataining the PhysicalInterfaceEntity IDs of peering ports
     * * array `['ixpports']` conataining the PhysicalInterfaceEntity IDs of peering ports
     *
     * @return array
     */
    public function getPeeringPorts(): array
    {
        $data = [];
        $data['ixpports']            = [];
        $data['ixpports_maxbytes']   = 0;
        $data['infras']              = [];
        $data['infraports']          = [];
        $data['infraports_maxbytes'] = [];
        $data['custs']               = [];
        $data['custports']           = [];
        $data['custlags']            = [];
        $data['locs']                = [];
        $data['locports']            = [];
        $data['locports_maxbytes']   = [];
        $data['sws']                 = [];
        $data['swports']             = [];
        $data['swports_maxbytes']    = [];
        $data['cbs']                 = [];
        $data['cbports']             = [];
        $data['cbbundles']           = [];


        // we need to wrap switch ports in physical interfaces for switch aggregates and, as such, we need to use unused physical interface IDs
        $maxPiID = 0;

        foreach( Customer::all() as $c ) {
            foreach( $c->virtualInterfaces as $vi ) {
                // we do not include core bundle interfaces here
                if( $vi->getCoreBundle() !== false ) {
                    continue;
                }

                foreach( $vi->physicalInterfaces as $pi ) {
                    if( $pi->id > $maxPiID ) {
                        $maxPiID = $pi->id;
                    }

                    // per inex/IXP-Manager##746 - added ifIndex check to skip manually added dummy ports
                    if( !$pi->isConnectedOrQuarantine() || !$pi->switchPort->ifIndex || !( $pi->switchPort->switcher->active && $pi->switchPort->switcher->poll ) ) {
                        continue;
                    }

                    $data[ 'pis' ][ $pi->id ] = $pi;

                    if( !isset( $data[ 'custs' ][ $c->id ] ) ) {
                            $data[ 'custs' ][ $c->id ] = $c;
                    }

                    if( !isset( $data['sws'][ $pi->switchPort->switcher->id ] ) ) {
                        $s = $pi->switchPort->switcher;
                        $data['sws'][ $s->id ] = $s;
                        $data['swports_maxbytes'][ $s->id ] = 0;
                    }

                    if( !isset( $data['locs'][ $pi->switchPort->switcher->cabinet->location->id ] ) ) {
                        $l = $pi->switchPort->switcher->cabinet->location;
                        $data['locs'][ $l->id ] = $l;
                        $data['locports_maxbytes'][ $l->id ] = 0;
                    }

                    if( !isset( $data['infras'][ $pi->switchPort->switcher->infrastructureModel->id ] ) ) {
                        $i = $pi->switchPort->switcher->infrastructureModel;
                        $data['infras'][ $i->id ] = $i;
                        $data['infraports_maxbytes'][ $i->id ] = 0;
                    }

                    $data[ 'custports' ][ $c->id ][] = $pi->id;

                    if( $vi->physicalInterfaces->count() > 1 ) {
                        $data[ 'custlags' ][ $c->id ][ $vi->id ][] = $pi->id;
                    }

                    $data['swports'][ $pi->switchPort->switcher->id ][] = $pi->id;
                    $data['locports'][ $pi->switchPort->switcher->cabinet->location->id ][] = $pi->id;
                    $data['infraports'][ $pi->switchPort->switcher->infrastructureModel->id ][] = $pi->id;
                    $data['ixpports'][] = $pi->id;

                    $maxbytes = $pi->detectedSpeed() * 1000000 / 8; // Mbps * bps / to bytes
                    $switcher = $pi->switchPort->switcher;
                    $location = $pi->switchPort->switcher->cabinet->location;
                    $data['swports_maxbytes'   ][ $switcher->id ] += $maxbytes;
                    $data['locports_maxbytes'  ][ $location->id ] += $maxbytes;
                    $data['infraports_maxbytes'][ $switcher->infrastructureModel->id ] += $maxbytes;
                    $data['ixpports_maxbytes'] += $maxbytes;
                }
            }
        }

        // core bundles
        foreach( Infrastructure::all() as $infra ) {
            foreach( $infra->switchers as $switch ) {
                if( !( $switch->active && $switch->poll ) ) {
                    continue;
                }

                if( !isset( $data['sws'][ $switch->id ] ) ) {
                    $data['sws'][$switch->id ] = $switch;
                }

                // Handle Core Bundles
                foreach( $switch->getCoreBundles() as $cb ) {
                    // because we iterate through each switch, we see each $cb twice
                    if( isset( $data['cbs'][ $cb->id ] ) ) {
                        continue;
                    }

                    $data['cbs'][ $cb->id ] = $cb;

                    foreach( $cb->corelinks as $cl ) {
                        foreach( [ 'sidea', 'sideb' ] as $side ) {
                            $pi = ( $side === 'sidea' ) ?
                                $cl->coreinterfacesidea->physicalinterface
                                : $cl->coreinterfacesideb->physicalinterface;

                            $data[ 'cbports' ][ $cb->id ][ $cl->id ][ $side ] = $pi->id;

                            if( !isset( $data[ 'pis' ][ $pi->id ] ) ) {
                                $data[ 'pis' ][ $pi->id ] = $pi;
                            }

                            if( $pi->id > $maxPiID ) {
                                $maxPiID = $pi->id;
                            }

                            $data[ 'cbbundles' ][ $cb->id ][ $side ][] = $pi->id;
                        }
                    }
                }
            }
        }

        // include core switch ports.
        // This is a slight hack as the template requires PhysicalInterfaces so we wrap core SwitchPorts in temporary PhyInts.
        foreach( Infrastructure::all() as $infra ) {
            foreach( $infra->switchers as $switch ) {

                if( !( $switch->active && $switch->poll ) ) {
                    continue;
                }

                foreach( $switch->switchPorts as $sp ) {
                    if( $sp->typeCore() ) {
                        // this needs to be wrapped in a physical interface for the template
                        $pi = $this->wrapSwitchPortInPhysicalInterface( $sp, ++$maxPiID );
                        $data[ 'pis' ][ $pi->id ] = $pi;
                        $data[ 'swports' ][ $switch->id ][] = $pi->id;

                        if( !isset( $data[ 'swports_maxbytes' ][ $switch->id ] ) ) {
                            $data[ 'swports_maxbytes' ][ $switch->id ] = 0;
                        }

                        $data[ 'swports_maxbytes' ][ $switch->id ] += ( ( $pi->detectedSpeed() > 0 ) ? $pi->detectedSpeed() : 1 ) * 1000000 / 8;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Wrap a switch port in a temporary PhysicalInterface.
     *
     * @see getPeeringPorts() for usage
     *
     * @param SwitchPort    $sp
     * @param int           $id The ID to set in the physical interface
     *
     * @return PhysicalInterface
     */
    public function wrapSwitchPortInPhysicalInterface( SwitchPort $sp, int $id ): PhysicalInterface
    {
        $pi                 = new PhysicalInterface;
        $pi->id             = $id;
        $pi->switchportid   = $sp->id;
        $pi->speed          = $sp->ifHighSpeed;
        return $pi;
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
        $rrd = config('grapher.backends.mrtg.dbtype') === 'rrd';

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
            'location' => [
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
            'corebundle' => [
                'protocols'   => [ Graph::PROTOCOL_ALL => Graph::PROTOCOL_ALL ],
                'categories'  => Graph::CATEGORIES,
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
    public function data( Graph $graph ): array
    {
        try {
            if( config('grapher.backends.mrtg.dbtype') === 'log' ) {
                $mrtg = new MrtgFile( $this->resolveFilePath( $graph, 'log' ) );
                return $mrtg->data( $graph );
            }

            $rrd = new RrdUtil( $this->resolveFilePath( $graph, 'rrd' ), $graph );
            return $rrd->data();
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
    public function png( Graph $graph ): string
    {
        try {
            if( config('grapher.backends.mrtg.dbtype') === 'log' ) {
                if( ( $img = @file_get_contents( $this->resolveFilePath( $graph, 'png' ) ) ) === false ) {
                    // couldn't load the image so return a placeholder
                    Log::notice( "[Grapher] {$this->name()} png(): could not load file {$this->resolveFilePath( $graph, 'png' )}" );
                    return @file_get_contents( public_path() . "/images/image-missing.png" );
                }
                return $img;
            }

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
            if( config('grapher.backends.mrtg.dbtype') === 'log' ) {
                return '';
            }
            return file_get_contents( $this->resolveFilePath( $graph, 'rrd' ) );
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
     *
     * @return string Path or empty string
     *
     * @throws
     */
    public function dataPath( Graph $graph ): string
    {
        try {
            if( config( 'grapher.backends.mrtg.dbtype' ) === 'log' ) {
                return $this->resolveFilePath( $graph, 'log' );
            }

            return $this->resolveFilePath( $graph, 'rrd' );
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
    private function shardMemberDir( int $id ): string
    {
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
    public function resolveFilePath( Graph $graph, string $type ): string
    {
        $config     = config('grapher.backends.mrtg');
        $loggyType  = $type === 'rrd' || $type === 'log';

        switch( $graph->classType() ) {
            case 'IXP':
                /** @var Graph\IXP $graph */
                return sprintf( "%s/ixp/ixp%03d-%s%s.%s", $config[ 'logdir' ], 1,
                    $graph->category(), $loggyType ? '' : "-{$graph->period()}", $type );
            case 'Infrastructure':
                /** @var Graph\Infrastructure $graph */
                return sprintf( "%s/infras/%03d/ixp%03d-infra%03d-%s%s.%s", $config['logdir'],
                    $graph->infrastructure()->id, 1,
                    $graph->infrastructure()->id, $graph->category(), $loggyType ? '' : "-{$graph->period()}", $type );
            case 'Location':
                /** @var Graph\Location $graph */
                return sprintf( "%s/locations/%03d/location-aggregate-%05d-%s%s.%s", $config['logdir'],
                    $graph->location()->id, $graph->location()->id,
                    $graph->category(), $loggyType ? '' : "-{$graph->period()}", $type );
            case 'Switcher':
                /** @var Graph\Switcher $graph */
                return sprintf( "%s/switches/%03d/switch-aggregate-%05d-%s%s.%s", $config['logdir'],
                    $graph->switch()->id, $graph->switch()->id,
                    $graph->category(), $loggyType ? '' : "-{$graph->period()}", $type );
            case 'Trunk':
                /** @var Graph\Trunk $graph */
                return sprintf( "%s/trunks/%s%s.%s", $config['logdir'], $graph->trunkname(),
                    $loggyType ? '' : "-{$graph->period()}", $type );
            case 'CoreBundle':
                /** @var Graph\CoreBundle $graph */
                return sprintf( "%s/corebundles/%05d/%s-%s%s.%s", $config['logdir'],
                    $graph->coreBundle()->id,
                    $graph->identifier(), $graph->category(),
                    $loggyType ? '' : "-{$graph->period()}", $type );
            case 'PhysicalInterface':
                /** @var Graph\PhysicalInterface $graph */
                return sprintf( "%s/members/%s/ints/%s-%s%s.%s", $config['logdir'],
                    $this->shardMemberDir( $graph->physicalInterface()->virtualInterface->customer->id ),
                    $graph->identifier(), $graph->category(),
                    $loggyType ? '' : "-{$graph->period()}", $type );
            case 'VirtualInterface':
                /** @var Graph\VirtualInterface $graph */
                return sprintf( "%s/members/%s/lags/%s-%s%s.%s", $config['logdir'],
                    $this->shardMemberDir( $graph->virtualInterface()->customer->id ),
                    $graph->identifier(), $graph->category(),
                    $loggyType ? '' : "-{$graph->period()}", $type );
            case 'Customer':
                /** @var Graph\Customer $graph */
                return sprintf( "%s/members/%s/%s-%s%s.%s", $config['logdir'],
                    $this->shardMemberDir( $graph->customer()->id ),
                    $graph->identifier(), $graph->category(),
                    $loggyType ? '' : "-{$graph->period()}", $type );
            default:
                throw new CannotHandleRequestException("Backend asserted it could process but cannot handle graph of type: {$graph->type()}" );
        }
    }
}