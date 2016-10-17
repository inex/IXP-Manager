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
 * This script parses a csv file as defined in getFields() function.
 *
 * From this parsed data it creates or update customer, billing and
 * registration details, customer notes and contacts.
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
$countries = OSS_Countries::getCountriesArray();

//Checking if file path is given
if( !isset( $argv[1] ) )
{
    echo "ERROR: Usage: member-sync.php <data.csv>\n";
    exit( 1 );
}

//Parsing file
$origin_members = parseFile( $argv[1] );

if( !$origin_members )
{
    echo "No members found\n";
    exit( 1 );
}

//$key is equal to $member['as_number']
$cnt = 0;
foreach( $origin_members as $key => $member )
{
    $cust = createUpdateCustomer( $member, $em );
    createUpdateRegistrationDetails( $cust, $member, $em, $countries );
    createUpdateBilingDetails( $cust, $member, $em, $countries );
    createUpdateContacts( $cust, $member, $em );
    createUpdateNotes( $cust, $member, $em );
    
    if( ++$cnt % 10 == 0 )
        $em->flush();
}

$em->flush();

/**
 * Creates or updates customer from given member array.
 *
 * @param array  $member Members details array parsed from csv file.
 * @param object $em     Doctrine entity manager.
 * @return \Entities\Customer
 */
function createUpdateCustomer( $member, $em )
{
    $cust = $em->getRepository( "\\Entities\\Customer" )->findOneBy( [ 'autsys' => $member['as_number'] ] );

    if( !$cust )
    {
        $cust = new \Entities\Customer();
        $em->persist( $cust );
        $cust->setAutsys( $member['as_number'] );
        $cust->setMaxprefixes( 100 );
        $cust->setPeeringpolicy( \Entities\Customer::PEERING_POLICY_OPEN );
        $cust->setActivepeeringmatrix( 1 );
        $cust->setCreated( new \DateTime() );
    }

    $cust->setName( $member['trading_name_www'] ? $member['trading_name_www'] : $member['company_name'] );
    $cust->setAbbreviatedName( $cust->getName() );
    $cust->setShortname( $member['seb_memberid'] );
    $cust->setStatus( mapCustStatus( $member['status'] ) );
    $cust->setType( mapCustType( $member['type'] ) );
    $cust->setPeeringmacro( $member['as_macro'] );
    $cust->setCorpwww( $member['memsrc_URL'] );
    $cust->setPeeringemail( $member['memsrc_peering_email'] );
    $cust->setNocphone( $member['memsrc_noc_phone'] );
    $cust->setNocemail( $member['memsrc_noc_email'] );
    $cust->setMD5Support( strtoupper( $member['md5_support'] ) );
    $cust->setIRRDB( mapIRRDB( $member['as_routing_registry'], $em ) );
    $cust->setLastupdated( new \DateTime() );
    $cust->setDatejoin( getJoinedDate( $member ) );
    $cust->setLastupdatedby( null );

    return $cust;
}

/**
 * Creates or updates customers registration details from given member array.
 *
 * @param \Entities\Customer $cust   Customer to update or create registration details
 * @param array              $member Members details array parsed from csv file.
 * @param object             $em     Doctrine entity manager.
 * @return void
 */
function createUpdateRegistrationDetails( $cust, $member, $em, $countries )
{
    $rdetail = $cust->getRegistrationDetails();
    if( !$rdetail )
    {
        $rdetail = new \Entities\CompanyRegisteredDetail();
        $em->persist( $rdetail );
        $rdetail->addCustomer( $cust );
        $cust->setRegistrationDetails( $rdetail );
    }
    $rdetail->setRegisteredName( $member['company_name'] );
    $rdetail->setCompanyNumber( $member['company_no'] );
    $rdetail->setJurisdiction( $member['company_reg_jurisdiction'] );
    $rdetail->setAddress1( $member['reg_addr1'] );
    $rdetail->setAddress2( $member['reg_addr2'] );
    $rdetail->setAddress3( $member['reg_towncity'] );
    $rdetail->setTownCity( $member['reg_countyregion'] );
    $rdetail->setPostcode( $member['reg_postcode'] );
    $rdetail->setCountry( mapCountry( $member['reg_country'], $countries ) );
}

