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


/*
 *
 *
 * http://www.inex.ie/
 * (c) Internet Neutral Exchange Association Ltd
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
        
            'listColumns' => [
                'id' => [ 'title' => 'UID', 'display' => false ],
                'username'   => 'Username',
                'email'      => 'Email'
            ],
        
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
                    'email'         => 'Email'
                ];
                break;
                
            case \Entities\User::AUTH_CUSTADMIN:
                $this->_feParams->pagetitle = 'User Admin for ' . $this->getUser()->getCustomer()->getName();
    
                $this->_feParams->listColumns = [
                    'id' => [ 'title' => 'UID', 'display' => false ],
                    'username'      => 'Userame',
                    'email'         => 'Email',
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
        $this->_feParams->viewColumns = $this->_feParams->listColumns;
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
                    u.created as created, c.id as custid, c.name as customer' )
            ->from( '\\Entities\\User', 'u' )
            ->leftJoin( 'u.Customer', 'c' );

        if( $this->getUser()->getPrivs() == \Entities\User::AUTH_CUSTADMIN )
        {
            $qb->where( 'u.Customer = ?1' )
                ->setParameter( 1, $this->getUser()->getCustomer() );
        }
        
        if( isset( $this->_feParams->listOrderBy ) )
            $qb->orderBy( $this->_feParams->listOrderBy, isset( $this->_feParams->listOrderByDir ) ? $this->_feParams->listOrderByDir : 'ASC' );
    
        if( $id !== null )
            $qb->andWhere( 'u.id = ?2' )->setParameter( 2, $id );
    
        return $qb->getQuery()->getResult();
    }
    
    /**
     * Get the `Zend_Form` object for adding / editing actions with some processing.
     *
     * We shouldn't override this but I've changed the constructor...
     *
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @param object $object The Doctrine2 entity (being edited or blank for add)
     * @param array $options Options passed onto Zend_Form
     * @param string $cancelLocation Where to redirect to if 'Cancal' is clicked
     * @return Zend_Form
     */
    protected function getForm( $isEdit, $object, $options = null, $cancelLocation = null )
    {
        if( $cancelLocation === null )
            $cancelLocation = $this->_getBaseUrl() . '/index';
    
        $formName = $this->feGetParam( 'form' );
        $form = new $formName( $options, $isEdit, $cancelLocation, $this->getUser()->getPrivs() == \Entities\User::AUTH_CUSTADMIN  );
        return $this->formPostProcess( $form, $object, $isEdit, $options = null, $cancelLocation = null );
    }
    
    
    
    /**
     * Checks / actions before we try and validate the form
     */
    protected function formPrevalidate( $form, $isEdit, $object )
    {
        // If we're a super user, then the length of the username is up to us
        if( $this->user['privs'] == User::AUTH_SUPERUSER )
            $form->getElement( 'username' )->removeValidator( 'stringLength' );
        
        if( $cid = $this->_getParam( 'custid', false ) )
        {
            $form->getElement( 'custid' )->setValue( $cid );
            $form->getElement( 'cancel' )->setAttrib( 'onClick', "parent.location='"
                . $this->genUrl( 'customer', 'dashboard', array( 'id' => $cid ) ) . "'"
            );
        }
        else if( $isEdit )
        {
            $form->getElement( 'cancel' )->setAttrib( 'onClick', "parent.location='"
                . $this->genUrl( 'customer', 'dashboard', array( 'id' => $object['custid'] ) ) . "'"
            );
        }
            
        // propose a random password to help the user out
        if( !$isEdit )
            $form->getElement( 'password' )->setValue( INEX_String::random() );
        
    }

    protected function _addEditSetReturnOnSuccess( $form, $object )
    {
        if( $this->user['privs'] == User::AUTH_SUPERUSER )
            return "customer/dashboard/id/{$object['custid']}";
        else
            return 'user';
    }
    
    /**
     * Additional checks when a new object is being added.
     */
    protected function formValidateForAdd( $form )
    {

        // is there already a user with this username?
        if( Doctrine::getTable( $this->getModelName() )->findOneByUsername( $form->getValue( 'username' ) ) ) {
            $form->getElement( 'username' )->addError( 'This username is not available' );
            return false;
        }


    }


    /**
     * If we're adding or editing a user, set the parent_id accordingly
     *
     * @param Doctrine_Record $object The object being built for adding or edited
     * @param bool $isEdit True if this is an edit, false if it's an add
     * @param Zend_Form $form The submitted add / edit form
     */
    protected function addEditPreSave( $object, $isEdit, $form )
    {
        if( $object['privs'] == User::AUTH_CUSTUSER && $user = Doctrine::getTable( 'User' )->findOneByCustidAndPrivs( $form->getValue( 'custid' ), User::AUTH_CUSTADMIN ) )
            $object['Parent'] = $user;
    }

    /**
     * Send the user a message by SMS
     */
    protected function sendSmsAction()
    {
        $options = $this->_bootstrap->getApplication()->getOptions();

        if( $this->getRequest()->getParam( 'id' ) !== NULL && is_numeric( $this->getRequest()->getParam( 'id' ) ) ) {
            // is the ID valid?
            if( !($object = Doctrine::getTable( $this->frontend[ 'model' ] )->find( $this->getRequest()->getParam( 'id' ) )) ) {
                echo "0:Err:No entry with ID: " . $this->getRequest()->getParam( 'id' );
                return false;
            }

            // we actually don't use the ID at all in the end!
            if( $this->getRequest()->getParam( 'to' ) !== NULL && is_numeric( $this->getRequest()->getParam( 'to' ) ) && $this->getRequest()->getParam( 'message' ) !== NULL && strlen( $this->getRequest()->getParam( 'message' ) ) > 5 )
            {

                $sms = new INEX_SMS_Clickatell(
                        $options['sms']['clickatell']['username'],
                        $options['sms']['clickatell']['password'],
                        $options['sms']['clickatell']['api_id'],
                        $options['sms']['clickatell']['sender_id']
                );

                if( $sms->send( $this->getRequest()->getParam( 'to' ), stripslashes( $this->getRequest()->getParam( 'message' ) ) ) )
                    echo "1:SMS successfully sent ({$sms->apiResponse})";
                else
                    echo "0:{$sms->apiResponse}";
            }
            else {
                echo '0:Err:One or more of your submitted parameters were incorrect.';
            }

        }
    }

    /**
     * Send the user an email by SMS
     */
    protected function sendEmailAction()
    {
        $options = $this->_bootstrap->getApplication()->getOptions();

        if( $this->getRequest()->getParam( 'id' ) !== NULL && is_numeric( $this->getRequest()->getParam( 'id' ) ) ) {
            // is the ID valid?
            if( !($object = Doctrine::getTable( $this->frontend[ 'model' ] )->find( $this->getRequest()->getParam( 'id' ) )) ) {
                echo "0:Err:No entry with ID: " . $this->getRequest()->getParam( 'id' );
                return false;
            }

            // we actually don't use the ID at all in the end!
            if( $this->getRequest()->getParam( 'to' ) !== NULL && $this->getRequest()->getParam( 'message' ) !== NULL && strlen( $this->getRequest()->getParam( 'message' ) ) > 5 ) {
                try {
                    $mail = new Zend_Mail( );
                    $mail->setBodyHtml( stripslashes( $this->getRequest()->getParam( 'message' ) ) )
                         ->setFrom( $options['identity']['email'], $options['identity']['name'] )
                         ->addTo( $this->getRequest()->getParam( 'to' ) )
                         ->setSubject( $this->getRequest()->getParam( 'to' ) )
                         ->send();

                    echo "1:Email successfully sent";
                }
                catch( Zend_Exception $e ) {
                    echo '0:' . $e->getMessage();
                }
            }
            else {
                echo '0:Err:One or more of your submitted parameters were incorrect.<pre>' . $this->getRequest()->getParam( 'message' ) . '</pre>';
            }

        }
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

?>