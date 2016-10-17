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
 * Form: adding / editing physical interfaces
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Form
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXP_Form_Interface_Physical extends IXP_Form
{

    public function init()
    {
        $this->setDecorators( [ [ 'ViewScript', [ 'viewScript' => 'physical-interface/forms/edit.phtml' ] ] ] );

        $switch = IXP_Form_Switch::getPopulatedSelect( 'switchid' )
                      ->setAttrib( 'class', 'chzn-select span8' );
        $this->addElement( $switch );

        $switchPorts = $this->createElement( 'select', 'switchportid' );
        $switchPorts->setRequired( true )
            ->setRegisterInArrayValidator( false )
            ->setLabel( 'Port' )
            ->setAttrib( 'class', 'chzn-select span8' )
            ->addValidator( 'greaterThan', false, array( 'min' => 0 ) )
            ->setErrorMessages( array( 'Please select a switch port' ) );
        $this->addElement( $switchPorts );

        $virtualInterface = $this->createElement( 'hidden', 'virtualinterfaceid' );
        $this->addElement( $virtualInterface );

        $status = $this->createElement( 'select', 'status' );
        $status->setMultiOptions( \Entities\PhysicalInterface::$STATES )
            ->setRegisterInArrayValidator( true )
            ->setAttrib( 'class', 'chzn-select span8' )
            ->setLabel( 'Status' )
            ->setErrorMessages( array( 'Please set the status' ) );
        $this->addElement( $status );


        $speed = $this->createElement( 'select', 'speed' );
        $speed->setMultiOptions( \Entities\PhysicalInterface::$SPEED )
            ->setRegisterInArrayValidator( true )
            ->setAttrib( 'class', 'chzn-select span8' )
            ->setLabel( 'Speed' )
            ->setErrorMessages( array( 'Please set the speed' ) );
        $this->addElement( $speed );


        $duplex = $this->createElement( 'select', 'duplex' );
        $duplex->setMultiOptions( \Entities\PhysicalInterface::$DUPLEX )
            ->setRegisterInArrayValidator( true )
            ->setAttrib( 'class', 'chzn-select span8' )
            ->setLabel( 'Duplex' )
            ->setErrorMessages( array( 'Please set the duplex' ) );
        $this->addElement( $duplex );


        $monitorindex = $this->createElement( 'text', 'monitorindex' );
        $monitorindex->addValidator( 'int' )
            ->setLabel( 'Monitor Index' )
            ->setAttrib( 'class', 'span8' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $monitorindex );


        $notes = $this->createElement( 'textarea', 'notes' );
        $notes->setLabel( 'Notes' )
            ->setRequired( false )
            ->addFilter( new OSS_Filter_StripSlashes() )
            ->setAttrib( 'cols', 60 )
            ->setAttrib( 'rows', 5 );
        $this->addElement( $notes );

        $this->addElement( self::createSubmitElement( 'submit', _( 'Add' ) ) );
        $this->addElement( $this->createCancelElement() );

        $preselectSwitchPort = $this->createElement( 'hidden', 'preselectSwitchPort' );
        $this->addElement( $preselectSwitchPort );

        $preselectPhysicalInterface = $this->createElement( 'hidden', 'preselectPhysicalInterface' );
        $this->addElement( $preselectPhysicalInterface );
    }

    /**
     * Enables Fanout ports form elements in customer form
     *
     * @param bool $modeEnabled Status of reseller mode enabled or not.
     * @return IXP_Form_Customer
     */
    public function enableFanoutPort( $modeEnabled )
    {
        if( !$modeEnabled )
            return $this;

        $fanout = $this->createElement( 'checkbox', 'fanout' );
        $fanout->setLabel( 'Associate a fanout port' )
            ->setCheckedValue( '1' );
        $this->addElement( $fanout );

        $switcher = IXP_Form_Switch::getPopulatedSelect( 'fn_switchid' );
        $switcher->setRequired( false )
            ->setAttrib( 'class', 'chzn-select' )
            ->setAttrib( 'chzn-fix-width', '1' )
            ->removeValidator( 'between' );
        $this->addElement( $switcher );

        $switchPorts = $this->createElement( 'select', 'fn_switchportid' );
        $switchPorts->setRequired( false )
            ->setRegisterInArrayValidator( false )
            ->setLabel( 'Port' )
            ->setAttrib( 'class', 'chzn-select' )
            ->setAttrib( 'chzn-fix-width', '1' )
            ->addValidator( 'greaterThan', false, array( 'min' => 1 ) )
            ->setErrorMessages( array( 'Please select a switch port' ) );
        $this->addElement( $switchPorts );

        $monitorindex = $this->createElement( 'text', 'fn_monitorindex' );
        $monitorindex->addValidator( 'int' )
            ->setLabel( 'Monitor Index' )
            ->setAttrib( 'class', 'span3' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $monitorindex );

        $preselectSwitchPort = $this->createElement( 'hidden', 'fn_preselectSwitchPort' );
        $this->addElement( $preselectSwitchPort );

        $preselectPhysicalInterface = $this->createElement( 'hidden', 'fn_preselectPhysicalInterface' );
        $this->addElement( $preselectPhysicalInterface );

        return $this;
    }
}
