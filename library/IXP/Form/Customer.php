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
        $name->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setRequired( true )
            ->setLabel( 'Name' )
            ->setAttrib( 'class', 'span10' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $name  );

        $type = $this->createElement( 'select', 'type' );
        $type->setMultiOptions( [ '' => '' ] + \Entities\Customer::$CUST_TYPES_TEXT )
            ->setRegisterInArrayValidator( true )
            ->setLabel( 'Type' )
            ->setRequired( true )
            ->setAttrib( 'class', 'chzn-select span8' )
            ->setErrorMessages( array( 'Please select a customer type' ) );
        $this->addElement( $type );

        $shortname = $this->createElement( 'text', 'shortname' );
        $shortname->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->addValidator( 'alnum' )
            ->addValidator( 'regex', false, array('/^[a-z0-9]+/' ) )
            ->setRequired( true )
            ->setAttrib( 'class', 'span8' )
            ->setLabel( 'Short Name' )
            ->addFilter( 'StringToLower' )
            ->addFilter( 'StringTrim' );
        $this->addElement( $shortname  );

        $corpwww = $this->createElement( 'text', 'corpwww' );
        $corpwww->addValidator( 'stringLength', false, array( 0, 255, 'UTF-8' ) )
            ->setRequired( false )
            ->setAttrib( 'placeholder', 'http://www.example.com/' )
            ->setAttrib( 'class', 'span10' )
            ->setLabel( 'Corporate Website' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $corpwww );

        $datejoin = $this->createElement( 'text', 'datejoin' );
        $datejoin->addValidator( 'stringLength', false, array( 10, 10, 'UTF-8' ) )
            ->addValidator( 'regex', false, array('/^\d\d\d\d-\d\d-\d\d/' ) )
            ->setRequired( false )
            ->setLabel( 'Date Joined' )
            ->setAttrib( 'placeholder', 'YYYY-MM-DD' )
            ->setAttrib( 'class', 'span4' )
            ->addFilter( 'StringTrim' )
            ->setAttrib( 'id', 'datejoin' );
        $this->addElement( $datejoin );

        $dateleave = $this->createElement( 'text', 'dateleave' );
        $dateleave->addValidator( 'stringLength', false, array( 10, 10, 'UTF-8' ) )
            ->addValidator( 'regex', false, array('/^\d\d\d\d-\d\d-\d\d/' ) )
            ->setRequired( false )
            ->setAttrib( 'placeholder', 'YYYY-MM-DD' )
            ->setAttrib( 'class', 'span4' )
            ->setLabel( 'Date Left' )
            ->addFilter( 'StringTrim' );
        $this->addElement( $dateleave );

        $status = $this->createElement( 'select', 'status' );
        $status->setMultiOptions( [ '' => '' ] + \Entities\Customer::$CUST_STATUS_TEXT )
            ->setRegisterInArrayValidator( true )
            ->setLabel( 'Status' )
            ->setRequired( true )
            ->setAttrib( 'class', 'chzn-select span8' )
            ->setErrorMessages( array( 'Please set the customer\'s status' ) );
        $this->addElement( $status );

        $MD5Support = $this->createElement( 'select', 'MD5Support' );
        $MD5Support->setMultiOptions( \Entities\Customer::$MD5_SUPPORT )
            ->setRegisterInArrayValidator( true )
            ->setLabel( 'MD5 Support' )
            ->setRequired( false )
            ->setAttrib( 'class', 'chzn-select span8' );
        $this->addElement( $MD5Support );

        $abbreviatedName = $this->createElement( 'text', 'abbreviatedName' );
        $abbreviatedName->setRequired( false )
            ->setAttrib( 'class', 'span10' )
            ->setLabel( 'Abbreviated Name' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $abbreviatedName );


        $autsys = $this->createElement( 'text', 'autsys' );
        $autsys->addValidator('int')
            ->addValidator( 'greaterThan', false, array( -1 ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span6' )
            ->setLabel( 'AS Number' );
        $this->addElement( $autsys  );

        $maxprefixes = $this->createElement( 'text', 'maxprefixes' );
        $maxprefixes->addValidator('int')
            ->addValidator( 'greaterThan', false, array( -1 ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span6' )
            ->setLabel( 'Max Prefixes' );
        $this->addElement( $maxprefixes  );

        $peeringemail = $this->createElement( 'text', 'peeringemail' );
        $peeringemail->addValidator('emailAddress' )
            ->addValidator( 'stringLength', false, array( 0, 64, 'UTF-8' ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span8' )
            ->setAttrib( 'placeholder', 'peering@example.com' )
            ->setLabel( 'Email' );
        $this->addElement( $peeringemail );

        $peeringpolicy = $this->createElement( 'select', 'peeringpolicy' );
        $peeringpolicy->setMultiOptions( [ '' => '' ] + \Entities\Customer::$PEERING_POLICIES )
            ->setRegisterInArrayValidator( true )
            ->setLabel( 'Peering Policy' )
            ->setRequired( false )
            ->setAttrib( 'class', 'chzn-select span8' );

        $this->addElement( $peeringpolicy );


        $peeringmacro = $this->createElement( 'text', 'peeringmacro' );
        $peeringmacro->addValidator( 'stringLength', false, array( 0, 255, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'IPv4 Peering Macro' )
            ->addFilter( 'StringTrim' )
            ->setAttrib( 'class', 'span8' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $peeringmacro );

        $peeringmacrov6 = $this->createElement( 'text', 'peeringmacrov6' );
        $peeringmacrov6->addValidator( 'stringLength', false, array( 0, 255, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'IPv6 Peering Macro' )
            ->addFilter( 'StringTrim' )
            ->setAttrib( 'class', 'span8' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $peeringmacrov6 );


        $this->addElement( IXP_Form_IrrdbConfig::getPopulatedSelect() );


        $activepeeringmatrix = $this->createElement( 'checkbox', 'activepeeringmatrix' );
        $activepeeringmatrix->setLabel( 'Active Peering Matrix' )
            ->setCheckedValue( '1' )
            ->setChecked( true );
        $this->addElement( $activepeeringmatrix );


        $this->addDisplayGroup(
            [ 'autsys', 'maxprefixes', 'peeringemail', 'peeringmacro', 'peeringmacrov6', 'peeringpolicy', 'irrdb', 'activepeeringmatrix' ],
            'peeringDisplayGroup'
        );
        $this->getDisplayGroup( 'peeringDisplayGroup' )->setLegend( 'Peering Details' );

        $nocphone = $this->createElement( 'text', 'nocphone' );
        $nocphone->addValidator( 'stringLength', false, array( 0, 255, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'Phone' )
            ->setAttrib( 'placeholder', '+353 1 123 4567' )
            ->setAttrib( 'class', 'span8' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $nocphone );

        $noc24hphone = $this->createElement( 'text', 'noc24hphone' );
        $noc24hphone->addValidator( 'stringLength', false, array( 0, 255, 'UTF-8' ) )
            ->setRequired( false )
            ->setAttrib( 'placeholder', '+353 86 876 5432' )
            ->setAttrib( 'class', 'span8' )
            ->setLabel( '24h Phone' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $noc24hphone );

        $nocfax = $this->createElement( 'text', 'nocfax' );
        $nocfax->addValidator( 'stringLength', false, array( 0, 40, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'Fax' )
            ->setAttrib( 'placeholder', '+353 1 765 4321' )
            ->setAttrib( 'class', 'span8' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $nocfax );

        $nocemail = $this->createElement( 'text', 'nocemail' );
        $nocemail->addValidator('emailAddress' )
            ->addValidator( 'stringLength', false, array( 0, 40, 'UTF-8' ) )
            ->setRequired( false )
            ->setAttrib( 'class', 'span8' )
            ->setAttrib( 'placeholder', 'noc@example.com' )
            ->setLabel( 'E-Mail' );
        $this->addElement( $nocemail );

        $nochours = $this->createElement( 'select', 'nochours' );
        $nochours->setMultiOptions( [ '0' => '' ] + \Entities\Customer::$NOC_HOURS )
            ->setRegisterInArrayValidator( true )
            ->setLabel( 'Hours' )
            ->setRequired( false )
            ->setAttrib( 'class', 'chzn-select span8' );
        $this->addElement( $nochours );


        $nocwww = $this->createElement( 'text', 'nocwww' );
        $nocwww->addValidator( 'stringLength', false, array( 0, 255, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'Website' )
            ->setAttrib( 'placeholder', 'http://www.noc.example.com/' )
            ->setAttrib( 'class', 'span10' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $nocwww );

        $this->addDisplayGroup(
            array( 'nocphone', 'noc24hphone', 'nocfax', 'nocemail', 'nochours', 'nocwww' ),
            'nocDisplayGroup'
        );
        $this->getDisplayGroup( 'nocDisplayGroup' )->setLegend( 'NOC Details' );

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
            ->setAttrib( 'class', 'span2 chzn-select' )
            ->addValidator( 'between', false, array( 1, $maxId ) )
            ->setErrorMessages( array( _( 'Please select a customer' ) ) );

        return $cust;
    }

    /**
     * Enables reseller form elements in customer form
     *
     * @param bool $modeEnabled Status of reseller mode enabled or not.
     * @return IXP_Form_Customer
     */
    public function enableResller( $modeEnabled )
    {
        if( !$modeEnabled )
            return $this;

        $isReseller = $this->createElement( 'checkbox', 'isReseller' );
        $isReseller->setLabel( 'Is a Reseller' )
            ->setCheckedValue( '1' );
        $this->addElement( $isReseller );

        $isResold = $this->createElement( 'checkbox', 'isResold' );
        $isResold->setLabel( 'Resold Customer' )
            ->setCheckedValue( '1' );
        $this->addElement( $isResold );

        $reseller = $this->createElement( 'select', 'reseller' );
        $reseller->setMultiOptions( [ '0' => '' ] + Zend_Registry::get( 'd2em' )['default']->getRepository( '\\Entities\\Customer' )->getResellerNames() )
            ->setRegisterInArrayValidator( true )
            ->setLabel( 'Reseller' )
            ->setRequired( false )
            ->setAttrib( 'chzn-fix-width', '1' )
            ->setAttrib( 'class', 'chzn-select' );
        $this->addElement( $reseller );

        return $this;
    }

    /**
     * Sets IXP form element to drop down or hidden depends on
     * multi IXP is enabled or not.
     *
     * @param bool $multiIXP Flag if multi ixp mode enabled
     * @return IXP_Form_Infrastructure
     */
    public function setMultiIXP( $multiIXP, $isEdit )
    {
        if( !$multiIXP )
        {
            $ixp = $this->createElement( 'hidden', 'ixp' );
            $ixp->setValue( '1' );
            $this->addElement( $ixp  );
        }
        else if( !$isEdit )
        {
            $ixp = IXP_Form_IXP::getPopulatedSelect( 'ixp' );
            $ixp->setLabel( "Intial IXP" );
            $this->addElement( $ixp  );
        }

        return $this;
    }

}
