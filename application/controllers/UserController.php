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


/*
 *
 *
 * http://www.inex.ie/
 * (c) Internet Neutral Exchange Association Ltd
 */

class UserController extends INEX_Controller_FrontEnd
{

    public function init()
    {
        $this->frontend[ 'defaultOrdering' ] = 'name';
        $this->frontend[ 'model' ] = 'User';
        $this->frontend[ 'name' ] = 'User';
        $this->frontend[ 'pageTitle' ] = 'Users';

        $this->frontend[ 'columns' ] = array(

            'displayColumns' => array(
                'id', 'username', 'email', 'authorisedMobile', 'custid', 'privs', 'disabled'
            ),

	        'viewPanelRows' => array(
	            'username', 'email', 'authorisedMobile', 'custid', 'privs', 'disabled'
	        ),

	        'viewPanelTitle' => 'username',

	        'sortDefaults' => array(
	            'column' => 'username', 'order' => 'asc'
	        ),

	        'id' => array(
	            'label' => 'ID', 'hidden' => true
	        ),


	        'username' => array(
	            'label' => 'Username', 'sortable' => true
	        ),

	        'password' => array(
	            'label' => 'Password', 'sortable' => true
	        ),

	        'email' => array(
	            'label' => 'E-mail', 'sortable' => true
	        ),

	        'authorisedMobile' => array(
	            'label' => 'Authorised Mobile', 'sortable' => false
	        ),

	        'custid' => array(
	            'type' => 'hasOne', 'model' => 'Cust', 'controller' => 'customer', 'field' => 'name', 'label' => 'Customer', 'sortable' => true
	        ),

	        'privs' => array(
	            'label' => 'Privileges', 'sortable' => true, 'type' => 'xlate', 'xlator' => User::$PRIVILEGES_TEXT
	        ),

	        'disabled' => array(
	            'label' => 'Disabled', 'sortable' => true
	        )
        );

        parent::feInit();
    }

    /**
     * Checks / actions before we try and validate the form
     */
    protected function formPrevalidate( $form, $isEdit, $object )
    {
        // If we're a super user, then the length of the username is up to us
        if( $this->user['privs'] == User::AUTH_SUPERUSER )
            $form->getElement( 'username' )->removeValidator( 'stringLength' );
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
            ->select( 'up.attribute, up.value, u.username, c.shortname' )
            ->from( 'UserPref up' )
            ->leftJoin( 'up.User u' )
            ->leftJoin( 'u.Cust c' )
            ->where( 'up.attribute = ?', 'auth.last_login_at' )
            ->orderBy( 'up.value DESC' )
            ->execute( null, Doctrine_Core::HYDRATE_SCALAR );

        $this->view->last = $last;
        $this->view->display( 'user/last.tpl' );
    }
    
    
    
}

?>