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


/*
 *
 *
 * http://www.inex.ie/
 * (c) Internet Neutral Exchange Association Ltd
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

        $this->view->lans = array(
            10 => 'Primary Peering LAN',
            12 => 'Secondary Peering LAN'
        );
        
        $lan = $this->_request->getParam( 'lan', 10 );
        if( !isset( $this->view->lans[$lan] ) )
            $lan = 10;
        
        $this->view->lan = $lan;
        $this->view->proto = $proto;
                
        $this->view->sessions = BgpsessiondataTable::getPeers( $lan, $proto );
        $this->view->custs    = VlaninterfaceTable::getForPeeringMatrix( $lan, $proto );
        
        $asns = array_keys( $this->view->custs );
        $maxLenOfASN = strlen( $asns[ count( $asns ) - 1 ] );
        $this->view->asnStringFormat = "% {$maxLenOfASN}s";
        
        $this->view->display( 'peering-matrix/index.tpl' );
    }
                 
}


