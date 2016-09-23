#!/usr/bin/env php
<?php

/**
 * Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee.
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
                         

// Parses route server CSV and updates VlanInterface accordingly.

define( 'PEERING_LAN_NAME', 'Peering LAN #1' );

                              
//ini_set('memory_limit', -1);
                              
date_default_timezone_set('Europe/Dublin');
mb_internal_encoding('UTF-8');
mb_language('uni');
setlocale(LC_ALL, "en_IE.utf8");

require_once( dirname( __FILE__ ) . '/../../bin/utils.inc' );
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

Zend_Registry::set( 'bootstrap', $application->getBootstrap() );

$application->getBootstrap()->bootstrap( 'OSSAutoLoader' );
$application->getBootstrap()->bootstrap( 'doctrine2' );

$em = $application->getBootstrap()->getResource( 'doctrine2' );
$config = $application->getOption( 'resources' );

//Checking if file path is given
if( !isset( $argv[1] ) )
{
    echo "ERROR: Missing file path.\n";
    exit( 1 );
}

$vlan = loadVlan( PEERING_LAN_NAME, $em );
if( !$vlan )
{
    echo "ERROR: VLAN can not be loaded for further actions.\n";
    exit( 1 );
}

$handle = @fopen( $argv[1], "r" );
$fnames = false;
if( $handle ) {
    
    while( ( $line = fgets($handle, 4096 ) ) !== false )
    {
        $row = explode( ",", str_replace( "\n", "", $line ) );
        if( count( $row ) < 2 )
            continue;

        if( !updateVlanInterface( $row, $vlan, $em ) )
        {
            echo "Virtual interface for address " . strtolower( $row[0] ) . " was not found.\n";
            continue;
        }
    }
    
    if( !feof( $handle ) )
    {
        echo "Error: unexpected fgets() exception\n";
        exit( 1 );
    }
    fclose( $handle );
}
else
{
    echo "Error: file '{$argv[1]}' can not be opened.\n";
    exit( 1 );
}

$em->flush();


/**
 * Updates vlan interface
 *
 * Script looks for vlan interface by IPv4 or IPv6 found in parsed data from CFG file.
 * If vlan interface is founded then its rsclient field is set to true. If $row[4] is set
 * then maxbgprefix field is set to $row[4] value.
 *
 * @param array          $row  Data row parsed form cfg file.
 * @param \Entities\Vlan $vlan Vlan for IP addresses and vlan interface search
 * @param object         $em   Entity manager
 * @return
 */
function updateVlanInterface( $row, $vlan, $em )
{
    if( strpos( $row[0], ":" ) )
        $ip4 = false;
    elseif( strpos( $row[0], "." ) )
        $ip4 = true;
    else
        return false;

    $vlInt = false;
    if( $ip4 )
    {
        $ip = $em->getRepository( "\\Entities\\IPv4Address" )->findOneBy( [ 'Vlan' => $vlan->getId(), 'address'=> $row[0] ] );
        if( $ip )
            $vlInt = $em->getRepository( "\\Entities\\VlanInterface" )->findOneBy( [ 'Vlan' => $vlan->getId(), 'IPv4Address'=> $ip ] );
    }
    else
    {
        $ip = $em->getRepository( "\\Entities\\IPv6Address" )->findOneBy( [ 'Vlan' => $vlan->getId(), 'address'=> strtolower( $row[0] ) ] );
        if( $ip )
            $vlInt = $em->getRepository( "\\Entities\\VlanInterface" )->findOneBy( [ 'Vlan' => $vlan->getId(), 'IPv6Address'=> $ip ] );
    }

    if( $vlInt )
    {
        $vlInt->setRsclient( 1 );
        if( isset( $row[4] ) )
            $vlInt->setMaxbgpprefix( $row[4] );
        return true;
    }
    else
        return false;
}


/**
 * Loads vlan
 *
 * @param string $name Vlan name to find by
 * @param object $em   Entity manager
 * @return \Entities\Vlan
 */
function loadVlan( $name, $em )
{
    $vlan = $em->getRepository( "\\Entities\\Vlan" )->findOneBy( [ 'name' => $name ] );
    return $vlan;
}