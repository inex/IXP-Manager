#! /usr/bin/env php
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
 * INEX CLI Tool
 *
 * Executes actions in the CliController
 *
 * Barry O'Donovan <barryo@inex.ie>
 *
 */

require __DIR__.'/../bootstrap/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

define( 'SCRIPT_NAME', 'ixptool - IXP Manager CLI Management Tool' );
define( 'SCRIPT_COPY', 'Copyright (c) 2010 - ' . date( 'Y' ) . ' Internet Neutral Exchange Association Company Limited By Guarantee' );

error_reporting( E_ALL|E_STRICT );

ini_set( 'display_errors', true );

$application = $app->make('ZendFramework');

try {
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
            'help|h'         => 'Displays usage information.',
            'force|f'        => 'Run even if maintenance mode is enabled.',
            'action|a=s'     => 'Action to perform in format of module.controller.action',
            'verbose|v'      => 'Verbose messages will be dumped to the default output.',
            'debug|d'        => 'Enables debug mode.',
            'config|c=s'     => 'Some actions require an external config file - put the full path here',
            'p1=s'           => 'Generic paramater #1 for various actions',
            'parameters|p=s' => 'Set parameters you want to pass for script. E.g. cust_id=3,type=resller or cust_id=1',
        )
    );

    $opts->parse();
}
catch( Zend_Console_Getopt_Exception $e )
{
    exit( $e->getMessage() ."\n\n". $e->getUsageMessage() );
}

if( !isset( $opts->f ) && file_exists( '../MAINT_MODE_ENABLED' ) )
{
    die( "IXPtool - CLI tool exiting as maintenance mode is enabled. Use -f to force.\n" );
}

if( isset( $opts->h ) )
{
    echo SCRIPT_NAME . "\n" . SCRIPT_COPY . "\n\n";

    echo $opts->getUsageMessage();
    exit;
}

if( isset( $opts->a ) )
{
    try
    {
        $reqRoute = array_reverse( explode( '.', $opts->a ) );

        @list( $action, $controller, $module ) = $reqRoute;

        $front = $bootstrap->frontController;

        $front->throwExceptions( true );

        $front->setRequest(  new Zend_Controller_Request_Simple( $action, $controller, $module ) );
        $front->setRouter(   new IXP_Controller_Router_Cli() );
        $front->setResponse( new Zend_Controller_Response_Cli() );

        if( isset( $opts->p ) )
        {
            $opts->p = trim( $opts->p );
            if( strpos( $opts->p, "," ) )
            {
                $params = explode( ",", $opts->p );
                foreach( $params as $param )
                {
                    $param = trim( $param );
                    if( strpos( $param, "=" ) >= 0 )
                    {
                        $param = explode( "=", $param );
                        $front->getRequest()->setParam( trim( $param[0] ), trim( $param[1] ) );
                    }
                }
            }
            else if( strpos( $opts->p, "=" ) )
            {
                $param = explode( "=", $opts->p );
                $front->getRequest()->setParam( trim( $param[0] ), trim( $param[1] ) );
            }

        }

        $front->setParam( 'noViewRenderer', true )
              ->setParam( 'disableOutputBuffering', true );

        if( $opts->v )
            $front->setParam( 'verbose', true );
        else
            $front->setParam( 'verbose', false );

        if( $opts->d )
            $front->setParam( 'debug', true );
        else
            $front->setParam( 'debug', false );

        if( $opts->p1 )
            $front->setParam( 'param1', $opts->p1 );

        if( $opts->c )
            $front->setParam( 'config', $opts->c );

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
