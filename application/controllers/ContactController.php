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
        }
        else if( $this->getParam( 'custid', false ) && ( $cust = $this->getD2EM()->getRepository( '\\Entities\\Customer' )->find( $this->getParam( 'custid' ) ) ) )
        {
            $form->getElement( 'custid' )->setValue( $cust->getId() );
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
    
    
}
