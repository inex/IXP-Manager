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

class CustAdminController extends INEX_Controller_Action
{


    public function preDispatch()
    {
        // let's get the user's details sorted before everything else
        if( !$this->identity )
            $this->_redirect( 'auth/login' );
        else if( $this->user->privs != User::AUTH_CUSTADMIN )
	    {
	        $this->view->message = new INEX_Message(
	            "You must be a customer administrator to access this page. This attempt to access private and "
	            . "secure sections of the site has been recorded and our administrators alerted.",
	            INEX_Message::MESSAGE_TYPE_ERROR
	        );

	        $this->getLogger()->alert( $this->user->username . " tried to access dashboard/users without sufficient permissions" );

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
        $this->_forward( 'users' );
    }

    /**
     * Allow a CUSTADMIN to manage their users
     */
    public function usersAction()
    {
        if( isset( $this->session->custadminInstructions ) )
            $this->view->skipInstructions = true;
        else
        {
            $this->session->custadminInstructions = true;
            $this->view->skipInstructions = false;
        }

        // find the user's users
        $this->view->users = Doctrine_Query::create()
            ->from( 'user' )
            ->where( 'custid = ?', $this->user->custid )
            ->andWhere( 'privs = ?', User::AUTH_CUSTUSER )
            ->orderBy( 'username ASC' )
            ->execute();

        $this->view->display( 'cust-admin/list.tpl' );
    }

    public function editUserAction()
    {
        $this->_forward( 'add-user' );
    }

    public function addUserAction()
    {
        $u = new User();

        // is this an attempt to edit?
        if( $this->getRequest()->getParam( 'id' ) !== NULL && is_numeric( $this->getRequest()->getParam( 'id' ) ) )
        {
            $isEdit = true;

	        // load the user and see if it exists
	        if( !( $u = Doctrine::getTable( 'User' )->find( $this->getRequest()->getParam( 'id' ) ) ) )
	        {
	            $this->view->message = new INEX_Message( 'There is no such user in our database', INEX_Message::MESSAGE_TYPE_ERROR );
	            return( $this->_forward( 'users' ) );
	        }

	        // now is the current CUSTADMIN user entitled to edit the specified user?
	        if( $u->custid != $this->user->custid )
	        {
	            $this->getLogger()->alert( "In cust-admin/toggle-enabled, user ($this->user->username} tried to illegally edit {$u->username}!" );
	            $this->view->message = new INEX_Message( 'You have tried to edit a user that is not yours. Our administrators have been alerted and will act accordingly.', INEX_Message::MESSAGE_TYPE_ALERT );
	            return( $this->_forward( 'users' ) );
	        }

            $form = new INEX_Form_User( null, $isEdit,
                Zend_Controller_Front::getInstance()->getBaseUrl() . "/cust-admin",
                true
            );

            if( $this->_request->getParam( 'commit', null ) != 1 )
            {
	            $form->getElement( 'username'         )->setValue( $u->username );
	            $form->getElement( 'email'            )->setValue( $u->email );
	            $form->getElement( 'authorisedMobile' )->setValue( $u->authorisedMobile );
	            if( $u->disabled )
	                $form->getElement( 'disabled' )->setChecked( true );
            }

            $form->setAction( Zend_Controller_Front::getInstance()->getBaseUrl() . "/cust-admin/edit-user/id/" . $u->id );
            $form->getElement( 'submit' )->setLabel( 'Save Changes' );
        }
        else
        {
            $isEdit = false;

            $form = new INEX_Form_User( null, $isEdit,
                Zend_Controller_Front::getInstance()->getBaseUrl() . "/cust-admin",
                true
            );
            $form->setAction( Zend_Controller_Front::getInstance()->getBaseUrl() . "/cust-admin/add-user" );
        }

        $form->removeElement( 'privs' );
        $form->removeElement( 'custid' );
        $form->removeElement( 'password' );

        $this->view->isEdit = $isEdit;

        if( $this->_request->getParam( 'commit', null ) == 1 && $form->isValid( $_POST ) )
        {
            do
            {
                try
                {
                    // is the username unique?
                    if( !$isEdit && Doctrine::getTable( 'User' )->findOneByUsername( $form->getValue( 'username' ) ) )
                    {
                        $form->getElement( 'username' )->addError( 'This username is not available' );
                        break;
                    }

                    if( !$isEdit )
                    {
                        // hard code some values to prevent injection from malicious users
                        $u->custid  = $this->user['custid'];
	                    $u->privs   = User::AUTH_CUSTUSER;
	                    $u->creator = $this->user['username'];
	                    $u->id      = null;
	                    $u->created = date( 'Y-m-d H:i:s' );
	                    $u->Parent  = $this->user;

                        $u->username         = $form->getValue( 'username' );
                        $u->password         = UserTable::createRandomPassword( 8 );

                        $c = new Contact();
                        $c->custid = $this->user['custid'];
                        $c->name   = $form->getValue( 'name' );
                        $c->email  = $form->getValue( 'email' );
                        $c->mobile = $form->getValue( 'authorisedMobile' );
                        $c->creator = $this->user['username'];
                        $c->created = date( 'Y-m-d H:i:s' );
                        $c->save();
                    }

                    $u->email            = $form->getValue( 'email' );
                    $u->authorisedMobile = $form->getValue( 'authorisedMobile' );
                    $u->disabled         = $form->getElement( 'disabled' )->isChecked() ? 1 : 0;
                    $u->save();

                    if( !$isEdit )
                    {
	                    $mail = new Zend_Mail();
	                    $mail->setFrom( $this->config['identity']['email'], $this->config['identity']['name'] )
	                         ->setSubject( 'Access details for ' . $this->config['identity']['ixp']['fullname'] )
	                         ->setType( Zend_Mime::MULTIPART_RELATED )
	                         ->addTo( $u->email );

	                    $ixp_logo = $mail->createAttachment(
	                        file_get_contents( $this->config['identity']['ixp']['logo'] ),
	                        "image/jpg",
	                        Zend_Mime::DISPOSITION_INLINE,
	                        Zend_Mime::ENCODING_BASE64,
	                        'ixp-logo.jpg'
	                    );
	                    $ixp_logo->id = 'ixp_logo';

	                    $this->view->u = $u;
	                    $mail->setBodyHtml( $this->view->render( 'cust-admin/mail/welcome.tpl' ) );

	                    $mail_sent = true;

	                    try { $mail->send(); }
	                    catch( Zend_Mail_Exception $e ) { $mail_sent = false; }

	                    $this->getLogger()->notice( "New user created by {$this->user->username}: {$u->username}/{$u->email}" );

	                    if( $mail_sent )
	                    {
	                        $this->session->message = new INEX_Message(
	                            "New user {$u->username} created. A welcome email has been sent to the new user.",
	                            INEX_Message::MESSAGE_TYPE_SUCCESS
	                        );
	                    }
	                    else
	                    {
	                        $this->session->message = new INEX_Message(
	                            "New user {$u->username} created. A welcome email could not be sent.",
	                            INEX_Message::MESSAGE_TYPE_ALERT
	                        );
	                    }

                    }
                    else
                    {
                        // $isEdit
                        $this->getLogger()->info( "cust-admin/edit-user User {$u->username} edited by {$this->user->username}" );

                        $this->session->message = new INEX_Message(
                            "User {$u->username} edited.",
                            INEX_Message::MESSAGE_TYPE_SUCCESS
                        );
                    }

                    return( $this->_redirect( 'cust-admin/users' ) );
                }
                catch( Exception $e )
                {
                    Zend_Registry::set( 'exception', $e );
                    return( $this->_forward( 'error', 'error' ) );
                }
            }while( false );
        }

        $this->view->form   = $form->render( $this->view );
        $this->view->u = $u;

        $this->view->display( 'cust-admin' . DIRECTORY_SEPARATOR . 'add-edit.tpl' );
    }


    public function toggleEnabledAction()
    {
        // load the user and see if it exists
        if( !( $u = Doctrine::getTable( 'User' )->find( $this->getRequest()->getParam( 'id' ) ) ) )
        {
            $this->view->message = new INEX_Message( 'There is no such user in our database', INEX_Message::MESSAGE_TYPE_ERROR );
            return( $this->_forward( 'users' ) );
        }

        // now is the current CUSTADMIN user entitled to edit the specified user?
        if( $u->custid != $this->user->custid )
        {
            $this->getLogger()->alert( "In cust-admin/toggle-enabled, user ($this->user->username} tried to illegally edit {$u->username}!" );
            $this->view->message = new INEX_Message( 'You have tried to edit a user that is not yours. Our administrators have been alerted and will act accordingly.', INEX_Message::MESSAGE_TYPE_ALERT );
            return( $this->_forward( 'users' ) );
        }

        $u->disabled = ( $u->disabled + 1 ) % 2;
        $u->save();

        if( $u->disabled )
            $this->session->message = new INEX_Message( "You have disabled user {$u->username}.", INEX_Message::MESSAGE_TYPE_SUCCESS );
        else
            $this->session->message = new INEX_Message( "You have enabled user {$u->username}.", INEX_Message::MESSAGE_TYPE_SUCCESS );

        $this->getLogger()->info( "cust-admin/toggle-enabled: {$this->user->username} set disbaled flag of {$u->username} to {$u->disabled}" );

        $this->_redirect( 'cust-admin/users' );
    }

}

