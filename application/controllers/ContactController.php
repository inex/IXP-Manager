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
 * Controller: Manage contacts
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ContactController extends IXP_Controller_FrontEnd
{

    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\Contact',
            'form'          => 'IXP_Form_Contact',
            'pagetitle'     => 'Contacts',

            'titleSingular' => 'Contact',
            'nameSingular'  => 'a contact',

            'defaultAction' => 'list',                    // OPTIONAL; defaults to 'list'

            'listOrderBy'    => 'name',
            'listOrderByDir' => 'ASC',

            'addWhenEmpty'   => false
        ];

        switch( $this->getUser()->getPrivs() )
        {
            case \Entities\User::AUTH_SUPERUSER:

                $this->_feParams->listColumns = [

                    'id'        => [ 'title' => 'UID', 'display' => false ],

                    'customer'  => [
                        'title'      => 'Customer',
                        'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                        'controller' => 'customer',
                        'action'     => 'overview',
                        'idField'    => 'custid'
                    ],

                    'name'      => 'Name',
                    'position'  => 'Position',
                    'email'     => 'Email',
                    'created'       => [
                        'title'     => 'Created',
                        'type'      => self::$FE_COL_TYPES[ 'DATETIME' ]
                    ],
                    'lastupdated'       => [
                        'title'     => 'Updated',
                        'type'      => self::$FE_COL_TYPES[ 'DATETIME' ]
                    ]
                ];
                break;

            case \Entities\User::AUTH_CUSTADMIN:
                $this->_feParams->pagetitle = 'Contact Admin for ' . $this->getUser()->getCustomer()->getName();

                $this->_feParams->listColumns = [
                    'id'        => [ 'title' => 'UID', 'display' => false ],
                    'name'      => 'Name',
                    'position'  => 'Position',
                    'email'     => 'Email',
                    'phone'     => 'Phone',
                    'uid'       => [
                        'title'         => 'Can Login',
                        'type'          => self::$FE_COL_TYPES[ 'SCRIPT' ],
                        'script'        => 'contact/list-column-uid.phtml'
                    ],
                    'created'       => [
                        'title'     => 'Created',
                        'type'      => self::$FE_COL_TYPES[ 'DATETIME' ]
                    ]
                ];
                break;

            default:
                $this->redirectAndEnsureDie( 'error/insufficient-permissions' );
        }

        // display the same information in the view as the list
        if( $this->getUser()->getPrivs() != \Entities\User::AUTH_SUPERUSER )
            $this->_feParams->viewColumns = $this->_feParams->listColumns;
        else
            $this->_feParams->viewColumns = $this->_feParams->listColumns + ['phone'     => 'Phone', 'mobile'    => 'Mobile' ];
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
                c.lastupdated AS lastupdated, c.lastupdatedby AS lastupdatedby, c.position AS position,
                c.creator AS creator, c.created AS created, cust.name AS customer, cust.id AS custid,
                u.id AS uid'
            )
            ->from( '\\Entities\\Contact', 'c' )
            ->leftJoin( 'c.User', 'u' )
            ->leftJoin( 'c.Customer', 'cust' );

        $roles = $this->getD2R( '\\Entities\\ContactGroup' )->getGroupNamesTypeArray( 'ROLE' );

        if( isset( $roles['ROLE'] ) )
        {
            $this->view->roles = $roles = $roles['ROLE'];
            $this->view->role = $role = $this->getParam( 'role', false );
            if( isset( $roles[ $role ] ) )
                $qb->leftJoin( 'c.Groups', 'g' )
                   ->andWhere( "g.id = :role" )->setParameter( 'role', $role );
        }

        if( $this->getParam( "cgid", false ) )
        {
            $qb->leftJoin( "c.Groups", "cg" )
                ->andWhere( "cg.id = ?2" )
                ->setParameter( 2, $this->getParam( "cgid" ) );

            $this->view->group = $this->getD2EM()->getRepository( "\\Entities\\ContactGroup" )->find( $this->getParam( "cgid" ) );
        }

        if( $this->getUser()->getPrivs() != \Entities\User::AUTH_SUPERUSER )
        {
            $qb->andWhere( 'c.Customer = :cust' )->setParameter( 'cust', $this->getUser()->getCustomer() );
            $qb->andWhere( '( c.User IS NULL OR u.privs = :privs )' )->setParameter( 'privs', \Entities\User::AUTH_CUSTUSER );
        }

        if( isset( $this->_feParams->listOrderBy ) )
            $qb->orderBy( $this->_feParams->listOrderBy, isset( $this->_feParams->listOrderByDir ) ? $this->_feParams->listOrderByDir : 'ASC' );

        if( $id !== null )
            $qb->andWhere( 'c.id = :id' )->setParameter( 'id', $id );

        $data = $qb->getQuery()->getResult();

        if( $this->getUser()->getPrivs() != \Entities\User::AUTH_SUPERUSER )
            return $data;

        $data = $this->setRolesAndGroups( $data, $id );

        return $data;
    }

    /**
     * Sets roles and groups for contacts from the data array.
     *
     * From data array gets contact ids  and loads the role and group names by ids array.
     * Then it iterates throw $data and if roles or groups was found for preview it appends
     * that data to $data array. For list only roles is appended to $data array. Function returns
     * appended $data array.
     *
     * @param array $data Data loaded form DQL query for list or view action
     * @param int   $id   The `id` of the row to load for `viewAction`. `null` if `listAction`
     * @return array
     */
    private function setRolesAndGroups( $data, $id )
    {
        $ids = [];
        foreach( $data as $row )
            $ids[] = $row['id'];

        $roles = $this->getD2R( '\\Entities\\Contact' )->getRolesByIds( $ids );

        if( $id !== null )
            $groups = $this->getD2R( '\\Entities\\Contact' )->getGroupsByIds( $ids );

        foreach( $data as $idx => $contact )
        {
            if( $id !== null )
            {
                if( isset( $roles[ $contact['id'] ] ) )
                {
                    asort( $roles[ $contact['id'] ] );

                    if( !isset( $this->_feParams->viewColumns->role ) )
                        $this->_feParams->viewColumns = $this->_feParams->viewColumns + [ 'role'  => 'Role' ];

                    $data[$idx]['role'] = implode( '<br/>',  $roles[ $contact['id'] ] );
                }
                if( isset( $groups[ $contact['id'] ] ) )
                {
                    if( !isset( $this->_feParams->viewColumns->role ) )
                        $this->_feParams->viewColumns = $this->_feParams->viewColumns + [ 'group'  => 'Group' ];

                    asort( $groups[ $contact['id'] ] );
                    $group = "";
                    foreach( $groups[ $contact['id'] ] as $gdata )
                    {
                        if( $group != "" )
                            $group .= "<br />";

                        $group .= $this->_options['contact']['group']['types'][ $gdata['type'] ] . " " . $gdata['name'];
                    }

                    $data[$idx]['group'] = $group;
                }
            }
            else
            {
                if( isset( $roles[ $contact['id'] ] ) )
                {
                    asort( $roles[ $contact['id'] ] );
                    $data[$idx]['role'] = $roles[ $contact['id'] ];
                }
                else
                    $data[$idx]['role'] = [];

                if( !isset( $this->_feParams->listColumns['role']  ) )
                {
                    $this->_feParams->listColumns = $this->_feParams->listColumns + [ 'role' => [
                            'title'  => 'Role',
                            'type'   => self::$FE_COL_TYPES[ 'SCRIPT' ],
                            'script' => 'contact/list-role.phtml' ]
                    ];
                }
            }
        }
        return $data;
    }


    protected function listPreamble()
    {
        if( $this->getUser()->getPrivs() == \Entities\User::AUTH_CUSTADMIN )
        {
            if( !isset( $this->getSessionNamespace()->custadminInstructions ) || !$this->getSessionNamespace()->custadminInstructions )
            {
                $this->getSessionNamespace()->custadminInstructions = true;

                $this->addMessage(
                        "<p><strong>Remember! This admin account is only intended for creating contacts for your organisation.</strong></p>"
                        . "<p>For full IXP Manager functionality, graphs and member information, log in under one of your user accounts</p>",
                        OSS_Message::INFO,
                        OSS_Message::TYPE_BLOCK
                );
            }
        }
    }




    /**
     * Gets the ID of the object for editing - which, by default, returns the id parameter from the request
     *
     * @return int|false
     */
    protected function editResolveId()
    {
        if( $this->_getParam( 'id', false ) )
            return $this->_getParam( 'id' );
        else if( $this->_getParam( 'uid', false ) )
        {
            if( $user = $this->getD2EM()->getRepository( "\\Entities\\User" )->find( $this->getParam( "uid" ) ) )
                return $user->getContact()->getId();

            $this->addMessage( 'The requested contact / user does not exist', OSS_Message::ERROR );
            $this->redirect();
        }
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
        $this->view->groups     = $this->getD2EM()->getRepository( "\\Entities\\ContactGroup" )->getGroupNamesTypeArray();
        $this->view->jsonGroups = json_encode( $this->view->groups );

        // ROLE is treated as a special group and if it is not set, it will disable the contact role functionality
        if( !isset( $this->_options['contact']['group']['types'][ \Entities\ContactGroup::TYPE_ROLE ] ) )
            $form->removeElement( 'role' );

        // redirect back to whence we came on form submission
        if( $this->getParam( "user", false ) )
        {
            $form->getElement( 'login' )->setValue( 1 );
            $form->setAction( OSS_Utils::genUrl( 'contact', ( $isEdit ? 'edit' : 'add' ), false, [ 'user' => true ] ) );
        }
        else if( $this->getParam( "uid", false ) )
            $form->setAction( OSS_Utils::genUrl( 'contact', ( $isEdit ? 'edit' : 'add' ), false, [ 'uid' => $this->getParam( "uid" ) ] ) );

        if( $cid = $this->getParam( 'cid', false ) )
        {
            $form->updateCancelLocation( OSS_Utils::genUrl( 'customer', 'overview', false,
                    [
                        'id' => $cid,
                        'tab' => ( $this->getParam( 'user', false ) || $this->getParam( 'uid', false ) ) ? 'users' : 'contacts'
                    ]
                )
            );
        }

        if( $isEdit )
        {
            $form->getElement( 'custid' )->setValue( $object->getCustomer()->getId() );
            $this->view->contactGroups = $this->getD2R( "\\Entities\\ContactGroup" )->getGroupNamesTypeArray( false, $object->getId() );
        }
        else if( $this->getParam( 'custid', false ) && ( $cust = $this->getD2R( '\\Entities\\Customer' )->find( $this->getParam( 'custid' ) ) ) )
        {
            $form->getElement( 'custid' )->setValue( $cust->getId() );
        }

        if( $object->getUser() )
        {
            $form->getElement( 'login'    )->setValue( 1 );
            $form->getElement( 'username' )->setValue( $object->getUser()->getUsername() );
            $form->getElement( 'password' )->setAttrib( 'placeholder', 'Set to change password' );
            $form->getElement( 'privs'    )->setValue( $object->getUser()->getPrivs() );
            $form->getElement( 'disabled' )->setValue( $object->getUser()->getDisabled() );
        }
        else
        {
            $form->getElement( 'password' )->setValue( OSS_String::random( 12 ) );
            $form->getElement( 'username' )->addValidator( 'OSSDoctrine2Uniqueness', true,
                [ 'entity' => '\\Entities\\User', 'property' => 'username' ]
            );
        }

        switch( $this->getUser()->getPrivs() )
        {
            case \Entities\User::AUTH_SUPERUSER:
                $form->getElement( 'username' )->removeValidator( 'stringLength' );
                break;

            case \Entities\User::AUTH_CUSTADMIN:
                $form->removeElement( 'password' );
                $form->removeElement( 'privs' );
                $form->removeElement( 'custid' );
                $form->removeElement( 'facilityaccess' );
                $form->removeElement( 'mayauthorize' );
                $form->removeElement( 'notes' );

                if( $isEdit && $object->getUser() )
                    $form->getElement( 'username' )->setAttrib( 'readonly', 'readonly' );
                break;

            default:
                throw new OSS_Exception( 'Unhandled user type / security issues' );
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
        $this->addMessage(
            'Contact successfully ' . ( $isEdit ? ' edited.' : ' added.' ) . ' '
                . 'If you gave the contact a login account, they will have been emailed with instructions '
                . 'for setting their password and accessing it. You will have been copied on this email also.',
            OSS_Message::SUCCESS
        );

        if( $this->getUser()->getPrivs() != \Entities\User::AUTH_SUPERUSER )
        {
            $this->redirect( 'contact/list' );
        }

        if( $this->getParam( 'user', false ) || $this->getParam( 'uid', false ) )
            $this->redirect( 'customer/overview/tab/users/id/' . $object->getCustomer()->getId() );
        else
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
        // if I'm not an admin, then make sure I have permission!
        if( $this->getUser()->getPrivs() != \Entities\User::AUTH_SUPERUSER )
        {
            if( $object->getCustomer() != $this->getUser()->getCustomer() )
            {
                $this->getLogger()->notice( "{$this->getUser()->getUsername()} tried to delete other customer user {$object->getUser()->getUsername()}" );
                $this->addMessage( 'You are not authorised to delete this user. The administrators have been notified.' );
                return false;
            }
        }
        else
        {
            // keep the customer ID for redirection on success
            $this->getSessionNamespace()->ixp_contact_delete_custid = $object->getCustomer()->getId();
        }

        if( $object->getUser() )
            $this->_deleteUser( $object );

        // if we are only deleting the user login account, take over control here and redirect
        if( $this->getParam( 'useronly', false ) )
        {
            $this->getD2EM()->flush();
            $this->addMessage( 'User login account successfully removed.', OSS_Message::SUCCESS );
            $this->redirectAndEnsureDie( 'customer/overview/tab/users/id/' . $object->getCustomer()->getId() );
        }

        return true;
    }

    /**
     * Do the heavy lifting for deleting a user
     *
     * @param \Entities\Contact $contact The contact entity
     */
    private function _deleteUser( $contact )
    {
        $user = $contact->getUser();

        // delete all the user's preferences
        foreach( $user->getPreferences() as $pref )
        {
            $user->removePreference( $pref );
            $this->getD2EM()->remove( $pref );
        }

        // delete all the user's login records
        foreach( $user->getLastLogins() as $ll )
        {
            $user->removeLastLogin( $ll );
            $this->getD2EM()->remove( $ll );
        }

        // delete all the user's API keys
        foreach( $user->getApiKeys() as $ak )
        {
            $user->removeApiKey( $ak );
            $this->getD2EM()->remove( $ak );
        }

        // clear the user from the contact and remove the user then
        $contact->unsetUser();
        $this->getD2EM()->remove( $user );

        // in case the user is currently logged in:
        $this->clearUserFromCache( $user->getId() );

        $this->getLogger()->info( "{$this->getUser()->getUsername()} deleted user {$user->getUsername()}" );
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
     * Preparation hook that can be overridden by subclasses for add and edit.
     *
     * This is called just before we process a possible POST / submission and
     * will allow us to change / alter the form or object.
     *
     * @param IXP_Form_Contact $form The Send form object
     * @param \Entities\Contact $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True if we are editing, otherwise false
     */
    protected function addPrepare( $form, $object, $isEdit )
    {
        if( $isEdit && $this->getUser()->getPrivs() != \Entities\User::AUTH_SUPERUSER )
        {
            if( $this->getUser()->getCustomer() != $object->getCustomer() )
            {
                $this->addMessage( 'Illegal attempt to edit a user not under your control. The security team have been notified.' );
                $this->getLogger()->alert( "User {$this->getUser()->getUsername()} illegally tried to edit {$object->getName()}" );
                $this->redirect();
            }
        }


        if( !$isEdit )
        {
            // defaults
            $object->setFacilityaccess( false );
            $object->setMayauthorize( false );
        }
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
        if( isset( $_POST['login'] ) && $_POST['login'] == '1' )
        {
            $form->getElement( "username" )->setRequired( true );

            if( !$isEdit && $this->getUser()->getPrivs() == \Entities\User::AUTH_SUPERUSER )
            {
                $form->getElement( "password" )->setRequired( true );
                $form->getElement( "privs"    )->setRequired( true );
            }
        }
        else
            $_POST['login'] = 0;

        $this->view->contactGroups = $this->_postedGroupsToArray();

        return true;
    }


    /**
     * Process submitted groups (and roles) into an array
     *
     * @return array Array of submitted contact groups
     */
    private function _postedGroupsToArray()
    {
        $groups = [];
        foreach( [ 'role', 'group' ] as $groupType )
        {
            if( isset( $_POST[ $groupType ] ) )
            {
                foreach( $_POST[ $groupType ] as $cgid )
                {
                    if( $cg = $this->getD2R( "\\Entities\\ContactGroup" )->find( $cgid ) )
                        $groups[ $cg->getType() ][$cgid] = [ "id" => $cgid ];
                }
            }
        }

        return $groups;
    }

    /**
     *
     * @param IXP_Form_Contact $form The form object
     * @param \Entities\Contact $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @return bool If false, the form is not processed
     */
    protected function addPostValidate( $form, $object, $isEdit )
    {
        if( $this->getUser()->getPrivs() == \Entities\User::AUTH_SUPERUSER )
            $object->setCustomer( $this->getD2R( '\\Entities\\Customer' )->find( $form->getElement( 'custid' )->getValue() ) );
        else
            $object->setCustomer( $this->getUser()->getCustomer() );

        if( !$isEdit )
        {
            $object->setCreated( new DateTime() );
            $object->setCreator( $this->getUser()->getUsername() );
        }

        $object->setLastupdated( new DateTime() );
        $object->setLastupdatedby( $this->getUser()->getId() );

        if( !$this->_processUser( $form, $object, $isEdit ) )
            return false;

        // let the group processor have the final say as to whether post validation
        // passes or not
        return $this->_setContactGroups( $form, $object );
    }

    /**
     * Process submitted groups (and roles) for this contact and update the relationships
     *
     * @param IXP_Form_Contact $contact
     * @param \Entities\Contact $contact
     * @return boolean
     */
    private function _setContactGroups( $form, $contact )
    {
        $groups = [];

        foreach( [ 'role', 'group' ] as $groupType )
        {
            if( $form->getValue( $groupType ) )
            {
                foreach( $form->getValue( $groupType ) as $cgid )
                {
                    if( $group = $this->getD2R( "\\Entities\\ContactGroup" )->find( $cgid ) )
                    {
                        if( $group->getLimitedTo() != 0 )
                        {
                            $contactsWithGroupForCustomer = $this->getD2R( "\\Entities\\ContactGroup" )->countForCustomer( $contact->getCustomer(), $cgid );

                            if( !$contact->getGroups()->contains( $group ) && $group->getLimitedTo() <= $contactsWithGroupForCustomer )
                            {
                                $this->addMessage( "Contact group {$group->getName()} has a limited membership and is full.", OSS_Message::WARNING );
                                return false;
                            }
                        }

                        if( !$contact->getGroups()->contains( $group ) )
                        {
                            $contact->addGroup( $group );
                            $group->addContact( $contact );
                        }

                        $groups[] = $group;
                    }
                }
            }
        }

        foreach( $contact->getGroups() as $key => $group )
        {
            if( !in_array( $group, $groups ) )
            {
                $contact->getGroups()->remove( $key );
            }
        }

        return true;
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
        if( isset( $this->_feParams->userStatus ) && $this->_feParams->userStatus == "created" )
        {
            $this->view->newuser = $object->getUser();
            $this->sendWelcomeEmail( $object );
        }

        return $this->postFlush( $object );
    }

    /**
     * Post database flush hook that can be overridden by subclasses and is called by
     * default for a successful add / edit / delete.
     *
     * Called by `addPostFlush()` and `postDelelte()` - if overriding these, ensure to
     * call this if you have overridden it.
     *
     * @param object $object The Doctrine2 entity (being edited or blank for add)
     * @return bool
     */
    protected function postFlush( $object )
    {
        // clear the user from the cache (e.g. if user is logged in, it will be cached)
        if( $object->getUser() )
            $this->clearUserFromCache( $object->getUser()->getId() );

        return true;
    }


     /**
      * Creates/updates/deletes the user for a contact when adding / editing a contact
      *
      * @param IXP_Form_Contact $form The form object
      * @param \Entities\Contact $contact The Doctrine2 entity (being edited or blank for add)
      * @param bool $isEdit True of we are editing an object, false otherwise
      */
    private function _processUser( $form, $contact, $isEdit )
    {
        if( $form->getValue( "login" ) )
        {
            // the contact has a user already or one needs to be created

            if( !( $user = $contact->getUser() ) )
            {
                $user = new \Entities\User();
                $contact->setUser( $user );

                $user->setCreated( new DateTime() );
                $user->setCreator( $this->getUser()->getUsername() );

                // these should only be updated by CUSTADMIN on creation of a login account
                if( $this->getUser()->getPrivs() <= \Entities\User::AUTH_CUSTADMIN )
                {
                    $user->setPrivs( \Entities\User::AUTH_CUSTUSER );
                    $user->setPassword(
                        OSS_Auth_Password::hash( OSS_String::random( 16 ), $this->_options['resources']['auth']['oss'] )
                    );
                    $user->setUsername( $form->getValue( "username" ) );
                }
                else
                {
                    // if this is an admin user, let them start with no unread notes
                    if( $form->getValue( "privs" ) == \Entities\User::AUTH_SUPERUSER )
                        $user->setPreference( 'customer-notes.read_upto', time() );
                }

                $this->getD2EM()->persist( $user );
                $this->_feParams->userStatus = "created";
            }

            $user->setCustomer( $contact->getCustomer() );

            $user->setDisabled( $form->getValue( "disabled" ) );
            $user->setEmail( $form->getValue( "email" ) );
            $user->setLastupdated( new DateTime() );
            $user->setLastupdatedby( $this->getUser()->getId() );

            // SUPERADMIN can update these always
            if( $this->getUser()->getPrivs() == \Entities\User::AUTH_SUPERUSER )
            {
                if( $form->getValue( "password", '' ) != '' )
                {
                    $user->setPassword(
                        OSS_Auth_Password::hash( $form->getValue( "password" ), $this->_options['resources']['auth']['oss'] )
                     );
                }

                // ensure the username is not already taken
                if( $user->getUsername() != $form->getValue( "username" )
                        && $this->getD2R( '\\Entities\\User' )->findOneBy( [ 'username' => $form->getValue( "username" ) ] ) )
                {
                    $this->addMessage( 'That username is already is use by another user', OSS_Message::ERROR );
                    return false;
                }

                $user->setUsername( $form->getValue( "username" ) );
                $user->setPrivs( $form->getValue( "privs" ) );
            }

            $this->getLogger()->info( "{$this->getUser()->getUsername()} created user {$user->getUsername()}" );
        }
        else // !$form->getValue( "login" )
        {
            if( $contact->getUser() )
                $this->_deleteUser( $contact );
        }

        return true;
    }


    /**
     * Send a welcome email to a new user
     *
     * @param \Entities\Contact $contact The recipient of the email
     * @return bool True if the mail was sent successfully
     */
    private function sendWelcomeEmail( $contact )
    {
        try
        {
            $mail = $this->getMailer();

            // This may be useful... needs more thought first
            // if( defined( APPLICATION_ENV ) && APPLICATION_ENV == 'production' )
            //    ->addCc( $this->getUser()->getEmail(), $this->getUser()->getContact()->getName() );

            $mail->setFrom( $this->_options['identity']['email'], $this->_options['identity']['name'] )
                ->setSubject( $this->_options['identity']['sitename'] . ' - ' . _( 'Your Access Details' ) )
                ->addTo( $contact->getEmail(), $contact->getName() )
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
