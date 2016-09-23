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
                              
/**
 * This script parses a csv file and takes only these rows which have an IPv4 address
 * defined. It groups ports by switch and then loads the defined vlan (assumes one VLAN
 * per file).
 *
 * Then it iterates through the switches from the parsed data (and if the switch is
 * no defined in the database, the script stops).
 *
 * The it iterates over switch ports and finds that port in the database. If it does
 * not exist, or it is not related with customer by ASN(autsys) it prints
 * and error message and stops.
 *
 * If port exists then script checks if physical interface and virtual interface
 * also exists for that port and if not it creates them.
 *
 * If port has pchan_master / is_pchannel set, the script tries to find the parent
 * virtual interface and adds the new physical interface to it.
 *
 * Then virtual interface and port data is processed to check if IPs are already
 * assigned to to the Vlan (if not then assign them). The script also checks if
 * VlanInterfce is not already created for same ip to same virtual interface;
 * if not then creates it.
 *
 *
 * Script call: ./port-sync.php file.csv
 */
                              
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

//Parsing file
$switches = parseFile( $argv[1] );

if( !$switches )
{
    echo "No switches found\n";
    exit( 1 );
}

$vlan = loadVlan( PEERING_LAN_NAME, $em );
if( !$vlan )
{
    echo "ERROR: VLAN '" . PEERING_LAN_NAME . "' could not be loaded - please create it.\n";
    exit( 1 );
}

//$name is equal to $port['switch']
$cnt = 0;
foreach( $switches as $name => $switch )
{
    $sw = loadSwitch( $name, $em );
    if( !$sw )
    {
        echo "ERROR: switch {$name} was not found. Skipping ports related to this switch.\n";
        continue;
    }

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

/**
 * Loads switch
 *
 * @param string $name Switch name to create or find by
 * @param object $em   Entity manager
 * @return \Entities\Switcher
 */
function loadSwitch( $name, $em )
{
    $sw = $em->getRepository( "\\Entities\\Switcher" )->findOneBy( ['name'=> $name ] );
    
    if( !$sw )
    {
        //$sw = new \Entities\Switcher();
        //$em->persist( $sw );
        //$sw->setName( $name );
        //$sw->setActive( 1 );
        //$sw->setInfrastructure( 1 );
        //$sw->setSwitchtype( \Entities\Switcher::TYPE_SWITCH );
    }

    return $sw;
}

/**
 * Updates or creates physical interface for port data from parsed CSV file
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
    
    $dbPort = loadPort( $port, $switcher, $em );
    
    if( !$dbPort )
    {
        echo "ERROR: port with ifIndex '{$port['snmp_oid']}' can not be found for switch {$switcher->getName()}\n";
        return false;
    }
    
    // make sure this port is of type 'peer'
    $dbPort->setType( \Entities\SwitchPort::TYPE_PEERING );
    
    $phInt  = createLoadPhysicalInterface( $dbPort, $port, $cust, $em );
    $viInt  = createUpdateVirtualInterface( $phInt, $port, $switcher, $cust, $em );
    
    return $viInt;
}

/**
 * Loads switch port form database
 *
 * @param array              $port    Parsed port data from csv
 * @param \Entities\Switcher $switcher Switch for adding port
 * @param object             $em       Entity manager
 * @return \Entities\SwitchPort
 */
function loadPort( $port, $switcher, $em )
{
    $dbPort = $em->getRepository( "\\Entities\\SwitchPort" )->findOneBy( ['Switcher'=> $switcher->getId(), 'ifIndex' => $port['snmp_oid'] ] );
    /*if( !$dbPort )
    {
        $dbPort = new \Entities\SwitchPort();
        $em->persist( $dbPort );
        $dbPort->setName( "" );
        $dbPort->setIfAlias( $port['port_desc'] );
        $dbPort->setActive( 1 );
        $dbPort->setType( \Entities\SwitchPort::TYPE_UNSET );
        $dbPort->setSwitcher( $switcher );
        $switcher->addPort( $dbPort );
        $dbPort->setIfIndex( $port['snmp_oid'] );
        $dbPort->setIfName( $port['port_id'] );
        $dbPort->setIfPhysAddress( str_replace( ':', '', strtoupper( $port['mac'] ) ) );
        $dbPort->setIfOperStatus( $port['ifoperstatus'] == "up" ? 1 : 2 );
        $dbPort->setIfAdminStatus( $port['ifadminstatus'] == "enabled" ? 1 : 2 );
    }*/
    return $dbPort;
}

/**
 * Creates or loads physical interface for port
 *
 * @param \Entities\SwitchPort $dbPort Port to create or load Physical interface
 * @param array                $port    Parsed port data from csv to check if is LAG or simple port
 * @param \Entities\Customer   $cust   Customer to get next monitor index
 * @param object               $em     Entity manager
 * @return \Entities\PhysicalInterface
 */
function createLoadPhysicalInterface( $dbPort, $port, $cust, $em )
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

    if( $port['port_type'] )
    {
        $speed = split( "base", strtolower( $port[ 'port_type' ] ) );

        switch( $speed[0] )
        {
            case '10gig':
                $phInt->setSpeed( 10000 );
                break;
            
            case '1000':
                $phInt->setSpeed( 1000 );
                break;

            case '100':
                $phInt->setSpeed( 100 );
                break;

            case '10':
                $phInt->setSpeed( 10 );
                break;

            default:
                echo "Uh oh: could not parse speed {$port['port_type']}\n";
                break;
        }
    }
    else
        echo "Uh oh: no port type for switchport {$dbPort->getSwitcher()->getName()}:{$dbPort->getName()}\n";

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
        if( $port['pchan_master'] )
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
 * @return void
 */
function createUpdateVlanInterface( $virtInt, $vlan, $port, $em )
{
    $ip4 = loadIPv4( $port, $vlan, $em );
    if( !$ip4 )
    {
        echo "ERROR: ip {$port['ipv4addr']} does not exist in Vlan {$vlan->getName()}";
        return false;
    }
    
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
 * Loads IPv4Address
 *
 * @param array          $port Parsed port data from csv file
 * @param \Entities\Vlan $vlan Vlan to assing or load ip address
 * @param object         $em   Entity manager
 * @return \Entities\IPv4Address
 */
function loadIPv4( $port, $vlan, $em )
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
    return $ip4;
}

/**
 * Creates or loads IPv6Address
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
            $vlan->addIPv6Addresses( $ip6 );
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