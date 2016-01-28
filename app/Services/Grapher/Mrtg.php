<?php namespace IXP\Services\Grapher;

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
class Mrtg implements GrapherContract {

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
        return [
            View::make( 'services.grapher.mrtg.monolithic',
                [
                    'ixp'                   => $ixp,
                    'portsByInfrastructure' => $this->getPeeringPortsByInfrastructure( $ixp ),
                ]
            )->render()
        ];

        // $this->view->TRAFFIC_TYPES         = IXP_Mrtg::$TRAFFIC_TYPES;

        // get all active trafficing customers
        $this->view->custs = $this->getD2R( '\\Entities\\Customer' )->getCurrentActive( false, true, false, $ixp );

        echo $this->view->render( 'statistics-cli/mrtg/index.cfg' );
    }

    /**
     * Utility function to slurp all peering ports from the database and arrange them in
     * arrays by infrastructure and switch for genertaing Mrtg configuration files.
     *
     * The array returned contains the:
     *
     * * calculated maxbytes for the infrastructure and switches
     * * the mrtg IDs for polling for each qualifying port and traffic type
     * * other details such as infrastructure graph names, infra and switch names
     *
     * A 'qualifying port' is any port marked as a peering port.
     *
     * @param Entities\IXP $ixp The IXP to generate the config for (multi-IXP mode)
     * @return array
     */
    private function getPeeringPortsByInfrastructure( IXP $ixp ): array {
        $data = [];

        foreach( $ixp->getInfrastructures() as $infra ) {

            $data[ $infra->getId() ]['mrtgIds']              = [];
            $data[ $infra->getId() ]['name']                 = $infra->getName();
            $data[ $infra->getId() ]['aggregate_graph_name'] = sprintf( 'ixp%03d-infra%03d', $ixp->getId(), $infra->getId() );
            $data[ $infra->getId() ]['maxbytes']             = 0;
            $data[ $infra->getId() ]['switches']             = '';

            foreach( $infra->getSwitchers() as $switch ) {
                if( $switch->getSwitchtype() != Switcher::TYPE_SWITCH || !$switch->getActive() )
                    continue;

                $data[ $infra->getId() ]['switches'][ $switch->getId() ]             = [];
                $data[ $infra->getId() ]['switches'][ $switch->getId() ]['name']     = $switch->getName();
                $data[ $infra->getId() ]['switches'][ $switch->getId() ]['maxbytes'] = 0;
                $data[ $infra->getId() ]['switches'][ $switch->getId() ]['mrtgIds']  = [];

                foreach( $switch->getPorts() as $port ) {
                    if( $port->getIfName() ) {
                        $snmpId = $port->ifnameToSNMPIdentifier();

                        foreach( MrtgFile::TRAFFIC_TYPES as $type => $vars ) {
                            $id = "{$vars['in']}#{$snmpId}&{$vars['out']}#{$snmpId}:{$switch->getSnmppasswd()}@{$switch->getHostname()}:::::2";

                            if( $port->getType() == SwitchPort::TYPE_PEERING ) {
                                $data[ $infra->getId() ]['mrtgIds'][$type][] = $id;
                                $data[ $infra->getId() ]['switches'][ $switch->getId() ]['mrtgIds'][$type][] = $id;

                                $data[ $infra->getId() ]['switches'][ $switch->getId() ]['maxbytes'] += $port->getIfHighSpeed() * 1000000 / 8;
                                $data[ $infra->getId() ]['maxbytes'] += $port->getIfHighSpeed() * 1000000 / 8; // Mbps * bps / to bytes
                            }
                        }
                    }
                }
            }
        }
        return $data;
    }

}
