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
 * @package IXP_Controller
 *
 */
class IXP_Controller_CliAction extends OSS_Controller_Action
{

    // traits we want to use
    use OSS_Controller_Action_Trait_Doctrine2Cache;
    use OSS_Controller_Action_Trait_Doctrine2;
    use OSS_Controller_Action_Trait_Mailer;
    use OSS_Controller_Action_Trait_Logger;

    /**
     * Verbose flag
     */
    private $_verbose = false;

    /**
     * Debug flag
     */
    private $_debug = false;

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

        //Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
        if ( php_sapi_name() != 'cli' )
        {
            $this->getLogger()->warn( 'Non CLI access to a CLI controller from ' . $_SERVER['REMOTE_ADDR'] . ' tp ' . $_SERVER['REQUEST_URI'] );
            die( 'Unauthorised access to action!' );
        }

        $this->_verbose = $this->getFrontController()->getParam( 'verbose', false );
        $this->_debug   = $this->getFrontController()->getParam( 'debug', false );
    }

    /**
     * True if the user has requested verbose mode
     */
    public function isVerbose()
    {
        return $this->_verbose;
    }

    /**
     * If running in verbose mode, echoes the request msg
     *
     * @param string $msg The message
     * @param bool $implicitNewline Set to false to prevent a newline from being echoed
     */
    public function verbose( $msg, $implicitNewline = true )
    {
        if( $this->_verbose )
            echo "{$msg}" . ( $implicitNewline ? "\n" : "" );
    }


    /**
     * True if the user has requested debug mode
     */
    public function isDebug()
    {
        return $this->_debug;
    }

    /**
     * If running in debug mode, echoes the request msg
     *
     * @param string $msg The message
     * @param bool $implicitNewline Set to false to prevent a newline from being echoed
     */
    public function debug( $msg, $implicitNewline = true )
    {
        if( $this->_debug )
            echo "{$msg}" . ( $implicitNewline ? "\n" : "" );
    }
}

