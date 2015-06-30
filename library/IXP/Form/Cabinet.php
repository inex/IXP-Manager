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


/**
 * Form: adding / editing cabinets
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Form
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXP_Form_Cabinet extends IXP_Form
{
    public function init()
    {
        $name = $this->createElement( 'text', 'name' );
        $name->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setRequired( true )
            ->setAttrib( 'class', 'span3' )
            ->setLabel( 'Name' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $name );


        $this->addElement( IXP_Form_Location::getPopulatedSelect( 'locationid' ) );


        $cololocation = $this->createElement( 'text', 'cololocation' );
        $cololocation->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setAttrib( 'class', 'span3' )
            ->setRequired( true )
            ->setLabel( 'Colo Location' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $cololocation );

        
        $type = $this->createElement( 'text', 'type' );
        $type->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setAttrib( 'class', 'span3' )
            ->setRequired( false )
            ->setLabel( 'Type' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $type );

        $height = $this->createElement( 'text', 'height' );
        $height->addValidator( 'between', false, array( 0, 100 ) )
            ->setAttrib( 'class', 'span3' )
            ->setLabel( 'Height (U)' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $height );

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
     * Create a SELECT / dropdown element of all cabinet names indexed by their id.
     *
     * @param string $name The element name
     * @return Zend_Form_Element_Select The select element
     */
    public static function getPopulatedSelect( $name = 'cabinetid' )
    {
        $cab = new Zend_Form_Element_Select( $name );
        
        $maxId = self::populateSelectFromDatabase( $cab, '\\Entities\\Cabinet', 'id', 'name', 'name', 'ASC' );
        
        $cab->setRegisterInArrayValidator( true )
            ->setRequired( true )
            ->setLabel( _( 'Cabinet' ) )
            ->setAttrib( 'class', 'span2 chzn-select' )
            ->addValidator( 'between', false, array( 1, $maxId ) )
            ->setErrorMessages( array( _( 'Please select a cabinet' ) ) );
        
        return $cab;
    }
}

