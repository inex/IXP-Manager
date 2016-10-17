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
 * Controller: Manage users
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
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
            
            'addWhenEmpty'   => false
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
                    ],

                    'lastupdated'   => [
                        'title'     => 'Updated',
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

    
    public function addAction()
    {
        $this->redirect( 'contact/add' );
    }
    
    public function deleteAction()
    {
        // disabled as it is handled by the contact controller
        $this->redirect( 'user/list' );
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
                    u.created as created, u.disabled as disabled, c.id as custid, c.name as customer,
                    u.lastupdated AS lastupdated, contact.id AS contactid' )
            ->from( '\\Entities\\User', 'u' )
            ->leftJoin( 'u.Customer', 'c' )
            ->leftJoin( 'u.Contact', 'contact' );

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