/**
 * Creates or updates customers billing details from given member array.
 *
 * @param \Entities\Customer $cust   Customer to update or create registration details
 * @param array              $member Members details array parsed from csv file.
 * @param object             $em     Doctrine entity manager.
 * @return void
 */
function createUpdateBilingDetails( $cust, $member, $em, $countries )
{
    $bdetail = $cust->getBillingDetails();
    if( !$bdetail )
    {
        $bdetail = new \Entities\CompanyBillingDetail();
        $em->persist( $bdetail );
        $bdetail->addCustomer( $cust );
        $cust->setBillingDetails( $bdetail );
    }

    $bdetail->setBillingContactName( $member['billing_name'] );
    $bdetail->setBillingAddress1( $member['bill_addr1'] );
    $bdetail->setBillingAddress2( $member['bill_addr2'] .", " . $member['bill_towncity'] );
    $bdetail->setBillingTownCity( $member['bill_countyregion'] );
    $bdetail->setBillingPostcode( $member['bill_postcode'] );
    $bdetail->setBillingCountry( mapCountry( $member['bill_country'], $countries ) );
    $bdetail->setBillingEmail( $member['billing_email'] );
    $bdetail->setBillingTelephone( $member['billing_phone'] );
    $bdetail->setBillingFrequency( mapBillFreq( $member['billing_frequency'] ) );
    $bdetail->setVatNumber( $member['vat_no'] );
    $bdetail->setVatRate( $member['vat_rate'] );
    $bdetail->setPurchaseOrderRequired( strtolower( $member['po_required'] ) == 'yes' ? 1 : 0 );
    $bdetail->setInvoiceMethod( mapInvoiceMethod( $member['invoice_method'] ) );
    $bdetail->setInvoiceEmail( $member['invoice_email'] );
}

/**
 * Creates or updates customers contacts from given member array.
 *
 * @param \Entities\Customer $cust   Customer to update or create contacts
 * @param array              $member Members details array parsed from csv file.
 * @param object             $em     Doctrine entity manager.
 * @return void
 */
function createUpdateContacts( $cust, $member, $em )
{
    createUpdateContact( 'admin', $cust, $member, $em );
    createUpdateContact( 'tech', $cust, $member, $em );
    createUpdateContact( 'billing', $cust, $member, $em );
}

/**
 * Creates or updates customers contact from given member array by type.
 *
 * Function checks if contact exits for given type as role and contact name. If contact
 * is not existent then function creates contact and otherwise it updates it.
 *
 * @param string             $type   Contact type. Valid options: admin | tech | billing
 * @param \Entities\Customer $cust   Customer to update or create contacts
 * @param array              $member Members details array parsed from csv file.
 * @param object             $em     Doctrine entity manager.
 * @return void
 */
function createUpdateContact( $type, $cust, $member, $em )
{
    if( $type == 'admin' )
        $role_name = 'Admin';
    else if( $type == 'tech' )
        $role_name = 'Technical';
    elseif( $type == 'billing' )
        $role_name = 'Billing';
    else
        throw new Exception( "Unknown type" );
    
    $role = $em->getRepository( "\\Entities\\ContactGroup" )->findOneBy( [ 'name' => $role_name ] );
    if( $member[ $type . '_name' ] || $member[ $type . '_email' ] || $member [ $type . '_phone' ] )
    {
        $cont = false;
        
        if( $cust->getContacts() )
        {
            foreach( $cust->getContacts() as $tcont )
            {
                if( $tcont->getName() == $member[ $type . '_name'] && $tcont->getGroups()->contains( $role ) )
                {
                    $cont = $tcont;
                    break;
                }
            }
        }

        if( !$cont )
        {
            $cont = new \Entities\Contact();
            $em->persist( $cont );
            $cont->setCreated( new \DateTime() );
            $cont->setName( $member[$type . '_name'] ? $member[$type . '_name'] : "$role_name Contact" );
            $cont->addGroup( $role );
            $cont->setCustomer( $cust );
            $cont->setFacilityaccess( 0 );
            $cont->setMayauthorize( 0 );
        }
        $cont->setLastupdated( new \DateTime() );
        $cont->setEmail( $member[ $type . '_email'] );
        $cont->setPhone( $member[ $type . '_phone'] );
        
        if( $type == 'billing' )
            $cont->setNotes( $member['billing_c2_notes'] );

        $cont->setLastupdatedby( null );
    }
}

