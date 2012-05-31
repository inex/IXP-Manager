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
    


    public function peeringRequestAction()
    {
        $TESTMODE = true;
        
        $this->view->peer = $peer = Doctrine_Core::getTable( 'Cust' )->find( $this->_request->getParam( 'custid', null ) );
        
        if( !$peer )
        {
            echo "ERR:Could not find peer's information in the database. Please contact support.";
            return true;
        }
        
        $f = new INEX_Form_PeeringRequest();
        
        // need to get VLAN interfaces in common for these two members
        $myints = Doctrine_Core::getTable( 'ViewVlaninterfaceDetailsByCustid' )->findByCustid(
            $this->getCustomer()['id'], Doctrine_Core::HYDRATE_ARRAY
        );
        
        $pints = Doctrine_Core::getTable( 'ViewVlaninterfaceDetailsByCustid' )->findByCustid(
            $peer['id'], Doctrine_Core::HYDRATE_ARRAY
        );
        
        // potential peerings
        $pp = array(); $count = 0;
        
        foreach( $myints as $myint )
        {
            // does b member have one (or more than one)?
            foreach( $pints as $pint )
            {
                if( $myint['vlanid'] == $pint['vlanid'] )
                {
                    $pp[$count]['my']   = $myint;
                    $pp[$count]['your'] = $pint;
                    $count++;
                }
            }
        }
        
        // INEX_Debug::dd( $pp );
        $this->view->pp = $pp;
        
        $f->getElement( 'to' )->setValue( $peer['peeringemail'] );
        $f->getElement( 'cc' )->setValue( $this->getCustomer()['peeringemail'] );

        if( $this->getRequest()->isPost() )
        {
            if( $f->isValid( $_POST ) )
            {
                $bccOk = true;
                $bcc = array();
                if( strlen( $bccs = $f->getValue( 'bcc' ) ) )
                {
                    foreach( explode( ',', $bccs ) as $b )
                    {
                        $b = trim( $b );
                        if( !Zend_Validate::is( $b, 'EmailAddress' ) )
                        {
                            $f->getElement( 'bcc' )->addError( 'One or more email address(es) here are invalid' );
                            $bccOk = false;
                        }
                        else
                            $bcc[] = $b;
                    }
                }
                
                if( $bccOk )
                {
                
                    $mail = new Zend_Mail();
                    $mail->setFrom( 'no-reply@inex.ie', $this->getCustomer()['name'] . ' Peering Team' )
                         ->setReplyTo( $this->getCustomer()['peeringemail'], $this->getCustomer()['name'] . ' Peering Team' )
                         ->setSubject( $f->getValue( 'subject' ) )
                         ->addTo( $TESTMODE ? 'barryo@inex.ie' : $peer['peeringemail'], "{$peer['name']} Peering Team" )
                         ->addCc( $TESTMODE ? 'barryo@inex.ie' : $this->getCustomer()['peeringemail'], "{$this->getCustomer()['name']} Peering Team" )
                         ->setBodyText( $f->getValue( 'message' ) );

                    if( count( $bcc ) )
                        foreach( $bcc as $b )
                            $mail->addBcc( $b );
                    
                    try {
                        $mail->send();
                        
                        // get this customer/peer peering manager table entry
                        $pm = PeeringManagerTable::getEntry( $this->getCustomer()['id'], $peer['id'] );
                        $pm['email_last_sent'] = date( 'Y-m-d' );
                        $pm['emails_sent'] = $pm['emails_sent'] + 1;
                        $pm['updated'] = date( 'Y-m-d H:i:s' );
                        $pm->save();
                    }
                    catch( Zend_Exception $e )
                    {
                        $this->getLogger()->err( $e->getMessage() . "\n\n" . $e->getTraceAsString() );
                        echo "ERR:Could not send the peering email. Please send manually yourself or contact support.";
                        return true;
                    }
                    
                    echo "OK:Peering request sent.";
                    return true;
                }
            }
        }
        else
        {
            $f->getElement( 'bcc' )->setValue( $this->getUser()['email'] );
            $f->getElement( 'subject' )->setValue( "[INEX] Peering Request from {$this->getCustomer()['name']} (ASN{$this->getCustomer()['autsys']})" );
            $f->getElement( 'message' )->setValue( $this->view->render( 'peering-manager' . DIRECTORY_SEPARATOR . 'peering-request-message.tpl' ) );
        }
        
        $this->view->form = $f;

        $this->view->display( 'peering-manager' . DIRECTORY_SEPARATOR . 'peering-request.tpl' );
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
