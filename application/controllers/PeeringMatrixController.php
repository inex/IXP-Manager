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
 * Controller: Peering Matrices
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   INEX
 * @package    INEX_Controller
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PeeringMatrixController extends INEX_Controller_Action
{

    public function indexAction()
    {
        $this->view->protos = array(
            4 => 'IPv4',
            6 => 'IPv6'
        );
        
        $proto = $this->_request->getParam( 'proto', 4 );
        if( !isset( $this->view->protos[$proto] ) )
            $proto = 4;

        $lans = array();
        
        foreach( $this->config['peering_matrix']['public'] as $id => $pm )
            $lans[ $pm['number'] ] = $pm['name'];

        $this->view->lans = $lans;
        
        $lan = $this->_request->getParam( 'lan', 10 );
        if( !isset( $lans[$lan] ) )
            $lan = 10;
        
        $this->view->lan = $lan;
        $this->view->proto = $proto;
                
        $this->view->sessions = $this->_getSessions( $lan, $proto );
        $this->view->custs    = $this->_getCusts( $lan, $proto );
        
        $this->view->jsessions = json_encode( $this->view->sessions );
        $this->view->jcusts    = json_encode( $this->view->custs );
        
        $asns = array_keys( $this->view->custs );
        $maxLenOfASN = strlen( $asns[ count( $asns ) - 1 ] );
        $this->view->asnStringFormat = "% {$maxLenOfASN}s";
        
        $this->view->display( 'peering-matrix/index.tpl' );
    }
                 
    
    private function _getSessions( $lan, $proto )
    {
        $key = "pm_sessions_{$lan}_{$proto}";
        
        if( !( $sessions = $this->apcFetch( $key ) ) )
        {
            $sessions = BgpsessiondataTable::getPeers( $lan, $proto );
            $this->apcStore( $key, $sessions, 86400 );
        }
        
        return $sessions;
    }

    private function _getCusts( $lan, $proto )
    {
        $key = "pm_custs_{$lan}_{$proto}";
        
        if( !( $custs = $this->apcFetch( $key ) ) )
        {
            $custs = VlaninterfaceTable::getForPeeringMatrix( $lan, $proto );
            $this->apcStore( $key, $custs, 86400 );
        }
        
        return $custs;
    }
}


