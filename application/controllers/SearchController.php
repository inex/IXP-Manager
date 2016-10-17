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
 * Controller: Search
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SearchController extends IXP_Controller_AuthRequiredAction
{
    
    public function preDispatch()
    {
        $this->assertPrivilege( \Entities\User::AUTH_SUPERUSER );
    }
    
    public function doAction()
    {
        if( ( $this->view->search = $search = trim( stripslashes( $this->getParam( 'search', '' ) ) ) ) == '' )
            return;

        // what kind of search are we doing?
        if( preg_match( '/^\.\d{1,3}$/', $search ) || preg_match( '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $search ) )
        {
            $this->view->type = 'ipv4';
            $this->processIPSearch( $this->getD2R( '\\Entities\\IPv4Address' )->findVlanInterfaces( $search ) );
        }
        else if( preg_match( '/^[a-f0-9]{12}$/', strtolower( $search ) ) || preg_match( '/^[a-f0-9]{2}:[a-f0-9]{2}:[a-f0-9]{2}:[a-f0-9]{2}:[a-f0-9]{2}:[a-f0-9]{2}$/', strtolower( $search ) ) )
        {
            $this->view->type = 'mac';
            $this->processMACSearch( $search );
        }
        else if( preg_match( '/^:[0-9a-fA-F]{1,4}$/', $search ) || preg_match( '/^[0-9a-fA-F]{1,4}:.*:[0-9a-fA-F]{1,4}$/', $search ) )
        {
            $this->view->type = 'ipv6';
            $this->processIPSearch( $this->getD2R( '\\Entities\\IPv6Address' )->findVlanInterfaces( $search ) );
        }
        else if( preg_match( '/^as(\d+)$/', strtolower( $search ), $matches ) || preg_match( '/^(\d+)$/', $search, $matches ) )
        {
            $this->view->type = 'asn';
            $this->view->results = $this->getD2R( '\\Entities\\Customer' )->findByASN( $matches[1] );
        }
        else if( preg_match( '/^AS-(.*)$/', strtoupper( $search ) ) )
        {
            $this->view->type = 'asmacro';
            $this->view->results = $this->getD2R( '\\Entities\\Customer' )->findByASMacro( $search );
        }
        else if( preg_match( '/^@([a-zA-Z0-9]+)$/', $search, $matches ) )
        {
            $this->view->type = 'username';
            $this->view->results = $this->getD2R( '\\Entities\\Contact' )->findByUsername( $matches[1] . '%' );
        }
        else if( filter_var( $search, FILTER_VALIDATE_EMAIL ) !== false )
        {
            $this->view->type = 'email';
            $this->view->results = $this->getD2R( '\\Entities\\Contact' )->findByEmail( $search );
        }
        else if( preg_match( '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\/\d{1,2}$/', $search ) 
                || preg_match( '/^[0-9a-fA-F]{1,4}:.*:[0-9a-fA-F]{0,4}\/\d{1,3}$/', $search ) )
        {
            $this->view->type = 'rsprefix';
            $this->view->results = $this->getD2R( '\\Entities\\RSPrefix' )->findBy( [ 'prefix' => $search ] );
        }
        else
        {
            $this->view->type = 'cust_wild';
            $this->view->results = $this->getD2R( '\\Entities\\Customer' )->findWild( $search );
        }
    }

    private function processMACSearch( $search )
    {
        $results = [];
        $interfaces = [];
        foreach( $this->getD2R( '\\Entities\\MACAddress' )->findVirtualInterface( $search ) as $vi )
        {
            $results[ $vi->getCustomer()->getId() ] = $vi->getCustomer();
            $interfaces[ $vi->getCustomer()->getId() ][] = $vi;
        }

        $this->view->results    = $results;
        $this->view->interfaces = $interfaces;
    }

    private function processIPSearch( $vlis )
    {
        $results = [];
        foreach( $vlis as $vli )
        {
            $results[$vli->getVirtualInterface()->getCustomer()->getId()] = $vli->getVirtualInterface()->getCustomer();
            $interfaces[ $vli->getVirtualInterface()->getCustomer()->getId() ][] = $vli;
        }

        $this->view->results    = $results;
        $this->view->interfaces = $interfaces;
    }
    
}

