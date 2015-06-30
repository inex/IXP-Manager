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
        $billingContact = $this->createElement( 'text', 'billingContactName' );
        $billingContact->addValidator( 'stringLength', false, array( 0, 64, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'Contact' )
            ->addFilter( 'StringTrim' )
            ->addFilter( 'StripTags' )
            ->setAttrib( 'class', 'span6' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $billingContact );

        $billingAddress1 = $this->createElement( 'text', 'billingAddress1' );
        $billingAddress1->addValidator( 'stringLength', false, array( 0, 64, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'Address' )
            ->setAttrib( 'class', 'span6' )
            ->addFilter( 'StringTrim' )
            ->addFilter( 'StripTags' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $billingAddress1 );

        $billingAddress2 = $this->createElement( 'text', 'billingAddress2' );
        $billingAddress2->addValidator( 'stringLength', false, array( 0, 64, 'UTF-8' ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span6' )
            ->setLabel( '' )
            ->addFilter( 'StringTrim' )
            ->addFilter( 'StripTags' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $billingAddress2 );

        $billingAddress3 = $this->createElement( 'text', 'billingAddress3' );
        $billingAddress3->addValidator( 'stringLength', false, array( 0, 64, 'UTF-8' ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span6' )
            ->setLabel( '' )
            ->addFilter( 'StripTags' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $billingAddress3 );

        $billingCity = $this->createElement( 'text', 'billingTownCity' );
        $billingCity->addValidator( 'stringLength', false, array( 0, 64, 'UTF-8' ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span4' )
            ->setLabel( 'City' )
            ->addFilter( 'StringTrim' )
            ->addFilter( 'StripTags' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $billingCity );

        $billingPostcode = $this->createElement( 'text', 'billingPostcode' );
        $billingPostcode->addValidator( 'stringLength', false, array( 0, 64, 'UTF-8' ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span4' )
            ->setLabel( 'Postcode' )
            ->addFilter( 'StringTrim' )
            ->addFilter( 'StripTags' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $billingPostcode );

        $billingCountry = $this->createElement( 'select', 'billingCountry' );
        $billingCountry->setMultiOptions( [ '' => '' ] + OSS_Countries::getCountriesArray() )
            ->setRegisterInArrayValidator( true )
            ->setValue( 'IE' )
            ->setLabel( 'Country' )
            ->setRequired( false )
            ->setAttrib( 'class', 'chzn-select' )
            ->setAttrib( 'chzn-fix-width', '1' );


        $this->addElement( $billingCountry );

        $billingEmail = $this->createElement( 'text', 'billingEmail' );
        $billingEmail->addValidator('emailAddress' )
            ->setRequired( false )
            ->setAttrib( 'class', 'span6' )
            ->setAttrib( 'placeholder', 'billing@gmail.com' )
            ->setLabel( 'E-Mail' )
            ->addFilter( 'StringTrim' )
            ->addFilter( 'StripTags' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $billingEmail );

        $billingTelephone = $this->createElement( 'text', 'billingTelephone' );
        $billingTelephone->addValidator( 'stringLength', false, array( 0, 64, 'UTF-8' ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span6' )
            ->setAttrib( 'placeholder', '+353 1 234 5678' )
            ->setLabel( 'Telephone' )
            ->addFilter( 'StripTags' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $billingTelephone );

        /* Probably do not want to let the customer update this themselves...

            $vatNumber = $this->createElement( 'text', 'vatNumber' );
            $vatNumber->addValidator( 'stringLength', false, array( 0, 64, 'UTF-8' ) )
                ->setRequired( false )
                ->setAttrib( 'class', 'span6' )
                ->setLabel( 'VAT Number' )
                ->addFilter( 'StringTrim' )
                ->addFilter( 'StripTags' )
                ->addFilter( new OSS_Filter_StripSlashes() );
            $this->addElement( $vatNumber );

            $vatRate = $this->createElement( 'text', 'vatRate' );
            $vatRate->addValidator( 'stringLength', false, array( 0, 64, 'UTF-8' ) )
                ->setRequired( false )
                ->setAttrib( 'class', 'span4' )
                ->setLabel( 'VAT Rate' )
                ->addFilter( 'StringTrim' )
                ->addFilter( 'StripTags' )
                ->addFilter( new OSS_Filter_StripSlashes() );
            $this->addElement( $vatRate );
        */

        $invoiceEmail = $this->createElement( 'text', 'invoiceEmail' );
        $invoiceEmail->addValidator('emailAddress' )
            ->setRequired( false )
            ->setAttrib( 'class', 'span6' )
            ->setAttrib( 'placeholder', 'invoicing@example.com' )
            ->setLabel( 'Invoice E-Mail' )
            ->addFilter( 'StringTrim' )
            ->addFilter( 'StripTags' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $invoiceEmail );

        $this->addDisplayGroup(
            [ 'billingContactName', 'billingAddress1', 'billingAddress2', 'billingAddress3', 'billingTownCity',
                'billingPostcode', 'billingCountry', 'billingEmail', 'billingTelephone',
                'invoiceEmail'
            ],
        	'billingDisplayGroup'
        );
        $this->getDisplayGroup( 'billingDisplayGroup' )->setLegend( 'Billing Details' );

        $this->addElement( self::createSubmitElement( 'submit', _( 'Update' ) ) );
    }

}
