<?php

/*
 * Copyright (C) 2009-2011 Internet Neutral Exchange Association Limited.
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


/*
 *
 *
 * http://www.inex.ie/
 * (c) Internet Neutral Exchange Association Ltd
 */

/**
 *
 * @package INEX_Form
 */
class INEX_Form_Switch extends INEX_Form
{
    /**
     *
     *
     */
    public function __construct( $options = null, $isEdit = false, $cancelLocation )
    {
        parent::__construct( $options, $isEdit );

        ////////////////////////////////////////////////
        // Create and configure elements
        ////////////////////////////////////////////////

        $name = $this->createElement( 'text', 'name' );
        $name->addValidator( 'stringLength', false, array( 1, 255 ) )
            ->setRequired( true )
            ->setLabel( 'Name' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $name );

        $switchtype = $this->createElement( 'select', 'switchtype' );
        $switchtype->setMultiOptions( SwitchTable::$SWITCHTYPES_TEXT )
            ->setRegisterInArrayValidator( true )
            ->addValidator( 'greaterThan', true, array( 0 ) )
            ->setLabel( 'Type' )
            ->setErrorMessages( array( 'Please set the switch type' ) );

        $this->addElement( $switchtype );





        $dbCabinets = Doctrine_Query::create()
            ->from( 'Cabinet' )
            ->execute();

        $cabinets = array( '0' => '' );
        $maxId = 0;

        foreach( $dbCabinets as $c )
        {
            $cabinets[ $c['id'] ] = "{$c->Location->name} :: {$c['name']}";
            if( $c['id'] > $maxId ) $maxId = $c['id'];
        }

        $cabinet = $this->createElement( 'select', 'cabinetid' );
        $cabinet->setMultiOptions( $cabinets );
        $cabinet->setRegisterInArrayValidator( true )
            ->setLabel( 'Cabinet' )
            ->addValidator( 'between', false, array( 1, $maxId ) )
            ->setErrorMessages( array( 'Please select a cabinet' ) );

        $this->addElement( $cabinet );


        $infrastructre = $this->createElement( 'text', 'infrastructure' );
        $infrastructre->setLabel( 'Infrastructure' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $infrastructre );



        $ipv4addr = $this->createElement( 'text', 'ipv4addr' );
        $ipv4addr->addValidator( 'stringLength', false, array( 1, 255 ) )
            ->setRequired( true )
            ->setLabel( 'IPv4 Address' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $ipv4addr );

        $ipv6addr = $this->createElement( 'text', 'ipv6addr' );
        $ipv6addr->addValidator( 'stringLength', false, array( 1, 255 ) )
            ->setLabel( 'IPv6 Address' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $ipv6addr );

        $snmppasswd = $this->createElement( 'text', 'snmppasswd' );
        $snmppasswd->addValidator( 'stringLength', false, array( 1, 255 ) )
            ->setLabel( 'SNMP Password' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $snmppasswd );





        $dbVendors = Doctrine_Query::create()
            ->from( 'Vendor' )
            ->execute();

        $vendors = array( '0' => '' );
        $maxId = 0;

        foreach( $dbVendors as $v )
        {
            $vendors[ $v['id'] ] = "{$v['name']}";
            if( $v['id'] > $maxId ) $maxId = $v['id'];
        }

        $vendor = $this->createElement( 'select', 'vendorid' );
        $vendor->setMultiOptions( $vendors );
        $vendor->setRegisterInArrayValidator( true )
            ->setLabel( 'Vendor' )
            ->addValidator( 'between', false, array( 1, $maxId ) )
            ->setErrorMessages( array( 'Please select a vendor' ) );

        $this->addElement( $vendor );




        $model = $this->createElement( 'text', 'model' );
        $model->addValidator( 'stringLength', false, array( 1, 255 ) )
            ->setLabel( 'Model' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $model );


        $notes = $this->createElement( 'textarea', 'notes' );
        $notes->setLabel( 'Notes' )
            ->setRequired( false )
            ->addFilter( new INEX_Filter_StripSlashes() )
            ->setAttrib( 'cols', 60 )
            ->setAttrib( 'rows', 5 );
        $this->addElement( $notes );


        $this->addElement( 'button', 'cancel', array( 'label' => 'Cancel', 'onClick' => "parent.location='"
        . $cancelLocation . "'" ) );
        $this->addElement( 'submit', 'commit', array( 'label' => 'Add' ) );

    }

}

?>