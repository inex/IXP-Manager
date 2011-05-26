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
 * ProvisionController
 *
 * @author
 * @version
 */

class ProvisionController extends INEX_Controller_Action
{

    /**
     * Provision a new interface
     */
    public function interfaceAction()
    {
        $outstanding = Doctrine_Query::create()
            ->from( 'ProvisioningInterface pi' )
            ->where( 'complete = 0' )
            ->orderBy( 'created_at ASC' )
            ->execute( null, Doctrine_Core::HYDRATE_RECORD );

        $this->view->outstanding = $outstanding;
        $this->view->display( 'provision/interface/list.tpl' );
    }


    /**
     * Provision a new interface
     */
    public function interfaceOverviewAction()
    {
        if( $this->_getParam( 'new', false ) == 'yes' )
        {
            $progress = new ProvisioningInterface();

            $this->view->customers = CustTable::getAllNames();
        }
        else
        {
            if( $id = $this->_getParam( 'id', false ) )
                $this->session->provisioning_interface_active_id = $id;
            else
                $id = $this->session->provisioning_interface_active_id;

            if( !( $progress = Doctrine_Core::getTable( 'ProvisioningInterface' )->find( $id ) ) )
            {
                throw new INEX_Exception( 'ERROR: Missing interface provisioning task ID' );
            }
        }

        $this->view->progress = $progress;
        $this->view->display( 'provision/interface/overview.tpl' );
    }

    /**
     * First step in interface provisioning - choose a customer.
     *
     * This also creates the provision object.
     */
    public function interfaceChooseCustAction()
    {
        // get the id and make sure it's valid
        $cust = Doctrine_Core::getTable( 'Cust' )->find( $this->_getParam( 'cust', false ) );

        if( !$cust )
        {
            $this->view->message = new INEX_Message(
                'No or invalid customer specified.', INEX_Message::MESSAGE_TYPE_ERROR
            );
            $this->_setParam( 'new', 'yes' );
            return $this->_forward( 'interface-overview' );
        }

        // okay, we have a customer, create the provisioning object
        $progress = new ProvisioningInterface();
        $progress['Cust'] = $cust;
        $progress['created_by'] = $this->getUser()->id;
        $progress['created_at'] = date( 'Y-m-d H:i:s' );
        $progress->save();

        $this->session->provisioning_interface_active_id = $progress['id'];

        $this->session->message = new INEX_Message(
            "Provisioning process started and customer set. Please create a virtual interface.",
            INEX_Message::MESSAGE_TYPE_SUCCESS
        );

        $this->_redirect( 'provision/interface-overview/id/' . $progress['id'] );
    }

    /**
     * Second step in interface provisioning - virtual interface selected
     */
    public function interfaceVirtualInterfaceAction()
    {
        if( !( $progress = Doctrine_Core::getTable( 'ProvisioningInterface' )->find(
                $this->session->provisioning_interface_active_id ) ) )
        {
            throw new INEX_Exception( 'ERROR: Missing interface provisioning task ID' );
        }

        $progress['virtualinterface_id'] = $this->_getParam( 'objectid', 0 );
        $progress->save();

        $this->_redirect( 'provision/interface-overview/id/' . $progress['id'] );
    }

    /**
     * Third step in interface provisioning - physical interface selected
     */
    public function interfacePhysicalInterfaceAction()
    {
        if( !( $progress = Doctrine_Core::getTable( 'ProvisioningInterface' )->find(
                $this->session->provisioning_interface_active_id ) ) )
        {
            throw new INEX_Exception( 'ERROR: Missing interface provisioning task ID' );
        }

        $progress['physicalinterface_id'] = $this->_getParam( 'objectid', 0 );
        $progress->save();

        $this->_redirect( 'provision/interface-overview/id/' . $progress['id'] );
    }

    /**
     * Fourth step in interface provisioning - physical interface selected
     */
    public function interfaceVlanInterfaceAction()
    {
        if( !( $progress = Doctrine_Core::getTable( 'ProvisioningInterface' )->find(
                $this->session->provisioning_interface_active_id ) ) )
        {
            throw new INEX_Exception( 'ERROR: Missing interface provisioning task ID' );
        }

        $progress['vlaninterface_id'] = $this->_getParam( 'objectid', 0 );
        $progress->save();

        $this->_redirect( 'provision/interface-overview/id/' . $progress['id'] );
    }

