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
class IXP_Form_Switch_AddBySNMP extends IXP_Form
{
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

        $switchtype = $this->createElement( 'select', 'switchtype' );
        $switchtype->setMultiOptions( \Entities\Switcher::$TYPES )
            ->setAttrib( 'class', 'span3 chzn-select' )
            ->setRegisterInArrayValidator( true )
            ->addValidator( 'greaterThan', true, array( 0 ) )
            ->setLabel( 'Type' )
            ->setErrorMessages( array( 'Please set the switch type' ) );
        $this->addElement( $switchtype );

        $this->addElement( IXP_Form_Cabinet::getPopulatedSelect( 'cabinetid' ) );

        $infrastructure = IXP_Form_Infrastructure::getPopulatedSelect( 'infrastructure' );
        $this->addElement( $infrastructure );

        $snmppasswd = $this->createElement( 'text', 'snmppasswd' );
        $snmppasswd->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setAttrib( 'class', 'span3' )
            ->setLabel( 'SNMP Community' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $snmppasswd );

        $active = $this->createElement( 'checkbox', 'active' );
        $active->setLabel( 'Active?' )
            ->setCheckedValue( '1' )
            ->setUncheckedValue( '0' )
            ->setValue( '1' );
        $this->addElement( $active );
        

        $this->addElement( self::createSubmitElement( 'submit', _( 'Next' ) ) );
        $this->addElement( $this->createCancelElement( 'cancel', OSS_Utils::genUrl( 'switch', 'list' ) ) );
        
        $manualAdd = new OSS_Form_Element_Buttonlink( 'manualAdd' );
        $manualAdd->setAttrib( 'href', OSS_Utils::genUrl( 'switch', 'add' ) )
            ->setAttrib( 'label', _( 'Manual / Non-SNMP Add' ) );
        $this->addElement( $manualAdd );
    }
    
}
