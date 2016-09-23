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

$SRCDIR_LOG  = "/srv/mrtg";
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

foreach( $em->getRepository( '\\Entities\\Customer' )->findAll() as $cust )
{
    $logs = [];

    foreach( $cust->getVirtualInterfaces() as $viInt )
    {
        foreach( $viInt->getPhysicalInterfaces() as $pInt )
        {
            $mrtg = sprintf( "%s/members/%s/%s-%d-bits.log",
                $SRCDIR_LOG,
                $cust->getShortname(),
                $cust->getShortname(),
                $pInt->getMonitorindex()
            );

            if( !file_exists( $mrtg ) || !( $src = fopen( $mrtg, 'r' ) ) )
            {
                echo "ERROR: Missing / cannot access source file for customer {$cust->getShortname()} for mointor index {$pInt->getMonitorindex()}.\n";
                continue;
            }

            while( ( $buffer = fgets( $src, 4096 ) ) !== false )
            {
                $row = explode( ' ', $buffer );

                if( count( $row ) == 3 )
                {
                    if( !isset( $logs[0] ) )
                        $logs[0] = [ $row[0], 0, 0 ];
                    else if( $row[0] > $logs[0][0] )
                        $logs[0][0] = $row[0];

                    $logs[0][1] += $row[1];
                    $logs[0][2] += $row[2];
                }
                else
                {
                    if( !isset( $logs[ $row[0] ] ) )
                        $logs[ $row[0] ] = [ $row[0], 0, 0, 0, 0 ];

                    $logs[ $row[0] ][1] += $row[1];
                    $logs[ $row[0] ][2] += $row[2];
                    $logs[ $row[0] ][3] += $row[3];
                    $logs[ $row[0] ][4] += $row[4];
                }
            }

            if( !feof( $src ) )
                echo "Error: unexpected fgets() fail\n";

            fclose( $src );
        }
    }

    $out = sprintf( "%s/members/%s/%s-aggregate-bits.log",
        $DESTDIR_LOG,
        $cust->getShortname(),
        $cust->getShortname()
    );

    $dir = dirname( $out );
    if( !is_dir( $dir ) )
        mkdir( $dir, 0700, true );

    if( !( $dst = fopen( $out, 'w' ) ) )
    {
        echo "ERROR: Failed to open output file [$out].\n";
        continue;
    }

    if( isset( $logs[0] ) )
    {
        fputs( $dst, implode( " ", $logs[0] ) . "\n" );
        unset( $logs[0] );
    }

    krsort( $logs, SORT_NUMERIC );

    foreach( $logs as $row )
        fputs( $dst, implode( " ", $row ) . "\n" );

    fclose( $dst );
}
