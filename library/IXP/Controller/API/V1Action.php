<?php

/*
 * Copyright (C) 2009-2013 Internet Neutral Exchange Association Limited.
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
 * @package IXP_Controller
 *
 */
class IXP_Controller_API_V1Action extends OSS_Controller_Action
{

    // traits we want to use
    // use OSS_Controller_Action_Trait_Namespace;
    // use OSS_Controller_Action_Trait_Doctrine2User;
    // use OSS_Controller_Action_Trait_Auth;
    // use OSS_Controller_Action_Trait_AuthRequired;
    use OSS_Controller_Action_Trait_Doctrine2Cache;
    use OSS_Controller_Action_Trait_Doctrine2;
    use OSS_Controller_Action_Trait_Mailer;
    // use OSS_Controller_Action_Trait_License;
    use OSS_Controller_Action_Trait_Logger;
    // use OSS_Controller_Action_Trait_Smarty;
    // use OSS_Controller_Action_Trait_StatsD;
    // use OSS_Controller_Action_Trait_Freshbooks;
    // use OSS_Controller_Action_Trait_Messages;
    // use OSS_Controller_Action_Trait_News;
    
    use IXP_Controller_Trait_Common;
    
    
    /**
     * A variable to hold the user record
     *
     * @var \Entities\User An instance of the user record
     */
    protected $_user = false;
    
    
    /**
     * Override the Zend_Controller_Action's constructor (which is called
     * at the very beginning of this function anyway).
     *
     * @param object $request See Parent class constructer
     * @param object $response See Parent class constructer
     * @param object $invokeArgs See Parent class constructer
     */
    public function __construct(
                Zend_Controller_Request_Abstract $request,
                Zend_Controller_Response_Abstract $response,
                array $invokeArgs = null )
    {
        // call the parent's version where all the Zend magic happens
        parent::__construct( $request, $response, $invokeArgs );

        $errorHandler = $this->getFrontController()->getPlugin( 'Zend_Controller_Plugin_ErrorHandler' );
        $errorHandler->setErrorHandlerModule( 'apiv1' );
        $errorHandler->setErrorHandlerController( 'error' );
        $errorHandler->setErrorHandlerAction( 'error' );
    }

    
    /**
     * Get the user ORM object.
     *
     * Returns the instance of the Doctrine User object for the logged in user.
     *
     * @return \Entities\User The user object or false.
     */
    protected function getUser()
    {
        if( $this->_user === false )
        {
            try
            {
                if( !( $apiKey = $this->getParam( 'key', false ) ) )
                    return false;
                
                $this->_user = $this->getD2EM()->createQuery(
                        "SELECT u FROM \\Entities\\User u LEFT JOIN u.ApiKeys a WHERE a.apiKey = ?1" )
                    ->setParameter( 1, $apiKey )
                    ->useResultCache( true, 3600, 'oss_d2u_user_apikey_' . $apiKey )
                    ->getSingleResult();
            }
            catch( \Doctrine\ORM\NoResultException $e )
            {
                return false;
            }
        }
    
        return $this->_user;
    }
    
    /**
     * Get the customer object
     *
     * @return Entities\Customer The customer object for the current user
     */
    protected function getCustomer()
    {
        return $this->getUser()->getCustomer();
    }
    
    /**
     * Assert that a valid API key has been provided and that the user has the specified permissions
     *
     * Throws an exception which is caught by the API Error Controller if tests fail.
     *
     * @param int $priv From \Entities\User::AUTH_XXX
     * @return \Entities\User
     */
    protected function assertUserPriv( $priv )
    {
        $u = $this->getUser();
        
        if( $u && $u->getPrivs() == $priv )
            return $u;
        else if( $u )
            throw new Zend_Controller_Action_Exception( 'Invalid user privileges', 401 );
        else
            throw new Zend_Controller_Action_Exception( 'Valid API key required', 401 );
    }
    
    
    /**
     * Dummy function to allow CLI and API code to intermingle.
     */
    protected function verbose( $msg, $implictNewline = true )
    {}
    

}

