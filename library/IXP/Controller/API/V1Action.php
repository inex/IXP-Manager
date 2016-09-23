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
    use OSS_Controller_Action_Trait_Smarty;
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

                $key = $this->getD2EM()->createQuery(
                        "SELECT a FROM \\Entities\\ApiKey a WHERE a.apiKey = ?1" )
                    ->setParameter( 1, $apiKey )
                    ->useResultCache( true, 3600, 'oss_d2u_user_apikey_' . $apiKey )
                    ->getSingleResult();

                $this->_user = $key->getUser();

                $key->setLastseenAt( new \DateTime() );
                $key->setLastseenFrom( $_SERVER['REMOTE_ADDR'] );
                $this->getD2EM()->flush();
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
     * Assert that a valid API key has been provided and that the user has the minimum specified permissions
     *
     * Throws an exception which is caught by the API Error Controller if tests fail.
     *
     * @param int $priv From \Entities\User::AUTH_XXX
     * @return \Entities\User
     */
    protected function assertMinUserPriv( $priv )
    {
        $u = $this->getUser();

        if( $u && $u->getPrivs() >= $priv )
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



    /**
     * API utility function to get and validate a given VLAN by ID
     *
     * @param bool $required If false, will return false if no / invalid VLAN ID specified
     * @return \Entities\Vlan The requested VLAN
     */
    public function apiGetParamVlan( $required = true )
    {
        $vlanid = $this->getParam( 'vlanid', false );

        if( !$vlanid || !( $vlan = $this->getD2R( '\\Entities\\Vlan' )->find( $vlanid ) ) )
        {
            if( $required )
                throw new Zend_Controller_Action_Exception( 'Invalid or no VLAN ID specified.', 401 );

            return false;
        }

        return $vlan;
    }

    /**
     * API utility function to get and validate a given IP protocol
     *
     * @param bool $required If false, will return false if no / invalid protocol specified
     * @return int|bool The specified protocol (4/6) or false
     */
    public function apiGetParamProtocol( $required = true )
    {
        $p = $this->getParam( 'proto', false );

        if( !$p || !in_array( $p, [ 4, 6 ] ) )
        {
            if( $required )
                throw new Zend_Controller_Action_Exception( 'Invalid or no protocol specified.', 401 );

            return false;
        }

        return $p;
    }

    /**
     * API utility function to get a named parameter
     *
     * @param string $param The name of the parameter to get
     * @param bool $required If true, will throw an error and die if not present
     * @param string $default If not set in the CLI parameters, default to this (if not false)
     * @return int|bool The requested parameter or false
     */
    public function apiGetParam( $param, $required = false, $default = false )
    {
        $p = $this->getParam( $param, false );

        if( $p === false && $default )
            $p = $default;

        if( $p === false && $required )
            throw new Zend_Controller_Action_Exception( "Required parameter {$param} missing", 401 );

        return $p;
    }

    /**
     * Utility function to (optionally) load a Smarty config file specified by a 'config' parameter.
     *
     * This config parameter must reference the name of a config file as follows:
     *
     *     APPLICATION_PATH . "/configs/" . preg_replace( '/[^\da-z_\-]/i', '', $cfile ) . ".conf";
     *
     * @throws Zend_Controller_Action_Exception If the file cannot be read
     * @return bool True if a config file was specified and loaded. False otherwise.
     */
    public function apiLoadConfig()
    {
        $cfile = $this->getParam( 'config', false );
        if( $cfile )
        {
            $cfile = APPLICATION_PATH . "/configs/" . preg_replace( '/[^\da-z_\-]/i', '', $cfile ) . ".conf";
            if( file_exists( $cfile ) && is_readable( $cfile ) )
            {
                $this->getView()->configLoad( $cfile );
                return true;
            }

            throw new Zend_Controller_Action_Exception( 'Cannot open / read specified configuration file', 401 );
        }

        return false;
    }

}
