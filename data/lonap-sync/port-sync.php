#!/usr/bin/env php
<?php

/**
 * Copyright (C) 2009-2011 Internet Neutral Exchange Association Limited.
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
 * Script parses csv file by schema from getFields() function. From parsed data it
 * creates or update customer, billing and registration details, customer notes 
 * and contacts.
 *
 * Script call: ./member-sync.php file.csv
 */                              
                              
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

//Parsing file
$switches = parseFile( $argv[1] );

if( !$switches )
{
    echo "No members found\n";
    exit( 1 );
}

//$name is equal to $port['switch']
$cnt = 0;
foreach( $switches as $name => $switch )
{
    $sw = loadCreateSwitch( $name, $em );
    foreach( $switch as $port )
    {
        $virtual_interface = updatePhysicalInterface( $port, $em, $sw );
        if( $virtual_interface )
            updateVlanInterface( $virtual_interface, $port, $em );
    }
    
    //die();
    if( ++$cnt % 10 == 0 )
    {
        //$em->flush();
        //die();
    }
}

$em->flush();


function loadCreateSwitch( $name, $em )
{
    $sw = $em->getRepository( "\\Entities\\Switcher" )->findOneBy( ['name'=> $name ] );
    if( !$sw )
    {
        $sw = new \Entities\Switcher();
        $em->persist( $sw );
        $sw->setName( $name );
        $sw->setActive( 1 );
        $sw->setInfrastructure( 0 );
        $sw->setSwitchtype( \Entities\Switcher::TYPE_SWITCH );
    }

    return $sw;
}


function updatePhysicalInterface( $port, $em, $switcher )
{
    $cust = $em->getRepository( "\\Entities\\Customer" )->findOneBy( ['autsys'=> $port['asn'] ] );
    if( !$cust )
    {
        echo "WARNING: '{$port['membername']}' with autsys {$port['asn']} was not found in database. For port {$port['port_id']} in switch {$port['switch']}\n";
        return false;
    }
    
    $dbPort = $em->getRepository( "\\Entities\\SwitchPort" )->findOneBy( ['Switcher'=> $switcher->getId(), 'name' => $port['port_desc'], 'ifIndex' => $port['bridge_id'] ] );
    if( !$dbPort )
    {
        $dbPort = new \Entities\SwitchPort();
        $em->persist( $dbPort );
        $dbPort->setName( $port['port_desc'] );
        $dbPort->setActive( 1 );
        $dbPort->setType( \Entities\SwitchPort::TYPE_UNSET );
        $dbPort->setSwitcher( $switcher );
        $switcher->addPort( $dbPort );
        $dbPort->setIfIndex( $port['bridge_id'] );
        $dbPort->setIfName( $port['port_id'] );
        $dbPort->setIfPhysAddress( str_replace( ':', '', strtoupper($port['mac'] ) ) );
        $dbPort->setIfOperStatus( $port['ifoperstatus'] == "up" ? 1 : 2 );
        $dbPort->setIfAdminStatus( $port['ifadminstatus'] == "enabled" ? 1 : 2 );
    }

    $phInt = $dbPort->getPhysicalInterface();
    if( !$phInt )
    {

        $phInt = new \Entities\PhysicalInterface();
        $em->persist( $phInt );
        $phInt->setStatus( \Entities\PhysicalInterface::STATUS_CONNECTED );
        $phInt->setDuplex( 'full' );
        $phInt->setMonitorindex(
            $em->getRepository( "\\Entities\\PhysicalInterface" )->getNextMonitorIndex( $cust )
        );
        $phInt->setSwitchPort( $dbPort );
        $dbPort->setPhysicalInterface( $phInt );
        
        
    }

    $viInt = $phInt->getVirtualInterface();
    if( !$viInt )
    {
        if( $port['pchan_master'] && !$port['is_pchannel'] )
        {
            foreach( $switcher->getPorts() as $sport )
            {
                if( $sport->getIfName() == $port['pchan_master'] )
                {
                    $viInt = $sport->getPhysicalInterface()->getVirtualInterface();
                    break;
                }
            }
        }
        
        if( !$viInt )
        {
            $viInt = new \Entities\VirtualInterface();
            $em->persist( $viInt );
            $viInt->setCustomer( $cust );
            $cust->addVirtualInterface( $viInt );
            $viInt->setMtu( 0 );
            $viInt->setTrunk( 0 );
            $viInt->setChannelgroup( 0 );
        }

        $phInt->setVirtualInterface( $viInt );
        $viInt->addPhysicalInterface( $phInt );
    }

    return $viInt;
}

function updateVlanInterface( $virtual_interface, $port, $em )
{

}


/**
 * Parses csv file
 *
 * @param string $filename FIle name including path.
 * @return array
 */
function parseFile( $filename )
{
    $result = [];
 
    $handle = @fopen( $filename, "r" );
    $fnames = false;
    if( $handle ) {
        
        while( ( $row = fgetcsv($handle, 4096, ",") ) !== false )
        {
            if( count( $row ) != 20 )
                continue;

            if( !$fnames )
            {
                $fnames = $row;
                foreach( $fnames as $key => $value )
                    $fnames[$key] = strtolower( $value );
            }
            else
            {
                $tmp = processRow( $row, $fnames );
                if( $tmp['ipv4addr'] )
                    $result[$row[0]][$row[2]] = $tmp;
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
        echo "Error: file '{$filename}' can not be opened.\n";
        exit( 1 );
    }
    return $result;
}

/**
 * Process row from csv file.
 * 
 * Function sets field names as array indexes. And then iterates thought it and
 * fixes data type by schema for easier array usage.
 *
 * @param array $row    Parsed row form csv.
 * @param array $fields Schema array
 * @param array $fnames Field names array
 * @return array
 */
function processRow( $row, $fnames )
{
    $row = array_combine( $fnames, $row );
    
    foreach( $row as $key => $value )
    {
        if( trim( $value ) == "" || trim( $value ) == "-" )    
            $row[$key] = null;
    }
    return $row;
}