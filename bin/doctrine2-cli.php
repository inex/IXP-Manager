#!/usr/bin/env php
<?php

/**
 * Copyright (c) 2012 Open Source Solutions Limited
 * All rights reserved.
 *
 * These files are part of Open Source Solutions Limited's "PBX
 * APPLICATION PLATFORM".
 *
 * Information in these files are strictly confidential and the
 * property of Open Source Solutions Limited and may not be
 * extracted or distributed, in whole or in part, for any
 * purpose whatsoever, without the express written consent
 * from Open Source Solutions Limited.
 *
 * Open Source Solutions Limited is a company registered in Dublin,
 * Ireland with the Companies Registration Office (#438231). We
 * trade as Open Solutions with registered business name (#329120).
 * Our registered office is 147 Stepaside Park, Stepaside,
 * Dublin 18, Ireland.
 *
 * Contact us via http://www.opensolutions.ie/
 *   or info@opensolutions.ie   or  +353 1 685 4220.
 *
 * @category   PBX APPLICATION PLATFORM
 * @copyright  Copyright (c) 2007 - 2012, Open Source Solutions Limited, Dublin, Ireland
 * @license    Proprietary License - See LICENSE file bundled with this application
 * @link       http://www.opensolutions.ie/ Open Source Solutions Limited
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @author     The Skilled Team of PHP Developers at Open Solutions <info@opensolutions.ie>
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


