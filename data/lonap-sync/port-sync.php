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
 * Script parses csv file and takes only these rows which have IPv4 addresses and
 * group ports by server. Then it creates Vlan for LONAP if is not exist. Then
 * iterates through servers and creates them if it not exists. Later it iterates
 * through server ports and create them in database if not exists and it is related
 * with customer by ASN(autsys). If Port was created then physical interface for
 * that port is also created with virtual interface. If port exists then script 
 * checks if physical interface and virtual interface is existent for that port.
 * If port have pchan_master and it is_pchannel set to false then scripts find
 * virtual interface for pchan_master and where is_pchannel is true and appends with
 * not pchannel interface (Builds LAG). Then Virtual interface and port data is
 * passed forward where script checks if ports IPs are already assign to LONAP's 
 * Vlan if not then assign them then script checks if VlanInterfce is not already
 * created for same ip to same virtual interface if not then creates it.
 * 
 *
 * Script call: ./port-sync.php file.csv
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

$vlan = loadCreateVlan( 'LONAP', 'LONAP', $em );

//$name is equal to $port['switch']
$cnt = 0;
foreach( $switches as $name => $switch )
{
    $sw = loadCreateSwitch( $name, $em );
    foreach( $switch as $port )
    {
        $virtual_interface = updatePhysicalInterface( $port, $em, $sw );
        if( $virtual_interface )
            createUpdateVlanInterface( $virtual_interface, $vlan, $port, $em );
    }
    
    if( ++$cnt % 10 == 0 )
        $em->flush();
}

$em->flush();

/**
 * Creates or loads vlan
 *
 * @param string $name      Vlan name to create or find by
 * @param string $rcvrfname RCVRF name
 * @param object $em        Entity manager
 * @return \Entities\Vlan
 */
function loadCreateVlan( $name, $rcvrfname, $em )
{
    $vlan = $em->getRepository( "\\Entities\\Vlan" )->findOneBy( [ 'name' => $name ] );
    if( !$vlan )
    {
        $vlan = new \Entities\Vlan();
        $em->persist( $vlan );
        $vlan->setName( $name );
        $vlan->setRcvrfname( $rcvrfname );
        $vlan->setPrivate( 0 );
        $vlan->setNumber( 
            count( $em->getRepository( "\\Entities\\Vlan" )->findAll() ) + 1
        );
    }

    return $vlan;
}

/**
 * Creates or loads switch
 *
 * @param string $name Switch name to create or find by
 * @param object $em   Entity manager
 * @return \Entities\Switcher
 */
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

/**
 * Updates or creates physical interface for port data form parsed CSV file
 *
 * First function creates or loads port for database. Than it creates or loads
 * physical interface. And finally updates or creates virtual interface.
 *
 * NOTICE: If customer is not related with port it returns false and skip all actions otherwise
 *         It returns new or updated virtual interface for vlan interface actions.
 *
 * @param array              $port     Parsed port data from csv
 * @param object             $em       Entity manager
 * @param \Entities\Switcher $switcher Switch for adding port and ports search
 * @return bool|\Entities\VirtualInterface
 */
function updatePhysicalInterface( $port, $em, $switcher )
{
    $cust = $em->getRepository( "\\Entities\\Customer" )->findOneBy( ['autsys'=> $port['asn'] ] );
    if( !$cust )
    {
        echo "WARNING: '{$port['membername']}' with autsys {$port['asn']} was not found in database. For port {$port['port_id']} in switch {$port['switch']}\n";
        return false;
    }
    
    $dbPort = createLoadPort( $port, $switcher, $em );
    $phInt  = createLoadPhysicalInterface( $dbPort, $cust, $em );
    $viInt  = createUpdateVirtualInterface( $phInt, $port, $switcher, $cust, $em );
    
    return $viInt;
}

/**
 * Creates or loads switch port form database
 *
 * @param array              $port    Parsed port data from csv
 * @param \Entities\Switcher $switcher Switch for adding port 
 * @param object             $em       Entity manager
 * @return \Entities\SwitchPort
 */
function createLoadPort( $port, $switcher, $em )
{
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
        $dbPort->setIfPhysAddress( str_replace( ':', '', strtoupper( $port['mac'] ) ) );
        $dbPort->setIfOperStatus( $port['ifoperstatus'] == "up" ? 1 : 2 );
        $dbPort->setIfAdminStatus( $port['ifadminstatus'] == "enabled" ? 1 : 2 );
    }

    return $dbPort;
}

/**
 * Creates or loads physical interface for port
 *
 * @param \Entities\SwitchPort $dbPort Port to create or load Physical interface
 * @param \Entities\Customer   $cust   Customer to get next monitor index
 * @param object               $em     Entity manager
 * @return \Entities\PhysicalInterface
 */
