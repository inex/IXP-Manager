#! /usr/bin/env php
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
 * INEX CLI Tool
 *
 * Executes actions in the CliController
 *
 * Barry O'Donovan <barryo@inex.ie>
 *
 * http://www.inex.ie/
 * (c) Copyright 2009 Internet Neutral Exchange Association Ltd (INEX)
 *
 */

date_default_timezone_set( 'Europe/Dublin' );

error_reporting( E_ALL|E_STRICT );
//error_reporting( ( E_ALL | E_STRICT ) ^ E_NOTICE );

ini_set( 'display_errors', true );

defined( 'APPLICATION_PATH' ) || define( 'APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application' ) );

// Define application environment
define( 'APPLICATION_ENV', 'productioncli' );

defined( 'APPLICATION_ENV' ) || define( 'APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production' ) );

// Ensure library/ is on include_path
set_include_path( implode( PATH_SEPARATOR,
        array(
            realpath( APPLICATION_PATH . '/../library' ),
            get_include_path()
        )
    )
);

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

try {
    $application->bootstrap();

    $bootstrap = $application->getBootstrap();
    $bootstrap->bootstrap( 'frontController' );
}
catch( Exception $e )
{
    die( print_r( $e, true ) );
}

try
{
    $opts = new Zend_Console_Getopt(
        array(
            'help|h'        => 'Displays usage information.',
            'action|a=s'    => 'Action to perform in format of module.controller.action',
            'verbose|v'     => 'Verbose messages will be dumped to the default output.',
            'development|d' => 'Enables development mode.',
        )
    );

    $opts->parse();
}
catch( Zend_Console_Getopt_Exception $e )
{
    exit( $e->getMessage() ."\n\n". $e->getUsageMessage() );
}

if( isset( $opts->h ) )
{
    echo $opts->getUsageMessage();
    exit;
}

if( isset( $opts->a ) )
{
    try
    {
        $reqRoute = array_reverse( explode( '.', $opts->a ) );

        @list( $action, $controller, $module ) = $reqRoute;

        if( $opts->v )
        {
            echo "Action:     $action\n";
            echo "Controller: $controller\n";
            echo "Module:     $module\n\n";
        }

        $front = $bootstrap->frontController;

        $front->throwExceptions( true );

        $front->setRequest(  new Zend_Controller_Request_Simple( $action, $controller, $module ) );
        $front->setRouter(   new INEX_Controller_Router_Cli() );
        $front->setResponse( new Zend_Controller_Response_Cli() );

        $front->setParam( 'noViewRenderer', true )
              ->setParam( 'disableOutputBuffering', true );

        if( $opts->v )
            $front->setParam( 'verbose', true );
        else
            $front->setParam( 'verbose', false );

        // $front->addModuleDirectory( APPLICATION_PATH . '/modules');

        $application->run();
    }
    catch( Exception $e )
    {
        echo "ERROR: " . $e->getMessage() . "\n\n";

        if( $opts->v )
        {
            echo $e->getTraceAsString();
        }
    }
}

