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
 * @category   INEX
 * @package    INEX_Controller
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class UserController extends INEX_Controller_FrontEnd
{

    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\User',
            'form'          => 'INEX_Form_User',
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
                $this->_feParams->pagetitle = 'Customer Users';

                $this->_feParams->listColumns = [
                    'id' => [ 'title' => 'UID', 'display' => false ],

                    'customer'  => [
                        'title'      => 'Customer',
                        'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                        'controller' => 'customer',
                        'action'     => 'overview',
                        'idField'    => 'custid'
                    ],

                    'username'      => 'Userame',
                    'email'         => 'Email',

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
                    'username'      => 'Userame',
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
            ->select( 'u.id as id, u.username as username, u.email as email,
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
     * @param INEX_Form_User $form The form object
     * @param \Entities\User $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @return void
     */
    protected function formPostProcess( $form, $object, $isEdit )
    {
        switch( $this->getUser()->getPrivs() )
        {
            case \Entities\User::AUTH_SUPERUSER:
                $form->removeElement( 'name' );
                $form->getElement( 'username' )->removeValidator( 'stringLength' );
                if( !$isEdit && !$this->isPost() )
                    $form->getElement( 'password' )->setValue( OSS_String::random( 12 ) );
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
     * @param INEX_Form_User $form The form object
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
                return false;
            }
        }

        return true;
    }

    /**
     *
     * @param INEX_Form_User $form The form object
     * @param \Entities\User $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @return void
     */
    protected function addPostValidate( $form, $object, $isEdit )
    {
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
                $object->setParent(
                    $this->getD2EM()->createQuery(
                        'SELECT u FROM Entities\User u WHERE u.privs = ?1 AND u.custid = ?2 LIMIT 1'
                    )
                    ->setParameter( ':p1', \Entities\User::AUTH_CUSTADMIN )
                    ->setParameter( ':p2', $form->getElement( 'custid' )->getValue() )
                    ->getScalarResult()
                );
            }
        }

        return true;
    }


    /**
     *
     * @param INEX_Form_User $form The form object
     * @param \Entities\User $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @return void
     */
    protected function addPostFlush( $form, $object, $isEdit )
    {
        if( !$isEdit )
        {
            $this->view->newuser = $object;

            try
            {
                $mail = $this->getMailer();
                $mail->setFrom( $this->_options['identity']['email'], $this->_options['identity']['name'] )
                     ->setSubject( $this->_options['identity']['sitename'] . ' - ' . _( 'Your Access Details' ) )
                     ->addTo( $object->getEmail(), $form->getElement( 'name' )->getValue() )
                     ->setBodyHtml( $this->view->render( 'user/email/html/welcome.phtml' ) )
                     ->send();
            }
            catch( Zend_Mail_Exception $e )
            {
                $this->getLogger()->alert( "Could not send welcome email for new user!\n\n" . $e->toString() );
            }
        }
        else
        {
            // users are cached so we should delete any existing cache entry for an edited user
            $this->getD2Cache()->delete( 'ixp_user_' . $object->getId() );
        }

        return true;
    }



    /**
     * Show the last users to login
     *
     * Named for the UNIX 'last' command
     */
    public function lastAction()
    {
        $last = Doctrine_Query::create()
            ->select( 'up.attribute, up.value, u.username, u.email, c.name, c.id' )
            ->from( 'UserPref up' )
            ->leftJoin( 'up.User u' )
            ->leftJoin( 'u.Cust c' )
            ->where( 'up.attribute = ?', 'auth.last_login_at' )
            ->orderBy( 'up.value DESC' )
            ->limit( 100 )
            ->execute( null, Doctrine_Core::HYDRATE_SCALAR );

        $this->view->last = $last;
        $this->view->display( 'user/last.tpl' );
    }



}

