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

    public function logoutAction()
    {
        $this->view->clearVars();
        $this->view->config = $this->config;
       
        $auth = Zend_Auth::getInstance();

        if( $auth->hasIdentity() )
        {
            $auth->clearIdentity();
            $this->view->message = new INEX_Message( 'You have been logged out', INEX_Message::MESSAGE_TYPE_INFO );
        }

        if( $this->_request->getParam( 'auto', 0 ) == 1 )
            $this->view->message = new INEX_Message( 'To protect your account and its information, '
                . 'you have been logged out automatically.', INEX_Message::MESSAGE_TYPE_ALERT );


        Zend_Session::destroy( true, true );
        $this->session = null;
        $this->view->display( 'auth/login.tpl' );
    }


    public function forgottenPasswordAction()
    {
        if( $this->getRequest()->getParam( 'fpsubmitted', false ) )
        {
            // does the username exist?
            if( $user = Doctrine_Core::getTable( 'User' )->findOneByUsername( $this->getRequest()->getParam( 'loginusername' ) ) )
            {
                // generate a password change token
                $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
                $token = substr( str_shuffle( $chars ), 0, 32 );
                
                $user->setPreference( 'pwreset.token', $token );
                $user->setPreference( 'pwreset.timeout', mktime() + 86400 );
                
                $this->view->user  = $user;
                $this->view->token = $token;
                
                try
                {
                    $mail = new Zend_Mail( );
                    $mail->setBodyText( $this->view->render( 'auth/email/password-reset.tpl' ) )
                        ->setFrom( $this->config['identity']['email'], $this->config['identity']['name'] )
                         ->addTo( $user['email'] )
                         ->setSubject( $this->config['identity']['ixp']['fullname'] . ' :: Password Reset' )
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
                $this->view->message = new INEX_Message( 'That username does not exist', 'error' );
            }
        }

        $this->view->display( 'auth/forgotten-password.tpl' );
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
    
    public function resetPasswordAction()
    {
        $this->view->username = trim( stripslashes( $this->_getParam( 'username', '' ) ) );
        $this->view->token    = trim( stripslashes( $this->_getParam( 'token', '' ) ) );
        
        if( $this->_getParam( 'fpsubmitted', false ) )
        {
            $pass1 = trim( stripslashes( $this->_getParam( 'pass1', '' ) ) );
            $pass2 = trim( stripslashes( $this->_getParam( 'pass2', '' ) ) );
            
            do{
                
                if( $this->view->username == '' || $this->view->token == '' || $pass1 == '' || $pass2 == '' )
                {
                    $this->view->message = new INEX_Message(
                        	'Please enter all details below!', INEX_Message::MESSAGE_TYPE_ERROR
                    );
                    break;
                }
                
                // is the username and token valid?
                if( !( $user = Doctrine_Core::getTable( 'User' )->findOneByUsername( $this->view->username ) )
                    || $user->getPreference( 'pwreset.token' ) != $this->view->token
                )
                {
                    $this->view->message = new INEX_Message(
                    	'Invalid username or token!', INEX_Message::MESSAGE_TYPE_ERROR
                    );
                    break;
                }
                
                // so, have valid user and matching token. Is the token in date?
                if( mktime() - $user->getPreference( 'pwreset.timeout' ) > 0 )
                {
                    $this->session->message = new INEX_Message(
                    	'Reset tokens are only valid for 24 hours. Yours has expired. Please generate a new token below.',
                        INEX_Message::MESSAGE_TYPE_ERROR
                    );

                    return $this->_redirect( 'auth/forgotten-password' );
                }
                
                // do the passwords live up to requirements?
                if(
                    !Zend_Validate::is( $pass1, 'StringLength', array( 8, 30 ) )
                    || !Zend_Validate::is( $pass1, 'Regex', array( '/^[a-zA-Z0-9\!\£\$\%\^\&\*\(\)\-\=\_\+\{\}\[\]\;\'\#\:\@\~\,\.\/\<\>\?\|]+$/' ) )
                )
                {
                    $this->view->message = new INEX_Message(
                    	'Password must be between 8 and 30 characters in length and cannot contain the " character', INEX_Message::MESSAGE_TYPE_ERROR
                    );
                    break;
                }
                
                // do the passwords match?
                if( $pass1 !== $pass2 )
                {
                    $this->view->message = new INEX_Message(
                    	'Your new password and the confirmation password do not match', INEX_Message::MESSAGE_TYPE_ERROR
                    );
                    break;
                }
                
                // all okay, change password!
                $user->deletePreference( 'pwreset.token' );
                $user->deletePreference( 'pwreset.timeout' );
                $user['password'] = $pass1;
                $user->save();
                
                // send a confirmation email
                try
                {
                    $mail = new Zend_Mail( );
                    $mail->setBodyText( $this->view->render( 'auth/email/password-reset-notice.tpl' ) )
                        ->setFrom( $this->config['identity']['email'], $this->config['identity']['name'] )
                         ->addTo( $user['email'] )
                         ->setSubject( $this->config['identity']['ixp']['fullname'] . ' :: Password Reset Confirmation' )
                         ->send();

                    $this->view->message = new INEX_Message(
                    	'Your password has been reset. You may now login below.',
                        INEX_Message::MESSAGE_TYPE_SUCCESS
                    );

                    return $this->_forward( 'login' );
                }
                catch( Zend_Exception $e ) {
                }
                
            }while( false );
        }
        
        $this->view->display( 'auth/reset-password.tpl' );
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
