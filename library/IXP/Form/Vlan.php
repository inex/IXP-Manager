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
 * Form: adding / editing VLANs
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Form
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXP_Form_VLAN extends IXP_Form
{
    public function init()
    {

        $name = $this->createElement( 'text', 'name' );
        $name->addValidator( 'stringLength', false, array( 1, 255 ) )
            ->setRequired( true )
            ->setLabel( 'Name' )
            ->setAttrib( 'class', 'span3' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $name );

        $number = $this->createElement( 'text', 'number' );
        $number->addValidator( 'int' )
            ->addValidator( 'between', false, array( 1, 4096 ) )
            ->setRequired( true )
            ->setLabel( 'Tag' )
            ->setAttrib( 'class', 'span3' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $number );

        $infrastructre = IXP_Form_Infrastructure::getPopulatedSelect( 'infrastructure' );
        $this->addElement( $infrastructre );

        $rcvrfname = $this->createElement( 'text', 'rcvrfname' );
        $rcvrfname->addValidator( 'stringLength', false, array( 1, 255 ) )
            ->setLabel( 'RC VRF Name' )
            ->addFilter( 'StringTrim' )
            ->setAttrib( 'class', 'span3' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $rcvrfname );

        $private = $this->createElement( 'checkbox', 'private' );
        $private->setLabel( 'Private VLAN between a subset of members' )
            ->setCheckedValue( '1' );
        $this->addElement( $private );
        
        $notes = $this->createElement( 'textarea', 'notes' );
        $notes->setLabel( 'Notes' )
            ->setRequired( false )
            ->addFilter( new OSS_Filter_StripSlashes() )
            ->setAttrib( 'cols', 60 )
            ->setAttrib( 'class', 'span3' )
            ->setAttrib( 'rows', 5 );
        $this->addElement( $notes );
        
        $this->addElement( self::createSubmitElement( 'submit', _( 'Add' ) ) );
        $this->addElement( $this->createCancelElement() );
    }

    /**
     * Create a SELECT / dropdown element of all VLAN names indexed by their id.
     *
     * @param string $name The element name
     * @return Zend_Form_Element_Select The select element
     */
    public static function getPopulatedSelect( $name = 'vlanid' )
    {
        $vlan = new Zend_Form_Element_Select( $name );
    
        $maxId = self::populateSelectFromDatabase( $vlan, '\\Entities\\Vlan', 'id', 'name', 'name', 'ASC' );
    
        $vlan->setRegisterInArrayValidator( true )
            ->setRequired( true )
            ->setLabel( _( 'VLAN' ) )
            ->setAttrib( 'class', 'span3 chzn-select' )
            ->addValidator( 'between', false, array( 1, $maxId ) )
            ->setErrorMessages( array( _( 'Please select a VLAN' ) ) );
    
        return $vlan;
    }
    
}