function createLoadPhysicalInterface( $dbPort, $cust, $em )
{
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

    return $phInt;
}

/**
 * Creates or updates physical interface for port
 *
 * @param \Entities\PhysicalInterface $phInt    Ports physical interface to get or create virtual interface
 * @param array                       $port     Parsed port data from csv to check if is LAG or simple port
 * @param \Entities\Switcher          $switcher Switch to look for port
 * @param \Entities\Customer          $cust     Customer to assign new virtual interface
 * @param object                      $em       Entity manager
 * @return \Entities\VirutalInterface
 */
function createUpdateVirtualInterface( $phInt, $port, $switcher, $cust, $em )
{
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

/**
 * Creates or updates virtual interface for port
 *
 * @param \Entities\VirtualInterface  $virtInt VirtualInterface for add or load vlan interface
 * @param \Entities\Vlan              $vlan    Vlan for IP addresses and to assign vlan interface.
 * @param array                       $port     Parsed port data from csv file
 * @param object                      $em       Entity manager
 * @return \Entities\SwitchPort
 */
function createUpdateVlanInterface( $virtInt, $vlan, $port, $em )
{
    $ip4 = createLoadIPv4( $port, $vlan, $em );
    $ip6 = createLoadIPv6( $port, $vlan, $em );

    $vlInt = false;
    foreach( $virtInt->getVlanInterfaces() as $vint )
    {
        if( $vint->getIPv4Address() == $ip4 )
        {
            $vlInt = $vint;
            break;
        }
    }

    if( !$vlInt )
    {
        $vlInt = new \Entities\VlanInterface();
        $em->persist( $vlInt );
        $vlInt->setVirtualInterface( $virtInt );
        $virtInt->addVlanInterface( $vlInt );
        $vlInt->setVlan( $vlan );
        $vlan->addVlanInterface( $vlInt );
        $vlInt->setIPv4Address( $ip4 );
        $vlInt->setIpv4enabled( 1 );
        $vlInt->setRsclient( 0 );
        $vlInt->setMaxbgpprefix( 100 );
    }

    $vlInt->setIpv4hostname( $port['ipv4_ptr'] );
    if( $port['ipv4_mcast'] )
        $vlInt->setMcastenabled( 1 );
    else
        $vlInt->setMcastenabled( 0 );

    if( $ip6 )
    {
        $vlInt->setIPv6Address( $ip6 );
        $vlInt->setIpv6enabled( 1 );
        $vlInt->setIpv6hostname( $port['ipv6_ptr'] );
    }
    else
    {
        $vlInt->setIPv6Address( null );
        $vlInt->setIpv6enabled( 0 );
        $vlInt->setIpv6hostname( null );   
    }

}

/**
 * Creates or updates IPv4Address
 *
 * @param array          $port Parsed port data from csv file
 * @param \Entities\Vlan $vlan Vlan to assing or load ip address
 * @param object         $em   Entity manager
 * @return \Entities\IPv4Address
 */
function createLoadIPv4( $port, $vlan, $em )
{
    $ip4 = false;
    foreach( $vlan->getIPv4Addresses() as $ip )
    {
        if( $ip->getAddress() == $port['ipv4addr'] )
        {
            $ip4 = $ip;
            break;
        }
    }
    if( !$ip4 )
    {
        $ip4 = new \Entities\IPv4Address();
        $em->persist( $ip4 );
        $ip4->setAddress( $port['ipv4addr'] );
        $ip4->setVlan( $vlan );
        $vlan->addIPv4Addresse( $ip4 );
    }
    return $ip4;
}

/**
 * Creates or updates IPv6Address
 *
 * @param array          $port Parsed port data from csv file
 * @param \Entities\Vlan $vlan Vlan to assing or load ip address
 * @param object         $em   Entity manager
 * @return \Entities\IPv6Address
 */
function createLoadIPv6( $port, $vlan, $em )
{
    $ip6 = false;
    if( $port['ipv6addr'] )
    {
        foreach( $vlan->getIPv6Addresses() as $ip )
        {
            if( $ip->getAddress() == $port['ipv6addr'] )
            {
                $ip6 = $ip;
                break;
            }
        }
        if( !$ip6 )
        {
            $ip6 = new \Entities\IPv6Address();
            $em->persist( $ip6 );
            $ip6->setAddress( $port['ipv6addr'] );
            $ip6->setVlan( $vlan );
            $vlan->addIPv6Addresse( $ip6 );
        }
    }
    
    return $ip6;
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