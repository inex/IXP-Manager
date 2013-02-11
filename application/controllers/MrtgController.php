<?php

/*
 * Copyright (C) 2009-2011 Internet Neutral Exchange Association Limited.
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
 * @category   INEX
 * @package    INEX_Controller
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class MrtgController extends INEX_Controller_AuthRequiredAction
{

    public static $GRAPH_CATEGORIES = array (
        'bits' => 'Bits',
        'pkts' => 'Packets',
        'errs' => 'Errors',
        'discs' => 'Discards',
    );

    protected $_flock = null;

    
    public function preDispatch()
    {
        Zend_Controller_Action_HelperBroker::removeHelper( 'viewRenderer' );
    }

    private function checkShortname( $shortname )
    {
        return $this->getD2EM()->getRepository( '\\Entities\\Customer' )->findOneBy( [ 'shortname' => $shortname ] );
    }


    function retrieveImageAction()
    {
        header( 'Content-Type: image/png' );
        header( 'Expires: Thu, 01 Jan 1970 00:00:00 GMT' );

        $monitorindex = $this->getRequest()->getParam( 'monitorindex', 'aggregate' );
        $period       = $this->getRequest()->getParam( 'period', INEX_Mrtg::$PERIODS['Day'] );
        $shortname    = $this->getRequest()->getParam( 'shortname' );
        $category     = $this->getRequest()->getParam( 'category', INEX_Mrtg::$CATEGORIES['Bits'] );
        $graph        = $this->getRequest()->getParam( 'graph', '' );

        $this->getLogger()->debug( "Request for {$shortname}-{$monitorindex}-{$category}-{$period}-{$graph} by {$this->getUser()->getUsername()}" );

        if( $shortname == 'X_Trunks' )
        {
            $filename = $this->_options['mrtg']['path']
                . '/trunks/' . $graph . '-' . $period . '.png';
        }
        else if( $shortname == 'X_SwitchAggregate' )
        {
            $filename = $this->_options['mrtg']['path']
                . '/switches/switch-aggregate-' . $graph . '-'
                . $category . '-' . $period . '.png';
        }
        else if( $shortname == 'X_Peering' )
        {
            $filename = $this->_options['mrtg']['path']
                . '/ixp_peering-' . $graph . '-'
                . $category . '-' . $period . '.png';
        }
        else
        {
            if( $this->getUser()->getPrivs() != \Entities\User::AUTH_SUPERUSER || !$this->checkShortname( $shortname ) )
                $shortname = $this->getCustomer()->getShortname();

            $filename = INEX_Mrtg::getMrtgFilePath( $this->_options['mrtg']['path'] . '/members'    , 'PNG',
                $monitorindex, $category, $shortname, $period
            );
        }

        $this->getLogger()->debug( "Serving {$filename} to {$this->getUser()->getUsername()}" );

        $stat = @readfile( $filename );

        if( $stat === false )
        {
            $this->getLogger()->debug( "Could not load {$filename} for mrtg/retrieveImageAction" );
            echo readfile(
                APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                    . 'public' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR
                    . 'image-missing.png'
            );
        }
    }


    function retrieveP2pImageAction()
    {
        header( 'Content-Type: image/png' );
        header( 'Expires: Thu, 01 Jan 1970 00:00:00 GMT' );

        $period       = $this->getRequest()->getParam( 'period',    INEX_Mrtg::$PERIODS['Day'] );
        $shortname    = $this->getRequest()->getParam( 'shortname', false );
        $svid         = $this->getRequest()->getParam( 'svid',      false );
        $dvid         = $this->getRequest()->getParam( 'dvid',      false );
        $category     = $this->getRequest()->getParam( 'category',  INEX_Mrtg::$CATEGORIES['Bits'] );
        $proto        = $this->getRequest()->getParam( 'proto',     INEX_Mrtg::PROTOCOL_IPV4 );
        $infra        = $this->getRequest()->getParam( 'infra',     INEX_Mrtg::INFRASTRUCTURE_PRIMARY );
        $period       = $this->getRequest()->getParam( 'period',    INEX_Mrtg::PERIOD_DAY );
        
        if( !$this->getIdentity() )
            exit(0);

        $_cust = $this->checkShortname( $shortname );
        
        if( $this->getUser()->getPrivs() != \Entities\User::AUTH_SUPERUSER || !$_cust )
        {
            $shortname = $this->getUser()->getCustomer()->getShortname();
            $_cust = $this->getUser()->getCustomer();
        }
        
        // make sure the svid and dvid is valid
        if( !$svid || !$dvid )
        {
            $this->getLogger()->alert( "P2P file request with svid={$svid} and pvid={$pvid}" );
            die();
        }
        
        $svidOk = false;
        foreach( $_cust->getVirtualInterfaces() as $vint )
        {
            if( $vint->getId() == $svid )
            {
                $svidOk = true;
                break;
            }
        }

        // make sure the svid and dvid is valid
        if( !$svidOk )
        {
            $this->getLogger()->alert( "P2P file request with illegal svid={$svid} for {$shortname}" );
            die();
        }
        
        // find the possible virtual interfaces that this customer peers with
        $dvidOk = false;
        $dshortname = '';
        
        $customersWithVirtualInterfaces = $this->getD2EM()->getRepository( '\\Entities\\VirtualInterface' )->getForInfrastructure( $infra, $proto );
        
        foreach( $customersWithVirtualInterfaces as $c )
        {
            if( $c['shortname'] == $shortname )
                continue;
                
            if( $c['id'] == $dvid )
            {
                $dshortname = $c['shortname'];
                $dvidOk = true;
                break;
            }
        }
        
        // make sure the svid and dvid is valid
        if( !$dvidOk )
        {
            $this->getLogger()->alert( "P2P file request with illegal pvid={$dvid} for {$shortname}" );
            die();
        }
        
        $filename = INEX_Mrtg::getMrtgP2pFilePath( $this->_options['mrtg']['p2ppath'],
            $svid, $dvid, $category, $period, $proto
        );
        
        $this->getLogger()->debug( "Serving $filename to {$this->user->username}" );

        $this->getLogger()->info( "P2P request for {$shortname}-{$dshortname}-{$category}-{$period}-ipv{$proto} by {$this->user->username}" );
        
        $stat = @readfile( $filename );

        if( !$stat )
        {
            $this->getLogger()->debug( 'Could not load ' . $filename . ' for mrtg/retrieveImageAction' );
            readfile(
                APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                    . 'public' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR
                    . '300x1.png'
            );
        }
    }
}
