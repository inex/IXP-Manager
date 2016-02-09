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
use IXP\Services\Grapher\Graph;

use IXP\Exceptions\Services\Grapher\CannotHandleRequestException;

use Entities\{IXP,Switcher,SwitchPort};
use IXP\Utils\Grapher\Mrtg as MrtgFile;

use View;

/**
 * Grapher Backend -> Mrtg
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (c) 2009 - 2016, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Mrtg implements GrapherBackendContract {

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
    private function getPeeringPorts( IXP $ixp ): array {
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
                        $data['custlags'][$vi->getId()][] = $pi->getId();
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
     * Examines the provided graph object and determines if this backend is able to
     * process the request or not.
     *
     * {inheritDoc}
     *
     * @param IXP\Services\Grapher\Graph $graph
     * @return bool
     */
    public function canProcess( Graph $graph ): bool {
        // The MRTG backend can process almost all graphs - except:

        // no per protocol graphs
        if( $graph->protocol() !== Graph::PROTOCOL_ALL ) {
            return false;
        }

        return true;
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
        $mrtg = new MrtgFile( $this->resolveFilePath( $graph, 'log' ) );
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
        return @file_get_contents( $this->resolveFilePath( $graph, 'png' ) );
    }


    /**
     * For a given graph, return the path where the appropriate log file
     * will be found.
     *
     * @param IXP\Services\Grapher\Graph $graph
     * @return string
     */
    private function resolveFilePath( Graph $graph, $type ): string {
        $config = config('grapher.backends.mrtg');
        $class  = explode( '\\', get_class( $graph ) );
        $gtype  = array_pop( $class );

        switch( $gtype ) {
            case 'IXP':
                return sprintf( "%s/ixp%03d-%s%s.%s", $config['logdir'], $graph->ixp()->getId(),
                    $graph->category(), $type == 'log' ? '' : "-{$graph->period()}", $type );
                break;

            case 'Infrastructure':
                return sprintf( "%s/ixp%03d-infra%03d-%s%s.%s", $config['logdir'], $graph->infrastructure()->getIXP()->getId(),
                    $graph->infrastructure()->getId(), $graph->category(), $type == 'log' ? '' : "-{$graph->period()}", $type );
                break;

            default:
                throw new CannotHandleRequestException("Backend asserted it could process but cannot handle graph of type: {$gtype}" );
        }
    }



}
