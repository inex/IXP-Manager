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
 * Form: editing billing details
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Form
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXP_Form_Customer_BillingDetails extends IXP_Form
{
    public function init()
    {
        $billingContact = $this->createElement( 'text', 'billingContact' );
        $billingContact->addValidator( 'stringLength', false, array( 0, 64 ) )
            ->setRequired( false )
            ->setLabel( 'Contact' )
            ->addFilter( 'StringTrim' )
            ->setAttrib( 'class', 'span6' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $billingContact );

        $billingAddress1 = $this->createElement( 'text', 'billingAddress1' );
        $billingAddress1->addValidator( 'stringLength', false, array( 0, 64 ) )
            ->setRequired( false )
            ->setLabel( 'Address' )
            ->setAttrib( 'class', 'span6' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $billingAddress1 );

        $billingAddress2 = $this->createElement( 'text', 'billingAddress2' );
        $billingAddress2->addValidator( 'stringLength', false, array( 0, 64 ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span6' )
            ->setLabel( '' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $billingAddress2 );

        $billingCity = $this->createElement( 'text', 'billingCity' );
        $billingCity->addValidator( 'stringLength', false, array( 0, 64 ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span4' )
            ->setLabel( 'City' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $billingCity );

        $billingCountry = $this->createElement( 'select', 'billingCountry' );
        $billingCountry->setMultiOptions( [ '' => '' ] + OSS_Countries::getCountriesArray() )
            ->setRegisterInArrayValidator( true )
            ->setValue( 'IE' )
            ->setLabel( 'Country' )
            ->setRequired( false )
            ->setAttrib( 'style', 'width: 150px;' )
            ->setAttrib( 'class', 'chzn-select' );
        
        $this->addElement( $billingCountry );
        
        
        $this->addDisplayGroup(
            [ 'billingContact', 'billingAddress1', 'billingAddress2', 'billingCity', 'billingCountry' ],
        	'billingDisplayGroup'
        );
        $this->getDisplayGroup( 'billingDisplayGroup' )->setLegend( 'Billing Details' );

        $this->addElement( self::createSubmitElement( 'submit', _( 'Update' ) ) );
    }

}

