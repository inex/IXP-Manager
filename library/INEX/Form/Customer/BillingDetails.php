<?php

/*
 * Copyright (C) 2009-2012 Internet Neutral Exchange Association Limited.
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
 *
 * @package INEX_Form
 */
class INEX_Form_Customer_BillingDetails extends INEX_Form
{
    public function __construct( $options = null, $isEdit = false )
    {
        parent::__construct( $options );

        $billingContact = $this->createElement( 'text', 'billingContact' );
        $billingContact->addValidator( 'stringLength', false, array( 0, 64 ) )
            ->setRequired( false )
            ->setLabel( 'Billing Contact' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $billingContact );

        $billingAddress1 = $this->createElement( 'text', 'billingAddress1' );
        $billingAddress1->addValidator( 'stringLength', false, array( 0, 64 ) )
            ->setRequired( false )
            ->setLabel( 'Billing Address' )
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
            ->setLabel( '' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $billingCity );

        $billingCountry = $this->createElement( 'text', 'billingCountry' );
        $billingCountry->addValidator( 'stringLength', false, array( 0, 2 ) )
            ->setRequired( false )
            ->setLabel( 'Country' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $billingCountry );

        $billingCountry = $this->createElement( 'select', 'billingCountry' );
        $billingCountry->setMultiOptions(
            array( '' => '' ) + INEX_Countries::getCountriesArray()
        );
        $billingCountry->setRegisterInArrayValidator( true )
            ->setLabel( 'Country' )
            ->setAttrib( 'class', 'chzn-select' )
            ->setErrorMessages( array( 'Please select a country' ) );
        
        $this->addElement( $billingCountry );
        
        $this->addDisplayGroup(
            array( 'billingContact', 'billingAddress1', 'billingAddress2', 'billingCity', 'billingCountry' ),
        	'billingDisplayGroup'
        );
        $this->getDisplayGroup( 'billingDisplayGroup' )->setLegend( 'Billing Details' );


        $this->addElement( 'submit', 'commit', array( 'label' => 'Update' ) );
    }

}

