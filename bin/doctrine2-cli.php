#!/usr/bin/env php
<?php

/**
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
 * Doctrine CLI script
 */

//ini_set('memory_limit', -1);

mb_internal_encoding('UTF-8');
mb_language('uni');
setlocale(LC_ALL, "en_IE.utf8");

require_once( dirname( __FILE__ ) . '/utils.inc' );
define( 'APPLICATION_ENV', scriptutils_get_application_env() );
define('APPLICATION_PATH', realpath( dirname(__FILE__) . '/../application' ) );

if( isset( $_SERVER['argv'][1] ) && $_SERVER['argv'][1] == '--database' )
{
    $db = $_SERVER['argv'][2];
    array_splice( $_SERVER['argv'], 1, 2 );
}
else
    $db = 'default';

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

$application->getBootstrap()->bootstrap( 'OSSAutoLoader' );

$config = $application->getOption( 'resources' );

$plugin = new OSS_Resource_Doctrine2( $config['doctrine2'] );
$application->getBootstrap()->registerPluginResource( $plugin );
$em = $plugin->getDoctrine2( $db );

$helpers = array(
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper( $em->getConnection() ),
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper( $em )
);

$cli = new \Symfony\Component\Console\Application( 'Doctrine Command Line Interface', Doctrine\Common\Version::VERSION );
$cli->setCatchExceptions(true);
$helperSet = $cli->getHelperSet();
foreach ($helpers as $name => $helper) {
    $helperSet->set($helper, $name);
}

Doctrine\ORM\Tools\Console\ConsoleRunner::addCommands( $cli );

$cli->run();


