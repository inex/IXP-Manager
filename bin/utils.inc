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

// load version information
require_once( dirname( __FILE__ ) . '/../library/IXP/Version.php' );    

/**
 * Parses public/.htaccess for application environment
 *
 * die()'s if not found
 */
function scriptutils_get_application_env()
{
    $htaccess_path = dirname( __FILE__ ) . "/../public/.htaccess";

    if( !is_readable( $htaccess_path ) )
    {
        die( "ERROR: public/.htaccess does not exist / is not readable - set this up first!\n" );
    }

    $htaccess = file_get_contents( $htaccess_path );

    $matches = array();
    preg_match_all( '/SetEnv\s+APPLICATION_ENV\s+([a-zA-Z0-9_\-\.]+)/i', $htaccess, $matches );

    if( isset( $matches[1][0] ) && strlen( $matches[1][0] ) )
        return trim( $matches[1][0] );
    
    die( "ERROR: Could not parse or find APPLICATION_ENV in $htaccess_path\n" );
}

function scriptutils_get_config() 
{
	static $config = NULL;
	static $environment = NULL;

	if ($environment == null) {
		$inifile = dirname( __FILE__ ) . '/../application/configs/application.ini';

		$environment = scriptutils_get_application_env();
	}

	if ($config == null) {
		$config = parse_ini_file ($inifile, true);
	}

	return isset ($config[$environment]) ? $config[$environment] : NULL;
}
