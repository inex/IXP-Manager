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
 * Controller: Retrive MRTG images
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class MrtgController extends IXP_Controller_AuthRequiredAction
{
    use IXP_Controller_Trait_Statistics;
    
    protected $_flock = null;
    
    public function preDispatch()
    {
        // there's no HTML output from this controller - just images
        Zend_Controller_Action_HelperBroker::removeHelper( 'viewRenderer' );
        
        header( 'Content-Type: image/png' );
        header( 'Expires: Thu, 01 Jan 1970 00:00:00 GMT' );
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
        $this->setIXP();
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
        $cust = $this->view->cust = $this->resolveCustomerByShortnameParam(); // includes security checks
        
        $this->setIXP( $cust );
        $category = $this->setCategory( 'category', false );
        $period   = $this->setPeriod();
        $proto    = $this->setProtocol();

        // Find the possible VLAN interfaces that this customer has for the given IXP
        // and ensure we have a valid svli
        $srcVlis = $this->getD2R( '\\Entities\\VlanInterface' )->getForCustomer( $cust, $this->ixp );
        
        if( !( $svli = $this->getParam( 'svli', false ) ) || !isset( $svli, $srcVlis ) )
        {
            $this->getLogger()->alert( "P2P file request with illegal svli={$svli} for {$cust->getShortname()}/{$this->getUser()->getUsername()}" );
            die();
        }
        
        // Find the possible interfaces that this customer peers with and ensure we have a valid dvli
        $dstVlis = $this->getD2R( '\\Entities\\VlanInterface' )->getObjectsForVlan( $srcVlis[ $svli ]->getVlan() );
        unset( $dstVlis[ $svli ] );
        
        if( !( $dvli = $this->getParam( 'dvli', false ) ) || !isset( $dvli, $dstVlis ) )
        {
            $this->getLogger()->alert( "P2P file request with illegal dvli={$dvli} for {$cust->getShortname()}/{$this->getUser()->getUsername()}" );
            die();
        }
        
        $filename = IXP_Mrtg::getMrtgP2pFilePath( $this->ixp->getMrtgP2pPath(),
            $svli, $dvli, $category, $period, $proto
        );
        
        $this->getLogger()->debug( "Serving P2P $filename to {$this->getUser()->getUsername()}" );

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
