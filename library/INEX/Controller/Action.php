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
 * INEX's version of Zend's Zend_Controller_Action implemented custom
 * functionality.
 *
 * All application controlers subclass this rather than Zend's version directly.
 *
 * @package INEX_Controller
 *
 */
class INEX_Controller_Action extends Zend_Controller_Action
{

    /**
     * A variable to hold an instance of the bootstrap object
     *
     * @var object An instance of the bootstrap object
     */
    protected $_bootstrap;

    /**
     * A variable to hold an instance of the configuration object
     *
     * @var object An instance of the configuration object
     */
    protected $config = null;

    /**
     * A variable to hold an instance of the logger object
     *
     * @see getLogger()
     * @var object An instance of the logger object
     */
    private $_logger = null;

    /**
     * A variable to hold the identity object
     *
     * @var object An instance of the user's identity or false
     */
    protected $auth = null;

    /**
     * A variable to hold an identify of the user
     *
     * Will be !false if there is a valid identity
     *
     * @var object An instance of the user's identity or false
     */
    protected $identity = false;

    /**
     * A variable to hold the user record
     *
     * @var object An instance of the user record
     */
    protected $user = null;

    /**
     * A variable to hold the session
     *
     * @var object An instance of the session
     */
    protected $session = null;

    /**
     * A variable to hold the customer record
     *
     * @var object An instance of the customer record
     */
    protected $customer = false;

    /**
     * A variable to hold an instance of our SMS class.
     *
     * @var object INEX_SMS_Clickatell instance.
     */
    private $_sms = null;

    
    /**
     * An array of id => cust.name for super users
     * @var array
     */
    protected $_customers = null;
    
    /**
     * Override the Zend_Controller_Action's constructor (which is called
     * at the very beginning of this function anyway).
     *
     * @param object $request See Parent class constructer
     * @param object $response See Parent class constructer
     * @param object $invokeArgs See Parent class constructer
     */
    public function __construct( Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = null )
    {
        // get the bootstrap object
        $this->_bootstrap = $invokeArgs[ 'bootstrap' ];
        
        // and from the bootstrap, we can get other resources:
        $this->config = $this->_bootstrap->getApplication()->getOptions();
        
        
        // Smarty must be set during bootstrap
        try
        {
            $this->view = $this->_bootstrap->getResource( 'view' );
            if( php_sapi_name() != 'cli' )
            {
                $this->view->pagebase = 'http' . (isset( $_SERVER[ 'HTTPS' ] ) ? 's' : '') . '://' . $_SERVER[ 'SERVER_NAME' ] . Zend_Controller_Front::getInstance()->getBaseUrl();
                $this->session = $this->_bootstrap->getResource( 'namespace' );
                $this->view->session = $this->session;
            }
            
            $this->view->basepath = Zend_Controller_Front::getInstance()->getBaseUrl();
            

            $this->auth = $this->_bootstrap->getResource( 'auth' );
            
            if( $this->auth->hasIdentity() )
            {
                $this->identity = $this->auth->getIdentity();
                $this->user = Doctrine::getTable( 'User' )->find( $this->identity[ 'user' ][ 'id' ] );
                $this->customer = Doctrine::getTable( 'Cust' )->find( $this->identity[ 'user' ][ 'custid' ] );
            }
            
            
            $this->view->auth = $this->auth;
            $this->view->hasIdentity = $this->auth->hasIdentity();
            $this->view->identity = $this->identity;
            $this->view->customer = $this->customer;
            $this->view->user = $this->user;
            $this->view->config = $this->config;
            
            // pull a message from the session if it exists
            // (this is when we do a ->_redirect after an action)
            if( php_sapi_name() != 'cli' && isset( $this->session->message ) && $this->session->message !== null )
            {
                $this->view->message = $this->session->message;
                $this->session->message = null;
            }
            

            // should we check the change log? (and if so, only once per session)
            if( php_sapi_name() != 'cli' && $this->config[ 'change_log' ][ 'enabled' ] && $this->auth->hasIdentity() )
            {
                if( isset( $this->session->change_log_has_updates ) ) {
                    $this->view->change_log_has_updates = $this->session->change_log_has_updates;
                }
                else {
                    $lastSeen = $this->user->getPreference( 'change_log.last_seen' );
                    
                    // don't alert past changes to new users
                    if( $lastSeen === false ) {
                        $this->user->setPreference( 'change_log.last_seen', date( 'Y-m-d H:i:s' ) );
                        $lastSeen = date( 'Y-m-d H:i:s' );
                    }
                    
                    $this->session->change_log_has_updates = ChangeLogTable::hasUpdates( $this->user[ 'privs' ], $lastSeen );
                    $this->view->change_log_has_updates = $this->session->change_log_has_updates;
                }
            }
        }
        catch( Zend_Exception $e ) {
            echo "Caught exception: " . get_class( $e ) . "\n";
            echo "Message: " . $e->getMessage() . "\n";
            
            die( "\n\nYou must set-up Smarty in the bootstrap code.\n\n" );
        }
        
        // call the parent's version where all the Zend magic happens
        parent::__construct( $request, $response, $invokeArgs );
        $this->view->controller = $this->getRequest()->getParam( 'controller' );
        $this->view->action = $this->getRequest()->getParam( 'action' );
        
        // see if the user's session has timed out
        if( php_sapi_name() != 'cli' && $this->auth->hasIdentity() )
        {
            if( (mktime() - $this->session->timeOfLastAction) > $this->config[ 'resources' ][ 'session' ][ 'remember_me_seconds' ] ) {
                $this->auth->clearIdentity();
                $this->view->message = new INEX_Message( 'To protect your account and information, you have been logged out automatically ' . 'due to an extended period of inactivity. Please log in again below to continue.', INEX_Message::MESSAGE_TYPE_ALERT );
                
                Zend_Session::destroy( true, true );
                $this->view->display( 'auth/login.tpl' );
                ob_end_flush();
                die();
            }
            
            $this->session->timeOfLastAction = mktime();
        }
        
        if( $this->auth->hasIdentity() && $this->identity['user']['privs'] == 3 )
            $this->superuserSetup();
    }

