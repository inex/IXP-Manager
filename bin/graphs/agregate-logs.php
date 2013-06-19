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
$DESTDIR_LOG = "/home/nerijus/tmp/test-lonap";

foreach( $em->getRepository( '\\Entities\\Customer' )->findAll() as $cust )
{   
    $logs = [];
    foreach( $cust->getVirtualInterfaces() as $viInt )
    {
        foreach( $viInt->getPhysicalInterfaces() as $pInt )
        {   
            $mrtg = sprintf( "%s/mrtg/members/%s/%s-%d-bits.log", $SRCDIR_LOG, $cust->getShortname(), $cust->getShortname(), $pInt->getMonitorindex() );
            
            //echo "MRTG: $mrtg \n RRD: $rrd\n";
            if( !file_exists( $mrtg ) )
            {
                echo "ERROR: Missing one of source files for customer {$cust->getShortname()} on switch {$switch}.\n";
                continue;
            }

            $src = fopen( $mrtg, 'r' );

            if( $src )
            {
                while( ( $buffer = fgets( $src, 4096 ) ) !== false )
                {
                    $row = explode( ' ', $buffer );
                    if( count( $row ) == 3 )
                    {
                        if( isset( $logs[ 0 ] ) )
                        {
                            if( $row[0] > $logs[0][0] )
                                $logs[0][0] = $row[0];

                            $logs[ 0 ][1] += $row[1];
                            $logs[ 0 ][2] += $row[2];
                        }
                        else
                            $logs[ 0 ] = [ $row[0], intval( $row[1] ), intval( $row[2] ) ];
                    }
                    else
                    {
                        if( isset( $logs[ $row[0] ] ) )
                        {
                            $logs[ $row[0] ][1] += $row[1];
                            $logs[ $row[0] ][2] += $row[2];
                            $logs[ $row[0] ][3] += $row[3];
                            $logs[ $row[0] ][4] += $row[4];
                        }
                        else
                            $logs[ $row[0] ] = [ $row[0], intval( $row[1] ), intval( $row[2] ), intval( $row[3] ), intval( $row[4] ) ];
                    }
                }
  
                if( !feof( $src ) )
                    echo "Error: unexpected fgets() fail\n";

                fclose( $src );
            }

        }
    }
    
    $out = sprintf( "%s/mrtg/members/%s/%s-agregate-bits.log", $DESTDIR_LOG, $cust->getShortname(), $cust->getShortname() );
    
    $dir = dirname( $out );
    if( !is_dir( $dir ) )
        mkdir( $dir, 0777, true );

    $dst = fopen( $out, 'w' );

    krsort( $logs );
    $row = implode( " ", $logs[0] ) . "\n";
    unset( $logs[0] );
    
    fputs( $dst, $row );
    foreach( $logs as $idx => $row )
    {
        $row = implode( " ", $row ) . "\n";
        fputs( $dst, $row );
    }
    
    fclose( $dst );
}