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
 * Controller: Nagios CLI Actions
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class NagiosCliController extends IXP_Controller_CliAction
{

    /**
     * Generates a Nagios configuration for supported switches in the database
     */
    public function genSwitchConfigAction()
    {
        $this->view->ixp = $ixp = $this->cliResolveIXP();
        
        // we want a fresh switch list here
        $this->getD2EM()->getRepository( '\\Entities\\Switcher' )->clearCache( true );
        $switches = $this->getD2EM()->getRepository( '\\Entities\\Switcher' )->getAndCache( true );
    
        echo $this->view->render( 'nagios-cli/conf/switch-definitions.phtml' );
    
        $vendors        = [];
        $vendor_strings = '';
        $all            = [];
    
        foreach( $switches as $s )
        {
            $this->view->sw = $s;
            echo $this->view->render( 'nagios-cli/conf/switch-hosts.phtml' );

            $vendors[ $s->getVendor()->getShortname() ][] = $s;
            
            if( !isset( $vendor_strings[ $s->getVendor()->getShortname() ] ) )
                $vendor_strings[ $s->getVendor()->getShortname() ] = '';
            
            $vendor_strings[ $s->getVendor()->getShortname() ]
                .= ( strlen( $vendor_strings[ $s->getVendor()->getShortname() ] ) ? ', ' : '' ) . $s->getName();
            
            $all[] = $s->getName();
    
            if( isset( $locations[ $s->getCabinet()->getLocation()->getShortname() ] ) )
                $locations[ $s->getCabinet()->getLocation()->getShortname() ] .= ", " . $s->getName();
            else
                $locations[ $s->getCabinet()->getLocation()->getShortname() ] = $s->getName();
        }
    
        $this->view->locations = $locations;
    
        $this->view->vendors = $vendors;
        $this->view->vendor_strings = $vendor_strings;
        $this->view->all = implode( ', ', $all );
        
        echo $this->view->render( 'nagios-cli/conf/switch-templates.phtml' );
    }
    
    public function genMemberConfigAction()
    {
        $this->view->ixp = $ixp = $this->cliResolveIXP();
        
        $this->genMemberConf_getVlanInterfaces( $ixp );
        
        //print_r( $this->getD2R( '\\Entities\\VlanInterface' )->getForIXP( $ixp ) ); die();
        
        echo $this->view->render( 'nagios-cli/conf/members.cfg' );
    }
    
    /**
     * Utility function to slurp all peering VLAN interfaces ports from the database and
     * arrange them in arrays for use by the Nagios config templates
     *
     * @param \Entities\IXP $ixp
     */
    private function genMemberConf_getVlanInterfaces( $ixp )
    {
        $custs     = [];
        $switches  = [];
        $cabinets  = [];
        $locations = [];
        $ipv4hosts = [];
        $ipv6hosts = [];
        
        foreach( $ixp->getCustomers() as $c )
        {
            if( !$c->isTypeFull() || $c->hasLeft() || $c->getStatus() != \Entities\Customer::STATUS_NORMAL )
                continue;

            $custs[ $c->getId() ]['shortname'] = $c->getShortname();
            $custs[ $c->getId() ]['name']      = $c->getAbbreviatedName();
            
            $custs[ $c->getId() ]['hostnames'] = [];
            $custs[ $c->getId() ]['vints']     = [];
            
            foreach( $c->getVirtualInterfaces() as $vi )
            {
                // make sure we have a phsyical connection
                $haveConnection = false;
                
                foreach( $vi->getPhysicalInterfaces() as $pi )
                {
                    if( $pi->getStatus() == \Entities\PhysicalInterface::STATUS_CONNECTED )
                        $haveConnection = true;
                }
                
                if( !$haveConnection )
                    continue;
                
                foreach( $vi->getVlanInterfaces() as $vli )
                {
                    if( $vli->getVlan()->getPrivate() )
                        continue;
                    
                    foreach( [ 'v4', 'v6' ] as $proto )
                    {
                        $getIpEnabled = "getIp{$proto}enabled";
                        $getIpCanping = "getIp{$proto}canping";
                        $getIpAddress = "getIP{$proto}Address";
                        $getIpMonBGP  = "getIp{$proto}monitorrcbgp";
                         
                        if( $vli->$getIpEnabled() && $vli->$getIpCanping() )
                        {
                            if( !$vli->$getIpAddress() )
                                continue;
                            
                            $hn = "{$c->getShortname()}-ip{$proto}-vlan{$vli->getVlan()->getNumber()}-{$vli->getId()}";
                            $custs[ $c->getId() ]['hostnames'][] = $hn;
                            
                            $custs[ $c->getId() ]['vints'][ $vli->getId() ][$proto]['hostname'] = $hn;
                            $custs[ $c->getId() ]['vints'][ $vli->getId() ][$proto]['address']  = $vli->$getIpAddress()->getAddress();
                            $custs[ $c->getId() ]['vints'][ $vli->getId() ][$proto]['canping']  = $vli->$getIpCanping();
                            $custs[ $c->getId() ]['vints'][ $vli->getId() ][$proto]['monrc']    = $vli->$getIpMonBGP();
                            $custs[ $c->getId() ]['vints'][ $vli->getId() ][$proto]['vlan']     = $vli->getVlan()->getName();
                            $custs[ $c->getId() ]['vints'][ $vli->getId() ][$proto]['busyhost'] = $vli->getBusyhost();
                            
                            $pi = $vi->getPhysicalInterfaces()[0];
                            $sw = $pi->getSwitchPort()->getSwitcher();
                            
                            if( !isset( $switches[ $sw->getName() ] ) || !in_array( $hn, $switches[ $sw->getName() ] ) )
                                $switches[ $sw->getName() ][] = $hn;
                            
                            if( !isset( $cabinets[ $sw->getCabinet()->getName() ] ) || !in_array( $hn, $cabinets[ $sw->getCabinet()->getName() ] ) )
                                $cabinets[ $sw->getCabinet()->getName() ][] = $hn;
                            
                            if( !isset( $locations[ $sw->getCabinet()->getLocation()->getShortname() ] ) || !in_array( $hn, $locations[ $sw->getCabinet()->getLocation()->getShortname() ] ) )
                                $locations[ $sw->getCabinet()->getLocation()->getShortname() ][] = $hn;
                            
                            if( $proto == 'v4' )
                                $ipv4hosts[] = $hn;
                            else
                                $ipv6hosts[] = $hn;
                            
                            if( !isset( $custs[ $c->getId() ]['vints'][ $vli->getId() ]['phys'] ) )
                            {
                                $custs[ $c->getId() ]['vints'][ $vli->getId() ]['phys']['switch'] = $sw->getName();
                                $custs[ $c->getId() ]['vints'][ $vli->getId() ]['phys']['port']   = $pi->getSwitchPort()->getName();
                                $custs[ $c->getId() ]['vints'][ $vli->getId() ]['phys']['lag']    = count( $vi->getPhysicalInterfaces() ) - 1;
                            }
                        }
                    
                    }
                }
            }
        }

        $this->view->custs     = $custs;
        $this->view->switches  = $switches;
        $this->view->cabinets  = $cabinets;
        $this->view->locations = $locations;
        $this->view->ipv4hosts = $ipv4hosts;
        $this->view->ipv6hosts = $ipv6hosts;
    }
    
}