    /**
     * Fifth step in interface provisioning - send email with details
     */
    public function interfaceSendMailAction()
    {
        if( !( $progress = Doctrine_Core::getTable( 'ProvisioningInterface' )->find(
                $this->session->provisioning_interface_active_id ) ) )
        {
            throw new INEX_Exception( 'ERROR: Missing interface provisioning task ID' );
        }

        $cancelLocation = $this->config['identity']['ixp']['url']
            . '/provision/interface-overview/id/' . $this->session->provisioning_interface_active_id;

        $form = new INEX_Form_Provision_InterfaceEmail( null, false, $cancelLocation );

        $form->getElement( 'to' )->setValue( $progress['Cust']['nocemail'] );

        $userEmails = array();
        foreach( $progress['Cust']['User'] as $user )
        {
            if( Zend_Validate::is( $user['email'], 'EmailAddress' ) )
                $userEmails[] = $user['email'];
        }

        $form->getElement( 'cc' )->setValue( implode( ',', $userEmails ) );

        $form->getElement( 'bcc' )->setValue( 'ops@inex.ie' );

        $form->getElement( 'subject' )->setValue( 'INEX Operations :: New Interface Details' );

        $this->view->networkInfo = Networkinfo::toStructuredArray();
        $this->view->progress    = $progress;

        $form->getElement( 'message' )->setValue(
            $this->view->render( 'provision/interface/mail/interface-details.tpl' )
        );


        // Process a submitted form if it passes initial validation
        if( $this->inexGetPost( 'commit' ) !== null && $form->isValid( $_POST ) )
        {
            $validForm = true;
            // Validate all e-mail addresses
            foreach( array( 'to', 'cc', 'bcc' ) as $recipient )
            {
                if( $form->getValue( $recipient ) != '' )
                {
                    foreach( explode( ',', $form->getElement( $recipient )->getValue() ) as $email )
                    {
                        if( !Zend_Validate::is( $email, 'EmailAddress' ) )
                        {
                            $form->getElement( $recipient )->addError( 'Invalid e-mail address: ' . $email );
                            $validForm = false;
                        }
                    }
                }
            }

            if( $validForm )
            {
                $mail = new Zend_Mail();
                $mail->setBodyText( $form->getValue( 'message' ) );
                $mail->setFrom( 'operations@inex.ie', 'INEX Operations' );
                $mail->setSubject( $form->getValue( 'subject' ) );

                foreach( array( 'To', 'Cc', 'Bcc' ) as $recipient )
                    if( $form->getValue( strtolower( $recipient ) ) != '' )
                        foreach( explode( ',', $form->getElement( strtolower( $recipient ) )->getValue() ) as $email )
                            if( Zend_Validate::is( $email, 'EmailAddress' ) )
                            {
                                $fn = "add$recipient";
                                $mail->$fn( $email );
                            }

                if( $mail->send() )
                {
                    $progress['mail_sent'] = 1;
                    $progress->save();
                    $this->logger->info( "Interface details email sent for {$progress['Cust']['name']}" );
                    $this->session->message = new INEX_Message( "Interface email successfully sent to {$progress['Cust']['name']}", INEX_Message::MESSAGE_TYPE_SUCCESS );
                    $this->_redirect( 'provision/interface-overview/id/' . $progress['id'] );
                    return true;
                }
                else
                {
                    $this->logger->err( "Could not sent welcome email for {$progress['Cust']['name']}: " . print_r( $mail, true ) );
                    $this->session->message = new INEX_Message( "Welcome email could not be sent to {$progress['Cust']['name']}. Please see logs for more verbose output.", INEX_Message::MESSAGE_TYPE_ERROR );
                }

            }
        }

        $this->view->form   = $form->render( $this->view );
        $this->view->display( 'provision/interface/send-mail.tpl' );

        //$this->_redirect( 'provision/interface-overview/id/' . $progress['id'] );
    }



    /**
     * Sixth step in interface provisioning - generate switch configuration
     */
    public function interfaceSwitchConfigAction()
    {
        if( !( $progress = Doctrine_Core::getTable( 'ProvisioningInterface' )->find(
                $this->session->provisioning_interface_active_id ) ) )
        {
            throw new INEX_Exception( 'ERROR: Missing interface provisioning task ID' );
        }

        if( !$progress['switch_config'] )
        {
            $progress['switch_config'] = 1;
            $progress->save();
        }

        $this->view->progress = $progress;
        $this->view->display( 'provision/interface/switch-config.tpl' );
    }


}

