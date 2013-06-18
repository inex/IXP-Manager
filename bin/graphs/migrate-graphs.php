#!/usr/bin/env php
<?php

mb_internal_encoding('UTF-8');
mb_language('uni');
setlocale(LC_ALL, "en_IE.utf8");

require_once( dirname( __FILE__ ) . '/../utils.inc' );
define( 'APPLICATION_ENV', scriptutils_get_application_env() );
define('APPLICATION_PATH', realpath( dirname(__FILE__) . '/../../application' ) );

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
$em = $plugin->getDoctrine2( 'default' );


$SRCDIR_LOG = "/home/nerijus/tmp/ixp-lonap";
$SRCDIR_RRD = "/home/nerijus/tmp/ixp-lonap";
$DESTDIR_LOG = "/home/nerijus/tmp/test-lonap";

foreach( $em->getRepository( '\\Entities\\Customer' )->findAll() as $cust )
{
    //if( $cust->getShortname() != "akamai" )
        //continue;
        
    foreach( $cust->getVirtualInterfaces() as $viInt )
    {
        foreach( $viInt->getPhysicalInterfaces() as $pInt )
        {
            $switch = $pInt->getSwitchPort()->getSwitcher()->getName();
            $port = $pInt->getSwitchPort()->getName();
            $port = str_replace( " ", "_", strtolower( $port ) );
            
            $mrtg = sprintf( "%s/mrtg/members/%s/%s-%d-bits.log", $SRCDIR_LOG, $cust->getShortname(), $cust->getShortname(), $pInt->getMonitorindex() );
            $rrd = sprintf( "%s/cricket/%s/%s.rrd", $SRCDIR_RRD, $switch, $port );
            $out = sprintf( "%s/mrtg/members/%s/%s-%d-bits.log", $DESTDIR_LOG, $cust->getShortname(), $cust->getShortname(), $pInt->getMonitorindex() );
            
            //echo "MRTG: $mrtg \n RRD: $rrd\n";
            if( !file_exists( $mrtg ) || !file_exists( $rrd ) )
            {
                echo "ERROR: Missing one of source files\n";
                continue;
            }
            
            $command = "./graph-sync.php -m $mrtg -r $rrd -o $out";
            @exec( $command );   
        }
    }
}
