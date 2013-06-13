<?php

/*
 * Copyright (C) 2009-2012 Internet Neutral Exchange Association Limited.
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
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PeeringMatrixController extends IXP_Controller_Action
{
    public function preDispatch()
    {
        if( isset( $this->_options['frontend']['disabled'][ $this->getRequest()->getControllerName() ] )
                && $this->_options['frontend']['disabled'][ $this->getRequest()->getControllerName() ] )
        {
            $this->addMessage( _( 'This controller has been disabled.' ), OSS_Message::ERROR );
            $this->redirect( '' );
        }
    }
    
    public function indexAction()
    {
        $this->view->protos = array(
            4 => 'IPv4',
            6 => 'IPv6'
        );

        $proto = $this->getParam( 'proto', 4 );
        if( !isset( $this->view->protos[$proto] ) || !in_array( $proto, $this->view->protos ) )
            $proto = 4;

        $this->view->vlans = $vlans = $this->getD2EM()->getRepository( '\\Entities\\Vlan' )->getNames();

        $vid = $this->getParam( 'vid', $this->_options['identity']['vlans']['default'] );
        if( !isset( $vlans[ $vid ] ) )
            $vid = $this->_options['identity']['vlans']['default'];

        $this->view->vid   = $vid;
        $this->view->proto = $proto;

        $this->view->sessions = $this->getD2EM()->getRepository( '\\Entities\\BGPSessionData' )->getPeers( $vid, $proto );
        $this->view->custs    = $this->getD2EM()->getRepository( '\\Entities\\Vlan' )->getCustomers( $vid, $proto );

        $this->view->jsessions = json_encode( $this->view->sessions );
        $this->view->jcusts    = json_encode( $this->view->custs );

        $asns = array_keys( $this->view->custs );
        $maxLenOfASN = strlen( $asns[ count( $asns ) - 1 ] );
        $this->view->asnStringFormat = "% {$maxLenOfASN}s";
    }

}