/**
 * Creates or updates customers notes.
 *
 * Notes are created for:
 *  - $member['po_number'] if is not null
 *  - $member['bill_notes'] if is not null
 *  - $member['joining_form_comments'] if is not null
 *  - Original Migration Data
 *
 * @param \Entities\Customer $cust   Customer to update or create notes
 * @param array              $member Members details array parsed from csv file.
 * @param object             $em     Doctrine entity manager.
 * @return void
 */
function createUpdateNotes( $cust, $member, $em )
{
    if( $member['po_number'] )
    {
        createUpdateNote( "Purchase Orders",
            "Purchase order data recorded during migration to IXP Manager:<br /><br />{$member['po_number']}\n",
            $cust, $em
        );
    }

    if( $member['bill_notes'] )
    {
        createUpdateNote( "Billing Notes",
            "Billing notes as recorded during migration to IXP Manager:<br /><br />{$member['bill_notes']}",
            $cust, $em
        );
    }

    if( $member['joining_form_comments'] )
    {
        createUpdateNote( "Joining Form Comments",
            "Joining form comments as recorded during migration to IXP Manager:<br /><br />{$member['joining_form_comments']}",
            $cust, $em
        );
    }

    $original_data = "<h4>Data Used for Migration to IXP Manager</h4><table class=\"table\">";
    
    foreach( $member as $key => $value )
    {
        if( $value instanceof \DateTime )
        {
            $date = $value->format('d/m/Y H:i:s');
            $original_data .= "<tr><th align=\"right\">{$key}:</th><td>{$date}</td></tr>";
        }
        else
            $original_data .= "<tr><th align=\"right\">{$key}:</th><td>{$value}</td></tr>";
    }
    
    $original_data .= "</table><br />Updated: " . date( "d/m/Y H:i:s" );

    createUpdateNote( "Original Migration Data", $original_data, $cust, $em );
}

/**
 * Creates or updates customer notes from given member array by type.
 *
 * @param string             $title   Note title
 * @param string             $contemt Note content
 * @param \Entities\Customer $cust    Customer to update or create notes
 * @param object             $em      Doctrine entity manager.
 * @return void
 */
function createUpdateNote( $title, $content, $cust, $em )
{
    $cnote = false;
    if( $cust->getNotes() )
    {
        foreach( $cust->getNotes() as $tnote )
        {
            if( $tnote->getTitle() == $title )
            {
                $cnote = $tnote;
                break;
            }
        }
    }

    if( !$cnote )
    {
        $cnote = new \Entities\CustomerNote();
        $em->persist( $cnote );
        $cnote->setCustomer( $cust );
        $cust->addNote( $cnote );
        $cnote->setTitle( $title );
        $cnote->setPrivate( 1 );
        $cnote->setCreated( new \DateTime() );
    }
    
    $cnote->setUpdated( new \DateTime() );
    $cnote->setNote( $content );
}

/**
 * Maps member status value from parsed csv file value to \Entities\Customer status.
 *
 * Returns null or one of \Entities\Customer::STATUS constants.
 *
 * @param string $status Member status
 * @return mixed
 */
function mapCustStatus( $status )
{
    $status = strtolower( $status );
    $cust_status = [
        "active"       => \Entities\Customer::STATUS_NORMAL,
        "ex-member"    => \Entities\Customer::STATUS_SUSPENDED,
        "provisioning" => \Entities\Customer::STATUS_NOTCONNECTED
    ];

    return isset( $cust_status[ $status ] ) ? $cust_status[ $status ] : null;
}

/**
 * Maps member type value from parsed csv file value to \Entities\Customer type.
 *
 * Returns null or one of \Entities\Customer::TYPE constants.
 *
 * @param string $type Member type
 * @return mixed
 */
