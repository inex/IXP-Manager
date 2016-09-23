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

if( file_exists( '../MAINT_MODE_ENABLED' ) )
{
    define( 'MAINTENANCE_MODE', true );
    
    // if it's an API request, we need to handle that differently
    if( strpos( $_SERVER['REQUEST_URI'], '/apiv1/' ) !== false )
    {
        header( "HTTP/1.0 503 Service Unavailable - Maintenance Mode Enabled" );
        die();
    }
    require_once( 'maintenance.php' );
}
else
    define( 'MAINTENANCE_MODE', false );

// let's time how long it takes to execute
define( 'APPLICATION_STARTTIME', microtime( true ) );

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment 
if( php_sapi_name() == 'cli-server' )
{
    // running under PHP's built in web server: php -S
    // as such, .htaccess is not processed
    include( dirname( __FILE__ ) . '/../bin/utils.inc' );
    define( 'APPLICATION_ENV', scriptutils_get_application_env() );
}
else
{
    // probably Apache or other web server
    defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
}

// Ensure library/ is on include_path
set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            realpath( APPLICATION_PATH . '/../library' ),
            get_include_path(),
        )
    )
);

/** Zend_Application */
require_once 'Zend/Application.php';

require_once( APPLICATION_PATH . '/../library/IXP/Version.php' );


// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap()
            ->run();

