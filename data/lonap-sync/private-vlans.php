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
 * This script will go through a given list of VLANs on all switches and
 * find all ports tagged or untagged in that VLAN of type peering to
 * identify private VLANs via SNMP.
 *
 *
 *
 * USAGE: private-vlans.php $vlan1 $vlan2 $vlan3 ...
 */


define( 'VERBOSE', false );  // set to true for testing
define( 'DBWRITE', true ); // set to false for testing

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

array_shift( $argv );
$vlans = $argv;

if( !count( $vlans ) )
    die( "USAGE: private-vlans.php \$vlan1 \$vlan2 \$vlan3 ...\n\n" );

// get all switches
$switches = $em->getRepository( '\\Entities\\Switcher' )->getAndCache( true, \Entities\Switcher::TYPE_SWITCH );

foreach( $switches as $switch )
{
    $sw = new \OSS_SNMP\SNMP( $switch->getHostname(), $switch->getSnmppasswd() );

    foreach( $vlans as $tag )
    {
        if( VERBOSE ) echo "Polling {$switch->getName()} for VLAN $tag\n";

        // get ports for the given VLAN(s)
        $vlanIfIndex = $sw->useExtreme_Vlan()->ifVlanIdsToIfIndexes()[ $tag ];

        // does our VLAN exist?
        if( !( $vlan = $em->getRepository( '\\Entities\\Vlan' )->findOneBy( [ 'number' => $tag ] ) ) )
        {
            $vlan = new \Entities\Vlan();
            $vlan->setName( $sw->useExtreme_Vlan()->ifDescriptions()[ $vlanIfIndex ] );
            $vlan->setNumber( $tag );
            $vlan->setPrivate( true );

            if( DBWRITE )
            {
                if( VERBOSE ) echo "Adding VLAN $tag to database\n";
                $em->persist( $vlan );
                $em->flush();
            }
        }

        // find all ports tagged / untagged for the given VLAN
        $ports = $sw->useExtreme_Vlan()->getPortsForVlan( $vlanIfIndex );

        foreach( $ports as $ifIndex => $isMember )
        {
            if( !$isMember )
                continue;

            if( !( $sp = $em->getRepository( '\\Entities\\SwitchPort' )->findOneBy( [ 'ifIndex' => $ifIndex, 'Switcher' => $switch ] ) ) )
            {
                echo "ERROR: no port found with ifIndex $ifIndex on {$switch->getName()} for VLAN $tag\n";
            }

            if( $sp->getType() != \Entities\SwitchPort::TYPE_PEERING )
                continue;

            if( $sp->getPhysicalInterface() && ( $vi = $sp->getPhysicalInterface()->getVirtualInterface() ) )
            {
                // do we have an existing vlan interface?
                $haveVli = false;
                foreach( $vi->getVlanInterfaces() as $vli )
                {
                    if( $vli->getVlan()->getNumber() == $tag )
                    {
                        if( VERBOSE ) echo "FOUND EXISTING VLI on {$sp->getName()} on {$switch->getName()} for VLAN $tag for {$vi->getCustomer()->getShortname()}\n";
                        $haveVli = true;
                        break;
                    }
                }

                if( !$haveVli )
                {
                    if( VERBOSE ) echo "CREATING NEW VLI on {$sp->getName()} on {$switch->getName()} for VLAN $tag for {$vi->getCustomer()->getShortname()}\n";

                    $vli = new \Entities\VlanInterface();
                    $vli->setVirtualInterface( $vi );
                    $vli->setVlan( $vlan );
                    $vli->setIpv4enabled( false );
                    $vli->setIpv6enabled( false );

                    if( DBWRITE )
                    {
                        $em->persist( $vli );
                        $em->flush();
                    }
                }
            }
            else
                echo "Found peering port {$sp->getName()} on {$switch->getName()} for VLAN $tag with no customer\n";
        }
    }
}
