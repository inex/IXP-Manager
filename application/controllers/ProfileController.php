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


class ProfileController extends INEX_Controller_AuthRequiredAction
{
    use OSS_Controller_Trait_Profile;
    
    /**
     * Users mailing list subs as set via init() -> _initMailingListSubs()
     *
     * @var array
     */
    protected $_mailinglists;
    

    /**
     * Return the appropriate change password form for your application
     */
    protected function _getFormChangePassword()
    {
        return new INEX_Form_ChangePassword();
    }
    
    
    public function init()
    {

        /*
        $this->_profileForm = new INEX_Form_Profile();
        $this->_profileForm->getElement( 'username' )->setValue( $this->user['username'] );
        $this->_profileForm->getElement( 'email' )->setValue( $this->user['email'] );
        $this->_profileForm->getElement( 'mobile' )->setValue( $this->user['authorisedMobile'] );
        $this->_profileForm->setAction(
            Zend_Controller_Front::getInstance()->getBaseUrl()
            . '/' . $this->getRequest()->getParam( 'controller' )
            . '/change-profile'
        );
        */
        // mailing list management
        $this->_initMailingListSubs();
    }

    public function indexAction()
    {
        $this->view->registerClass( 'CUSTOMER', '\\Entities\\Customer' );
        $this->view->profileForm  = new INEX_Form_Profile();
        
        if( !isset( $this->view->passwordForm ) )
            $this->view->passwordForm = $this->_getFormChangePassword();
        
        $this->view->mailinglists = $this->_mailinglists;
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
            // $this->user['email']            = $this->_profileForm->getValue( 'email' );

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


    
    public function updateMailingListsAction()
    {
        // need to capture all users with the given email
        $users = Doctrine::getTable( 'User' )->findByEmail( $this->getUser()->email );
        
        foreach( $this->_mailinglists as $name => $ml )
        {
            if( isset( $_POST["ml_{$name}"] ) && $_POST["ml_{$name}"] )
            {
                $this->_mailinglists[$name]['subscribed'] = 1;
                foreach( $users as $u )
                    $u->setPreference( "mailinglist.{$name}.subscribed", 1 );
            }
            else
            {
                $this->_mailinglists[$name]['subscribed'] = 0;
                foreach( $users as $u )
                    $u->setPreference( "mailinglist.{$name}.subscribed", 0 );
            }
        }
        
        $this->view->message = new INEX_Message( 'Your mailing list subscriptions have been updated and will take effect within 12 hours.', 'success' );
        
        $this->_forward( 'index' );
    }
    
    private function _initMailingListSubs()
    {
        // are we using mailing lists?
        if( !isset( $this->config['mailinglist']['enabled'] ) || !$this->config['mailinglist']['enabled'] )
        {
            $this->view->mailinglist_enabled = false;
            return;
        }
        
        $this->view->mailinglist_enabled = true;
        
        if( !isset( $this->config['mailinglists'] ) )
            return;
        
        $this->_mailinglists = $this->config['mailinglists'];
        
        foreach( $this->_mailinglists as $name => $ml )
        {
            $this->_mailinglists[$name]['subscribed'] = $this->getUser()->getPreference( "mailinglist.{$name}.subscribed" );
        }
    }
}

