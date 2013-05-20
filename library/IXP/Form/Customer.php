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
class IXP_Form_Customer extends IXP_Form
{
    public function init()
    {
        $this->setDecorators( [ [ 'ViewScript', [ 'viewScript' => 'customer/forms/edit.phtml' ] ] ] );
        
        $name = $this->createElement( 'text', 'name' );
        $name->addValidator( 'stringLength', false, array( 1, 255 ) )
            ->setRequired( true )
            ->setLabel( 'Name' )
            ->setAttrib( 'class', 'span6' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $name  );

        $type = $this->createElement( 'select', 'type' );
        $type->setMultiOptions( [ '0' => '' ] + \Entities\Customer::$CUST_TYPES_TEXT )
            ->setRegisterInArrayValidator( true )
            ->setLabel( 'Type' )
            ->setAttrib( 'class', 'chzn-select span6' )
            ->setErrorMessages( array( 'Please select a customer type' ) );
        $this->addElement( $type );

        $shortname = $this->createElement( 'text', 'shortname' );
        $shortname->addValidator( 'stringLength', false, array( 1, 255 ) )
            ->addValidator( 'alnum' )
            ->addValidator( 'regex', false, array('/^[a-z0-9]+/' ) )
            ->setRequired( true )
            ->setAttrib( 'class', 'span4' )
            ->setLabel( 'Short Name' )
            ->addFilter( 'StringToLower' )
            ->addFilter( 'StringTrim' );
        $this->addElement( $shortname  );

        $corpwww = $this->createElement( 'text', 'corpwww' );
        $corpwww->addValidator( 'stringLength', false, array( 0, 255 ) )
            ->setRequired( false )
            ->setAttrib( 'placeholder', 'http://www.example.com/' )
            ->setAttrib( 'class', 'span6' )
            ->setLabel( 'Corporate Website' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $corpwww );

        $datejoin = $this->createElement( 'text', 'datejoin' );
        $datejoin->addValidator( 'stringLength', false, array( 10, 10 ) )
            ->addValidator( 'regex', false, array('/^\d\d\d\d-\d\d-\d\d/' ) )
            ->setRequired( false )
            ->setLabel( 'Date Joined' )
            ->setAttrib( 'placeholder', 'YYYY-MM-DD' )
            ->setAttrib( 'class', 'span4' )
            ->addFilter( 'StringTrim' )
            ->setAttrib( 'id', 'datejoin' );
        $this->addElement( $datejoin );

        $dateleave = $this->createElement( 'text', 'dateleave' );
        $dateleave->addValidator( 'stringLength', false, array( 10, 10 ) )
            ->addValidator( 'regex', false, array('/^\d\d\d\d-\d\d-\d\d/' ) )
            ->setRequired( false )
            ->setAttrib( 'placeholder', 'YYYY-MM-DD' )
            ->setAttrib( 'class', 'span4' )
            ->setLabel( 'Date Left' )
            ->addFilter( 'StringTrim' );
        $this->addElement( $dateleave );

        $status = $this->createElement( 'select', 'status' );
        $status->setMultiOptions( [ '0' => '' ] + \Entities\Customer::$CUST_STATUS_TEXT )
            ->setRegisterInArrayValidator( true )
            ->setLabel( 'Status' )
            ->setRequired( true )
            ->setAttrib( 'class', 'chzn-select span6' )
            ->setErrorMessages( array( 'Please set the customer\'s status' ) );
        $this->addElement( $status );


        $autsys = $this->createElement( 'text', 'autsys' );
        $autsys->addValidator('int')
            ->addValidator( 'greaterThan', false, array( -1 ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span4' )
            ->setLabel( 'AS Number' );
        $this->addElement( $autsys  );

        $maxprefixes = $this->createElement( 'text', 'maxprefixes' );
        $maxprefixes->addValidator('int')
            ->addValidator( 'greaterThan', false, array( -1 ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span2' )
            ->setLabel( 'Max Prefixes' );
        $this->addElement( $maxprefixes  );

        $peeringemail = $this->createElement( 'text', 'peeringemail' );
        $peeringemail->addValidator('emailAddress' )
            ->addValidator( 'stringLength', false, array( 0, 64 ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span6' )
            ->setAttrib( 'placeholder', 'peering@example.com' )
            ->setLabel( 'Email' );
        $this->addElement( $peeringemail );

        $peeringpolicy = $this->createElement( 'select', 'peeringpolicy' );
        $peeringpolicy->setMultiOptions( [ 0 => '' ] + \Entities\Customer::$PEERING_POLICIES )
            ->setRegisterInArrayValidator( true )
            ->setLabel( 'Peering Policy' )
            ->setRequired( false )
            ->setAttrib( 'class', 'chzn-select span6' );
        
        $this->addElement( $peeringpolicy );
        
        
        $peeringmacro = $this->createElement( 'text', 'peeringmacro' );
        $peeringmacro->addValidator( 'stringLength', false, array( 0, 255 ) )
            ->setRequired( false )
            ->setLabel( 'Peering Macro' )
            ->addFilter( 'StringTrim' )
            ->setAttrib( 'class', 'span4' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $peeringmacro );


        $this->addElement( IXP_Form_IrrdbConfig::getPopulatedSelect() );


        $peeringDb = $this->createElement( 'text', 'peeringDb' );
        $peeringDb->addValidator( 'int' )
            ->setRequired( false )
            ->setAttrib( 'placeholder', 'XXX (from url ...id=XXX)' )
            ->setAttrib( 'class', 'span6' )
            ->setLabel( 'PeeringDB ID' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $peeringDb );
        
        
        $activepeeringmatrix = $this->createElement( 'checkbox', 'activepeeringmatrix' );
        $activepeeringmatrix->setLabel( 'Active Peering Matrix' )
            ->setCheckedValue( '1' );
        $this->addElement( $activepeeringmatrix );


        $this->addDisplayGroup(
            [ 'autsys', 'maxprefixes', 'peeringemail', 'peeringmacro', 'peeringpolicy', 'irrdb', 'peeringDb', 'activepeeringmatrix' ],
    		'peeringDisplayGroup'
        );
        $this->getDisplayGroup( 'peeringDisplayGroup' )->setLegend( 'Peering Details' );

        
        
        $nocphone = $this->createElement( 'text', 'nocphone' );
        $nocphone->addValidator( 'stringLength', false, array( 0, 255 ) )
            ->setRequired( false )
            ->setLabel( 'Phone' )
            ->setAttrib( 'placeholder', '+353 1 123 4567' )
            ->setAttrib( 'class', 'span4' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $nocphone );

        $noc24hphone = $this->createElement( 'text', 'noc24hphone' );
        $noc24hphone->addValidator( 'stringLength', false, array( 0, 255 ) )
            ->setRequired( false )
            ->setAttrib( 'placeholder', '+353 86 876 5432' )
            ->setAttrib( 'class', 'span4' )
            ->setLabel( '24h Phone' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $noc24hphone );

        $nocfax = $this->createElement( 'text', 'nocfax' );
        $nocfax->addValidator( 'stringLength', false, array( 0, 40 ) )
            ->setRequired( false )
            ->setLabel( 'Fax' )
            ->setAttrib( 'placeholder', '+353 1 765 4321' )
            ->setAttrib( 'class', 'span4' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $nocfax );

        $nocemail = $this->createElement( 'text', 'nocemail' );
        $nocemail->addValidator('emailAddress' )
            ->addValidator( 'stringLength', false, array( 0, 40 ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span6' )
            ->setAttrib( 'placeholder', 'noc@example.com' )
            ->setLabel( 'E-Mail' );
        $this->addElement( $nocemail );

        $nochours = $this->createElement( 'select', 'nochours' );
        $nochours->setMultiOptions( [ '0' => '' ] + \Entities\Customer::$NOC_HOURS )
            ->setRegisterInArrayValidator( true )
            ->setLabel( 'Hours' )
            ->setRequired( false )
            ->setAttrib( 'class', 'chzn-select span6' );
        $this->addElement( $nochours );
        
        
        $nocwww = $this->createElement( 'text', 'nocwww' );
        $nocwww->addValidator( 'stringLength', false, array( 0, 255 ) )
            ->setRequired( false )
            ->setLabel( 'Website' )
            ->setAttrib( 'placeholder', 'http://www.noc.example.com/' )
            ->setAttrib( 'class', 'span6' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $nocwww );

        $this->addDisplayGroup(
            array( 'nocphone', 'noc24hphone', 'nocfax', 'nocemail', 'nochours', 'nocwww' ),
        	'nocDisplayGroup'
        );
        $this->getDisplayGroup( 'nocDisplayGroup' )->setLegend( 'NOC Details' );

        
        
        
        $billingContact = $this->createElement( 'text', 'billingContactName' );
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

        $billingTownCity = $this->createElement( 'text', 'billingTownCity' );
        $billingTownCity->addValidator( 'stringLength', false, array( 0, 64 ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span4' )
            ->setLabel( 'City' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $billingTownCity );

        $billingCountry = $this->createElement( 'select', 'billingCountry' );
        $billingCountry->setMultiOptions( OSS_Countries::getCountriesArray() )
            ->setRegisterInArrayValidator( true )
            ->setValue( 'IE' )
            ->setLabel( 'Country' )
            ->setRequired( false )
            ->setAttrib( 'class', 'chzn-select span6' );
        
        $this->addElement( $billingCountry );

        $billingPostcode = $this->createElement( 'text', 'billingPostcode' );
        $billingPostcode->addValidator( 'stringLength', false, array( 0, 64 ) )
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
            ->setAttrib( 'placeholder', 'billing@gmail.com' )
            ->setLabel( 'E-Mail' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $billingEmail );

        $billingTelephone = $this->createElement( 'text', 'billingTelephone' );
        $billingTelephone->addValidator( 'stringLength', false, array( 0, 64 ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span6' )
            ->setAttrib( 'placeholder', '+353 1 234 5678' )
            ->setLabel( 'Telephone' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $billingTelephone );

        $vatNumber = $this->createElement( 'text', 'vatNumber' );
        $vatNumber->addValidator( 'stringLength', false, array( 0, 64 ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span6' )
            ->setLabel( 'VAT Number' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $vatNumber );

        $vatRate = $this->createElement( 'text', 'vatRate' );
        $vatRate->addValidator( 'stringLength', false, array( 0, 64 ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span4' )
            ->setLabel( 'VAT Rate' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $vatRate );

        $registeredName = $this->createElement( 'text', 'registeredName' );
        $registeredName->addValidator( 'stringLength', false, array( 0, 64 ) )
            ->setRequired( false )
            ->setLabel( 'Registered Name' )
            ->addFilter( 'StringTrim' )
            ->setAttrib( 'class', 'span6' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $registeredName );

        $companyNumber = $this->createElement( 'text', 'companyNumber' );
        $companyNumber->addValidator( 'stringLength', false, array( 0, 64 ) )
            ->setRequired( false )
            ->setLabel( 'Company Number' )
            ->addFilter( 'StringTrim' )
            ->setAttrib( 'class', 'span6' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $companyNumber );

        $jurisdiction = $this->createElement( 'text', 'jurisdiction' );
        $jurisdiction->addValidator( 'stringLength', false, array( 0, 64 ) )
            ->setRequired( false )
            ->setLabel( 'Jurisdiction' )
            ->addFilter( 'StringTrim' )
            ->setAttrib( 'class', 'span6' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $jurisdiction );

        $address1 = $this->createElement( 'text', 'address1' );
        $address1->addValidator( 'stringLength', false, array( 0, 64 ) )
            ->setRequired( false )
            ->setLabel( 'Address' )
            ->setAttrib( 'class', 'span6' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $address1 );

        $address2 = $this->createElement( 'text', 'address2' );
        $address2->addValidator( 'stringLength', false, array( 0, 64 ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span6' )
            ->setLabel( '' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $address2 );

        $address3 = $this->createElement( 'text', 'address3' );
        $address3->addValidator( 'stringLength', false, array( 0, 64 ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span6' )
            ->setLabel( '' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $address3 );

        $townCity = $this->createElement( 'text', 'townCity' );
        $townCity->addValidator( 'stringLength', false, array( 0, 64 ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span4' )
            ->setLabel( 'City' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $townCity );

        $country = $this->createElement( 'select', 'country' );
        $country->setMultiOptions( OSS_Countries::getCountriesArray() )
            ->setRegisterInArrayValidator( true )
            ->setValue( 'IE' )
            ->setLabel( 'Country' )
            ->setRequired( false )
            ->setAttrib( 'class', 'chzn-select span6' );

        $this->addElement( $country );

        $postcode = $this->createElement( 'text', 'postcode' );
        $postcode->addValidator( 'stringLength', false, array( 0, 64 ) )
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

        $this->addElement( self::createSubmitElement( 'submit', _( 'Add' ) ) );
        $this->addElement( $this->createCancelElement() );
    }


    
    /**
     * Create a SELECT / dropdown element of all customer names indexed by their id.
     *
     * @param string $name The element name
     * @return Zend_Form_Element_Select The select element
     */
    public static function getPopulatedSelect( $name = 'custid' )
    {
        $cust = new Zend_Form_Element_Select( $name );
        
        $maxId = self::populateSelectFromDatabase( $cust, '\\Entities\\Customer', 'id', 'name', 'name', 'ASC' );
        
        $cust->setRegisterInArrayValidator( true )
            ->setRequired( true )
            ->setLabel( _( 'Customer' ) )
            ->setAttrib( 'class', 'span3 chzn-select' )
            ->addValidator( 'between', false, array( 1, $maxId ) )
            ->setErrorMessages( array( _( 'Please select a customer' ) ) );
        
        return $cust;
    }

}

