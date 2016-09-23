<?php

use Entities\CustomerNotes;
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
 * Controller: Customer Notes
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CustomerNotesController extends IXP_Controller_AuthRequiredAction
{

    public function readAllAction()
    {
        $lastReads = $this->getUser()->getAssocPreference( 'customer-notes' )[0];
        foreach( $lastReads as $id => $data )
        {
            if( is_numeric( $id ) )
                $this->getUser()->deletePreference( "customer-notes.$id.last_read" );
        }
       
        $this->getUser()->setPreference( 'customer-notes.read_upto', time() );
        $this->getD2EM()->flush();

        $this->redirect( '/customer/unread-notes' );
    }

    public function ajaxAddAction()
    {
        $this->assertPrivilege( \Entities\User::AUTH_SUPERUSER, true );
        
        $f = new IXP_Form_Customer_Notes();
        $r = [ 'error' => true ];
        
        if( $f->isValid( $_POST ) )
        {
            // locate the customer
            $cust = $this->getD2EM()->getRepository( '\\Entities\\Customer' )->find( $f->getValue( 'custid' ) );

            // if we're editing, we need to find the note
            if( $f->getValue( 'noteid' ) )
            {
                $isEdit = true;
                $n = $this->getD2EM()->getRepository( '\\Entities\\CustomerNote' )->find( $f->getValue( 'noteid' ) );
                $old = clone( $n );
            }
            else
            {
                $isEdit = false;
                $n = new \Entities\CustomerNote();
                $old = false;
            }
            
            if( $cust && $n )
            {
                $n->setTitle( $f->getValue( 'title' ) );
                $n->setNote( $f->getValue( 'note' ) );
                $n->setPrivate( $f->getValue( 'public' ) == 'makePublic' ? false : true );
                $n->setUpdated( new DateTime() );
                
                if( !$isEdit )
                {
                    $n->setCreated( $n->getUpdated() );
                    $n->setCustomer( $cust );
                    $this->getD2EM()->persist( $n );
                }
                
                // update the user's notes last read so he won't be told his own is new
                $this->getUser()->setPreference( "customer-notes.{$this->getParam( 'custid' )}.last_read", mktime() );
                
                $this->getD2EM()->flush();

                if( !$old || $old->getTitle() != $n->getTitle() || $old->getNote() != $n->getNote() || $old->getPrivate() != $n->getPrivate() )
                    $this->_sendNotifications( $old , $n );
                
                $r[ 'error' ] = false;
                $r[ 'noteid' ] = $n->getId();
            }
            else
            {
                $r['error'] = "Invalid customer / note specified.";
                $this->getLogger()->alert( "[ID: {$this->getUser()->getId()}] AJAX Customer Note addition - invalid customer / note specified" );
            }
        }
        
        $this->_helper->json( $r );
    }

    public function ajaxGetAction()
    {
        $r = [ 'error' => true ];
        
        if( $note = $this->getD2EM()->getRepository( '\\Entities\\CustomerNote' )->find( $this->getParam( 'id' ) ) )
        {
            if( $this->getUser()->getPrivs() != \Entities\User::AUTH_SUPERUSER
                && ( $note->getCustomer() != $this->getCustomer() || $note->getPrivate() ) )
            {
                $this->getLogger()->alert(
                    "User {$this->getUser()->getUsername()} tried to access other / private note with ID {$note->getId()}"
                );
            }
            else
            {
                $r = $note->toArray();
                $r['created'] = $r['created']->format( 'Y-m-d H:i' );
                $r['error'] = false;
            }
        }
        
        $this->_helper->json( $r );
    }

    public function ajaxDeleteAction()
    {
        $this->assertPrivilege( \Entities\User::AUTH_SUPERUSER, true );
        
        $r = [ 'error' => true ];
        
        if( $note = $this->getD2EM()->getRepository( '\\Entities\\CustomerNote' )->find( $this->getParam( 'id' ) ) )
        {
            $old = clone( $note );
            $this->getD2EM()->remove( $note );
            $this->getD2EM()->flush();
            $this->_sendNotifications( $old, false );
            $r = [ 'error' => false ];
        }
        
        $this->_helper->json( $r );
    }
    
    public function ajaxPingAction()
    {
        if( $this->getUser()->getPrivs() == \Entities\User::AUTH_SUPERUSER )
            $custid = $this->getParam( 'custid' );
        else
            $custid = $this->getCustomer()->getId();
        
        // update the last read for this user / customer combination
        if( is_numeric( $custid ) )
        {
            $this->getUser()->setPreference( "customer-notes.{$custid}.last_read", mktime() );
            $this->getD2EM()->flush();
        }
    }
        
    public function ajaxNotifyToggleAction()
    {
        $this->assertPrivilege( \Entities\User::AUTH_SUPERUSER, true );
                
        if( $this->getParam( 'custid', false ) )
        {
            $id   = $this->getParam( 'custid' );
            $name = sprintf( "customer-notes.%d.notify", $id );
            $value = 'all';
        }
        else if( $this->getParam( 'id', false ) )
        {
            $id = $this->getParam( 'id' );
            $name = sprintf( "customer-notes.watching.%d", $id );
            $value = 1;
        }
        
        // Toggles customer notes notification preference
        if( isset( $id ) && is_numeric( $id ) )
        {
            if( !$this->getUser()->getPreference( $name ) )
                $this->getUser()->setPreference( $name, $value );
            else
                $this->getUser()->deletePreference( $name );
                      
            $this->getD2EM()->flush();
            
            echo "ok";
        }
    }
    
    /**
     *
     * We can work out the action as follows:
     *
     *  * old == false, new != false: ADD
     *  * old != false, new == false: DELETE
     *  * old != false, new != false: EDIT
     *
     * @param string $old
     * @param string $new
     * @throws Exception
     */
    private function _sendNotifications( $old = false, $new = false )
    {
        // get admin users
        $users = $this->getD2R( "\\Entities\\User" )->findBy( [ 'privs' => \Entities\User::AUTH_SUPERUSER ] );
        
        if( $old )
            $this->view->cust = $cust = $old->getCustomer();
        else if( $new )
            $this->view->cust = $cust = $new->getCustomer();
        else
            throw new Exception( "Customer note is missing." );
                   
        $this->view->old = $old;
        $this->view->new = $new;
        
        $mail = $this->getMailer();
        $mail->setFrom( $this->_options['identity']['email'], $this->_options['identity']['name'] )
             ->setSubject( '[IXP Notes] [' . $cust->getName() . '] ' . ( $old ? $old->getTitle() : $new->getTitle() ) )
             ->setBodyText( $this->view->render( 'customer-notes/email/notification.txt' ) );
          
        foreach( $users as $user )
        {
            if( !$user->getPreference( "customer-notes.notify" ) )
            {
                if( !$user->getPreference( "customer-notes.{$cust->getId()}.notify" ) )
                {
                    if( !$old ) // adding
                        continue;
                    
                    if( !$user->getPreference( "customer-notes.watching.{$old->getId()}" ) )
                        continue;
                }
            }
            else if( $user->getPreference( "customer-notes.notify" ) == "none" )
                continue;
            
            $mail->addTo( $user->getContact()->getEmail(), $user->getContact()->getName() )
                 ->send();
        }
    }

}

