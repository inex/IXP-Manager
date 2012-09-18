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
 * Controller: Authentication controller
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   INEX
 * @package    INEX_Controller
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
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
    
    /**
     * Return the appropriate lost username form for your application
     */
    protected function _getFormLostUsername()
    {
        return new INEX_Form_Auth_LostUsername();
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
     * This function is called just before `switchUserAction()` processes anything.
     *
     * @return bool True unless you want the switch to fail.
     */
    protected function _switchUserPreCheck()
    {
        if( $this->getUser()->getPrivs() != \Entities\User::AUTH_SUPERUSER )
        {
            $this->getLogger()->notice( 'User ' . $this->getUser()->getUsername() . ' illegally tried to switch to user with ID '
                . $this->_getParam( 'id', '[unknown]' )
            );
            
            $this->addMessage(
                'You are not allowed to switch users! This attempt has been logged and the administrators notified.',
                OSS_Message::ERROR
            );
            
            $this->redirectAndEnsureDie( '' );
        }
        
        return true;
    }

    /**
     * This function is called after `switchUserAction()` loads the requested user object.
     *
     * @param \Entities\User $nuser The user to switch to
     * @return bool True unless you want the switch to fail.
     */
    protected function _switchUserCheck( $nuser )
    {
        return true;
    }
    
    /**
     * This function is called just before `switchUserBackAction()` actually switches
     * the user back.
     *
     * @param \Entities\User $subUser The current user we have switched to (substituted to)
     * @param \Entities\User $origUser The original user that we switched from
     * @return bool|array False if you want the switch back to fail.
     */
    protected function _switchUserBackCheck( $subUser, $origUser )
    {
        // record current user customer ID
        $custid = $this->getUser()->custid;
        
        $params['url'] = 'customer/dashboard/id/' . $subUser->getCustomer()->getId();
        
        return $params;
    }
    
}