function mapCustType( $type )
{
    $type = strtolower( $type );
    $cust_type = [
        "member"    => \Entities\Customer::TYPE_FULL,
        "secondary" => \Entities\Customer::TYPE_PROBONO,
        "affiliate" => \Entities\Customer::TYPE_ASSOCIATE
    ];

    return isset( $cust_type[ $type ] ) ? $cust_type[ $type ] : null;
}

/**
 * Maps member billing_frequency value from parsed csv file value to
 * \Entities\CompanyBillingDetail billing frequency.
 *
 * Returns null or one of \Entities\CompanyBillingDetail::BILLING_FREQUENCY constants.
 *
 * @param string $freq Member billing frequency
 * @return mixed
 */
function mapBillFreq( $freq )
{
    $freq = strtolower( $freq );
    $bill_freq = [
        "no-billing" => \Entities\CompanyBillingDetail::BILLING_FREQUENCY_NOBILLING,
        "quarterly" => \Entities\CompanyBillingDetail::BILLING_FREQUENCY_QUARTERLY,
        "annually" => \Entities\CompanyBillingDetail::BILLING_FREQUENCY_ANNUALLY
    ];

    return isset( $bill_freq[ $freq ] ) ? $bill_freq[ $freq ] : null;
}

/**
 * Maps member invoice_method value from parsed csv file value to
 * \Entities\CompanyBillingDetail invoice method.
 *
 * Returns null or one of \Entities\CompanyBillingDetail::INVOICE_METHOD constants.
 *
 * @param string $method Member invoice method
 * @return mixed
 */
function mapInvoiceMethod( $method )
{
    $method = strtolower( $method );
    $inv_methods = [
        "email" => \Entities\CompanyBillingDetail::INVOICE_METHOD_EMAIL,
        "post" => \Entities\CompanyBillingDetail::INVOICE_METHOD_POST
    ];

    return isset( $inv_methods[ $method ] ) ? $inv_methods[ $method ] : null;
}

/**
 * Maps member as_routing_registry value from parsed csv file value to
 * \Entities\IRRDBConfig object.
 *
 * Returns null or one of \Entities\IRRDBConfig object.
 *
 * @param string $key as_routing_registry value form parsed array
 * @param object $em  Doctrine entity manager.
 * @return mixed
 */
function mapIRRDB( $key, $em )
{
    $key = strtolower( $key );
    $map_irrdb = [
        'ripe' => 'RIPE',
        'radb' => 'RADB',
        'ripe & arin' => 'RIPE,ARIN',
        'arin' => 'ARIN'
    ];

    if( isset( $map_irrdb[$key] ) )
        $key = $map_irrdb[$key];
    else
        return null;

    $irrdb = $em->getRepository( "\\Entities\\IRRDBConfig" )->findOneBy( [ 'source' => $key ] );

    return $irrdb ?  $irrdb : null;
}

/**
 * Maps country
 *
 * Tries to get two letter code by country name to store proper in IXP Manager database. If country
 * is not exists in array returns country name as it was given
 *
 * @param string $key as_routing_registry value form parsed array
 * @param object $em  Doctrine entity manager.
 * @return string
 */
function mapCountry( $country, $countries )
{
    $result = array_search( $country, $countries );
    if( $result === false )
        return $country;
    else
        return $result;
}

/**
 * Get join date
 *
 * From parsed member array function tries to get proper joined date. If original_join_date and
 * join_date is null function returns null. If one of dates is not null then returns that date.
 * Else compares dates and returns that one which is earlier.
 *
 * @param string $key as_routing_registry value form parsed array
 * @param object $em  Doctrine entity manager.
 * @return string
 */
function getJoinedDate( $data )
{
    if( !$data['original_join_date'] && !$data['join_date'] )
        return null;
    elseif( !$data['original_join_date'] && $data['join_date'] )
        return $data['join_date'];
    elseif( $data['original_join_date'] && !$data['join_date'] )
        return $data['original_join_date'];
    elseif( $data['original_join_date'] < $data['join_date'] )
        return $data['original_join_date'];
    else
        return $data['join_date'];
}

/**
 * Parses csv file
 *
 * @param string $filename FIle name including path.
 * @return array
 */
