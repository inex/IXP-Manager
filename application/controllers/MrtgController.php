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
 * Controller: Retrive MRTG images
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (c) 2009 - 2013, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class MrtgController extends IXP_Controller_AuthRequiredAction
{
    protected $_flock = null;
    
    /**
     * The requested IXP
     * @var \Entities\IXP
     */
    protected $ixp = null;
    
    public function preDispatch()
    {
        // there's no HTML output from this controller - just images
        Zend_Controller_Action_HelperBroker::removeHelper( 'viewRenderer' );
        
        header( 'Content-Type: image/png' );
        header( 'Expires: Thu, 01 Jan 1970 00:00:00 GMT' );
        
        if( $this->multiIXP() && !$this->getParam( 'ixp', false ) )
                $this->errorAction();
        else if( !$this->multiIXP() )
            $this->setParam( 'ixp', 1 );
        
        if( !( $this->ixp = $this->loadIxpById( $this->getParam( 'ixp' ), false ) ) )
            $this->errorAction();
    }

    private function checkShortname( $shortname )
    {
        return $this->getD2R( '\\Entities\\Customer' )->findOneBy( [ 'shortname' => $shortname ] );
    }

    function errorAction()
    {
        @readfile(
                APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                . 'public' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR
                . 'image-missing.png'
        );
        die();
    }
    
    function retrieveImageAction()
    {
        $monitorindex = $this->getParam( 'monitorindex', 'aggregate' );
        $period       = $this->getParam( 'period', IXP_Mrtg::$PERIODS['Day'] );
        $shortname    = $this->getParam( 'shortname' );
        $category     = $this->getParam( 'category', IXP_Mrtg::$CATEGORIES['Bits'] );
        $graph        = $this->getParam( 'graph', '' );

        $this->getLogger()->debug( "Request for {$shortname}-{$monitorindex}-{$category}-{$period}-{$graph} by {$this->getUser()->getUsername()}" );

        if( $shortname == 'X_Trunks' )
        {
            $filename = $this->ixp->getMrtgPath()
                . '/trunks/' . $graph . '-' . $period . '.png';
        }
        else if( $shortname == 'X_SwitchAggregate' )
        {
            $filename = $this->ixp->getMrtgPath()
                . '/switches/switch-aggregate-' . $graph . '-'
                . $category . '-' . $period . '.png';
        }
        else if( $shortname == 'X_Peering' )
        {
            $filename = $this->ixp->getMrtgPath()
                . '/ixp_peering-' . $graph . '-'
                . $category . '-' . $period . '.png';
        }
        else
        {
            if( $this->getUser()->getPrivs() != \Entities\User::AUTH_SUPERUSER || !$this->checkShortname( $shortname ) )
                $shortname = $this->getCustomer()->getShortname();

            $filename = IXP_Mrtg::getMrtgFilePath( $this->ixp->getMrtgPath() . '/members'    , 'PNG',
                $monitorindex, $category, $shortname, $period
            );
        }

        $this->getLogger()->debug( "Serving {$filename} to {$this->getUser()->getUsername()}" );

        if( @readfile( $filename ) === false )
        {
            $this->getLogger()->notice( "Could not load {$filename} for mrtg/retrieveImageAction" );
            @readfile(
                APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                    . 'public' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR
                    . 'image-missing.png'
            );
        }
    }


    function retrieveP2pImageAction()
    {
        $period       = $this->getParam( 'period',    IXP_Mrtg::$PERIODS['Day'] );
        $shortname    = $this->getParam( 'shortname', false );
        $svli         = $this->getParam( 'svli',      false );
        $dvli         = $this->getParam( 'dvli',      false );
        $category     = $this->getParam( 'category',  IXP_Mrtg::$CATEGORIES['Bits'] );
        $proto        = $this->getParam( 'proto',     IXP_Mrtg::PROTOCOL_IPV4 );
        $infra        = $this->getParam( 'infra',     false );
        $period       = $this->getParam( 'period',    IXP_Mrtg::PERIOD_DAY );
        
        if( $infra === false )
        {
            // we default to the primary infrastructure
            $infra = $this->getD2R( '\\Entities\\Infrastructure' )->getPrimary( $this->ixp );
        }
        else
        {
            if( !( $infra = $this->getD2R( '\\Entities\\Infrastructure' )->find( $infra ) ) || $infra->getIXP()->getId() != $this->ixp->getId() )
                exit( 0 );
        }
        
        $_cust = $this->checkShortname( $shortname );
        
        if( $this->getUser()->getPrivs() != \Entities\User::AUTH_SUPERUSER || !$_cust )
        {
            $shortname = $this->getUser()->getCustomer()->getShortname();
            $_cust = $this->getUser()->getCustomer();
        }
        
        // make sure the svid and dvid is valid
        if( !$svli || !$dvli )
        {
            $this->getLogger()->alert( "P2P file request with svid={$svli} and dvid={$dvli}" );
            die();
        }
        
        $svliOk = false;
        foreach( $_cust->getVirtualInterfaces() as $vint )
        {
            foreach( $vint->getVlanInterfaces() as $vlint )
            {
                if( $vlint->getId() == $svli )
                {
                    $svliOk = true;
                    break 2;
                }
            }
        }

        // make sure the svid and dvid is valid
        if( !$svliOk )
        {
            $this->getLogger()->alert( "P2P file request with illegal svli={$svli} for {$shortname}" );
            die();
        }
        
        // find the possible virtual interfaces that this customer peers with
        $dvliOk = false;
        $dshortname = '';
        
        $customersWithVirtualInterfaces = $this->getD2R( '\\Entities\\VlanInterface' )->getForInfrastructure( $infra, $proto );
        
        foreach( $customersWithVirtualInterfaces as $c )
        {
            if( $c['cshortname'] == $shortname )
                continue;
                
            if( $c['vlanid'] == $dvli )
            {
                $dshortname = $c['cshortname'];
                $dvliOk = true;
                break;
            }
        }
        
        // make sure the svid and dvid is valid
        if( !$dvliOk )
        {
            $this->getLogger()->alert( "P2P file request with illegal pvid={$dvli} for {$shortname}" );
            die();
        }
        
        $filename = IXP_Mrtg::getMrtgP2pFilePath( $this->ixp->getMrtgP2pPath(),
            $svli, $dvli, $category, $period, $proto
        );
        
        $this->getLogger()->debug( "Serving $filename to {$this->getUser()->getUsername()}" );

        $this->getLogger()->info( "P2P request for {$shortname}-{$dshortname}-{$category}-{$period}-ipv{$proto} by {$this->getUser()->getUsername()}" );
        
        if( @readfile( $filename ) === false )
        {
            $this->getLogger()->notice( 'Could not load ' . $filename . ' for mrtg/retrieveImageAction' );
            @readfile(
                APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                    . 'public' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR
                    . '300x1.png'
            );
        }
    }
}
