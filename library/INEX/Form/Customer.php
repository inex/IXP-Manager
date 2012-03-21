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
class INEX_Form_Customer extends INEX_Form
{
    /**
     *
     *
     */
    public function __construct( $options = null, $isEdit = false )
    {
        parent::__construct( $options );

        $this->setDecorators(
            array(
                array(
                    'ViewScript',
                    array(
                        'viewScript' => 'customer/forms/edit.tpl'
                    )
                )
            )
        );
        
        ////////////////////////////////////////////////
        // Create and configure name element
        ////////////////////////////////////////////////

        $name = $this->createElement( 'text', 'name' );
        $name->addValidator( 'stringLength', false, array( 1, 255 ) )
            ->setRequired( true )
            ->setLabel( 'Name' )
            ->setAttrib( 'class', 'span3' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $name  );

        ////////////////////////////////////////////////
        // Create and configure type element
        ////////////////////////////////////////////////

        $type = $this->createElement( 'select', 'type' );
        $type->setMultiOptions( array( '0' => '' ) + Cust::$CUST_TYPES_TEXT )
            ->setRegisterInArrayValidator( true )
            ->setLabel( 'Type' )
            ->setAttrib( 'class', 'chzn-select' )
            ->setErrorMessages( array( 'Please select a customer type' ) );

        $this->addElement( $type );

        ////////////////////////////////////////////////
        // Create and configure shortname element
        ////////////////////////////////////////////////

        $shortname = $this->createElement( 'text', 'shortname' );
        $shortname->addValidator( 'stringLength', false, array( 1, 255 ) )
            ->addValidator('alnum')
            ->addValidator( 'regex', false, array('/^[a-z0-9]+/' ) )
            ->setRequired( true )
            ->setAttrib( 'class', 'span2' )
            ->setLabel( 'Short Name' )
            ->addFilter( 'StringToLower' )
            ->addFilter( 'StringTrim' );

        $this->addElement( $shortname  );


        $corpwww = $this->createElement( 'text', 'corpwww' );
        $corpwww->addValidator( 'stringLength', false, array( 0, 255 ) )
            ->setRequired( false )
            ->setAttrib( 'placeholder', 'http://www.example.com/' )
            ->setLabel( 'Corporate Website' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $corpwww );

        $datejoin = $this->createElement( 'text', 'datejoin' );
        $datejoin->addValidator( 'stringLength', false, array( 10, 10 ) )
            ->addValidator( 'regex', false, array('/^\d\d\d\d-\d\d-\d\d/' ) )
            ->setRequired( false )
            ->setLabel( 'Date Joined' )
            ->setAttrib( 'placeholder', 'YYYY-MM-DD' )
            ->setAttrib( 'class', 'span2' )
            ->addFilter( 'StringTrim' )
            ->setAttrib( 'id', 'datejoin' );
        $this->addElement( $datejoin );

        $dateleave = $this->createElement( 'text', 'dateleave' );
        $dateleave->addValidator( 'stringLength', false, array( 10, 10 ) )
            ->addValidator( 'regex', false, array('/^\d\d\d\d-\d\d-\d\d/' ) )
            ->setRequired( false )
            ->setAttrib( 'placeholder', 'YYYY-MM-DD' )
            ->setAttrib( 'class', 'span2' )
            ->setLabel( 'Date Left' )
            ->addFilter( 'StringTrim' );
        $this->addElement( $dateleave );

        $status = $this->createElement( 'select', 'status' );
        $status->setMultiOptions( array( '0' => '' ) + Cust::$CUST_STATUS_TEXT )
            ->setRegisterInArrayValidator( true )
            ->setLabel( 'Status' )
            ->setRequired( true )
            ->setAttrib( 'class', 'chzn-select' )
            ->setErrorMessages( array( 'Please set the customer\'s status' ) );
        $this->addElement( $status );



        $autsys = $this->createElement( 'text', 'autsys' );
        $autsys->addValidator('int')
            ->addValidator( 'greaterThan', false, array( -1 ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span2' )
            ->setLabel( 'AS Number' );
        $this->addElement( $autsys  );

        $maxprefixes = $this->createElement( 'text', 'maxprefixes' );
        $maxprefixes->addValidator('int')
            ->addValidator( 'greaterThan', false, array( -1 ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span1' )
            ->setLabel( 'Max Prefixes' );
        $this->addElement( $maxprefixes  );

        $peeringemail = $this->createElement( 'text', 'peeringemail' );
        $peeringemail->addValidator('emailAddress' )
            ->addValidator( 'stringLength', false, array( 0, 64 ) )
            ->setRequired( false )
            ->setAttrib( 'placeholder', 'peering@example.com' )
            ->setLabel( 'Email' );
        $this->addElement( $peeringemail );

        $peeringpolicy = $this->createElement( 'select', 'peeringpolicy' );
        $peeringpolicy->setMultiOptions( array( 0 => '' ) + Cust::$PEERING_POLICIES )
            ->setRegisterInArrayValidator( true )
            ->setLabel( 'Peering Policy' )
            ->setRequired( false )
            ->setAttrib( 'class', 'chzn-select' );
        
        $this->addElement( $peeringpolicy );
        
        
        $peeringmacro = $this->createElement( 'text', 'peeringmacro' );
        $peeringmacro->addValidator( 'stringLength', false, array( 0, 255 ) )
            ->setRequired( false )
            ->setLabel( 'Peering Macro' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $peeringmacro );



        $irrdb = $this->createElement( 'select', 'irrdb' );

        $maxIrrdbId = $this->createSelectFromDatabaseTable( $irrdb, 'Irrdbconfig', 'id',
            array( 'source', 'host' ),
            'source'
        );

        $irrdb->setRegisterInArrayValidator( true )
            ->setRequired( false )
            ->setLabel( 'IRRDB' )
            ->setAttrib( 'class', 'chzn-select' )
            ->setErrorMessages( array( 'Please select an IRRDB' ) );

        $this->addElement( $irrdb );



        $activepeeringmatrix = $this->createElement( 'checkbox', 'activepeeringmatrix' );
        $activepeeringmatrix->setLabel( 'Active Peering Matrix' )
            ->setCheckedValue( '1' );
        $this->addElement( $activepeeringmatrix );


        $this->addDisplayGroup(
            array(
            	'autsys', 'maxprefixes', 'peeringemail', 'peeringmacro', 'peeringpolicy', 'irrdb', 'activepeeringmatrix'
            ),
    		'peeringDisplayGroup'
            );
        $this->getDisplayGroup( 'peeringDisplayGroup' )->setLegend( 'Peering Details' );

        
        
        $nocphone = $this->createElement( 'text', 'nocphone' );
        $nocphone->addValidator( 'stringLength', false, array( 0, 255 ) )
            ->setRequired( false )
            ->setLabel( 'Phone' )
            ->setAttrib( 'placeholder', '+353 1 123 4567' )
            ->setAttrib( 'class', 'span2' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $nocphone );

        $noc24hphone = $this->createElement( 'text', 'noc24hphone' );
        $noc24hphone->addValidator( 'stringLength', false, array( 0, 255 ) )
            ->setRequired( false )
            ->setAttrib( 'placeholder', '+353 86 876 5432' )
            ->setAttrib( 'class', 'span2' )
            ->setLabel( '24h Phone' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $noc24hphone );

        $nocfax = $this->createElement( 'text', 'nocfax' );
        $nocfax->addValidator( 'stringLength', false, array( 0, 40 ) )
            ->setRequired( false )
            ->setLabel( 'Fax' )
            ->setAttrib( 'placeholder', '+353 1 765 4321' )
            ->setAttrib( 'class', 'span2' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $nocfax );

        $nocemail = $this->createElement( 'text', 'nocemail' );
        $nocemail->addValidator('emailAddress' )
            ->addValidator( 'stringLength', false, array( 0, 40 ) )
            ->setRequired( false )
            ->setAttrib( 'placeholder', 'noc@example.com' )
            ->setLabel( 'E-Mail' );
        $this->addElement( $nocemail );

        $nochours = $this->createElement( 'select', 'nochours' );
        $nochours->setMultiOptions( array( '0' => '' ) + Cust::$NOC_HOURS )
            ->setRegisterInArrayValidator( true )
            ->setLabel( 'Hours' )
            ->setRequired( false )
            ->setAttrib( 'class', 'chzn-select' );
        $this->addElement( $nochours );
        
        
        $nocwww = $this->createElement( 'text', 'nocwww' );
        $nocwww->addValidator( 'stringLength', false, array( 0, 255 ) )
            ->setRequired( false )
            ->setLabel( 'Website' )
            ->setAttrib( 'placeholder', 'http://www.noc.example.com/' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $nocwww );

        $this->addDisplayGroup(
            array( 'nocphone', 'noc24hphone', 'nocfax', 'nocemail', 'nochours', 'nocwww' ),
        	'nocDisplayGroup'
        );
        $this->getDisplayGroup( 'nocDisplayGroup' )->setLegend( 'NOC Details' );

        
        
        
        $billingContact = $this->createElement( 'text', 'billingContact' );
        $billingContact->addValidator( 'stringLength', false, array( 0, 64 ) )
            ->setRequired( false )
            ->setLabel( 'Contact' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $billingContact );

        $billingAddress1 = $this->createElement( 'text', 'billingAddress1' );
        $billingAddress1->addValidator( 'stringLength', false, array( 0, 64 ) )
            ->setRequired( false )
            ->setLabel( 'Address' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $billingAddress1 );

        $billingAddress2 = $this->createElement( 'text', 'billingAddress2' );
        $billingAddress2->addValidator( 'stringLength', false, array( 0, 64 ) )
            ->setRequired( false )
            ->setLabel( '' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $billingAddress2 );

        $billingCity = $this->createElement( 'text', 'billingCity' );
        $billingCity->addValidator( 'stringLength', false, array( 0, 64 ) )
            ->setRequired( false )
            ->setLabel( 'City' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $billingCity );

        $billingCountry = $this->createElement( 'select', 'billingCountry' );
        $billingCountry->setMultiOptions( INEX_Countries::getCountriesArray() )
            ->setRegisterInArrayValidator( true )
            ->setValue( 'IE' )
            ->setLabel( 'Country' )
            ->setRequired( false )
            ->setAttrib( 'class', 'chzn-select' );
        
        $this->addElement( $billingCountry );
        
        
        
        
        
        
        $this->addDisplayGroup(
            array( 'billingContact', 'billingAddress1', 'billingAddress2', 'billingCity', 'billingCountry' ),
        	'billingDisplayGroup'
        );
        $this->getDisplayGroup( 'billingDisplayGroup' )->setLegend( 'Billing Details' );


        $notes = $this->createElement( 'textarea', 'notes' );
        $notes->setLabel( 'Notes' )
            ->setRequired( false )
            ->addFilter( new INEX_Filter_StripSlashes() )
            ->setAttrib( 'cols', 60 )
            ->setAttrib( 'rows', 5 );
        $this->addElement( $notes );

        $this->addElement( 'button', 'cancel', array( 'label' => 'Cancel', 'onClick' => "parent.location='" . Zend_Controller_Front::getInstance()->getBaseUrl() . "/customer/list'" ) );

        $this->addElement( 'submit', 'commit', array( 'label' => 'Add New Customer' ) );

    }

    public function assignFormToModel( $model )
    {
        $columns = Doctrine::getTable( "Cust" )->getFieldNames();

        foreach( $this->getElements() as $elementName => $elementConfig )
            if( in_array( $elementName, $columns ) )
                $model->$elementName = $this->getValue( $elementName );

        return $model;
    }

    public function assignModelToForm( $model )
    {
        $columns = Doctrine::getTable( "Cust" )->getFieldNames();

        foreach( $this->getElements() as $elementName => $elementConfig )
            if( in_array( $elementName, $columns ) )
                $this->getElement( $elementName )->setValue( $model->$elementName );

        return $this;
    }

}

