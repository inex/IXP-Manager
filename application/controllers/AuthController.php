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


/*
 *
 *
 * http://www.inex.ie/
 * (c) Internet Neutral Exchange Association Ltd
 */

class AuthController extends INEX_Controller_Action
{
    use OSS_Controller_Trait_Auth;

    /**
     * Return the appropriate login form for your application
     */
    protected function _getFormLogin()
    {
        return new INEX_Form_Auth_Login();
    }

    /**
     * Return the appropriate lost password form for your application
     */
    protected function _getFormLostPassword()
    {
        return new INEX_Form_Auth_LostPassword();
    }

    /**
     * Return the appropriate reset password form for your application
     */
    protected function _getFormResetPassword()
    {
        return new INEX_Form_Auth_ResetPassword();
    }
    
    
    public function forgottenUsernameAction()
    {
        if( $this->getRequest()->getParam( 'fpsubmitted', false ) )
        {
            $email = stripslashes( trim( $this->_getParam( 'email' ) ) );
            // does the email exist?
            $users = Doctrine_Core::getTable( 'User' )->findByEmail( $email, Doctrine_Core::HYDRATE_ARRAY );

            if( count( $users ) )
            {
                $this->view->users  = $users;
                
                try
                {
                    $mail = new Zend_Mail( );
                    $mail->setBodyText( $this->view->render( 'auth/email/forgotten-username.tpl' ) )
                        ->setFrom( $this->config['identity']['email'], $this->config['identity']['name'] )
                         ->addTo( $users[0]['email'] )
                         ->setSubject( $this->config['identity']['ixp']['fullname'] . ' :: Username(s) Reminder' )
                         ->send();

                    $this->view->message = new INEX_Message(
                    	'We have sent you an email with further instructions.',
                        INEX_Message::MESSAGE_TYPE_SUCCESS
                    );

                    return $this->_forward( 'login' );
                }
                catch( Zend_Exception $e ) {
                }
            }
            else
            {
                $this->view->message = new INEX_Message( 'The email address ' . $email . ' does not exist', 'error' );
            }
        }

        $this->view->display( 'auth/forgotten-username.tpl' );
    }
    
    
    /**
     * Create a Drupal login button for admin users
     */
    protected function drupalLoginAction()
    {
        // let's be clear - you have to be an INEX member to access this!
        if( $this->identity['user']['privs'] == User::AUTH_SUPERUSER )
            $this->view->display( 'auth/drupal-login.tpl' );
        else
            $this->_forward( 'index', 'dashboard' );
    }

    /**
     * Switch the logged in user to another.
     *
     * Allows administrators to switch to another user and operate as them temporarily.
     */
    public function switchAction()
    {
        // only super admins can switch user!
        if( $this->user['privs'] != User::AUTH_SUPERUSER )
        {
            $this->getLogger()->notice( 'User ' . $this->user['username'] . ' tried to switch to user with ID '
                . $this->_request->getParam( 'id', '[unknown]' ) );
            $this->session->message = new INEX_Message(
                'You are not allowed to switch users! This attempt has been logged and the administrators notified.',
                INEX_Message::MESSAGE_TYPE_ERROR
            );

            if( $this->user['privs'] == User::AUTH_CUSTADMIN )
            {
                $this->_redirect( 'cust-admin/users' );
            }
            else
            {
                $this->_redirect( 'dashboard' );
            }
        }

        // store the fact that we're switching in the session
        $this->session->switched_user_from = $this->user['id'];

        // does the requested user exist
        $nu = Doctrine_Core::getTable( 'User' )->find( $this->_request->getParam( 'id', false ) );

        if( !$nu )
        {
            $this->session->message = new INEX_Message(
                'The requested user does not exist',
                INEX_Message::MESSAGE_TYPE_ERROR
            );

            $this->_redirect( 'user' );
        }

        // easiest way to switch users is to just re-autenticate as the new one
        // This maintains consistancy with Zend_Auth and future changes
        $result = $this->_reauthenticate( $nu );

        if( $result->getCode() == Zend_Auth_Result::SUCCESS )
        {
            $this->getLogger()->notice( 'User ' . $this->user['username'] . ' has switched to user '
                . $nu['username'] );

            $this->session->message = new INEX_Message(
                "You are now logged in as {$nu['username']}.", INEX_Message::MESSAGE_TYPE_SUCCESS
            );
        }
        else
        {
            $this->getLogger()->notice( 'User ' . $this->user['username'] . ' has failed to switch to user '
                . $nu['username'] );

            $this->session->message = new INEX_Message(
                "Error: Could not switch user.", INEX_Message::MESSAGE_TYPE_ERROR
            );

            $this->_redirect( 'user' );
        }

        if( $nu['privs'] == User::AUTH_CUSTADMIN )
            $this->_redirect( 'cust-admin/users' );
        else
            $this->_redirect( 'dashboard' );
    }

    /**
     * Switch back to the original user when switched to another.
     *
     * Allows administrators to switch back from another user who they operated as them temporarily.
     */
    public function switchBackAction()
    {
        // are we really operating as another?
        if( !isset( $this->session->switched_user_from ) or !$this->session->switched_user_from )
        {
            $this->session->message = new INEX_Message(
                'You are not currently logged in as another user. You are: ' . $this->user['username'],
                INEX_Message::MESSAGE_TYPE_ERROR
            );

            if( $this->user['privs'] == User::AUTH_SUPERUSER )
            {
                $this->_redirect( 'user' );
            }
            else if( $this->user['privs'] == User::AUTH_CUSTADMIN )
            {
                $this->_redirect( 'cust-admin/users' );
            }
            else
            {
                $this->_redirect( 'dashboard' );
            }
        }

        // record current user customer ID
        $custid = $this->getUser()->custid;
        
        // does the original user exist
        $ou = Doctrine_Core::getTable( 'User' )->find( $this->session->switched_user_from );

        if( !$ou )
            die( 'The user you are trying to switch back to no longer exists!!' );

        // easiest way to switch users is to just re-autenticate as the new one
        // This maintains consistancy with Zend_Auth and future changes
        $result = $this->_reauthenticate( $ou );

        if( $result->getCode() == Zend_Auth_Result::SUCCESS )
        {
            $this->getLogger()->info( 'User ' . $ou['username'] . ' has switched back from user '
                . $this->user['username'] );

            $this->session->message = new INEX_Message(
                "You are now logged in as {$ou['username']}.", INEX_Message::MESSAGE_TYPE_SUCCESS
            );
        }
        else
            die( 'Could not switch back!!' );

        unset( $this->session->switched_user_from );
        $this->_redirect( 'customer/dashboard/id/' . $custid );
    }

    /**
     * A simple private function to reauthenticate to a given user.
     *
     * @param User $user A Doctrine user object
     */
    private function _reauthenticate( $user )
    {
        $auth = Zend_Auth::getInstance();

        $authAdapter = new INEX_Auth_DoctrineAdapter(
            $user['username'], $user['password']
        );

        return $auth->authenticate( $authAdapter );
    }

}

?>
