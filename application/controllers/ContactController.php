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
 * Controller: Manage contacts
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ContactController extends IXP_Controller_FrontEnd
{
    
    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        $this->assertPrivilege( \Entities\User::AUTH_SUPERUSER );
        
        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\Contact',
            'form'          => 'IXP_Form_Contact',
            'pagetitle'     => 'Contacts',
        
            'titleSingular' => 'Contact',
            'nameSingular'  => 'a contact',
        
            'defaultAction' => 'list',                    // OPTIONAL; defaults to 'list'
        
            'listOrderBy'    => 'name',
            'listOrderByDir' => 'ASC',
    
            'listColumns'    => [
            
                'id'        => [ 'title' => 'UID', 'display' => false ],
    
                'customer'  => [
                    'title'      => 'Customer',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'customer',
                    'action'     => 'overview',
                    'idField'    => 'custid'
                ],
    
                'name'      => 'Name',
                'email'     => 'Email',
                'phone'     => 'Phone',
                'mobile'    => 'Mobile'
            ]
        ];
    
        // display the same information in the view as the list
        $this->_feParams->viewColumns = array_merge(
            $this->_feParams->listColumns,
            [
                'facilityaccess' => 'Facility Access',
                'mayauthorize'   => 'May Authorize',
                'lastupdated'    => [
                    'title'         => 'Last Updated',
                    'type'          => self::$FE_COL_TYPES[ 'DATETIME' ]
                ],
                'lastupdatedby'  => 'Last Updated By',
                'creator'        => 'Creator',
                'created'        => [
                    'title'         => 'Created',
                    'type'          => self::$FE_COL_TYPES[ 'DATETIME' ]
                ]
            ]
        );
        
    }


    /**
     * Provide array of users for the listAction and viewAction
     *
     * @param int $id The `id` of the row to load for `viewAction`. `null` if `listAction`
     */
    protected function listGetData( $id = null )
    {
        $qb = $this->getD2EM()->createQueryBuilder()
        ->select( 'c.id as id, c.name as name, c.email as email, c.phone AS phone, c.mobile AS mobile,
                c.facilityaccess AS facilityaccess, c.mayauthorize AS mayauthorize,
                c.lastupdated AS lastupdated, c.lastupdatedby AS lastupdatedby,
                c.creator AS creator, c.created AS created, cust.name AS customer, cust.id AS custid'
            )
        ->from( '\\Entities\\Contact', 'c' )
        ->leftJoin( 'c.Customer', 'cust' );
        
        if( $this->getParam( "cgid", false ) )
        {
            $qb->leftJoin( "c.Groups", "cg" )
                ->andWhere( "cg.id = ?2" )
                ->setParameter( 2, $this->getParam( "cgid" ) );
            
            $this->view->group = $this->getD2EM()->getRepository( "\\Entities\\ContactGroup" )->find( $this->getParam( "cgid" ) );
            $this->view->listPreamble = "contacts/list-preamble";
        }
    
        if( isset( $this->_feParams->listOrderBy ) )
            $qb->orderBy( $this->_feParams->listOrderBy, isset( $this->_feParams->listOrderByDir ) ? $this->_feParams->listOrderByDir : 'ASC' );
    
        if( $id !== null )
            $qb->andWhere( 'c.id = ?1' )->setParameter( 1, $id );
    
        return $qb->getQuery()->getResult();
    }
    
    
    /**
     *
     * @param IXP_Form_Contact $form The form object
     * @param \Entities\Contact $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @param array $options Options passed onto Zend_Form
     * @param string $cancelLocation Where to redirect to if 'Cancal' is clicked
     * @return void
     */
    protected function formPostProcess( $form, $object, $isEdit, $options = null, $cancelLocation = null )
    {
        $this->view->groups = $this->getD2EM()->getRepository( "\\Entities\\ContactGroup" )->getGroupNamesTypeArray();
        $this->view->jsonGroups = json_encode( $this->view->groups );
        if( $isEdit )
        {
            $form->getElement( 'custid' )->setValue( $object->getCustomer()->getId() );
            $this->view->contactGroups = $this->getD2EM()->getRepository( "\\Entities\\ContactGroup" )->getGroupNamesTypeArray( false, $object->getId() );
            
            if( $object->getUser() )
            {
                $form->getElement( 'login' )->setValue( 1 );
                $form->getElement( 'username' )->setValue( $object->getUser()->getUsername() );
                $form->getElement( 'password' )->setValue( $object->getUser()->getPassword() );
                $form->getElement( 'disabled' )->setValue( $object->getUser()->getDisabled() );
            }
            else
            {
                $form->getElement( 'password' )->setValue( OSS_String::random( 12 ) );
            }
        }
        else if( $this->getParam( 'custid', false ) && ( $cust = $this->getD2EM()->getRepository( '\\Entities\\Customer' )->find( $this->getParam( 'custid' ) ) ) )
        {
            $form->getElement( 'custid' )->setValue( $cust->getId() );
            $form->getElement( 'password' )->setValue( OSS_String::random( 12 ) );
        }
        
        if( !$isEdit )
        {
            $form->getElement( 'username' )->addValidator( 'OSSDoctrine2Uniqueness', true,
                [ 'entity' => '\\Entities\\User', 'property' => 'username' ]
            );
        }
        
        switch( $this->getUser()->getPrivs() )
        {
            case \Entities\User::AUTH_SUPERUSER:
                break;
                
            case \Entities\User::AUTH_CUSTADMIN:
                $form->removeElement( 'password' );
                $form->removeElement( 'privs' );
                $form->removeElement( 'custid' );
                if( $isEdit )
                    $form->getElement( 'username' )->setAttrib( 'readonly', 'readonly' );
                    
                break;

            default:
                throw new OSS_Exception( 'Unhandled user type' );
        }
    }
    
    
    /**
     * You can add `OSS_Message`s here and redirect to a custom destination after a
     * successful add / edit operation.
     *
     * By default it returns `false`.
     *
     * On `false`, the default action (`index`) is called and a standard success message is displayed.
     *
     *
     * @param OSS_Form $form The form object
     * @param object $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @return bool `false` for standard message and redirection, otherwise redirect within this function
     */
    protected function addDestinationOnSuccess( $form, $object, $isEdit  )
    {
        $this->addMessage( 'Contact successfully ' . ( $isEdit ? ' edited.' : ' added.' ), OSS_Message::SUCCESS );
        $this->redirect( 'customer/overview/tab/contacts/id/' . $object->getCustomer()->getId() );
    }

    /**
     * Function which can be over-ridden to perform any pre-deletion tasks
     *
     * You can stop the deletion by returning false but you should also add a
     * message to explain why.
     *
     * @param object $object The Doctrine2 entity to delete
     * @return bool Return false to stop / cancel the deletion
     */
    protected function preDelete( $object )
    {
        // keep the customer ID for redirection on success
        $this->getSessionNamespace()->ixp_contact_delete_custid = $object->getCustomer()->getId();
        return true;
    }
    
    /**
     * You can add `OSS_Message`s here and redirect to a custom destination after a
     * successful deletion operation.
     *
     * By default it returns `false`.
     *
     * On `false`, the default action (`index`) is called and a standard success message is displayed.
     *
     * @return bool `false` for standard message and redirection, otherwise redirect within this function
     */
    protected function deleteDestinationOnSuccess()
    {
        // retrieve the customer ID
        if( $custid = $this->getSessionNamespace()->ixp_contact_delete_custid )
        {
            unset( $this->getSessionNamespace()->ixp_contact_delete_custid );
            
            $this->addMessage( 'Contact successfully deleted', OSS_Message::SUCCESS );
            $this->redirect( 'customer/overview/tab/contacts/id/' . $custid );
        }
        
        return false;
    }
    
    /**
     * Prevalidation hook that can be overridden by subclasses for add and edit.
     *
     * This is called if the user POSTs a form just before the form is validated by Zend
     *
     * @param OSS_Form $form The Send form object
     * @param object $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True if we are editing, otherwise false
     * @return bool If false, the form is not validated or processed
     */
    protected function addPreValidate( $form, $object, $isEdit )
    {
        if( $isEdit && $this->getUser()->getPrivs() != \Entities\User::AUTH_SUPERUSER )
        {
            if( $this->getUser()->getCustomer() != $object->getCustomer() )
            {
                $this->addMessage( 'Illegal attempt to edit a user not under your control. The security team have been notified.' );
                $this->getLogger()->alert( "User {$this->getUser()->getUsername()} illegally tried to edit {$object->getName()}" );
                $this->redirect( 'contact/list' );
            }
        }
        
        if( isset( $_POST['login'] ) && $_POST['login'] )
        {
            $_POST['login'] = 1;
            $form->getElement( "username" )->setRequired( true );
            $form->getElement( "password" )->setRequired( true );
            $form->getElement( "privs" )->setRequired( true );
        }
        else
            $_POST['login'] = 0;
        
        if( isset( $_POST['role'] ) )
        {
            foreach( $_POST['role'] as $rid )
            {
                $groups[\Entities\ContactGroup::TYPE_ROLE][$rid] = ["id" => $rid];
            }
        }
            
        if( isset( $_POST['group'] ) )
        {
            foreach( $_POST['group'] as $gid )
            {
                $g = $this->getD2EM()->getRepository( "\\Entities\\ContactGroup" )->find( $gid );
                if( $g )
                    $groups[$g->getType()][$gid] = ["id" => $gid];
            }
        }   
        $this->view->contactGroups = $groups;
        
        return true;
    }
    
    
    /**
     *
     * @param IXP_Form_Contact $form The form object
     * @param \Entities\Contact $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @return void
     */
    protected function addPostValidate( $form, $object, $isEdit )
    {
        $object->setCustomer(
            $this->getD2EM()->getRepository( '\\Entities\\Customer' )->find( $form->getElement( 'custid' )->getValue() )
        );
    
        if( $isEdit )
        {
            $object->setLastupdated( new DateTime() );
            $object->setLastupdatedby( $this->getUser()->getId() );
            
        }
        else
        {
            $object->setCreated( new DateTime() );
            $object->setCreator( $this->getUser()->getUsername() );
        }
        
        $this->_processUser( $form, $object );
        
        $groupes = [];
        foreach( $form->getValue( "role" ) as $rid )
        {
            $role = $this->getD2EM()->getRepository( "\\Entities\\ContactGroup" )->find( $rid );
            if( $role && !$object->getGroups()->contains( $role ) )
            {
                $object->addGroup( $role );
                $role->addContact( $object );
            }
            if( $role->getLimitedTo() != 0 && $role->getLimitedTo() < count( $role->getContacts() ) )
            {
                $this->addMessage( "Role {$role->getName()} is full this contact can't be assign to this role.", OSS_Message::ERROR );
                return;
            }
            $groups[] = $role;
        }
        foreach( $form->getValue( "group" ) as $gid )
        {
            $group = $this->getD2EM()->getRepository( "\\Entities\\ContactGroup" )->find( $gid );
           
            if( $group && !$object->getGroups()->contains( $group ) )
            {
                $object->addGroup( $group );
                $group->addContact( $object );
            }
           
            if( $group->getLimitedTo() != 0 &&  $group->getLimitedTo() < count( $group->getContacts() ) )
            {
                $this->addMessage( "Group {$group->getName()} is full this contact can't be assign to this group.", OSS_Message::ERROR );
                return;                
            }
            $groups[] = $group;
        }
        
        foreach( $object->getGroups() as $key => $group )
        {
            if( !in_array( $group, $groups ) )
            {
                $object->getGroups()->remove( $key );
            }
        }
    
        return true;
    }
    
    
    /**
     * Creates/updates/deltes user for contact
     *
     * @param IXP_Form_Contact $form The form object
     * @param \Entities\Contact $object The Doctrine2 entity (being edited or blank for add)
     */
    private function _processUser( $form, $object )
    {
        $this->_feParams->userStatus = "none";
        if( $form->getValue( "login" ) )
        {
            if( $object->getUser() )
            {
                if( $this->getUser()->getPrivs() == \Entities\User::AUTH_SUPERUSER )
                {
                    $object->getUser()->setUsername( $form->getValue( "username" ) );
                    $object->getUser()->setPassword( $form->getValue( "password" ) );
                    $object->getUser()->setPrivs( $form->getValue( "privs" ) );
                }
                
                $object->getUser()->setDisabled( $form->getValue( "disabled" ) );                
                $object->getUser()->setEmail( $form->getValue( "email" ) );
                $object->getUser()->setLastupdated( new DateTime() );
                $object->getUser()->setLastupdatedby( $this->getUser()->getId() );
            }
            else
            {
                $user = new \Entities\User();
                $this->getD2EM()->persist( $user );
                
                $object->setUser( $user );
                $user->setEmail( $form->getValue( "email" ) );
                $user->setDisabled( $form->getValue( "disabled" ) );
                $user->setCreated( new DateTime() );
                $user->setCreator( $this->getUser()->getUsername() );
                $user->setCustomer( $object->getCustomer() );

                if( $this->getUser()->getPrivs() == \Entities\User::AUTH_CUSTADMIN )
                {
                    $user->setParent( $this->getUser() );
                    $user->setPrivs( \Entities\User::AUTH_CUSTUSER );
                    $user->setPassword( OSS_String::random( 16 ) );
                }
                else
                {
                    $user->setUsername( $form->getValue( "username" ) );
                    $user->setPassword( $form->getValue( "password" ) );
                    $user->setPrivs( $form->getValue( "privs" ) );
   
                    try
                    {
                        $user->setParent(
                            $this->getD2EM()->createQuery(
                                'SELECT u FROM \\Entities\\User u WHERE u.privs = ?1 AND u.Customer = ?2'
                            )
                            ->setParameter( 1, \Entities\User::AUTH_CUSTADMIN )
                            ->setParameter( 2, $object->getCustomer() )
                            ->setMaxResults( 1 )
                            ->getSingleResult()
                        );
                    }
                    catch( \Doctrine\ORM\NoResultException $e )
                    {
                        $user->setParent( $user );
                    }
                }
                
                $this->getLogger()->info( "{$this->getUser()->getUsername()} created user {$user->getUsername()}" );
                $this->_feParams->userStatus = "created";
            }
        }
        else
        {
            if( $object->getUser() )
            {
                if( $this->getUser()->getPrivs() != \Entities\User::AUTH_SUPERUSER )
                {
                    if( $object->getCustomer() != $this->getUser()->getCustomer() )
                    {
                        $this->getLogger()->notice( "{$this->getUser()->getUsername()} tried to delete other customer user {$object->getUser()->getUsername()}" );
                        $this->addMessage( 'You are not authorised to delete this user. The administrators have been notified.' );
                        return false;
                    }
                }
                
                // now delete all the users privileges also
                foreach( $object->getUser()->getPreferences() as $pref )
                {
                    $object->getUser()->removePreference( $pref );
                    $this->getD2EM()->remove( $pref );
                }

                $user = $object->getUser();
                $object->unsetUser();
                $this->getD2EM()->remove( $user );
                $this->_feParams->removedUserId = $user->getId();
                $this->getLogger()->info( "{$this->getUser()->getUsername()} deleted user {$user->getUsername()}" );
            }
        }
        $this->_feParams->userStatus = "removed";
    }
    
    
    /**
     *
     * @param IXP_Form_User $form The form object
     * @param \Entities\User $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @return void
     */
    protected function addPostFlush( $form, $object, $isEdit )
    {
        if( isset( $this->_feParams->userStatus ) )
        {
            if( $this->view->userStatus == "created" ) 
            {
                $this->view->newuser = $object->getUser();
                $this->sendWelcomeEmail( $object->getUser() );
            }
            else if( $this->_feParams->userStatus == "removed" && isset( $this->_feParams->removedUserId ) ) 
            {
                $this->clearUserFromCache( $this->_feParams->removedUserId );
            }

        }

        return true;
    }
    
    
    /**
     * Send a welcome email to a new user
     *
     * @param \Entities\User $user The recipient of the email
     * @return bool True if the mail was sent successfully
     */
    private function sendWelcomeEmail( $user )
    {
        try
        {
            $mail = $this->getMailer();
            $mail->setFrom( $this->_options['identity']['email'], $this->_options['identity']['name'] )
                ->setSubject( $this->_options['identity']['sitename'] . ' - ' . _( 'Your Access Details' ) )
                ->addTo( $user->getEmail(), $user->getUsername() )
                ->setBodyHtml( $this->view->render( 'user/email/html/welcome.phtml' ) )
                ->send();
        }
        catch( Zend_Mail_Exception $e )
        {
            $this->getLogger()->alert( "Could not send welcome email for new user!\n\n" . $e->toString() );
            return false;
        }
        
        return true;
    }
    
}
