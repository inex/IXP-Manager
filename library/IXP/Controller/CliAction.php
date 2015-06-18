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
class IXP_Controller_CliAction extends OSS_Controller_CliAction
{

    use IXP_Controller_Trait_Common;

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

        // we need this for access to class constants in the template
        $this->view->registerClass( 'USER',       '\\Entities\\User' );
        $this->view->registerClass( 'CUSTOMER',   '\\Entities\\Customer' );
        $this->view->registerClass( 'SWITCHER',   '\\Entities\\Switcher' );
        $this->view->registerClass( 'SWITCHPORT', '\\Entities\\SwitchPort' );
        $this->view->registerClass( 'VLAN',       '\\Entities\\Vlan' );

        $this->view->resellerMode  = $this->resellerMode();
        $this->view->multiIXP      = $this->multiIXP();
        $this->view->as112UiActive = $this->as112UiActive();
    }


    /**
     * CLI utility function to get the requested IXP.
     *
     * Most CLI actions require a specific IXP in multi-IXP mode. This function looks for
     * that paramater, validates it, loads and returns the requested IXP. In non-multi-IXP
     * environments, it returns the default IXP.
     *
     * @param bool $required If false, will return false in multi-IXP mode if no / invalid IXP specified
     * @return \Entities\IXP The requested / default IXP
     */
    public function cliResolveIXP( $required = true )
    {
        // what IXP are we running on here?
        if( $this->multiIXP() )
        {
            $ixpid = $this->getParam( 'ixp', false );

            if( !$ixpid || !( $ixp = $this->getD2R( '\\Entities\\IXP' )->find( $ixpid ) ) )
            {
                if( $required )
                    die( "ERROR: Invalid or no IXP specified.\n" );

                return false;
            }
        }
        else
            $ixp = $this->getD2R( '\\Entities\\IXP' )->getDefault();

        return $ixp;
    }

    /**
     * CLI utility function to get and validate a given VLAN by ID
     *
     * @param bool $required If false, will return false if no / invalid VLAN ID specified
     * @return \Entities\Vlan The requested VLAN
     */
    public function cliResolveVlanId( $required = true )
    {
        $vlanid = $this->getParam( 'vlanid', false );

        if( !$vlanid || !( $vlan = $this->getD2R( '\\Entities\\Vlan' )->find( $vlanid ) ) )
        {
            if( $required )
                die( "ERROR: Invalid or no VLAN ID specified.\n" );

            return false;
        }

        return $vlan;
    }

    /**
     * CLI utility function to get and validate a given IP protocol
     *
     * @param bool $required If false, will return false if no / invalid protocol specified
     * @return int|bool The specified protocol (4/6) or false
     */
    public function cliResolveProtocol( $required = true )
    {
        $p = $this->getParam( 'proto', false );

        if( !$p || !in_array( $p, [ 4, 6 ] ) )
        {
            if( $required )
                die( "ERROR: Invalid or no protocol specified.\n" );

            return false;
        }

        return $p;
    }

    /**
     * Looks for a ''asn'' parameter, or defaults to ''$default'', or throws an error.
     *
     * The generation of some configurations requires an ASN. We allow the user to specify
     * an ASN on the command line (highest precedence) or via ''application.ini'' (the default).
     * If neither are specified, we throw an error.
     *
     * @param string $default Typically from ''application.ini'' but any string can be passed
     * @return string The ASN
     */
    public function cliResolveASN( $default = false )
    {
        if( $a = $this->getParam( 'asn', false ) )
            return $a;

        if( $default )
            return $default;

        die( "ERROR: No ASN configured in application.ini or passed as a parameter\n" );
    }

    /**
     * Utility function to get a named CLI parameter
     *
     * @param string $param The name of the parameter to get
     * @param bool $required If true, will throw an error and die if not present
     * @param string $default If not set in the CLI parameters, default to this (if not false)
     * @return int|bool The requested parameter or false
     */
    public function cliResolveParam( $param, $required = false, $default = false )
    {
        $p = $this->getParam( $param, false );

        if( $p === false && $default )
            $p = $default;

        if( $p === false && $required )
            die( "ERROR: Required paramater '{$param}' missing\n" );

        return $p;
    }

    /**
     * Looks for a ''target'' parameter, or defaults to ''$default'', or throws an error.
     *
     * The generation of route configuration requires a target template directory. We allow
     * the user to specify a target directory on the command line (highest precedence) or via
     * ''application.ini'' (the default). If neither are specified, we throw an error.
     *
     * @param string $default Typically from ''application.ini'' but any string can be passed
     * @return string The target template directory
     */
    protected function cliResolveTarget( $default = false )
    {
        if( $t = $this->getParam( 'target', false ) )
            return $t;
    
        if( $default )
            return $default;
    
        die( "ERROR: No target router type configured in application.ini or passed as a parameter\n");
    }
    
    
    /**
     * Utility function to (optionally) load a Smarty config file specified by a 'config' parameter.
     *
     * This config parameter must reference the name of a config file as follows:
     *
     *     APPLICATION_PATH . "/configs/" . preg_replace( '/[^\da-z_\-]/i', '', $cfile ) . ".conf";
     *
     * @throws IXP_Exception If the file cannot be read
     * @return bool True if a config file was specified and loaded. False otherwise.
     */
    public function cliLoadConfig()
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
    
            throw new IXP_Exception( 'Cannot open / read specified configuration file' );
        }
    
        return false;
    }
    
}

