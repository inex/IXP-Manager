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
 * http://www.inex.ie/
 * (c) Internet Neutral Exchange Association Ltd
 */

class PeeringManagerController extends INEX_Controller_Action
{

    public function preDispatch()
    {
        // let's get the user's details sorted before everything else
        if( !$this->auth->hasIdentity() )
        {
            // record the page we wanted
            $this->session->postAuthRedirect = $this->_request->getPathInfo();
            $this->_redirect( 'auth/login' );
        }
        
        // we should only be available to CUSTUSERs
        if( $this->getUser()->privs != User::AUTH_CUSTUSER )
        {
            $this->session->message = new INEX_Message(
                "You must be logged in as a standard user to access the peering manager.",
                INEX_Message::MESSAGE_TYPE_ERROR
            );
            $this->_forward( 'index', 'index' );
        }
    }
    

    public function indexAction()
    {
        //echo '<pre>'; print_r( VlaninterfaceTable::getForPeeringManager() ); die();
        
        $this->view->vlans  = $vlans  = [ 10, 12 ];
        $this->view->protos = $protos = [ 4, 6 ];
        
        $bilat = array();
        foreach( $vlans as $vlan )
            foreach( $protos as $proto )
                $bilat[$vlan][$proto ] = $this->_getSessions( $vlan, $proto );
        
        $this->view->bilat = $bilat;

        //echo '<pre>'; print_r( $bilat ); die();
        
        $custs = VlaninterfaceTable::getForPeeringManager();

        $this->view->me = $me = $custs[ $this->getCustomer()->autsys ];
        $this->view->myasn = $this->getCustomer()->autsys;
        unset( $custs[ $this->getCustomer()->autsys ] );
        
        $potential       = array();
        $potential_bilat = array();
        $peered          = array();
        
        foreach( $custs as $c )
        {
            $custs[ $c['autsys' ] ]['ispotential'] = false;
            
            foreach( $vlans as $vlan )
            {
                if( isset( $me['vlaninterfaces'][$vlan] ) )
                {
                    if( isset( $c['vlaninterfaces'][$vlan] ) )
                    {
                        foreach( $protos as $proto )
                        {
                            if( $me['vlaninterfaces'][$vlan][0]["ipv{$proto}enabled"] && $c['vlaninterfaces'][$vlan][0]["ipv{$proto}enabled"] )
                            {
                                if( in_array( $c['autsys'], $bilat[$vlan][4][$me['autsys']]['peers'] ) )
                                    $custs[ $c['autsys'] ][$vlan][$proto] = 2;
                                else if( $me['vlaninterfaces'][$vlan][0]['rsclient'] && $c['vlaninterfaces'][$vlan][0]['rsclient'] )
                                {
                                    $custs[ $c['autsys'] ][$vlan][$proto] = 1;
                                    $custs[ $c['autsys' ] ]['ispotential'] = true;
                                }
                                else
                                {
                                    $custs[ $c['autsys'] ][$vlan][$proto] = 0;
                                    $custs[ $c['autsys' ] ]['ispotential'] = true;
                                }
                            }
                        }
                    }
                }
            }
        }
        
        foreach( $custs as $c )
        {
            foreach( $vlans as $vlan )
            {
                foreach( $protos as $proto )
                {
                    if( isset( $c[$vlan][$proto] ) )
                    {
                        switch( $c[$vlan][$proto] )
                        {
                            case 2:
                                $peered[ $c['autsys' ] ] = true;
                                break;
                                
                            case 1:
                                $peered[ $c['autsys' ] ] = true;
                                $potential_bilat[ $c['autsys' ] ] = true;
                                break;
                                
                            case 0:
                                $potential[ $c['autsys' ] ] = true;
                                $potential_bilat[ $c['autsys' ] ] = true;
                                break;
                                
                        }
                    }
                }
            }
        }

        $this->view->custs = $custs;
        
        $this->view->potential       = $potential;
        $this->view->potential_bilat = $potential_bilat;
        $this->view->peered          = $peered;
        
        //echo '<pre>'; print_r( $custs ); die();
        
        $this->view->display( 'peering-manager' . DIRECTORY_SEPARATOR . 'index.tpl' );
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
    

    public function peeringMatrixAction()
    {
        $lan = $this->_request->getParam( 'lan', 0 );

        if( !isset( $this->config['peering_matrix']['public'][$lan] ) )
        {
            $this->session->message = new INEX_Message(
                            "Invalid peering matrix requested",
                            INEX_Message::MESSAGE_TYPE_ERROR
                        );

            return( $this->_redirect( 'dashboard' ) );
        }

        $peering_states = Doctrine_Query::create()
            ->select( 'pm.x_as, pm.y_as, pm.peering_status' )
            ->addSelect( 'xc.name, xc.id, xc.peeringmacro, xc.peeringpolicy' )
            ->addSelect( 'yc.name, yc.id, yc.peeringmacro, yc.peeringpolicy' )
            ->from( 'PeeringMatrix pm' )
            ->leftJoin( 'pm.X_Cust xc' )
            ->leftJoin( 'pm.Y_Cust yc' )
            ->where( 'pm.vlan = ?', $this->config['peering_matrix']['public'][$lan]['number'] )
            ->orderBy( 'pm.x_as ASC, pm.y_as ASC' )
            ->fetchArray();

        // try and arrange the array as n x n keyed by x's as number
        $matrix = array();

        $potential = 0;
        $active    = 0;

        foreach( $peering_states as $pm )
        {
            $matrix[$pm['x_as']][] = $pm;

            if( $pm['peering_status'] == 'YES' )
                $active++;

            $potential++;
        }

        $this->view->potential = $potential;
        $this->view->active    = $active;

        $this->view->lan    = $lan;
        $this->view->matrix = $matrix;
        $this->view->display( 'dashboard/peering-matrix.tpl' );
    }

    public function myPeeringManagerEmailAction()
    {
        $bcust = Doctrine_Core::getTable( 'Cust' )->find( $this->_request->getParam( 'id', null ) );

        $this->getResponse()
            ->setHeader('Content-Type', 'text/html');

        if( !$bcust && $this->_request->getParam( 'send' ) == '1' )
        {
            $this->getResponse()
                 ->setBody( Zend_Json::encode(
                    array(
                        'status' => '0',
                        'message' => "Error: Invalid parameters supplied"
                  ) ) )
            ->sendResponse();
            exit;
        }
        else if( !$bcust )
        {
            echo '';
            exit;
        }

        // need to get VLAN interfaces in common for these two members
        $aints = Doctrine_Core::getTable( 'ViewVlaninterfaceDetailsByCustid' )->findByCustid(
            $this->customer['id'], Doctrine_Core::HYDRATE_ARRAY
        );
        $bints = Doctrine_Core::getTable( 'ViewVlaninterfaceDetailsByCustid' )->findByCustid(
            $bcust['id'], Doctrine_Core::HYDRATE_ARRAY
        );

        // potential peerings
        $pp = array();
        $count = 0;
        foreach( $aints as $aint )
        {
            // does b member have one (or more than one)?
            foreach( $bints as $bint )
            {
                if( $aint['vlanid'] == $bint['vlanid'] )
                {
                    $pp[$count]['a'] = $aint;
                    $pp[$count]['b'] = $bint;
                    $count++;
                }
            }
        }

        $this->view->bcust  = $bcust;
        $this->view->pp     = $pp;

        if( $this->_request->getParam( 'send' ) == '1' )
        {
            $mail = new Zend_Mail();
            $mail->setFrom( $this->customer['peeringemail'], $this->customer['peeringemail'] . ' Peering Team' )
                 ->setSubject( stripslashes( $this->_request->getParam( 'subject' ) ) )
                 ->addTo( $bcust['peeringemail'], $bcust['name'] . ' Peering Team' )
                 ->addBcc( $this->customer['peeringemail'], $this->customer['peeringemail'] . ' Peering Team' )
                 ->setBodyText( stripslashes( $this->_request->getParam( 'message' ) ) );

            try {
                $mail->send();

                $myPeerRecord = Doctrine_Query::create()
                    ->from( 'MyPeeringMatrix mpm' )
                    ->where( 'mpm.custid = ?', $this->customer['id'] )
                    ->andWhere( 'mpm.peerid = ?', $bcust['id'] )
                    ->fetchOne( null, Doctrine_core::HYDRATE_RECORD );

                $myPeerRecord->updateNotes( date( 'Y-m-d ' ) . $this->user['username']
                        . ": Peering request sent by email via IXP Manager",
                    true
                );

                $this->getResponse()
                    ->setBody( Zend_Json::encode(
                        array(
                            'status' => '1',
                            'message' => "Email successfully sent to the {$bcust['name']} Peering Team",
                            'commentAdded' => '1', 'cid' => $bcust['id']
                        ) ) )
                    ->sendResponse();
            }
            catch( Zend_Exception $e )
            {
                $this->getLogger()->err( $e->getMessage() . "\n\n" . $e->getTraceAsString() );

                $this->getResponse()
                    ->setBody( Zend_Json::encode(
                        array(
                            'status' => '0',
                            'message' => "Error: Sorry, we could not send the email. Please try later or send manually."
                        ) ) )
                    ->sendResponse();
            }


        }
        else
        {

            $content = array(
                'subject' => $this->config['identity']['orgname'] . " Peering Request between AS" . $this->customer['autsys']
                                . ' - AS' . $bcust['autsys'],
                'to'      => $bcust['name'] . " Peering Team <" . $bcust['peeringemail'] . ">",
                'from'    => $this->customer['name'] . " Peering Team <" . $this->customer['peeringemail'] . ">",
                'bcc'     => $this->customer['name'] . " Peering Team <" . $this->customer['peeringemail'] . ">",
                'message' => $this->view->render( 'dashboard/email/peering-request.tpl' )
            );

            $this->getResponse()
                ->setHeader('Content-Type', 'text/html')
                ->setBody( Zend_Json::encode( $content ) )
                ->sendResponse();
        }

        exit();
    }


    public function myPeeringManagerNotesAction()
    {
        $bcust = Doctrine_Core::getTable( 'Cust' )->find( $this->_request->getParam( 'id', null ) );

        $this->getResponse()
            ->setHeader('Content-Type', 'text/html');

        if( !$bcust && $this->_request->getParam( 'save' ) == '1' )
        {
            $this->getResponse()
                 ->setBody( Zend_Json::encode(
                    array(
                        'status' => '0',
                        'message' => "Error: Invalid parameters supplied"
                  ) ) )
            ->sendResponse();
            exit;
        }
        else if( !$bcust )
        {
            echo '';
            exit;
        }

        $myPeerRecord = Doctrine_Query::create()
            ->from( 'MyPeeringMatrix mpm' )
            ->where( 'mpm.custid = ?', $this->customer['id'] )
            ->andWhere( 'mpm.peerid = ?', $bcust['id'] )
            ->fetchOne( null, Doctrine_core::HYDRATE_RECORD );

        if( $this->_request->getParam( 'save' ) == '1' )
        {
            try
            {
                $myPeerRecord->updateNotes( stripslashes( $this->_request->getParam( 'notes' ) ) );
                
                $this->getResponse()
                    ->setBody( Zend_Json::encode(
                        array(
                            'status' => '1',
                            'message' => "Peering notes updated for {$bcust['name']}.",
                            'commentAdded' => '1', 'cid' => $bcust['id']
                        ) ) )
                    ->sendResponse();
            }
            catch( Zend_Exception $e )
            {
                $this->getResponse()
                    ->setBody( Zend_Json::encode(
                        array(
                            'status' => '0',
                            'message' => "Error: Sorry, we could not save your updated notes. Please contact support to report this issue."
                        ) ) )
                    ->sendResponse();
            }
        }
        else
        {
            $prefix = date( 'Y-m-d ' ) . $this->user['username'] . ": ";
            $content = array(
                'name'  => $bcust['name'],
                'notes' => "$prefix\n\n" . $myPeerRecord->getNotes(),
                'pos'   => strlen( $prefix )
            );

            $this->getResponse()
                ->setHeader('Content-Type', 'text/html')
                ->setBody( Zend_Json::encode( $content ) )
                ->sendResponse();
        }

        exit();
    }


    public function myPeeringManagerPeeredStateAction()
    {
        $type  = $this->_request->getParam( 'type', 'state' );

        $bcust = Doctrine_Core::getTable( 'Cust' )->find( $this->_request->getParam( 'id', false ) );

        if( !$bcust )
            exit;

        // do we have a VLAN and is it valid
        $vlan = $this->_request->getParam( 'vlan', false );

        // is it one of the allowed VLANs?
        $vlan_valid = false;
        foreach( $this->config['peering_matrix']['public'] as $v )
        {
            if( $v['number'] == $vlan )
            {
                $vlan_valid = true;
                break;
            }
        }

        // if it's not valid, just bounce them to the first default
        if( !$vlan_valid )
            exit;


        $myPeeringMatrix = Doctrine_Query::create()
            ->from( 'MyPeeringMatrix mpm' )
            ->where( 'mpm.custid = ?', $this->customer['id'] )
            ->andWhere( 'mpm.peerid = ?', $bcust['id'] )
            ->andWhere( 'mpm.vlan = ?', $vlan )
            ->fetchOne( null, Doctrine_Core::HYDRATE_RECORD );

        if( !$myPeeringMatrix )
            exit;

        if( $type == 'ipv6' )
        {
            $myPeeringMatrix['ipv6'] = ( $myPeeringMatrix['ipv6'] + 1 ) % 2;
            $newstate = $myPeeringMatrix['ipv6'];
        }
        else
        {
	        $newstate = ( array_search( $myPeeringMatrix['peered'], MyPeeringMatrix::$PEERED_STATES ) + 1 )
	            % count( MyPeeringMatrix::$PEERED_STATES );

	        $myPeeringMatrix['peered'] = MyPeeringMatrix::$PEERED_STATES[ $newstate ];
        }

        $myPeeringMatrix->save();

        $content = array( 'newstate' => $newstate );

        $this->getResponse()
            ->setHeader('Content-Type', 'text/html')
            ->setBody( Zend_Json::encode( $content ) )
            ->sendResponse();
        exit();
    }


}
