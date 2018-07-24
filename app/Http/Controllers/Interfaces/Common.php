<?php

namespace IXP\Http\Controllers\Interfaces;

/*
 * Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee.
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


use D2EM;

use Entities\{
    IPv4Address as IPv4AddressEntity,
    IPv6Address as IPv6AddressEntity,
    PhysicalInterface as PhysicalInterfaceEntity,
    SwitchPort as SwitchPortEntity,
    VirtualInterface as VirtualInterfaceEntity,
    Vlan as VlanEntity,
    VlanInterface as VlanInterfaceEntity
};

use Illuminate\Http\Request;

use IXP\Http\Controllers\Controller;
use IXP\Http\Requests\StoreVirtualInterfaceWizard;

use IXP\Services\Grapher\Graph\VlanInterface;

use IXP\Utils\View\Alert\Alert;
use IXP\Utils\View\Alert\Container as AlertContainer;


/**
 * Common Functions
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Interfaces
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
abstract class Common extends Controller
{

    /**
     * Removes related interface
     *
     * Removes a related interface and if it only has one physical interface, removes the virtual interface also
     *
     * @param \Entities\PhysicalInterface $pi Physical interface to remove related physical interface.
     *
     * @return void
     * @throws \LaravelDoctrine\ORM\Facades\ORMInvalidArgumentException
     */
    public function removeRelatedInterface( $pi ){
        if( $pi->getRelatedInterface() ) {
            /** @var PhysicalInterfaceEntity $pi */
            $pi->getRelatedInterface()->getSwitchPort()->setPhysicalInterface( null );

            if( count( $pi->getRelatedInterface()->getVirtualInterface()->getPhysicalInterfaces() ) == 1 ) {
                foreach( $pi->getRelatedInterface()->getVirtualInterface()->getVlanInterfaces() as $vli ) {
                    /** @var VlanInterfaceEntity $vli */
                    foreach( $vli->getLayer2Addresses() as $l2a ) {
                        D2EM::remove( $l2a );
                    }

                    foreach( $pi->getRelatedInterface()->getVirtualInterface()->getMACAddresses() as $mac ){
                        D2EM::remove( $mac );
                    }

                    D2EM::remove( $vli );
                }

                D2EM::remove( $pi->getRelatedInterface()->getVirtualInterface() );
                D2EM::remove( $pi->getRelatedInterface() );
            } else {
                D2EM::remove( $pi->getRelatedInterface() );
            }
            $pi->setFanoutPhysicalInterface( null );
        }
    }

    /**
     * Links peering and fanout physical interfaces.
     *
     * If *link with fanout port* is checked in the form, then this function:
     *
     * * checks if the necessary fields are set;
     * * loads the selected SwitchPort;
     * * creates/loads this SwitchPort's physical interface;
     * * creates a link between the fanout and peering physical interfaces.
     *
     * If *link with fanout port* is not checked then this function checks
     * if the peering port has a related interface and, if so, removes the relation.
     *
     * @param  Request|StoreVirtualInterfaceWizard $request instance of the current HTTP reques
     * @param \Entities\PhysicalInterface $pi Peering physical interface to related with fanout physical interface (port).
     * @param \Entities\VirtualInterface $vi Virtual interface of peering physical intreface
     * @return boolean
     * @throws \LaravelDoctrine\ORM\Facades\ORMInvalidArgumentException
     */
    public function processFanoutPhysicalInterface( $request, $pi, $vi ) {

        if( !$request->input('fanout' ) ) {
            $this->removeRelatedInterface( $pi );
            return true;
        }

        /** @var SwitchPortEntity $fnsp */
        if( !( $fnsp = D2EM::getRepository( SwitchPortEntity::class )->find( $request->input( 'switch-port-fanout' ) ) ) ) {
            abort( 404, 'Unknown customer' );
        }

        $fnsp->setType( \Entities\SwitchPort::TYPE_FANOUT );

        // if switch port does not have a physical interface then create one
        if( !$fnsp->getPhysicalInterface() ) {
            /** @var PhysicalInterfaceEntity $fnpi */
            $fnpi = new PhysicalInterfaceEntity();
            $fnpi->setSwitchPort( $fnsp );
            $fnsp->setPhysicalInterface( $fnpi );
            D2EM::persist( $fnpi );
        } else {
            $fnpi = $fnsp->getPhysicalInterface();

            // check if the fanout port has a physical interface and if the physical interface is different of the current physical interface
            if( $fnsp->getPhysicalInterface()->getRelatedInterface() && $fnsp->getPhysicalInterface()->getRelatedInterface()->getId() != $pi->getId() ) {
                AlertContainer::push( "Missing bundle name not assigned as no bundle name set for this switch vendor (see Vendors)", Alert::WARNING );
                return false;
            }
        }

        // if the physical interace already has a related physical interface and it's not the same as the fanout physical interface
        if( $pi->getRelatedInterface() && $pi->getRelatedInterface()->getId() != $fnpi->getId() ) {
            // if fanout does not have a virtual interface, relate it with old fanout port virtual interface.
            if( !$fnpi->getVirtualInterface() ) {
                $pi->getRelatedInterface()->getVirtualInterface()->addPhysicalInterface( $fnpi );
                $fnpi->setVirtualInterface( $pi->getRelatedInterface()->getVirtualInterface() );
            }

            $this->removeRelatedInterface( $pi );

        } else if( !$fnpi->getVirtualInterface() ) {
            // create virtual interface for fanout physical interface if doesn't have one
            $fnvi = new VirtualInterfaceEntity();
            $fnvi->setCustomer( $vi->getCustomer()->getReseller() );
            $fnvi->addPhysicalInterface( $fnpi );
            $fnpi->setVirtualInterface( $fnvi );
            D2EM::persist( $fnvi );
        }

        $pi->setFanoutPhysicalInterface( $fnpi );
        $fnpi->setPeeringPhysicalInterface( $pi );

        $fnpi->setSpeed(  $pi->getSpeed()  );
        $fnpi->setStatus( $pi->getStatus() );
        $fnpi->setDuplex( $pi->getDuplex() );

        return true;
    }

    /**
     * When we have >1 phys int / LAG framing, we need to set other elements of the virtual interface appropriately:
     * @param VirtualInterfaceEntity $vi
     * @throws \IXP\Exceptions\GeneralException
     */
    public function setBundleDetails( VirtualInterfaceEntity $vi ){
        if( count( $vi->getPhysicalInterfaces() ) ) {

            // LAGs must have a channel group and bundle name. But only if they have a phys int:
            if( $vi->getLagFraming() && !$vi->getChannelgroup() ) {
                $vi->setChannelgroup( D2EM::getRepository( VirtualInterfaceEntity::class )->assignChannelGroup( $vi ) );
                AlertContainer::push( "Missing channel group assigned as this is a LAG port", Alert::INFO );
            }

            // LAGs must have a bundle name
            if( $vi->getLagFraming() && !$vi->getName() ) {
                // assumption on no mlags (multi chassis lags) here:

                if( $vi->getPhysicalInterfaces()[ 0 ]->getSwitchport()->getSwitcher()->getVendor() ) {
                    $vi->setName( $vi->getPhysicalInterfaces()[ 0 ]->getSwitchport()->getSwitcher()->getVendor()->getBundleName() );
                    AlertContainer::push( "Missing bundle name assigned as this is a LAG port", Alert::INFO );
                } else {
                    AlertContainer::push( "Missing bundle name not assigned as no bundle name set for this switch vendor (see Vendors)", Alert::WARNING );
                }
            }
        }
        else{
            // we don't allow setting channel group or name until there's >= 1 physical interface / LAG framing:
            $vi->setName('');
            $vi->setChannelgroup(null);
            $vi->setLagFraming(false);
            $vi->setFastLACP(false);
        }
    }

    /**
     * Sets IPv4 or IPv6 from form to given VlanInterface from request data.
     *
     * Function checks if IPvX address is provided if IPvX is enabled. Then
     * it checks if given IPvX address exists for current Vlan:
     *
     * NB: form sanity checking must be performed BEFORE calling this function
     *
     * * if it exists, it ensures is is not assigned to another interface;
     * * if !exists, creates a new one.
     *
     * @param Request $request
     * @param VlanEntity $v
     * @param VlanInterfaceEntity $vli Vlan interface to assign IP to
     * @param bool $ipv6 Bool to define if IP address is IPv4 or IPv6
     * @return bool
     * @throws \LaravelDoctrine\ORM\Facades\ORMInvalidArgumentException
     */
    public function setIp( Request $request, VlanEntity $v, VlanInterfaceEntity $vli, bool $ipv6 = false )
    {
        $iptype         = $ipv6 ? "ipv6" : "ipv4";
        $ipVer          = $ipv6 ? "IPv6" : "IPv4";
        $setterIPv      = "set{$ipVer}Address";
        $setterEnabled  = "set{$ipVer}enabled";
        $setterHostname = "set{$ipVer}hostname";
        $setterSecret   = "set{$ipVer}bgpmd5secret";
        $setterPing     = "set{$ipVer}canping";
        $setterMonitor  = "set{$ipVer}monitorrcbgp";

        $entity = $ipv6 ? IPv6AddressEntity::class : IPv4AddressEntity::class;

        $addressValue = $request->input( $iptype . '-address' );

        if( trim( $addressValue ) ) {
            if( !( $ip = D2EM::getRepository( $entity )->findOneBy( [ "Vlan" => $v->getId(), 'address' => $addressValue ] ) ) ) {
                /** @var IPv4AddressEntity|IPv6AddressEntity $ip */
                $ip = new $entity();
                $ip->setVlan( $v );
                $ip->setAddress( $addressValue );
                D2EM::persist( $ip );
            } else if( $ip->getVlanInterface() && $ip->getVlanInterface() != $vli ) {
                AlertContainer::push( "{$ipVer} address {$addressValue} is already in use by another VLAN interface on the same VLAN.", Alert::DANGER );
                return false;
            }

            $vli->$setterIPv( $ip );

        } else {
            $vli->$setterIPv( null );
        }

        $vli->$setterHostname( $request->input( $iptype . '-hostname' ) );
        $vli->$setterEnabled(  $request->input( $iptype . '-enabled', false ) );
        $vli->$setterSecret(   $request->input( $iptype . '-bgp-md5-secret' ) );
        $vli->$setterPing(     $request->input( $iptype . '-can-ping', false ) );
        $vli->$setterMonitor(  $request->input( $iptype . '-monitor-rcbgp', false ) );

        return true;
    }


    public function warnIfIrrdbFilteringButNoIrrdbSourceSet( VlanInterfaceEntity $vli )
    {
        if( $vli->getRsclient() && $vli->getIrrdbfilter() && !$vli->getVirtualInterface()->getCustomer()->getIRRDB() ) {
            AlertContainer::push( "You have enabled IRRDB filtering for this VLAN interface's route server sessions. "
                . "However, the customer does not have an IRRDB source set. As such, the route servers will block all prefix "
                . "advertisements. To rectify this, edit the customer and set an IRRDB source.", Alert::WARNING );
        }
    }



}