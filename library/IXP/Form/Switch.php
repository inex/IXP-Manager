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
 * Form: adding / editing switches
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Form
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXP_Form_Switch extends IXP_Form
{
    /**
     * @throws Zend_Form_Exception
     */
    public function init()
    {

        $name = $this->createElement( 'text', 'name' );
        $name->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setAttrib( 'class', 'span3' )
            ->setRequired( true )
            ->setLabel( 'Name' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $name );

        $hostname = $this->createElement( 'text', 'hostname' );
        $hostname->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setAttrib( 'class', 'span3' )
            ->setRequired( true )
            ->setLabel( 'Hostname' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $hostname );
        
        $this->addElement( self::getPopulatedSelectCabinet( ) );

        $infrastructure = self::getPopulatedSelectInfra( );
        $this->addElement( $infrastructure );

        $ipv4addr = $this->createElement( 'text', 'ipv4addr' );
        $ipv4addr->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setAttrib( 'class', 'span3' )
            ->setRequired( true )
            ->setLabel( 'IPv4 Address' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $ipv4addr );

        $ipv6addr = $this->createElement( 'text', 'ipv6addr' );
        $ipv6addr->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setAttrib( 'class', 'span3' )
            ->setLabel( 'IPv6 Address' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $ipv6addr );

        $snmppasswd = $this->createElement( 'text', 'snmppasswd' );
        $snmppasswd->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setAttrib( 'class', 'span3' )
            ->setLabel( 'SNMP Community' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $snmppasswd );

        $this->addElement( self::getPopulatedSelectVendor( 'vendorid' ) );
        

        $model = $this->createElement( 'text', 'model' );
        $model->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setLabel( 'Model' )
            ->setAttrib( 'class', 'span3' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $model );


        $notes = $this->createElement( 'textarea', 'notes' );
        $notes->setLabel( 'Notes' )
            ->setAttrib( 'class', 'span3' )
            ->setRequired( false )
            ->addFilter( new OSS_Filter_StripSlashes() )
            ->setAttrib( 'cols', 60 )
            ->setAttrib( 'rows', 5 );
        $this->addElement( $notes );
        
        $active = $this->createElement( 'checkbox', 'active' );
        $active->setLabel( 'Active?' )
            ->setCheckedValue( '1' )
            ->setUncheckedValue( '0' )
            ->setValue( '1' );
        $this->addElement( $active );

        $asn = $this->createElement( 'text', 'asn' );
        $asn->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->addValidator( 'greaterThan', false, array( 0 ) )
            ->setLabel( 'ASN' )
            ->setAttrib( 'class', 'span3' );
        $this->addElement( $asn );

        $loopback_ip = $this->createElement( 'text', 'loopback_ip' );
        $loopback_ip->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setLabel( 'Loopback IP' )
            ->setAttrib( 'class', 'span3' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $loopback_ip );

        $loopback_name = $this->createElement( 'text', 'loopback_name' );
        $loopback_name->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setLabel( 'Loopback Name' )
            ->setAttrib( 'class', 'span3' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $loopback_name );

        $mgmt_mac_address = $this->createElement( 'text', 'mgmt_mac_address' );
        $mgmt_mac_address->addValidator( 'regex', false, array('/^[a-f0-9]{12}$/' ) )
            ->setLabel( 'Mgmt MAC Address' )
            ->setAttrib( 'class', 'span3' )
            ->addFilter( 'StringTrim' )
            ->addFilter( 'StringToLower' )
            ->addFilter( new Zend_Filter_PregReplace( [ 'match' => '/[^0-9a-f]*/', 'replace' => '' ] ) );
        $this->addElement( $mgmt_mac_address );


        $this->addElement( self::createSubmitElement( 'submit', _( 'Add' ) ) );
        $this->addElement( $this->createCancelElement() );
        
        $manualAdd = new OSS_Form_Element_Buttonlink( 'manualAdd' );
        $manualAdd->setAttrib( 'href', OSS_Utils::genUrl( 'switch', 'add-by-snmp' ) )
            ->setAttrib( 'label', _( 'Add by SNMP' ) );
        $this->addElement( $manualAdd );                            
    }
    
    /**
     * Create a SELECT / dropdown element of all switch names indexed by their id.
     *
     * @param string $name The element name
     * @return Zend_Form_Element_Select The select element
     */
    public static function getPopulatedSelect( $name = 'switchid', $type = null )
    {
        $sw = new Zend_Form_Element_Select( $name );

        $qb = Zend_Registry::get( 'd2em' )['default']->createQueryBuilder()
            ->select( 'e.id AS id, e.name AS name' )
            ->from( '\\Entities\\Switcher', 'e' )
            ->orderBy( "e.name", 'ASC' );
        
        if( $type !== null )
            $qb->where( 'e.switchtype = ?1' )->setParameter( 1, $type );
        
        $maxId = self::populateSelectFromDatabaseQuery( $qb->getQuery(), $sw, '\\Entities\\Switcher', 'id', 'name', 'name', 'ASC' );
    
        $sw->setRegisterInArrayValidator( true )
            ->setRequired( true )
            ->setLabel( _( 'Switch' ) )
            ->setAttrib( 'class', 'span3 chzn-select' )
            ->addValidator( 'between', false, array( 1, $maxId ) )
            ->setErrorMessages( array( _( 'Please select a switch' ) ) );
    
        return $sw;
    }

    /**
     * Create a SELECT / dropdown element of all cabinet names indexed by their id.
     *
     * @return Zend_Form_Element_Select The select element
     */
    public static function getPopulatedSelectCabinet( )
    {
        $cab = new Zend_Form_Element_Select( 'cabinetid' );

        $maxId = self::populateSelectFromDatabase( $cab, '\\Entities\\Cabinet', 'id', 'name', 'name', 'ASC' );

        $cab->setRegisterInArrayValidator( true )
            ->setRequired( true )
            ->setLabel( _( 'Cabinet' ) )
            ->setAttrib( 'class', 'span2 chzn-select' )
            ->addValidator( 'between', false, array( 1, $maxId ) )
            ->setErrorMessages( array( _( 'Please select a cabinet' ) ) );

        return $cab;
    }

    /**
     * Create a SELECT / dropdown element of all infrastructures indexed by their id.
     *
     * Drop down list will be appended like this:
     *  ixp1 - inf1
     *  ixp1 - inf3
     *  ixp2 - inf2
     *
     * @return Zend_Form_Element_Select The select element
     */
    public static function getPopulatedSelectInfra( )
    {
        $sw = new Zend_Form_Element_Select( 'infrastructure' );

        $qb = Zend_Registry::get( 'd2em' )['default']->createQueryBuilder()
            ->select( 'e.id AS id, e.name AS name, ix.shortname AS ixp' )
            ->from( '\\Entities\\Infrastructure', 'e' )
            ->join( 'e.IXP', 'ix' )
            ->add( 'orderBy', "ixp ASC, name ASC" );

        $maxId = self::populateSelectFromDatabaseQuery( $qb->getQuery(), $sw, '\\Entities\\Infrastructure', 'id', [ 'ixp', 'name' ], 'ixp', 'ASC' );

        $sw->setRegisterInArrayValidator( true )
            ->setRequired( false )
            ->setAttrib( 'data-maxId', $maxId )
            ->setLabel( _( 'Infrastructure' ) )
            ->setAttrib( 'class', 'chzn-select-deselect' )
            //->addValidator( 'between', false, array( 1, $maxId ) )
            ->setErrorMessages( [ 'Please select an infrastructure' ] );

        return $sw;
    }

    /**
     * Create a SELECT / dropdown element of all vendor names indexed by their id.
     *
     * @param string $name The element name
     * @return Zend_Form_Element_Select The select element
     */
    public static function getPopulatedSelectVendor( )
    {
        $v = new Zend_Form_Element_Select( 'vendorid' );

        $maxId = self::populateSelectFromDatabase( $v, '\\Entities\\Vendor', 'id', 'name', 'name', 'ASC' );

        $v->setRegisterInArrayValidator( true )
            ->setRequired( true )
            ->setLabel( _( 'Vendor' ) )
            ->setAttrib( 'class', 'span3 chzn-select' )
            ->addValidator( 'between', false, array( 1, $maxId ) )
            ->setErrorMessages( array( _( 'Please select a vendor' ) ) );

        return $v;
    }


}
