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
 * Controller: User profile
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */

class ProfileController extends IXP_Controller_AuthRequiredAction
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
        return new IXP_Form_ChangePassword();
    }
    
    /**
     * Return the appropriate change profile form for your application
     */
    protected function _getFormProfile()
    {
        $this->view->groups        = $this->getD2EM()->getRepository( "\\Entities\\ContactGroup" )->getGroupNamesTypeArray();
        $this->view->contactGroups = $this->getD2R( "\\Entities\\ContactGroup" )->getGroupNamesTypeArray( false, $this->getUser()->getContact()->getId() );
        $pf = new IXP_Form_Profile();
        
        $pf->assignEntityToForm( $this->getUser()->getContact(), $this );

        if( !in_array( $this->getUser()->getPrivs(), [ \Entities\User::AUTH_CUSTADMIN, \Entities\User::AUTH_SUPERUSER ] ) )
            $pf->getElement( 'email')->setAttrib( 'readonly', 'readonly' );
        
        return $pf;
    }
    
    /**
     * Return the appropriate change profile form for your application
     */
    protected function _getFormCustomerNotes()
    {
        $cnf = new IXP_Form_Profile_CustomerNotes();
        
        if( $this->getUser()->getPreference( 'customer-notes.notify' ) )
            $cnf->getElement( 'notify' )->setValue( $this->getUser()->getPreference( 'customer-notes.notify' ) );
        
        return $cnf;
    }
    
    
    
    public function init()
    {
        $this->_initMailingListSubs();
    }

    public function indexAction()
    {
        if( !isset( $this->view->profileForm ) )
            $this->view->profileForm = $this->_getFormProfile();
        
        if( !isset( $this->view->passwordForm ) )
            $this->view->passwordForm = $this->_getFormChangePassword();
            
        if( $this->getUser()->getPrivs() == \Entities\User::AUTH_SUPERUSER && !isset( $this->view->customerNotesForm ) )
            $this->view->customerNotesForm = $this->_getFormCustomerNotes();
    }

    protected function changePasswordPostFlush()
    {
        $this->clearUserFromCache();
    }
    
    /**
     * Action to allow a user to change their profile
     *
     */
    public function changeProfileAction()
    {
        $this->view->profileForm = $form = $this->_getFormProfile();
        
        if( $this->getRequest()->isPost() && $form->isValid( $_POST ) )
        {
            if( !OSS_Auth_Password::verify( $form->getValue( 'current_password' ), $this->getUser()->getPassword(), $this->_options['resources']['auth']['oss'] ) )
            {
                $form->getElement( 'current_password' )->addError(
                    'Invalid current password'
                );
                return $this->forward( 'index' );
            }
            
            // update the users profile
            $form->assignFormToEntity( $this->getUser()->getContact(), $this, true );
            $this->getUser()->getContact()->setLastUpdated( new DateTime() );
            $this->getUser()->getContact()->setLastUpdatedBy( $this->getUser()->getId() );
            
            if( !in_array( $this->getUser()->getPrivs(), [ \Entities\User::AUTH_CUSTADMIN, \Entities\User::AUTH_SUPERUSER ] ) )
                $this->getUser()->setEmail( $form->getValue( 'email' ) );
            
            $this->getUser()->setLastUpdated( new DateTime() );
            $this->getUser()->setLastUpdatedBy( $this->getUser()->getId() );
            $this->getD2EM()->flush();
            $this->clearUserFromCache();
            
            $this->getLogger()->info( "User {$this->getUser()->getUsername()} updated own profile" );
            $this->addMessage( _( 'Your profile has been changed.' ), OSS_Message::SUCCESS );
            $this->redirect( 'profile/index' );
        }
    
        $this->forward( 'index' );
    }
    
    public function updateCustomerNotesAction()
    {
        $this->view->customerNotesForm = $form = $this->_getFormCustomerNotes();
        
        if( $this->getRequest()->isPost() && $form->isValid( $_POST ) )
        {
            if( $form->getValue( 'notify' ) != 'default' )
                $this->getUser()->setPreference( 'customer-notes.notify', $form->getValue( 'notify' ) );
            else
                $this->getUser()->deletePreference( 'customer-notes.notify' );
                
            $this->getD2EM()->flush();
            
            $this->addMessage( _( 'Your notification preference has been updated.' ), OSS_Message::SUCCESS );
            $this->redirect( 'profile/index' );
        }
    
        $this->forward( 'index' );
    }
    
    public function updateMailingListsAction()
    {
        // need to capture all users with the given email
        $users = $this->getD2EM()->getRepository( '\\Entities\\User' )->findBy( [ 'email' => $this->getUser()->getEmail() ] );
        
        foreach( $this->_options['mailinglists'] as $name => $ml )
        {
            if( isset( $_POST["ml_{$name}"] ) && $_POST["ml_{$name}"] )
                foreach( $users as $u )
                    $u->setPreference( "mailinglist.{$name}.subscribed", 1 );
            else
                foreach( $users as $u )
                    $u->setPreference( "mailinglist.{$name}.subscribed", 0 );
        }
        
        $this->getD2EM()->flush();
        $this->addMessage( 'Your mailing list subscriptions have been updated and will take effect within 12 hours.', OSS_Message::SUCCESS );
        $this->redirect( 'profile/index' );
    }
    
    private function _initMailingListSubs()
    {
        // are we using mailing lists?
        if( !isset( $this->_options['mailinglist']['enabled'] ) || !$this->_options['mailinglist']['enabled'] )
            return;
        
        $mlsubs = [];
        
        foreach( $this->_options['mailinglists'] as $name => $ml )
            $mlsubs[$name] = $this->getUser()->getPreference( "mailinglist.{$name}.subscribed" );
        
        $this->view->mlsubs = $mlsubs;
    }
}