function parseFile( $filename )
{
    $fields = getFields();
    $field_names = array_keys( $fields );
    $result = [];
 
    $handle = @fopen( $filename, "r" );
    if( $handle ) {
        
        while( ( $row = fgetcsv($handle, 20000, ",") ) !== false )
        {
            if( count( $row ) != 75 )
                continue;

            $result[ $row[59] ]  = processRow( $row, $fields, $field_names );
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
function processRow( $row, $fields, $fnames )
{
    $row = array_combine( $fnames, $row );
    
    foreach( $row as $key => $value )
    {
        if( $value != "\\N" )
        {
            switch( $fields[$key] )
            {
                case 'date':
                    $row[$key] = new DateTime( $value );
                    break;

                case 'double':
                    $row[$key] = (double) $value;
                    break;

                case 'int':
                case 'tinyint':
                case 'smallint':
                    $row[$key] = (int) $value;
                    break;
            }
        }
        else
            $row [$key] = null;
    }
    return $row;
}

/**
 * Returns schema array
 *
 * @return array
 */
function getFields()
{
    return [ 'seb_memberid' => 'varchar', 'status' => 'varchar', 'type'=> 'varchar',
        'join_date' => 'date', 'original_join_date' => 'date', 'acquisitions_transfers' => 'varchar',
        'Filename' => 'varchar', 'company_name' => 'varchar', 'trading_name_www' => 'varchar',
        'company_no' => 'varchar', 'company_reg_jurisdiction' => 'varchar', 'vat_no' => 'varchar',
        'vat_rate' => 'varchar', 'reg_addr1' => 'varchar', 'reg_addr2' => 'varchar',
        'reg_towncity' => 'varchar', 'reg_countyregion' => 'varchar', 'reg_postcode' => 'varchar',
        'reg_country' => 'varchar', 'admin_name' => 'varchar', 'admin_email' => 'varchar',
        'admin_phone' => 'varchar', 'admin_fax' => 'varchar', 'billing_name' => 'varchar',
        'billing_email' => 'varchar', 'billing_phone' => 'varchar', 'billing_fax' => 'varchar',
        'billing_c2_notes' => 'varchar', 'po_required' => 'varchar', 'po_number' => 'varchar',
        'invoice_method' => 'varchar', 'invoice_email' => 'varchar', 'billing_frequency' => 'varchar',
        'bill_fao' => 'varchar', 'bill_company' => 'varchar', 'bill_addr1' => 'varchar',
        'bill_addr2' => 'varchar', 'bill_towncity' => 'varchar', 'bill_countyregion' => 'varchar',
        'bill_postcode' => 'varchar', 'bill_country' => 'varchar', 'bill_notes' => 'varchar',
        'Connection_Type_1' => 'varchar', 'Connection_Site_1' => 'varchar', 'Connection_Rack_1' => 'varchar',
        'Connection_DNS_PTR_1' => 'varchar', 'Connection_Type_2' => 'varchar', 'Connection_Site_2' => 'varchar',
        'Connection_Rack_2' => 'varchar', 'Connection_DNS_PTR_2' => 'varchar', 'vlan_ipv4' => 'varchar',
        'vlan_ipv4_multicast' => 'varchar', 'vlan_ipv6' => 'varchar', 'md5_support' => 'varchar',
        'tech_name' => 'varchar', 'tech_email' => 'varchar', 'tech_phone' => 'varchar',
        'tec_fax' => 'varchar', 'memsrc_active' => 'smallint', 'as_number' => 'double',
        'as_macro' => 'varchar', 'as_routing_registry' => 'varchar', 'memsrc_conn_a_speed' => 'double',
        'memsrc_conn_b_speed' => 'double', 'memsrc_conn_a_ip' => 'varchar', 'memsrc_conn_b_ip' => 'varchar',
        'memsrc_peering_email' => 'varchar', 'memsrc_noc_email' => 'varchar', 'memsrc_noc_phone' => 'varchar',
        'memsrc_updated' => 'date', 'memsrc_URL' => 'varchar', 'memsrc_IPv6' => 'double',
        'joining_form_date' => 'date', 'joining_form_ip' => 'varchar','joining_form_comments' => 'varchar'
    ];
}

