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
 * Controller: Manage users
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class UserController extends IXP_Controller_FrontEnd
{

    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\User',
            'form'          => 'IXP_Form_User',
            'pagetitle'     => 'Users',

            'titleSingular' => 'User',
            'nameSingular'  => 'a user',

            'defaultAction' => 'list',                    // OPTIONAL; defaults to 'list'

            'listOrderBy'    => 'username',
            'listOrderByDir' => 'ASC',
        ];

        switch( $this->getUser()->getPrivs() )
        {
            case \Entities\User::AUTH_SUPERUSER:
                $this->_feParams->listColumns = [
                    'id' => [ 'title' => 'UID', 'display' => false ],

                    'customer'  => [
                        'title'      => 'Customer',
                        'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                        'controller' => 'customer',
                        'action'     => 'overview',
                        'idField'    => 'custid'
                    ],

                    'username'      => 'Username',
                    'email'         => 'Email',
                    
                    'privileges'    => [
                        'title'     => 'Privileges',
                        'type'      => self::$FE_COL_TYPES[ 'XLATE' ],
                        'xlator'    => \Entities\User::$PRIVILEGES_TEXT
                    ],

                    'enabled'       => [
                        'title'         => 'Enabled',
                        'type'          => self::$FE_COL_TYPES[ 'SCRIPT' ],
                        'script'        => 'user/list-column-enabled.phtml'
                    ],

                    'created'       => [
                        'title'     => 'Created',
                        'type'      => self::$FE_COL_TYPES[ 'DATETIME' ]
                    ]
                ];
                break;

            case \Entities\User::AUTH_CUSTADMIN:
                $this->_feParams->pagetitle = 'User Admin for ' . $this->getUser()->getCustomer()->getName();

                $this->_feParams->listColumns = [
                    'id' => [ 'title' => 'UID', 'display' => false ],
                    'username'      => 'Username',
                    'email'         => 'Email',

                    'enabled'       => [
                        'title'         => 'Enabled',
                        'type'          => self::$FE_COL_TYPES[ 'SCRIPT' ],
                        'script'        => 'user/list-column-enabled.phtml'
                    ],

                    'created'       => [
                        'title'         => 'Created',
                        'type'          => self::$FE_COL_TYPES[ 'DATETIME' ]
                    ]
                ];
                break;

            default:
                $this->redirectAndEnsureDie( 'error/insufficient-permissions' );
        }

        // display the same information in the view as the list
        $this->_feParams->viewColumns = $this->_feParams->listColumns;
    }

    
    
    protected function listPreamble()
    {
        if( $this->getUser()->getPrivs() == \Entities\User::AUTH_CUSTADMIN )
        {
            if( !isset( $this->getSessionNamespace()->custadminInstructions ) || !$this->getSessionNamespace()->custadminInstructions )
            {
                $this->getSessionNamespace()->custadminInstructions = true;
                
                $this->addMessage(
                    "<p><strong>Remember! This admin account is only intended for creating users for your organisation.</strong></p>"
                        . "<p>For full IXP Manager functionality, graphs and member information, log in under one of your user accounts</p>",
                    OSS_Message::INFO,
                    OSS_Message::TYPE_BLOCK
                );
            }
        }
    }
        


    /**
     * Provide array of users for the listAction and viewAction
     *
     * @param int $id The `id` of the row to load for `viewAction`. `null` if `listAction`
     */
    protected function listGetData( $id = null )
    {
        $qb = $this->getD2EM()->createQueryBuilder()
            ->select( 'u.id as id, u.username as username, u.email as email, u.privs AS privileges,
                    u.created as created, u.disabled as disabled, c.id as custid, c.name as customer' )
            ->from( '\\Entities\\User', 'u' )
            ->leftJoin( 'u.Customer', 'c' );

        if( $this->getUser()->getPrivs() == \Entities\User::AUTH_CUSTADMIN )
        {
            $qb->where( 'u.Customer = ?1' )
               ->andWhere( 'u.privs = ?2' )
               ->setParameter( 1, $this->getUser()->getCustomer() )
               ->setParameter( 2, \Entities\User::AUTH_CUSTUSER );
        }

        if( isset( $this->_feParams->listOrderBy ) )
            $qb->orderBy( $this->_feParams->listOrderBy, isset( $this->_feParams->listOrderByDir ) ? $this->_feParams->listOrderByDir : 'ASC' );

        if( $id !== null )
            $qb->andWhere( 'u.id = ?3' )->setParameter( 3, $id );

        return $qb->getQuery()->getResult();
    }


    /**
     *
     * @param IXP_Form_User $form The form object
     * @param \Entities\User $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @param array $options Options passed onto Zend_Form
     * @param string $cancelLocation Where to redirect to if 'Cancal' is clicked
     * @return void
     */
    protected function formPostProcess( $form, $object, $isEdit, $options = null, $cancelLocation = null )
    {
        switch( $this->getUser()->getPrivs() )
        {
            case \Entities\User::AUTH_SUPERUSER:
                $form->removeElement( 'name' );
                $form->getElement( 'username' )->removeValidator( 'stringLength' );
                
                if( !$isEdit && !$this->getRequest()->isPost() )
                {
                    $form->getElement( 'password' )->setValue( OSS_String::random( 12 ) );
                    
                    if( $this->getParam( 'custid', false ) && ( $cust = $this->getD2EM()->getRepository( '\\Entities\\Customer' )->find( $this->getParam( 'custid' ) ) ) )
                        $form->getElement( 'custid' )->setValue( $cust->getId() );
                }
                
                if( $isEdit )
                    $form->getElement( 'custid' )->setValue( $object->getCustomer()->getId() );
                break;

            case \Entities\User::AUTH_CUSTADMIN:
                $form->removeElement( 'password' );
                $form->removeElement( 'privs' );
                $form->removeElement( 'custid' );
                if( $isEdit )
                {
                    $form->removeElement( 'name' );
                    $form->getElement( 'username' )->setAttrib( 'readonly', 'readonly' );
                }
                break;

            default:
                throw new OSS_Exception( 'Unhandled user type' );
        }

        if( !$isEdit )
        {
            $form->getElement( 'username' )->addValidator( 'OSSDoctrine2Uniqueness', true,
                [ 'entity' => '\\Entities\\User', 'property' => 'username' ]
            );
        }
    }


    /**
     *
     * @param IXP_Form_User $form The form object
     * @param \Entities\User $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @return bool
     */
    protected function addPreValidate( $form, $object, $isEdit )
    {
        // is this user allowed to edit this object?
        if( $isEdit && $this->getUser()->getPrivs() != \Entities\User::AUTH_SUPERUSER )
        {
            if( $this->getUser()->getCustomer() != $object->getCustomer() )
            {
                $this->addMessage( 'Illegal attempt to edit a user not under your control. The security team have been notified.' );
                $this->getLogger()->alert( "User {$this->getUser()->getUsername()} illegally tried to edit {$object->getUsername()}" );
                $this->redirect( 'user/list' );
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
    protected function addPostValidate( $form, $object, $isEdit )
    {
        
        if( $this->getUser()->getPrivs() == \Entities\User::AUTH_SUPERUSER )
        {
            $object->setCustomer(
                $this->getD2EM()->getRepository( '\\Entities\\Customer' )->find( $form->getElement( 'custid' )->getValue() )
            );
        }
                
        if( $isEdit )
        {
            $object->setLastupdated( new DateTime() );
            $object->setLastupdatedby( $this->getUser()->getId() );
        }
        else
        {
            $object->setCreated( new DateTime() );
            $object->setCreator( $this->getUser()->getUsername() );

            if( $this->getUser()->getPrivs() == \Entities\User::AUTH_CUSTADMIN )
            {
                $object->setCustomer( $this->getUser()->getCustomer() );
                $object->setParent( $this->getUser() );
                $object->setPrivs( \Entities\User::AUTH_CUSTUSER );
                $object->setPassword( OSS_String::random( 16 ) );

                $c = new \Entities\Contact();
                $c->setCustomer( $this->getUser()->getCustomer() );
                $c->setName( $form->getElement( 'name' )->getValue() );
                $c->setEmail( $form->getElement( 'email' )->getValue() );
                $c->setMobile( $form->getElement( 'authorisedMobile' )->getValue() );
                $c->setCreator( $this->getUser()->getUsername() );
                $c->setCreated( new DateTime() );
                $this->getD2EM()->persist( $c );
            }
            else
            {
                try
                {
                    $object->setParent(
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
                    $object->setParent( $object );
                }
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
        if( !$isEdit )
        {
            $this->view->newuser = $object;
            $this->sendWelcomeEmail( $object );
        }
        else
        {
            // users are cached so we should delete any existing cache entry for an edited user
            $this->clearUserFromCache( $object->getId() );
        }

        return true;
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
        $this->addMessage( 'User successfully ' . ( $isEdit ? ' edited.' : ' added.' ), OSS_Message::SUCCESS );
        
        if( $this->getUser()->getPrivs() != \Entities\User::AUTH_SUPERUSER )
            $this->redirect( 'user/list' );
        else
            $this->redirect( 'customer/overview/tab/users/id/' . $object->getCustomer()->getId() );
    }
    
    /**
     * Function which can be over-ridden to perform any pre-deletion tasks
     *
     * @param \Entities\User $object The Doctrine2 entity to delete
     * @return bool Return false to stop / cancel the deletion
     */
    protected function preDelete( $object )
    {
        // if I'm not an admin, then make sure I have permission!
        if( $this->getUser()->getPrivs() != \Entities\User::AUTH_SUPERUSER )
        {
            if( $object->getCustomer() != $this->getUser()->getCustomer() )
            {
                $this->getLogger()->notice( "{$this->getUser()->getUsername()} tried to delete other customer user {$object->getUsername()}" );
                $this->addMessage( 'You are not authorised to delete this user. The administrators have been notified.' );
                return false;
            }
        }
        else
        {
            // keep the customer ID for redirection on success
            $this->getSessionNamespace()->ixp_user_delete_custid = $object->getCustomer()->getId();
        }
        
        // now delete all the users privileges also
        foreach( $object->getPreferences() as $pref )
        {
            $object->removePreference( $pref );
            $this->getD2EM()->remove( $pref );
        }
        
        $this->getLogger()->info( "{$this->getUser()->getUsername()} deleted user {$object->getUsername()}" );
        
        
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
        if( $this->getUser()->getPrivs() == \Entities\User::AUTH_SUPERUSER )
        {
            // retrieve the customer ID
            if( $custid = $this->getSessionNamespace()->ixp_user_delete_custid )
            {
                unset( $this->getSessionNamespace()->ixp_user_delete_custid );
        
                $this->addMessage( 'Contact successfully deleted', OSS_Message::SUCCESS );
                $this->redirect( 'customer/overview/tab/users/id/' . $custid );
            }
        }
                
        return false;
    }
    
    /**
     * Show the last users to login
     *
     * Named for the UNIX 'last' command
     */
    public function lastAction()
    {
        $this->assertPrivilege( \Entities\User::AUTH_SUPERUSER );
        $this->view->last = $this->getD2EM()->getRepository( '\\Entities\\User' )->getLastLogins();
    }


    public function welcomeEmailAction()
    {
        $query = 'SELECT u FROM \\Entities\\User u WHERE u.id = ?1';
        
        if( $this->getUser()->getPrivs() == \Entities\User::AUTH_CUSTADMIN )
            $query .= ' AND u.Customer = ?2 AND u.privs = ?3';

        $q = $this->getD2EM()->createQuery( $query )
                ->setParameter( 1, $this->getParam( 'id', 0 ) );
        
        if( $this->getUser()->getPrivs() == \Entities\User::AUTH_CUSTADMIN )
        {
            $q->setParameter( 2, $this->getUser()->getCustomer() )
              ->setParameter( 3, \Entities\User::AUTH_CUSTUSER );
        }

        try
        {
            $user = $q->getSingleResult();
        }
        catch( Doctrine\ORM\NoResultException $e )
        {
            $this->addMessage( "Unknown or invalid user.", OSS_Message::ERROR );
            return $this->_forward( 'list' );
        }

        $this->view->resend  = true;
        $this->view->newuser = $user;
        
        if( $this->sendWelcomeEmail( $user ) )
            $this->addMessage( "Welcome email has been resent to {$user->getEmail()}", OSS_Message::SUCCESS );
        else
            $this->addMessage( "Due to a system error, we could not resend the welcome email to {$user->getEmail()}", OSS_Message::ERROR );
        
        $this->redirect( 'user/list' );
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

