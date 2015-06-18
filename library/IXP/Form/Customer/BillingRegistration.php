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
 * Form: adding / editing customers
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Form
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXP_Form_Customer_BillingRegistration extends IXP_Form
{
    public function init()
    {
        $this->setDecorators( [ [ 'ViewScript', [ 'viewScript' => 'customer/forms/billing-registration.phtml' ] ] ] );
        
        $billingContact = $this->createElement( 'text', 'billingContactName' );
        $billingContact->addValidator( 'stringLength', false, array( 0, 64, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'Contact' )
            ->addFilter( 'StringTrim' )
            ->setAttrib( 'class', 'span6' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $billingContact );

        $billingAddress1 = $this->createElement( 'text', 'billingAddress1' );
        $billingAddress1->addValidator( 'stringLength', false, array( 0, 64, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'Address' )
            ->setAttrib( 'class', 'span6' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $billingAddress1 );

        $billingAddress2 = $this->createElement( 'text', 'billingAddress2' );
        $billingAddress2->addValidator( 'stringLength', false, array( 0, 64, 'UTF-8' ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span6' )
            ->setLabel( '' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $billingAddress2 );

        $billingAddress3 = $this->createElement( 'text', 'billingAddress3' );
        $billingAddress3->addValidator( 'stringLength', false, array( 0, 64, 'UTF-8' ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span6' )
            ->setLabel( '' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $billingAddress3 );

        $billingTownCity = $this->createElement( 'text', 'billingTownCity' );
        $billingTownCity->addValidator( 'stringLength', false, array( 0, 64, 'UTF-8' ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span4' )
            ->setLabel( 'City' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $billingTownCity );
        
        $billingCountry = $this->createElement( 'select', 'billingCountry' );
        $billingCountry->setMultiOptions( [ "" => "" ] + OSS_Countries::getCountriesArray() )
            ->setRegisterInArrayValidator( true )
            ->setValue( "" )
            ->setLabel( 'Country' )
            ->setRequired( false )
            ->setAttrib( 'class', 'chzn-select span6' );

        $this->addElement( $billingCountry );

        $billingPostcode = $this->createElement( 'text', 'billingPostcode' );
        $billingPostcode->addValidator( 'stringLength', false, array( 0, 64, 'UTF-8' ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span4' )
            ->setLabel( 'Postcode' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $billingPostcode );
        
        $billingEmail = $this->createElement( 'text', 'billingEmail' );
        $billingEmail->addValidator('emailAddress' )
            ->setRequired( false )
            ->setAttrib( 'class', 'span6' )
            ->setAttrib( 'placeholder', 'billing@example.com' )
            ->setLabel( 'E-Mail' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $billingEmail );

        $billingTelephone = $this->createElement( 'text', 'billingTelephone' );
        $billingTelephone->addValidator( 'stringLength', false, array( 0, 64, 'UTF-8' ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span6' )
            ->setAttrib( 'placeholder', '+353 1 234 5678' )
            ->setLabel( 'Telephone' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $billingTelephone );
        
        $invoiceMethod = $this->createElement( 'select', 'invoiceMethod' );
        $invoiceMethod->setMultiOptions( [ '' => '' ] + \Entities\CompanyBillingDetail::$INVOICE_METHODS )
            ->setRegisterInArrayValidator( true )
            ->setLabel( 'Invoice Method' )
            ->setRequired( false )
            ->setAttrib( 'class', 'chzn-select-deselect span6' );
        $this->addElement( $invoiceMethod );

        $purchaseOrderRequired = $this->createElement( 'checkbox', 'purchaseOrderRequired' );
        $purchaseOrderRequired->setLabel( 'Purchase Order Required' )
            ->setValue( '0' )
            ->addValidator( 'InArray', false, [ [ 0, 1 ] ] )
            ->addFilter( 'Int' );
        $this->addElement( $purchaseOrderRequired );
        
        $billingFrequency = $this->createElement( 'select', 'billingFrequency' );
        $billingFrequency->setMultiOptions( [ '' => '' ] + \Entities\CompanyBillingDetail::$BILLING_FREQUENCIES )
            ->setRegisterInArrayValidator( true )
            ->setLabel( 'Billing Frequency' )
            ->setRequired( false )
            ->setAttrib( 'class', 'chzn-select-deselect span6' );
        $this->addElement( $billingFrequency );
        
        $invoiceEmail = $this->createElement( 'text', 'invoiceEmail' );
        $invoiceEmail->addValidator('emailAddress' )
            ->setRequired( false )
            ->setAttrib( 'class', 'span6' )
            ->setAttrib( 'placeholder', 'invoicing@example.com' )
            ->setLabel( 'Invoice E-Mail' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $invoiceEmail );
        
        $vatNumber = $this->createElement( 'text', 'vatNumber' );
        $vatNumber->addValidator( 'stringLength', false, array( 0, 64, 'UTF-8' ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span6' )
            ->setLabel( 'VAT Number' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $vatNumber );

        $vatRate = $this->createElement( 'text', 'vatRate' );
        $vatRate->addValidator( 'stringLength', false, array( 0, 64, 'UTF-8' ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span4' )
            ->setLabel( 'VAT Rate' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $vatRate );

        $registeredName = $this->createElement( 'text', 'registeredName' );
        $registeredName->addValidator( 'stringLength', false, array( 0, 64, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'Registered Name' )
            ->addFilter( 'StringTrim' )
            ->setAttrib( 'class', 'span6' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $registeredName );

        $companyNumber = $this->createElement( 'text', 'companyNumber' );
        $companyNumber->addValidator( 'stringLength', false, array( 0, 64, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'Company Number' )
            ->addFilter( 'StringTrim' )
            ->setAttrib( 'class', 'span6' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $companyNumber );

        $jurisdiction = new OSS_Form_Element_DatabaseDropdown( 'jurisdiction', [ 'dql' => 'select crd.jurisdiction from \\Entities\\CompanyRegisteredDetail crd WHERE crd.jurisdiction IS NOT NULL' ] );
        $jurisdiction->addValidator( 'stringLength', false, array( 0, 64, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'Jurisdiction' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $jurisdiction );

        $address1 = $this->createElement( 'text', 'address1' );
        $address1->addValidator( 'stringLength', false, array( 0, 64, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'Address' )
            ->setAttrib( 'class', 'span6' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $address1 );

        $address2 = $this->createElement( 'text', 'address2' );
        $address2->addValidator( 'stringLength', false, array( 0, 64, 'UTF-8' ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span6' )
            ->setLabel( '' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $address2 );

        $address3 = $this->createElement( 'text', 'address3' );
        $address3->addValidator( 'stringLength', false, array( 0, 64, 'UTF-8' ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span6' )
            ->setLabel( '' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $address3 );

        $townCity = $this->createElement( 'text', 'townCity' );
        $townCity->addValidator( 'stringLength', false, array( 0, 64, 'UTF-8' ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span4' )
            ->setLabel( 'City' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $townCity );

        $country = $this->createElement( 'select', 'country' );
        $country->setMultiOptions( [ "" => "" ] + OSS_Countries::getCountriesArray() )
            ->setRegisterInArrayValidator( true )
            ->setValue( "" )
            ->setLabel( 'Country' )
            ->setRequired( false )
            ->setAttrib( 'class', 'chzn-select span6' );

        $this->addElement( $country );

        $postcode = $this->createElement( 'text', 'postcode' );
        $postcode->addValidator( 'stringLength', false, array( 0, 64, 'UTF-8' ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span4' )
            ->setLabel( 'Postcode' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $postcode );
                
        $this->addDisplayGroup(
            [ 'billingContact', 'billingAddress1', 'billingAddress2', 'billingTownCity', 'billingCountry' ],
        	'billingDisplayGroup'
        );
        $this->getDisplayGroup( 'billingDisplayGroup' )->setLegend( 'Billing Details' );

        $this->addElement( self::createSubmitElement( 'submit', _( 'Save Changes' ) ) );
        $this->addElement( $this->createCancelElement() );
    }
}

