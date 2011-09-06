#!/usr/bin/env php
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
 * Doctrine CLI utility.
 *
 * @category INEX
 * @version $Id: doctrine-cli.php 451 2011-05-26 13:56:06Z barryo $
 * @package INEX_Utilities
 * @copyright Copyright (c) 2009 Internet Neutral Exchange Association Ltd http://www.inex.ie/
 *
 */


define('APPLICATION_ENV', 'production');

define('APPLICATION_PATH', realpath( dirname(__FILE__) . '/../application' ) );

set_include_path( implode( PATH_SEPARATOR, array(
	realpath( APPLICATION_PATH . '/../library' ),
	get_include_path(),
)));

require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$application->getBootstrap()->bootstrap( 'doctrine' );

$config = $application->getOption( 'resources' );
$cli = new Doctrine_Cli( $config['doctrine'] );

$cli->run( $_SERVER['argv'] );


