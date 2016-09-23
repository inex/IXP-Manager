<?php

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


/**
 * A trait to set the IP address of a VlanInterface
 *
 * @author     Nerijus Barauskas <nerijus@opensolutions.ie>
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller_Trait
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
trait IXP_Controller_Trait_Interfaces
{

    /**
     * Sets IPv4 or IPv6 from form to given VlanInterface.
     *
     * Function checks if IPvX address is provided if IPvX is enabled. Then
     * it checks if given IPvX address exists for current Vlan:
     *
     * * if it exists, it ensures is is not assigned to another interface;
     * * if !exists, creates a new one.
     *
     * @param IXP_Form                     $form Form to get IP address data (one of _Interface_AddWizard or _Interface_Vlan)
     * @param \Entities\VirtualInterface   $vi   Virtual interface to check if IP is not assign to different
     * @param \Entities\VlanInterface      $vli  Vlan interface to assign IP to
     * @param bool                         $ipv6 Bool to define if IP address is IPv4 or IPv6
     * @return bool
     */
    private function setIp( $form, $vi, $vli, $ipv6 = false )
    {
        $iptype = $ipv6 ? "ipv6" : "ipv4";
        $ipVer  = $ipv6 ? "IPv6" : "IPv4";
        $setter = "set{$ipVer}Address";
        $getter = "get{$ipVer}Address";
        $entity = "\\Entities\\{$ipVer}Address";
        $vlan = $vli->getVlan();

        if( $form->getValue( $iptype . "enabled" ) )
        {
            if( !$form->getElement( $iptype . 'addressid' )->getValue() )
            {
                $form->getElement( $iptype . 'addressid' )->setErrors( ["Please select or enter an {$ipVer} address."] );
                return false;
            }

            $ip = $this->getD2EM()->getRepository( $entity )->findOneBy(
                    [ "Vlan" => $vlan->getId(), 'address' => $form->getElement( $iptype . 'addressid' )->getValue() ]
            );

            if( !$ip )
            {
                $ip = new $entity();
                $this->getD2EM()->persist( $ip );
                $ip->setVlan( $vlan );
                $ip->setAddress( $form->getElement( $iptype . 'addressid' )->getValue() );
            }
            else if( $ip->getVlanInterface() && $ip->getVlanInterface() != $vli )
            {
                $address = $form->getElement( $iptype . 'addressid' )->getValue();
                $form->getElement( $iptype . 'addressid' )->setErrors( ["{$ipVer} address '{$address}' is already in use." ] );
                return false;
            }

            $vli->$setter( $ip );
        }
        else
        {
            // ipvX has been disabled - see if we need to remove an assigned IP address
            if( !$form->getElement( $iptype . 'addressid' )->getValue() && $vli->$getter() )
            {
                // need to unset the IP address
                $vli->$setter( null );
            }
        }

        return true;
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
     * @param IXP_Form                     $form Form to get fanout data (one of _Interface_AddWizard or _Interface_Physical)
     * @param \Entities\PhysicalInterface  $pi   Peering physical interface to related with fanout physical interface (port).
     * @param \Entities\VirtualInterface   $vi   Virtual interface of peering physical intreface
     * @return bool
     */
    protected function processFanoutPhysicalInterface( $form, $pi, $vi )
    {
        if( !$form->getValue( 'fanout' ) )
        {
            if( $pi->getRelatedInterface() )
            {
                // if *link with fanout port* is unchecked and the physical interace
                // has a related fanout interface, we need to remove it
                $this->removeRelatedInterface( $pi );
                $pi->setFanoutPhysicalInterface( null );
            }

            return true;
        }

        // from here on, we assume $form->getValue( 'fanout' ) is true

        if( !$form->getValue( 'fn_switchid' ) )
        {
            $form->getElement( 'fn_switchid' )->setErrorMessages( ['Please select a switch'] )->markAsError();
            $form->getElement( 'fn_switchportid' )->setErrorMessages( ['Please select a switchport'] )->markAsError();
            return false;
        }

        if( !$form->getValue( 'fn_switchportid' ) )
        {
            $form->getElement( 'fn_switchportid' )->setErrorMessages( ['Please select a switchport'] )->markAsError();
            return false;
        }

        if( $form->getElement( 'fn_monitorindex' ) )
        {
            if( !$form->getValue( 'fn_monitorindex' ) )
            {
                $form->getElement( 'fn_monitorindex' )->setErrorMessages( ['This value can not be empty.'] )->markAsError();
                return false;
            }
            $monitorIdx = $form->getValue( 'fn_monitorindex' );
        }
        else
        {
            $monitorIdx = $this->getD2EM()->getRepository( '\\Entities\\PhysicalInterface' )->getNextMonitorIndex(
                              $vi->getCustomer()->getReseller()
                          );
        }

        $fnsp = $this->getD2R( '\\Entities\\SwitchPort' )->find( $form->getElement( 'fn_switchportid' )->getValue() );
        $fnsp->setType( \Entities\SwitchPort::TYPE_FANOUT );

        // if switchport does not have a physical interface then create one for it
        if( !$fnsp->getPhysicalInterface() )
        {

            $fnphi = new \Entities\PhysicalInterface();
            $fnphi->setSwitchPort( $fnsp );
            $fnsp->setPhysicalInterface( $fnphi );
            $fnphi->setMonitorindex( $monitorIdx );
            $this->getD2EM()->persist( $fnphi );
        }
        else
        {
            $fnphi = $fnsp->getPhysicalInterface();

            // checking if the fanout port already has physical interface and it is not this one
            if( $fnsp->getPhysicalInterface()->getRelatedInterface() && $fnsp->getPhysicalInterface()->getRelatedInterface()->getId() != $pi->getId() )
            {
                $this->addMesssage( "Selected fanout port already has a related physical interface.", OSS_Message::ERROR );
                return false;
            }
        }

        // if the physical interace already has a related physical interface and it's not the same as the fanout physical interface
        if( $pi->getRelatedInterface() &&  $pi->getRelatedInterface()->getId() != $fnphi->getId() )
        {
            // if fanout does not have a virtual interface, relate it with old fanout port virtual interface.
            if( !$fnphi->getVirtualInterface() )
            {
                $pi->getRelatedInterface()->getVirtualInterface()->addPhysicalInterface( $fnphi );
                $fnphi->setVirtualInterface( $pi->getRelatedInterface()->getVirtualInterface() );
            }

            $this->removeRelatedInterface( $pi );
        }
        else if( !$fnphi->getVirtualInterface() )
        {
            // creating virtual interface for fanout physical interface if it doesn't have one
            $fnvi = new \Entities\VirtualInterface();
            $fnvi->setCustomer( $vi->getCustomer()->getReseller() );
            $fnvi->addPhysicalInterface( $fnphi );
            $fnphi->setVirtualInterface( $fnvi );
            $this->getD2EM()->persist( $fnvi );
        }

        $pi->setFanoutPhysicalInterface( $fnphi );
        $fnphi->setPeeringPhysicalInterface( $pi );

        return true;
    }

     /**
     * Removes related interface
     *
     * Removes a related interface and if it only has one physical interface, removes the virtual interface also
     *
     * @param \Entities\PhysicalInterface $pi Physical interface to remove related physical interface.
     * @return void
     */
    private function removeRelatedInterface( $pi )
    {
        $pi->getRelatedInterface()->getSwitchPort()->setPhysicalInterface( null );
        if( count( $pi->getRelatedInterface()->getVirtualInterface()->getPhysicalInterfaces() ) == 1 )
        {
            foreach( $pi->getRelatedInterface()->getVirtualInterface()->getVlanInterfaces() as $fnvi )
                $this->getD2EM()->remove( $fnvi );

            foreach( $pi->getRelatedInterface()->getVirtualInterface()->getMACAddresses() as $mac )
                $this->getD2EM()->remove( $mac );

            $this->getD2EM()->remove( $pi->getRelatedInterface()->getVirtualInterface() );
            $this->getD2EM()->remove( $pi->getRelatedInterface() );
        }
        else
        {
            $this->getD2EM()->remove( $pi->getRelatedInterface() );
        }
    }
}
