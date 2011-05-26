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


/**
 * INEX Internet Exchange Point Application :: INEX IXP
 *
 * Copyright (c) 2009 Internet Neutral Exchange Limited <http://www.inex.ie/>
 * All rights reserved.
 *
 * @category INEX
 * @version $Id: Namespace.php 13 2009-09-30 15:15:33Z barryo $
 * @package INEX_Bootstrap_Resources
 * @copyright Copyright (c) 2009 Internet Neutral Exchange Limited <http://www.inex.ie/>
 *
 */


/**
 * Class to instantiate Namespace
 *
 * @category INEX
 * @package INEX_Bootstrap_Resources
 * @copyright Copyright (c) 2009 Internet Neutral Exchange Limited <http://www.inex.ie/>
 */
class INEX_Resource_Namespace extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Holds the Logger instance
     *
     * @var
     */
    protected $_namespace;


    public function init()
    {
        // Return session so bootstrap will store it in the registry
        return $this->getNamespace();
    }


    public function getNamespace()
    {
        if( null === $this->_namespace )
        {
            $this->getBootstrap()->bootstrap( 'session' );

            // Get session configuration options from the application.ini file
            $options = $this->getOptions();

            $ApplicationNamespace = new Zend_Session_Namespace( 'Application' );

            // Secutiry tip from http://framework.zend.com/manual/en/zend.session.global_session_management.html
            if( !isset( $Application->initialised ) )
            {
                // FIXME This breaks the graphs on the dashboard! Zend_Session::regenerateId();
                $ApplicationNamespace->initialized = true;
            }

            $ApplicationNamespace->setExpirationSeconds( $options['timeout'] );

            // ensure IP consistancy
            if( $options['checkip'] )
            {
                if( isset( $_SERVER ) && array_key_exists( 'REMOTE_ADDR', $_SERVER ) )
                    $ip = $_SERVER['REMOTE_ADDR'];
                else
                    $ip = 'CLI';

                if( !isset( $ApplicationNamespace->clientIP ) )
                {
                    $ApplicationNamespace->clientIP = $ip;
                }
                else if( $ApplicationNamespace->clientIP != $ip )
                {
                    // security violation - client IP has changed indicating a possible hijacked session
                    $this->getBootstrap()->bootstrap( 'Logger' );
                    $this->getBootstrap()->getResource('logger')->warn(
                        "IP address changed - possible session hijack attempt."
                        . "OLD: {$ApplicationNamespace->clientIP} NEW: {$_SERVER['REMOTE_ADDR']}"
                    );
                    Zend_Session::destroy( true, true );
                    die(
	                "Your IP address has changed. As such, your session has been destroyed for your own security. Please refresh your browser."
	            );
                }
            }

            $this->_namespace = $ApplicationNamespace;

        }

        return $this->_namespace;
    }


}
