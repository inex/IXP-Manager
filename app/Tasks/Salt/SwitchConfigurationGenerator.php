<?php

declare(strict_types=1);
namespace IXP\Tasks\Salt;

/*
 * Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Entities\Switcher as SwitchEntity;
use Entities\VirtualInterface as VirtualInterfaceEntity;
use Entities\PhysicalInterface as PhysicalInterfaceEntity;

use Illuminate\Contracts\View\View as ViewContract;

use IXP\Exceptions\GeneralException;

/**
 * ConfigurationGenerator
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Tasks
 * @package    IXP\Tasks\Router
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SwitchConfigurationGenerator
{
    /**
     *
     * @var SwitchEntity
     */
    private $switch = null;

    public function __construct( SwitchEntity $switch ) {
        $this->setSwitch( $switch );
    }

    /**
     * Set the switch
     *
     * @param SwitchEntity $switch
     * @return SwitchConfigurationGenerator
     */
    public function setSwitch( SwitchEntity $switch ): SwitchConfigurationGenerator {
        $this->switch = $switch;
        return $this;
    }

    /**
     * Get the switch options array
     *
     * @return SwitchEntity
     */
    public function getSwitch(): SwitchEntity {
        return $this->switch;
    }

    private function template(): string {
        $tmpl = preg_replace( '/[^\da-z_\-\/]/i', '', strtolower( 'api/v4/provisioner/salt/switch/' . $this->getSwitch()->getVendor()->getShortname() ) );

        if( !view()->exists( $tmpl ) ) {
            throw new GeneralException( "Template does not exist: " . $tmpl );
        }

        return $tmpl;
    }

    /**
     * Generate and return the configuration
     *
     * @throws GeneralException
     * @return ViewContract The configuration
     */
    public function render(): ViewContract
    {

        $ports = [];
        $visProcessed = [];
        foreach( $this->getSwitch()->getPorts() as $sp ) {

            /** @var \Entities\SwitchPort $sp */
            if( !$sp->isTypePeering() ) {
                continue;
            }

            // is the port in use?
            if( !($pi = $sp->getPhysicalInterface()) ) {
                continue;
            }

            if( !in_array($pi->getVirtualInterface()->getId(), $visProcessed) ) {
                $ports = array_merge($ports, $this->processVirtualInterface($pi->getVirtualInterface()));
                $visProcessed[] = $pi->getVirtualInterface()->getId();
            }
        }

        return view($this->template())->with(
            [ 'ports' => $ports ]
        );
    }

    private function processVirtualInterface( VirtualInterfaceEntity $vi ): array {

        $p                       = [];
        $p['description']        = "Cust: {$vi->getCustomer()->getAbbreviatedName()}";
        $p['dot1q']              = $vi->getTrunk() ? 'yes' : 'no';
        $p['virtualinterfaceid'] = $vi->getId();
        if( $vi->getChannelgroup() ) {
            $p['lagindex'] = $vi->getChannelgroup();
        }

        $p['vlans'] = [];
        foreach( $vi->getVlanInterfaces() as $vli ) {
            /** @var \Entities\VlanInterface $vli */
            $v = [];
            $v['number'] = $vli->getVlan()->getNumber();
            $v['macaddresses'] = [];
            foreach( $vi->getMACAddresses() as $mac ) {
                $v['macaddresses'][] = $mac->getMacFormattedWithColons();
            }
            $p['vlans'][] = $v;
        }

        // we now have the base port config. If this is not a LAG, just return it:
        if( !$vi->getLagFraming() ) {
            $pi = $vi->getPhysicalInterfaces()[0];
            $p['name']               = $pi->getSwitchport()->getIfName();
            $p['speed']              = $pi->getSpeed();
            return [ $p ];
        }

        $ports = [];

        // bundle definition:
        $p['name']      = $vi->getBundleName();
        $p['lagmaster'] = 'yes';
        $ports[]        = $p;

        // interface definitions:
        foreach( $vi->getPhysicalInterfaces() as $pi ) {
            /** @var PhysicalInterfaceEntity $pi */
            if( !$pi->getSwitchPort() ) {
                continue;
            }
            $p['name']      = $pi->getSwitchPort()->getIfName();
            $p['lagmaster'] = 'no';
            $p['speed']     = $pi->getSpeed();
            $ports[] = $p;
        }

        return $ports;
    }


}
