#! /usr/bin/env php
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


/**
 * INEX Log Processor Script
 *
 * Takes events from the Simple Event Correlator (see /usr/local/etc/sec/*.conf files)
 *
 * Barry O'Donovan <barryo@inex.ie>
 *
 */


/**
 * Instructions:
 * =============
 *
 * In sec.conf files, call this script with an action such as:
 *
 *  action=pipe 'type=%s:switch=$1:mac=$2:port=$3' /home/barryo/ixp-manager-v2/bin/sec-test.php
 *
 * Note how the parameters are piped to this script. This script reads a line from stdin and
 * parses for param1=val1:param2=val2:...
 *
 * If you pass the switch= param (where the value is, for example, sw01), the $switch variable
 * will be populated from the INEX database with the row of the relevent switch. Passing a
 * switch port as port= (where the value might be GigabitEthernet1/6) will additionally
 * populate $switchPort, $physicalInterface, $virtualInterface and $cust with the
 * appropriate details.
 *
 * Lastly, if you parse the log file date as follows:
 *
 *   Log file line: Feb 25 06:42:51 ...
 *   Reg exp: ^(\w+)\s(\d+)\s(\d\d):(\d\d):(\d\d)
 *
 * and pass in those parameters as:
 *
 *   'month=$1:day=$2:hour=$3:minute=$4:second=$5'
 *
 * then $date will contain (in this example) "Feb 25 06:42:51"
 *
 * NB: only parameters explicitly ALLOWED in the switch() below will be permitted. Please add new
 *  parameters there.
 *
 */



error_reporting( E_ALL|E_STRICT );

ini_set( 'display_errors', true );

define('APPLICATION_ENV', 'production');
defined( 'APPLICATION_PATH' ) || define( 'APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application' ) );

set_include_path( implode( PATH_SEPARATOR, array(
    realpath( APPLICATION_PATH . '/../library' ),
    get_include_path(),
)));

date_default_timezone_set( 'Europe/Dublin' );


require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

try
{
    $application->bootstrap();
    $bootstrap = $application->getBootstrap();
    $bootstrap->bootstrap( 'frontController' );
    $bootstrap->bootstrap();

    if( $bootstrap->hasResource( 'zfdebug' ) )
        $bootstrap->unregisterPluginResource( 'zfdebug' );

    $namespace = $bootstrap->getResource( 'namespace' );
}
catch( Exception $e )
{
    die( print_r( $e, true ) );
}

define( 'MAILTO', 'barryo@inex.ie' );

// Read the parameters from STDIN
if( !defined( 'STDIN' ) )
{
    define( 'STDIN', fopen( 'php://stdin', 'r' ) );
}

while( !feof( STDIN ) )
{
    $in = fgets( STDIN, 4096 );

    $bootstrap->getResource( 'logger' )->debug( "STDIN: $in" );
    break;
}

// Now process the parameters only allowing those explicitly listed
$args = split( '&', $in );

foreach( $args as $arg )
{
    $temp = split( '=', $arg );

    switch( $temp[0] )
    {
        case 'day':
        case 'hour':
        case 'minute':
        case 'month':
        case 'second':
        case 'switch':
            $$temp[0] = trim( $temp[1] );
            break;

        case 'ip':
        case 'mac':
        case 'port':
        case 'router':
        case 'state':
        case 'type':
            $namespace->$temp[0] = trim( $temp[1] );
            break;

        default:
            die( 'Illegal parameter: ' . $temp[0] . "\n" );
            break;
    }
}

//  die( print_r( $namespace->getIterator(), true ) );

if( isset( $hour ) )
{
    $namespace->date = ( isset( $month ) ? $month : '' )
            . ' ' . ( isset( $day ) ? $day : '' )
            . " $hour:$minute:$second";
}
else
    $namespace->date = '';


//
// Populate $switch, $switchPort, $physicalInterface, $virtualInterface, and $cust
// variables if we have that information
//

