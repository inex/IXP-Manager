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
 * Controller: Peering Manager
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PeeringManagerController extends IXP_Controller_AuthRequiredAction
{

    public function preDispatch()
    {
        if( isset( $this->_options['frontend']['disabled'][ $this->getRequest()->getControllerName() ] )
                && $this->_options['frontend']['disabled'][ $this->getRequest()->getControllerName() ] )
        {
            $this->addMessage( _( 'This controller has been disabled.' ), OSS_Message::ERROR );
            $this->redirect( '' );
        }

        // we should only be available to CUSTUSERs
        if( $this->getUser()->getPrivs() != \Entities\User::AUTH_CUSTUSER )
        {
            $this->addMessage( "You must be logged in as a standard user to access the peering manager.",
                OSS_Message::ERROR
            );
            $this->_redirect( '' );
        }

    }

    public function indexAction()
    {
        $this->view->vlans  = $vlans  = $this->getD2EM()->getRepository( '\\Entities\\Vlan' )->getPeeringManagerVLANs();

        if( !count( $vlans ) ) {
            $this->addMessage( 'No VLANs have been enabled for the peering manager. Please see <a href="'
                    . 'https://github.com/inex/IXP-Manager/wiki/Peering-Manager">these instructions</a>'
                    . ' / contact our support team.',
                OSS_Message::ERROR
            );
            $this->_redirect( '' );
        }

        $this->view->protos = $protos = [ 4, 6 ];

        $bilat = array();
        foreach( $vlans as $vlan )
            foreach( $protos as $proto )
                $bilat[ $vlan->getNumber() ][$proto ] = $this->getD2EM()->getRepository( '\\Entities\\BGPSessionData' )->getPeers( $vlan->getId(), $proto );

        $this->view->bilat = $bilat;

        $peers = $this->getD2EM()->getRepository( '\\Entities\\Customer' )->getPeers( $this->getCustomer()->getId() );
        foreach( $peers as $i => $p )
        {
            // days since last peering request email sent
            if( !$p['email_last_sent'] )
                $peers[ $i ]['email_days'] = 0;
            else
                $peers[ $i ]['email_days'] = floor( ( time() - $p['email_last_sent']->getTimestamp() ) / 86400 );
        }
        $this->view->peers = $peers;

        $custs = $this->getD2EM()->getRepository( '\\Entities\\Customer' )->getForPeeringManager();

        $this->view->me = $me = $custs[ $this->getCustomer()->getAutsys() ];
        $this->view->myasn = $this->getCustomer()->getAutsys();
        unset( $custs[ $this->getCustomer()->getAutsys() ] );

        $potential       = [];
        $potential_bilat = [];
        $peered          = [];
        $rejected        = [];

        foreach( $custs as $c )
        {
            $custs[ $c['autsys' ] ]['ispotential'] = false;

            foreach( $vlans as $vlan )
            {
                if( isset( $me['vlaninterfaces'][ $vlan->getNumber() ] ) )
                {
                    if( isset( $c['vlaninterfaces'][$vlan->getNumber()] ) )
                    {
                        foreach( $protos as $proto )
                        {
                            if( $me['vlaninterfaces'][$vlan->getNumber()][0]["ipv{$proto}enabled"] && $c['vlaninterfaces'][$vlan->getNumber()][0]["ipv{$proto}enabled"] )
                            {
                                if( in_array( $c['autsys'], $bilat[$vlan->getNumber()][4][$me['autsys']]['peers'] ) )
                                    $custs[ $c['autsys'] ][$vlan->getNumber()][$proto] = 2;
                                else if( $me['vlaninterfaces'][$vlan->getNumber()][0]['rsclient'] && $c['vlaninterfaces'][$vlan->getNumber()][0]['rsclient'] )
                                {
                                    $custs[ $c['autsys'] ][$vlan->getNumber()][$proto] = 1;
                                    $custs[ $c['autsys' ] ]['ispotential'] = true;
                                }
                                else
                                {
                                    $custs[ $c['autsys'] ][$vlan->getNumber()][$proto] = 0;
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
            $peered[          $c['autsys' ] ] = false;
            $potential_bilat[ $c['autsys' ] ] = false;
            $potential[       $c['autsys' ] ] = false;
            $rejected[        $c['autsys' ] ] = false;

            foreach( $vlans as $vlan )
            {
                foreach( $protos as $proto )
                {
                    if( isset( $c[$vlan->getNumber()][$proto] ) )
                    {
                        switch( $c[$vlan->getNumber()][$proto] )
                        {
                            case 2:
                                $peered[ $c['autsys' ] ] = true;
                                break;

                            case 1:
                                $peered[          $c['autsys' ] ] = true;
                                $potential_bilat[ $c['autsys' ] ] = true;
                                break;

                            case 0:
                                $potential[       $c['autsys' ] ] = true;
                                $potential_bilat[ $c['autsys' ] ] = true;
                                break;

                        }
                    }
                }
            }
        }

        foreach( $custs as $c )
        {
            if( isset( $peers[ $c['id'] ] ) )
            {
                if( isset( $peers[ $c['id'] ]['peered'] ) && $peers[ $c['id'] ]['peered'] )
                {
                    $peered[ $c['autsys' ] ] = true;
                    $rejected[ $c['autsys' ] ] = false;
                    $potential[ $c['autsys' ] ] = false;
                    $potential_bilat[ $c['autsys' ] ] = false;
                }
                else if( isset( $peers[ $c['id'] ]['rejected'] ) && $peers[ $c['id'] ]['rejected'] )
                {
                    $peered[ $c['autsys' ] ] = false;
                    $rejected[ $c['autsys' ] ] = true;
                    $potential[ $c['autsys' ] ] = false;
                    $potential_bilat[ $c['autsys' ] ] = false;
                }
            }
        }

        $this->view->custs = $custs;

        $this->view->potential       = $potential;
        $this->view->potential_bilat = $potential_bilat;
        $this->view->peered          = $peered;
        $this->view->rejected        = $rejected;

        //echo '<pre>'; print_r( $custs ); die();

        $this->view->date = date( 'Y-m-d' );
    }




    public function peeringRequestAction()
    {
        $peer = $this->_loadPeer( $this->getParam( 'custid', null ) );
        $f = new IXP_Form_PeeringRequest();

        // potential peerings
        $pp = array(); $count = 0;

        foreach( $this->getCustomer()->getVirtualInterfaces() as $myvis )
        {
            foreach( $myvis->getVlanInterfaces() as $myvli )
            {
                // does b member have one (or more than one)?
                foreach( $peer->getVirtualInterfaces() as $pvis )
                {
                    foreach( $pvis->getVlanInterfaces() as $pvli )
                    {
                        if( $myvli->getVlan()->getId() == $pvli->getVlan()->getId() )
                        {
                            $pp[$count]['my']   = $myvli;
                            $pp[$count]['your'] = $pvli;
                            $count++;
                        }
                    }
                }
            }
        }

        // IXP_Debug::dd( $pp );
        $this->view->pp = $pp;

        $f->getElement( 'to' )->setValue( $peer->getPeeringemail() );
        $f->getElement( 'cc' )->setValue( $this->getCustomer()->getPeeringemail() );

        if( $this->getRequest()->isPost() )
        {
            if( $f->isValid( $_POST ) )
            {
                $sendtome = $f->getValue( 'sendtome' ) == '1' ? true : false;
                $marksent = $f->getValue( 'marksent' ) == '1' ? true : false;

                $bccOk = true;
                $bcc = [];
                if( !$sendtome )
                {
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
                }

                if( $bccOk )
                {
                    $mail = new Zend_Mail('UTF-8');
                    $mail->setFrom( $this->_options['identity']['mailer']['email'], $this->getCustomer()->getName() . ' Peering Team' )
                         ->setReplyTo( $this->getCustomer()->getPeeringemail(), $this->getCustomer()->getName() . ' Peering Team' )
                         ->setSubject( $f->getValue( 'subject' ) )
                         ->setBodyText( $f->getValue( 'message' ) );

                    if( $sendtome )
                        $mail->addTo( $this->getUser()->getEmail() );
                    else
                    {
                        if( isset( $this->_options['peeringmanager']['testmode'] ) && $this->_options['peeringmanager']['testmode'] )
                        {
                            if( !isset( $this->_options['peeringmanager']['testemail'] ) )
                            {
                                $this->getLogger()->alert( "Peering Manager test mode enabled but testemail not defined" );
                                echo "ERR:Peering Manager test mode enabled but testemail not defined in application.ini.";
                                return true;
                            }
                            $mail->addTo( $this->_options['peeringmanager']['testemail'] );
                        }
                        else
                        {
                            $mail->addTo( $peer->getPeeringemail(), "{$peer->getName()} Peering Team" )
                                 ->addCc( $this->getCustomer()->getPeeringemail(), "{$this->getCustomer()->getName()} Peering Team" );
                        }
                    }

                    if( count( $bcc ) )
                        foreach( $bcc as $b )
                            $mail->addBcc( $b );

                    try {
                        if( !$marksent )
                            $mail->send();

                        if( !$sendtome )
                        {
                            // get this customer/peer peering manager table entry
                            $pm = $this->_loadPeeringManagerEntry( $this->getCustomer(), $peer );

                            if( isset( $this->_options['peeringmanager']['testmode'] ) && $this->_options['peeringmanager']['testmode']
                                    && isset( $this->_options['peeringmanager']['testdate'] ) && $this->_options['peeringmanager']['testdate'] )
                            {
                                $pm->setEmailLastSent( new DateTime() );
                                $pm->setEmailsSent( $pm->getEmailsSent() + 1 );
                                $pm->setUpdated( new DateTime() );
                            }

                            if( isset( $this->_options['peeringmanager']['testmode'] ) && $this->_options['peeringmanager']['testmode']
                                    && isset( $this->_options['peeringmanager']['testnote'] ) && $this->_options['peeringmanager']['testnote'] )
                            {
                                $pm->setNotes(
                                    date( 'Y-m-d' ) . " [{$this->getUser()->getUsername()}]: peering request " . ( $marksent ? 'marked ' : '' ) . "sent\n\n" . $pm->getNotes()
                                );
                            }

                            $this->getD2EM()->flush();
                        }
                    }
                    catch( Zend_Exception $e )
                    {
                        $this->getLogger()->err( $e->getMessage() . "\n\n" . $e->getTraceAsString() );
                        echo "ERR:Could not send the peering email. Please send manually yourself or contact support.";
                        return true;
                    }

                    if( $sendtome )
                        echo "OK:Peering request sample sent to your own email address ({$this->getUser()->getEmail()}).";
                    else if( $marksent )
                        echo "OK:Peering request marked as sent in your Peering Manager.";
                    else
                        echo "OK:Peering request sent to {$peer->getName()} Peering Team.";

                    return true;
                }
            }
        }
        else
        {
            $f->getElement( 'bcc' )->setValue( $this->getUser()->getEmail() );
            $f->getElement( 'subject' )->setValue(
                "[" . $this->_options['identity']['orgname'] . "] Peering Request from "
                    . $this->getCustomer()->getName() . " (ASN{$this->getCustomer()->getAutsys()})"
            );
            $f->getElement( 'message' )->setValue( $this->view->render( 'peering-manager/peering-request-message.phtml' ) );
        }

        $this->view->form = $f;
    }

    public function peeringNotesAction()
    {
        Zend_Controller_Action_HelperBroker::removeHelper( 'viewRenderer' );

        $peer = $this->_loadPeer( $this->getParam( 'custid', null ) );
        $pm = $this->_loadPeeringManagerEntry( $this->getCustomer(), $peer );

        if( $this->getRequest()->isPost() )
        {
            $pm->setUpdated( new DateTime() );

            if( trim( stripslashes( $this->getParam( 'message', '' ) ) ) )
                $pm->setNotes( trim( stripslashes( $this->getParam( 'message' ) ) ) );

            try
            {
                $this->getD2EM()->flush();
            }
            catch( Exception $e )
            {
                $this->getLogger()->err( $e->getMessage() . "\n\n" . $e->getTraceAsString() );
                echo "ERR:Could not update peering notes due to an unexpected error.";
                return true;
            }

            echo "OK:Peering notes updated for {$peer->getName()}.";
        }
        else
        {
            echo 'OK:' . $pm->getNotes();
        }
    }


    public function markPeeredAction()
    {
        $peer = $this->_loadPeer( $this->getParam( 'custid' ) );
        $pm = $this->_loadPeeringManagerEntry( $this->getCustomer(), $peer );

        $pm->setPeered( $pm->getPeered() ? false : true );
        if( $pm->getPeered() && $pm->getRejected() )
            $pm->setRejected( false );

        $this->getD2EM()->flush();

        $this->addMessage( "Peered flag " . ( $pm->getPeered() ? 'set' : 'cleared' ) . " for {$peer->getName()}.", OSS_Message::SUCCESS );
        return $this->_redirect( 'peering-manager/index' );
    }


    public function markRejectedAction()
    {
        $peer = $this->_loadPeer( $this->getParam( 'custid' ) );
        $pm = $this->_loadPeeringManagerEntry( $this->getCustomer(), $peer );

        $pm->setRejected( $pm->getRejected() ? false : true );
        if( $pm->getPeered() && $pm->getRejected() )
            $pm->setPeered( false );

        $this->getD2EM()->flush();

        $this->addMessage( "Ignored / rejected flag " . ( $pm->getRejected() ? 'set' : 'cleared' ) . " for {$peer->getName()}.", OSS_Message::SUCCESS );
        return $this->_redirect( 'peering-manager/index' );
    }


    /**
     * Utility function to load a peer from a submitted ID and issue an error and die() if not found.
     *
     * @return \Entities\Customer
     */
    private function _loadPeer()
    {
        if( $this->getParam( 'custid', false ) )
            $this->view->peer = $peer = $this->getD2EM()->getRepository( '\\Entities\\Customer' )->find( $this->getParam( 'custid' ) );

        if( !isset( $peer ) || !$peer )
        {
            echo "ERR:Could not find peer's information in the database. Please contact support.";
            die;
        }

        return $peer;
    }


    /**
     * Utility function to load a PeeringManager entity and initialise one if not found
     *
     * @return \Entities\PeeringManager
     */
    private function _loadPeeringManagerEntry( $cust, $peer )
    {
        // get this customer/peer peering manager table entry
        $pm = $this->getD2EM()->getRepository( '\\Entities\\PeeringManager' )->findOneBy(
            [ 'Customer' => $cust, 'Peer' => $peer ]
        );

        if( !$pm )
        {
            $pm = new \Entities\PeeringManager();
            $pm->setCustomer( $cust );
            $pm->setPeer( $peer );
            $pm->setCreated( new DateTime() );
            $pm->setPeered( false );
            $pm->setRejected( false );
            $pm->setNotes( '' );
            $this->getD2EM()->persist( $pm );
            $this->getD2EM()->flush();
        }

        return $pm;
    }

}
