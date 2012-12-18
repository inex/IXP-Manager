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
                              
                              
//ini_set('memory_limit', -1);
                              
date_default_timezone_set('Europe/Dublin');
mb_internal_encoding('UTF-8');
mb_language('uni');
setlocale(LC_ALL, "en_IE.utf8");

require_once( dirname( __FILE__ ) . '/utils.inc' );
define( 'APPLICATION_ENV', scriptutils_get_application_env() );

define('APPLICATION_PATH', realpath( dirname(__FILE__) . '/../application' ) );


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

echo "Installing fixtures\n";


#####################################################################
###
### MODIFY YOUR FIXTURES HERE
###
###
### First you need a "customer" object for your own IXP.
### Alter the following to suit

$c = new \Entities\Customer();

$c->setName( "Somecity Internet Exchange Point" );
$c->setShortname( "siep" );  // lowercase abbreviation (e.g. inex/ linx / lonap)
$c->setAutsys( 12345 );      // your ASN
$c->setMaxprefixes( 1000 );  // set appropriately if you peer with other members on the
                             // exchange. e.g friendly member providing transit
                             
$c->setPeeringemail( 'peering@siep.com' );
$c->setPeeringmacro( 'AS-SIEP' );
$c->setPeeringpolicy( \Entities\Customer::PEERING_POLICY_MANDATORY );

$c->setNocphone( '+353 1 123 4567' );
$c->setNoc24hphone( '+353 1 123 4567' );
$c->setNocfax( '+353 1 123 4568' );
$c->setNocemail( 'noc@siep.com' );
$c->setNochours( \Entities\Customer::NOC_HOURS_24x7 );
$c->setNocwww( 'http://www.siep.com/noc/' );

$c->setCorpwww( 'http://www.siep.com/' );

$c->setDatejoin( new DateTime() );

$c->setStatus( \Entities\Customer::STATUS_NORMAL );

$c->setActivepeeringmatrix( true );

$c->setType( \Entities\Customer::TYPE_INTERNAL );        // do not change this
$c->setCreated( new DateTime() );

$em->persist( $c );


// now you need your admin user!

$u = new \Entities\User();

$u->setUsername( 'username' );
$u->setPassword( 'letmein1' );        // if you're not using plaintext passwords, put anything here and
                                      // use the forgotten password facility
$u->setEmail( 'username@siep.com' );
$u->setPrivs( \Entities\User::AUTH_SUPERUSER );
$u->setDisabled( false );
$u->setCreator( $u->getUsername() );
$u->setCreated( new DateTime() );
$u->setCustomer( $c );
$u->setParent( $u );

$em->persist( $u );

$c->setCreator( $u->getUsername() );

$em->flush();



#####################################################################
###
### OTHER RECOMMENDED FIXTURES
###
### No need to edit beyond this point
###

## Vendors

$vendors = [
    "Cisco Systems",
    "Foundry Networks",
    "Extreme Networks",
    "Force10 Networks",
    "Glimmerglass",
    "Allied Telesyn",
    "Enterasys",
    "Dell",
    "Hitachi Cable",
    "MRV",
    "Transmode",
    "Brocade"
];

foreach( $vendors as $vendor )
{
    $e = new \Entities\Vendor();
    $e->setName( $vendor );
    $em->persist( $e );
}

$em->flush();


## IRRDBs

$irrdbs = [

    [
        'host'     => 'whois.ripe.net',
        'protocol' => 'ripe',
        'source'   => 'RIPE',
        'notes'    => 'RIPE Query from RIPE Database'
    ],

    [
        'host'     => 'whois.radb.net',
        'protocol' => 'irrd',
        'source'   => 'RADB',
        'notes'    => 'RADB Query from RADB Database'
    ],

        [
        'host'     => 'whois.lacnic.net',
        'protocol' => 'ripe',
        'source'   => 'LACNIC',
        'notes'    => 'LACNIC Query from LACNIC Database'
    ],

        [
        'host'     => 'whois.apnic.net',
        'protocol' => 'ripe',
        'source'   => 'APNIC',
        'notes'    => 'APNIC Query from APNIC Database'
    ],

        [
        'host'     => 'rr.level3.net',
        'protocol' => 'ripe',
        'source'   => 'LEVEL3',
        'notes'    => 'Level3 Query from Level3 Database'
    ],

        [
        'host'     => 'whois.radb.net',
        'protocol' => 'irrd',
        'source'   => 'ARIN',
        'notes'    => 'ARIN Query from RADB Database'
    ],

        [
        'host'     => 'whois.radb.net',
        'protocol' => 'irrd',
        'source'   => 'RADB,ARIN',
        'notes'    => 'RADB+ARIN Query from RADB Database'
    ],

        [
        'host'     => 'whois.radb.net',
        'protocol' => 'irrd',
        'source'   => 'ALTDB',
        'notes'    => 'ALTDB Query from RADB Database'
    ],

        [
        'host'     => 'whois.radb.net',
        'protocol' => 'irrd',
        'source'   => 'RADB,RIPE',
        'notes'    => 'RADB+RIPE Query from RADB Database'
    ],

        [
        'host'     => 'whois.radb.net',
        'protocol' => 'irrd',
        'source'   => 'RADB,APNIC,ARIN',
        'notes'    => 'RADB+APNIC+ARIN Query from RADB Database'
    ]

];



foreach( $irrdbs as $irrdb )
{
    $e = new \Entities\IRRDBConfig();
    $e->setHost(     $irrdb['host']     );
    $e->setProtocol( $irrdb['protocol'] );
    $e->setSource(   $irrdb['source']   );
    $e->setNotes( $irrdb['notes']       );
    $em->persist( $e );
}

$em->flush();


echo "Fixtures installed successfully\n";

