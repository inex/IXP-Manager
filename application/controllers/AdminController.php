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
 * CustAdminController
 *
 * @author
 * @version
 */

class AdminController extends INEX_Controller_Action
{


    public function preDispatch()
    {
        // let's get the user's details sorted before everything else
        if( !$this->identity )
            $this->_redirect( 'auth/login' );
        else if( $this->user->privs != User::AUTH_SUPERUSER )
	    {
	        $this->view->message = new INEX_Message(
	            "You must be an administrator to access this page. This attempt to access private and "
	            . "secure sections of the site has been recorded and our administrators alerted.",
	            INEX_Message::MESSAGE_TYPE_ERROR
	        );

	        $this->logger->alert( $this->user->username . " tried to access the admin controller without sufficient permissions" );

            Zend_Session::destroy( true, true );

	        $this->_forward( 'login', 'auth' );
	        return false;
	    }

    }


    /**
     * The default action - show the home page
     */
    public function indexAction()
    {
        LocationTable::getInterfacesByLocation();
        $this->_publicPeeringGraphs();
        $this->_dashboardStats();
        $this->view->display( 'admin/index.tpl' );
    }

    
    /**
     * Get public peering graphs
     *
     * FIXME On move to Doctrine2, use central cache rather than per user session cache
     */
    private function _publicPeeringGraphs()
    {
        // only do this once every five minutes
        if( !isset( $this->session->ahome_stats ) || $this->session->ahome_stats['gen_at'] < ( time() - 300 ) )
        {
            $this->session->ahome_stats = array();
            $this->session->ahome_stats['gen_at'] = time();
            
            foreach( $this->config['mrtg']['traffic_graphs'] as $g )
            {
                $p = explode( '::', $g );
                $graphs[$p[0]] = $p[1];
                $images[]      = $p[0];
                
                $mrtg = new INEX_Mrtg(
                    $this->config['mrtg']['path']
                        . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                        . 'inex_peering-' . $p[0] . '-' . INEX_Mrtg::CATEGORY_BITS . '.log'
                );
                
                $stats[$p[0]] = $mrtg->getValues( INEX_Mrtg::PERIOD_MONTH, INEX_Mrtg::CATEGORY_BITS );
            }
            
            $this->session->ahome_stats['graphs'] = $this->view->graphs     = $graphs;
            $this->session->ahome_stats['stats']  = $this->view->stats      = $stats;
        }
        else
        {
            $this->view->graphs = $this->session->ahome_stats['graphs'];
            $this->view->stats  = $this->session->ahome_stats['stats'];
        }
    }

    /**
     * Get type counts
     *
     * FIXME On move to Doctrine2, use central cache rather than per user session cache
     */
    private function _dashboardStats()
    {
        // only do this once every 30 minutes
        if( !isset( $this->session->ahome_ctypes ) || $this->session->ahome_ctypes['gen_at'] < ( time() - 1800 ) )
        {
            $this->session->ahome_ctypes = array();
            $this->session->ahome_ctypes['gen_at'] = time();
            $this->view->ctypes = $this->session->ahome_ctypes['types'] = CustTable::getTypeCounts();
            
            $ints = LocationTable::getInterfacesByLocation();
            
            $speeds = array();
            $bylocation = array();
            $bylan = array();
            foreach( $ints as $int )
            {
                if( !isset( $bylocation[ $int['locationname'] ] ) )
                    $bylocation[ $int['locationname'] ] = array();

                if( !isset( $bylan[ $int['infrastructure'] ] ) )
                    $bylan[ $int['infrastructure'] ] = array();

                if( !isset( $speeds[ $int['speed'] ] ) )
                    $speeds[ $int['speed'] ] = 1;
                else
                    $speeds[ $int['speed'] ]++;
                                    
                if( !isset( $bylocation[ $int['locationname'] ][ $int['speed'] ] ) )
                    $bylocation[ $int['locationname'] ][ $int['speed'] ] = 1;
                else
                    $bylocation[ $int['locationname'] ][ $int['speed'] ] = $bylocation[ $int['locationname'] ][ $int['speed'] ] + 1;

                if( !isset( $bylan[ $int['infrastructure'] ][ $int['speed'] ] ) )
                    $bylan[ $int['infrastructure'] ][ $int['speed'] ] = 1;
                else
                    $bylan[ $int['infrastructure'] ][ $int['speed'] ] = $bylan[ $int['infrastructure'] ][ $int['speed'] ] + 1;
            }
            
            ksort( $speeds, SORT_NUMERIC );
            $this->view->speeds      = $this->session->ahome_ctypes['speeds']      = $speeds;
            $this->view->bylocation  = $this->session->ahome_ctypes['bylocation']  = $bylocation;
            $this->view->bylan       = $this->session->ahome_ctypes['bylan']       = $bylan;
            
        }
        else
        {
            $this->view->ctypes      = $this->session->ahome_ctypes['types'];
            $this->view->speeds      = $this->session->ahome_ctypes['speeds'];
            $this->view->bylocation  = $this->session->ahome_ctypes['bylocation'];
            $this->view->bylan       = $this->session->ahome_ctypes['bylan'];
        }
    }
    
    public function staticAction()
    {
        $page = $this->_request->getParam( 'page', null );

        if( $page == null )
            return( $this->_redirect( 'index' ) );

        // does the requested static page exist? And if so, display it
        if( preg_match( '/^[a-zA-Z0-9\-]+$/', $page ) > 0
                && file_exists( APPLICATION_PATH . "/views/admin/static/{$page}.tpl" ) )
        {
            $this->view->display( "admin/static/{$page}.tpl" );
        }
        else
        {
            $this->session->message = new INEX_Message(
                "The requested page was not found.",
                INEX_Message::MESSAGE_TYPE_ERROR
            );
            $this->_redirect( 'index' );
        }
    }
}