    /**
     * A utility function to get parameters in $_GET. It strips the slashes
     * added by PHP as well as trimming the whitespace.
     *
     * @param string $name The parameter name to get (i.e. $_GET[$name])
     * @param bool $trim Trim whitespace from the string. Default: true.
     * @return mixed The parameter if it exists, else null.
     */
    public static function inexGetGet( $name, $trim = true )
    {
        if( isset( $_GET[ $name ] ) ) {
            if( $trim )
                return trim( stripslashes( $_GET[ $name ] ) );
            else
                return stripslashes( $_GET[ $name ] );
        }
        else
            return null;
    }

    
    /**
     * A utility function to get parameters in $_POST. It strips the slashes
     * added by PHP as well as trimming the whitespace.
     *
     * @param string $name The parameter name to get (i.e. $_POST[$name])
     * @param bool $trim Trim whitespace from the string. Default: true.
     * @return mixed The parameter if it exists, else null.
     */
    public static function inexGetPost( $name, $trim = true )
    {
        if( isset( $_POST[ $name ] ) ) {
            if( $trim )
                return trim( stripslashes( $_POST[ $name ] ) );
            else
                return stripslashes( $_GET[ $name ] );
        }
        else
            return null;
    }

    /**
     * A utility function to get parameters in either $_POST or $_GET respectivily.
     * It strips the slashes added by PHP as well as trimming the whitespace.
     *
     * If the parameter does not exist in POST, we try GET.
     *
     * @param string $name The parameter name to get (i.e. $_POST/GET[$name])
     * @param bool $trim Trim whitespace from the string. Default: true.
     * @return mixed The parameter if it exists, else null.
     */
    public static function inexGetRequest( $name, $trim = true )
    {
        if( ($value = INEX_Controller_Action::inexGetPost( $name )) !== null )
            return $value;
        else
            return INEX_Controller_Action::inexGetGet( $name );
    }

    /**
     * Utility function to instantiate the INEX_SMS_Clickatell resource.
     */
    public function getSMS()
    {
        if( $this->_sms === null ) {
            $this->_sms = new INEX_SMS_Clickatell( $this->config[ 'sms' ][ 'clickatell' ][ 'username' ], $this->config[ 'sms' ][ 'clickatell' ][ 'password' ], $this->config[ 'sms' ][ 'clickatell' ][ 'api_id' ], $this->config[ 'sms' ][ 'clickatell' ][ 'sender_id' ] );
        }
        
        return $this->_sms;
    }

    /**
     * Get the user object
     *
     * @return User The user object
     */
    protected function getUser()
    {
        return $this->user;
    }

    /**
     * Get the logger object (and bootstrap it if not already done)
     *
     * @return Zend_Log The log object
     */
    protected function getLogger()
    {
        if( $this->_logger === null )
            $this->_logger = $this->getBootstrap()->getResource( 'logger' );
            
        return $this->_logger;
    }

    /**
     * Get the bootstrap object
     *
     * @return Zend_Application_Bootstrap_Bootstrap object
     */
    protected function getBootstrap()
    {
        return $this->_bootstrap;
    }
    
    
    
    /**
     * Set an array of customer id and names
     *
     * FIXME Move to central cache rather than per-user
     */
    private function superuserSetup()
    {
        // get an array of customer id => names
        if( !isset( $this->session->ahome_customers ) )
            $this->session->ahome_customers = CustTable::getAllNames();
        
        $this->view->customers = $this->_customers = $this->session->ahome_customers;
    }
}

