#!/usr/bin/env php
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

$SRCDIR_LOG  = "/srv/mrtg-test";
$SRCDIR_RRD  = "/srv/cricket";
$DESTDIR_LOG = "/srv/mrtg";
                   
mb_internal_encoding('UTF-8');
mb_language('uni');
setlocale(LC_ALL, "en_IE.utf8");

require_once( dirname( __FILE__ ) . '/../../../bin/utils.inc' );
define( 'APPLICATION_ENV', scriptutils_get_application_env() );
define('APPLICATION_PATH', realpath( dirname(__FILE__) . '/../../../application' ) );

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


$count = 0;
foreach( $em->getRepository( '\\Entities\\Customer' )->findAll() as $cust )
    foreach( $cust->getVirtualInterfaces() as $viInt )
        foreach( $viInt->getPhysicalInterfaces() as $pInt )
            $count++;

echo "#! /bin/sh\n\n";

$i = 0;
foreach( $em->getRepository( '\\Entities\\Customer' )->findAll() as $cust )
{   
    foreach( $cust->getVirtualInterfaces() as $viInt )
    {
        foreach( $viInt->getPhysicalInterfaces() as $pInt )
        {
            $switch = $pInt->getSwitchPort()->getSwitcher()->getName();
            $port = $pInt->getSwitchPort()->getName();
            $port = str_replace( " ", "_", strtolower( $port ) );
            
            $mrtg = sprintf( "%s/members/%s/%s-%d-bits.log", $SRCDIR_LOG, $cust->getShortname(), $cust->getShortname(), $pInt->getMonitorindex() );
            $rrd = sprintf( "%s/%s/%s.rrd", $SRCDIR_RRD, $switch, $port );
            $out = sprintf( "%s/members/%s/%s-%d-bits.log", $DESTDIR_LOG, $cust->getShortname(), $cust->getShortname(), $pInt->getMonitorindex() );
            
            //echo "MRTG: $mrtg \n RRD: $rrd\n";
            if( !file_exists( $mrtg ) || !file_exists( $rrd ) )
                continue;
            
            $command = "./graph-sync.php -m " . escapeshellarg( $mrtg ) . " -r " . escapeshellarg( $rrd ) . " -o " . escapeshellarg( $out );
            //@exec( $command );   
            echo "$command\n";
            
            $i++;
            
            if( $i % 10 == 0 )
                echo sprintf( "echo '---> %0.1f%%'\n", ($i / $count) * 100 );
        }
    }
}
