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


class ProfileController extends INEX_Controller_Action
{
    /**
     *
     * @var INEX_Form_ProfilePassword
     */
    protected $_passwordForm;

    /**
     *
     * @var INEX_Form_Profile
     */
    protected $_profileForm;

    public function init()
    {
        // Show the users details (if logged in)
        if( !$this->auth->hasIdentity() )
            $this->_forward( 'index', 'auth' );

        $this->_passwordForm = new INEX_Form_ProfilePassword();
        $this->_passwordForm->setAction(
            Zend_Controller_Front::getInstance()->getBaseUrl()
            . '/' . $this->getRequest()->getParam( 'controller' )
            . '/change-password'
        );

        $this->_profileForm = new INEX_Form_Profile();
        $this->_profileForm->getElement( 'username' )->setValue( $this->user['username'] );
        $this->_profileForm->getElement( 'email' )->setValue( $this->user['email'] );
        $this->_profileForm->getElement( 'mobile' )->setValue( $this->user['authorisedMobile'] );
        $this->_profileForm->setAction(
            Zend_Controller_Front::getInstance()->getBaseUrl()
            . '/' . $this->getRequest()->getParam( 'controller' )
            . '/change-profile'
        );

    }

    public function indexAction()
    {
        $this->view->profileForm  = $this->_profileForm->render( $this->view );
        $this->view->passwordForm = $this->_passwordForm->render( $this->view );
        $this->view->display( 'profile/index.tpl' );
    }


    /**
     * Action to allow a user to change their profile
     *
     */
    public function changeProfileAction()
    {
        if( $this->_profileForm->isValid( $_POST ) )
        {
            // update the user
            $this->user['authorisedMobile'] = $this->_profileForm->getValue( 'mobile' );
            $this->user['email']            = $this->_profileForm->getValue( 'email' );

            try
            {
                $this->user->save();
            }
            catch( Doctrine_Exception $e )
            {
                $this->getLogger()->log( 'Doctrine save() error: ' . $e->getMessage() . ' in Profile/ChangePassword',
                    Zend_Log::CRIT
                );
                $this->view->message = new INEX_Message( 'Internal Error: Your profile could not be changed', 'error' );
                return( $this->indexAction() );
            }

            $this->view->message = new INEX_Message( 'Your profile has been changed', 'success' );

        }

        $this->indexAction();
    }


    /**
     * Action to allow a user to change their password
     *
     */
    public function changePasswordAction()
    {
        if( $this->_passwordForm->isValid( $_POST ) )
        {
            // let's do some suplementary checks
            if( $this->_passwordForm->getValue( 'password1' ) != $this->_passwordForm->getValue( 'password2' ) )
            {
                $this->_passwordForm->getElement( 'password2' )->addError(
                	'Your passwords do not match'
                );
                return( $this->indexAction() );
            }

            if( $this->_passwordForm->getValue( 'oldpassword' ) != $this->user->password )
            {
                $this->_passwordForm->getElement( 'oldpassword' )->addError(
                    'You have entered an incorrect current password'
                );
                return( $this->indexAction() );
            }

            // update the users password
            $this->user['password'] = $this->_passwordForm->getValue( 'password1' );

            $this->_passwordForm->reset();

            try
            {
                $this->user->save();
            }
            catch( Doctrine_Exception $e )
            {
                $this->getLogger()->log( 'Doctrine save() error: ' . $e->getMessage() . ' in Profile/ChangePassword',
                    Zend_Log::CRIT
                );
                $this->view->message = new INEX_Message( 'Internal Error: Your password could not be changed', 'error' );
                return( $this->indexAction() );
            }

            $this->view->message = new INEX_Message( 'Your password has been changed', 'success' );

        }

        $this->indexAction();
    }
}