if( isset( $switch ) )
{
    $namespace->switch = Doctrine::getTable('SwitchTable')->findOneByName( $switch );

    if( !$namespace->switch )
        die( "No such switch: $switch\n" );

    // Weed out core ports (and also Port-channel interfaces which are all currently core)
    // FIXME Port-channel could also be a customer but we don't have any yet

    $namespace->isCorePort = false;
    if( preg_match( "/^Port-channel/", $namespace->port ) )
    {
        $namespace->isCorePort        = true;
        $namespace->switchPort        = null;
    }
    else
    {
        $namespace->switchPort = Doctrine_Query::create()
            ->from( 'Switchport sp' )
            ->where( 'sp.switchid = ? AND sp.name = ?', array( $namespace->switch['id'], $namespace->port ) )
            ->fetchOne();
    }

    if( $namespace->isCorePort || $namespace->switchPort['type'] == Switchport::TYPE_CORE )
    {
        $namespace->isCorePort = true;
        $namespace->physicalInterface = null;
        $namespace->virtualInterface  = null;
        $namespace->cust              = Doctrine::getTable( 'Cust' )->findOneByShortname( 'inex' );
    }
    else
    {
        $namespace->physicalInterface = Doctrine::getTable( 'Physicalinterface' )->findOneBySwitchportid( $namespace->switchPort['id'] );
        $namespace->virtualInterface  = Doctrine::getTable( 'Virtualinterface' )->find( $namespace->physicalInterface['virtualinterfaceid'] );
        $namespace->cust = Doctrine::getTable( 'Cust' )->find( $namespace->virtualInterface['custid'] );
        $namespace->user = Doctrine::getTable( 'User' )->findOneByCustidAndPrivs( $namespace->cust['id'], User::AUTH_CUSTADMIN );
    }
}

if( isset( $namespace->ip ) )
{
    $ip = $namespace->ip;

    // is it an IPv4 or a v6 address?
    if( ip2long( $ip ) === false )
    {
        $namespace->ipv = 6;

        // FIXME: Yeah, this isn't good. We need a better way to reliably find an IPv6 address in the DB
        if( $namespace->ip = Doctrine::getTable('Ipv6address')->findOneByAddress( strtolower( $ip ) ) )
            $namespace->vlaninterface    = Doctrine::getTable( 'Vlaninterface' )->findOneByIpv6addressid( $namespace->ip['id'] );
    }
    else
    {
        $namespace->ipv = 4;

        if( $namespace->ip = Doctrine::getTable('Ipv4address')->findOneByAddress( $ip ) )
            $namespace->vlaninterface    = Doctrine::getTable( 'Vlaninterface' )->findOneByIpv4addressid( $namespace->ip['id'] );
    }

    $namespace->virtualinterface = Doctrine::getTable( 'Virtualinterface' )->find( $namespace->vlaninterface['virtualinterfaceid'] );
    $namespace->cust = Doctrine::getTable( 'Cust' )->find( $namespace->virtualinterface['custid'] );
    $namespace->vlan = Doctrine::getTable( 'Vlan' )->find( $namespace->vlaninterface->vlanid );
    $namespace->user = Doctrine::getTable( 'User' )->findOneByCustidAndPrivs( $namespace->cust['id'], User::AUTH_CUSTADMIN );
}

//
// Now act on individual log messages
//

$front = $bootstrap->getResource( 'frontController' );

$front->throwExceptions( true );

$front->setRouter(   new IXP_Controller_Router_Cli() );
$front->setResponse( new Zend_Controller_Response_Cli() );

switch( $namespace->type )
{
    case 'BGP_AUTH':
        $front->setRequest(  new IXP_Controller_Request_Simple( 'bgp-auth', 'sec', null ) );
        break;

    case 'PORT_UPDOWN':
    case 'LINEPROTO_UPDOWN':
        $front->setRequest(  new IXP_Controller_Request_Simple( 'port-updown', 'sec', null ) );
        break;

    case 'SECURITY_VIOLATION':
        $front->setRequest(  new IXP_Controller_Request_Simple( 'security-violation', 'sec', null ) );
        break;

    default:
        // FIXME!!
        break;
}

$front->setParam( 'noViewRenderer', true )
      ->setParam( 'disableOutputBuffering', true );

$application->run();


function switch_port_updown( $switch, $switchPort, $cust, $state, $date )
{
    $mail = new Zend_Mail();
    $mail->setBodyText( "\n=== TEST MODE :: NO MAIL GONE TO CUSTOMERS ===\n\n"
        . "Port $state alert:\n\n  Switch {$switch['name']}\n  Interface {$switchPort['name']}\n  Date: $date\n\nPort is owned by {$cust['name']}.\n\n"
    );
    $mail->setFrom( 'operations@inex.ie', 'INEX Operations' );
    $mail->addTo( MAILTO, 'INEX Operations' );
    $mail->setSubject( "Port $state alert :: {$cust['name']} :: {$switch['name']}/{$switchPort['name']}" );
    $mail->send();
}


?>