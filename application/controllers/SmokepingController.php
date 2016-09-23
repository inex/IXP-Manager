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
 * Controller: Smokeping / graphs
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SmokepingController extends IXP_Controller_AuthRequiredAction
{
    
    use IXP_Controller_Trait_Statistics;
    
    public static $PERIODS = [
        '3hours'  => 'Last 3 Hours',
        '30hours' => 'Last 30 Hours',
        '10days'  => 'Last 10 Days',
        '1year'   => 'Last Year'
    ];
    
    public static $PROTOCOLS = [
        'ipv4'  => 'IPv4',
        'ipv6'  => 'IPv6'
    ];
    
    public function memberDrilldownAction()
    {
        $this->view->periods   = self::$PERIODS;
        
        if( !( $viid = $this->getParam( 'vi', false ) ) )
            $this->redirect();
        
        if( !( $vi = $this->getD2R( '\\Entities\\VirtualInterface' )->find( $viid ) ) )
            $this->redirect();
        
        if( $this->getUser()->getPrivs() != \Entities\User::AUTH_SUPERUSER && $this->getCustomer()->getId() != $vi->getCustomer()->getId() )
        {
            $this->getLogger()->alert( "{$this->getUser()->getUsername()} tried to access Smokeping graphs of {$vi->getCustomer()->getName()}" );
            $this->redirectAndEnsureDie( 'error/insufficient-permissions' );
        }
        
        if( ( $vlanid = $this->getParam( 'vlanid', false ) ) )
        {
            foreach( $vi->getVlanInterfaces() as $vli )
            {
                if( $vli->getVLAN()->getId() == $vlanid && !$vli->getVLAN()->getPrivate() )
                {
                    $vlan = $vli->getVLAN();
                    break;
                }
            }
        }

        if( !isset( $vlan ) )
        {
            $vli = $vi->getVlanInterfaces()[0];
            $vlan = $this->view->vlan   = $vli->getVLAN();
        }
        
        $vlanid = $this->view->vlanid = $vlan->getId();

        $protos = self::$PROTOCOLS;
        
        foreach( $protos as $p => $n )
        {
            $enabled = 'get' . ucfirst( $p ) . 'enabled';
            $canping = 'get' . ucfirst( $p ) . 'canping';

            if( !$vli->$enabled() || !$vli->$canping() )
                unset( $protos[ $p ] );
        }

        $this->view->cust   = $cust = $vi->getCustomer();
        $this->view->vi     = $vi;
        $this->view->vli    = $vli;
        $this->view->pi     = $vi->getPhysicalInterfaces()[0];
        $this->view->inf    = $vi->getPhysicalInterfaces()[0]->getSwitchPort()->getSwitcher()->getInfrastructure();
        $this->view->ixp    = $ixp = $vi->getPhysicalInterfaces()[0]->getSwitchPort()->getSwitcher()->getInfrastructure()->getIXP();
        
        $proto = $this->getParam( 'proto' );
        
        if( count( $protos ) )
        {
            if( !in_array( $proto, array_keys( $protos ) ) )
                $proto = array_keys( $protos )[0];
            
            $ipfn = 'get' . $protos[ $proto ] . 'Address';
            $this->view->ip     = $vli->$ipfn()->getAddress();
        }
        else
        {
            $this->addMessage( 'This customer does not have pinging enabled for any IP address(es) on the requested interface', OSS_Message::INFO );
            $this->redirect( "statistics/member/ixp/{$ixp->getId()}/shortname/{$cust->getShortname()}" );
        }
        
        $this->view->protos = $protos;
        $this->view->proto  = $proto;
        
        // sanity check
        if( count( $protos ) == 0
                || !( $vi instanceof \Entities\VirtualInterface )
                || !( $vli instanceof \Entities\VlanInterface )
        )
            $this->redirect( "statistics/member/ixp/{$ixp->getId()}/shortname/{$cust->getShortname()}" );
    }
    
    
    function retrieveImageAction()
    {
        // there's no HTML output from this controller - just images
        Zend_Controller_Action_HelperBroker::removeHelper( 'viewRenderer' );
        
        header( 'Content-Type: image/png' );
        header( 'Expires: Thu, 01 Jan 1970 00:00:00 GMT' );
        
        $ixp = $this->loadIxpById( $this->getParam( 'ixp', false ) );
        
        $scale = $this->getParam( 'scale', array_keys( self::$PERIODS )[0] );
        if( !in_array( $scale, array_keys( self::$PERIODS ) ) )
            $scale = array_keys( self::$PERIODS )[0];
        
        $infra = intval( $this->getParam( 'infra', 0 ) );
        if( !is_numeric( $infra ) )
            die( 'ERR: Bad infrastructure' );

        $vlan = intval( $this->getParam( 'vlan', 0 ) );
        if( !is_numeric( $vlan ) )
            die( 'ERR: Bad VLAN' );
        
        $vlanint = intval( $this->getParam( 'vlanint', 0 ) );
        if( !is_numeric( $vlanint ) )
            die( 'ERR: Bad VLAN interface' );

        if( !( $vli = $this->getD2R( '\\Entities\\VlanInterface' )->find( $vlanint ) ) )
            die( 'ERR: Very bad VLAN interface' );
        
        if( $this->getUser()->getPrivs() != \Entities\User::AUTH_SUPERUSER
                && $this->getCustomer()->getId() != $vli->getVirtualInterface()->getCustomer()->getId() )
        {
            $this->getLogger()->alert( "{$this->getUser()->getUsername()} tried to access Smokeping graphs of {$vli->getVirtualInterface()->getCustomer()->getName()}" );
            $this->redirectAndEnsureDie( 'error/insufficient-permissions' );
        }
        
        $proto = $this->getParam( 'proto', array_keys( self::$PROTOCOLS )[0] );
        if( !in_array( $proto, array_keys( self::$PROTOCOLS ) ) )
            $scale = array_keys( self::$PROTOCOLS )[0];
        
        $target = "infra_{$infra}.vlan_{$vlan}.vlanint_{$vlanint}_{$proto}";

        $filename = "{$ixp->getSmokeping()}/?displaymode=a;start=now-{$scale};end=now;target={$target}";
        
        $this->getLogger()->debug( "Serving Smokeping {$target} to {$this->getUser()->getUsername()} from [{$filename}]" );
        
        if( @readfile( $filename ) === false )
        {
            $this->getLogger()->notice( "Could not load Smokeping {$filename}" );
            @readfile(
                    APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                    . 'public' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR
                    . 'image-missing.png'
            );
        }
    }
    
    
}

