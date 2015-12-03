#!/usr/bin/env php
<?php

/**
/* Billing Details migration script - see CHANGELOG for v3.0.14
 *
 *
 * Copyright (C) 2009-2013 Internet Neutral Exchange Association Limited.
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
 * Billing details migration script
 */

//ini_set('memory_limit', -1);

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

foreach( $em->getRepository( '\\Entities\\Customer' )->findAll() as $cust )
{
    echo "Processing customer {$cust->getName()}: ";

    if( $cust->getBillingDetails() )
    {
        echo " Already migrated\n";
        continue;
    }
    
    try{
        
        $bdetail = new \Entities\CompanyBillingDetail();
        $em->persist( $bdetail );
        $cust->setBillingDetails( $bdetail );

        $bdetail->setBillingContactName( $cust->getBillingContact()  );
        $bdetail->setBillingAddress1(    $cust->getBillingAddress1() );
        $bdetail->setBillingAddress2(    $cust->getBillingAddress2() );
        $bdetail->setBillingAddress3(    $cust->getBillingAddress3() );
        $bdetail->setBillingTownCity(    $cust->getBillingCity()     );
        $bdetail->setBillingCountry(     $cust->getBillingCountry()  );
        
        $rdetail = new \Entities\CompanyRegisteredDetail();
        $em->persist( $rdetail );
        $cust->setRegistrationDetails( $rdetail );

        $rdetail->setCountry( $cust->getBillingCountry() );
        
        $em->flush();        
    }
    catch ( Exception $e )
    {
        echo " ERROR: {$e->getMessage()}\n";    
        exit( 1 );
    }
    echo " Done\n";
}


