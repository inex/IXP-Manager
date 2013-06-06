<?php

/*
 * Copyright (C) 2009-2013 Internet Neutral Exchange Association Limited.
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
 * @copyright  Copyright (c) 2009 - 2013, Internet Neutral Exchange Association Ltd
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
    
        return true;
    }
    
    
}

