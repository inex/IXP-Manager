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
 * Form: adding / editing locations
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Form
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXP_Form_Location extends IXP_Form
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


    $shortname = $this->createElement( 'text', 'shortname' );
    $shortname->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
        ->setAttrib( 'class', 'span3' )
        ->setRequired( true )
        ->setLabel( 'Short Name' )
        ->addFilter( 'StringTrim' )
        ->addFilter( new OSS_Filter_StripSlashes() );
    $this->addElement( $shortname );

    $tag = $this->createElement( 'text', 'tag' );
    $tag->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
        ->setAttrib( 'class', 'span3' )
        ->setRequired( true )
        ->setLabel( 'Tag' )
        ->addFilter( 'StringTrim' )
        ->addFilter( new OSS_Filter_StripSlashes() );
    $this->addElement( $tag );

        $address = $this->createElement( 'textarea', 'address' );
        $address->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setLabel( 'Address' )
            ->addFilter( new OSS_Filter_StripSlashes() )
            ->setAttrib( 'cols', 60 )
            ->setAttrib( 'class', 'span3' )
            ->setAttrib( 'rows', 5 );
        $this->addElement( $address );

        $nocphone = $this->createElement( 'text', 'nocphone' );
        $nocphone->addValidator( 'stringLength', false, array( 0, 255, 'UTF-8' ) )
            ->setAttrib( 'class', 'span3' )
            ->setRequired( false )
            ->setLabel( 'Phone' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $nocphone );

        $nocfax = $this->createElement( 'text', 'nocfax' );
        $nocfax->addValidator( 'stringLength', false, array( 0, 255, 'UTF-8' ) )
            ->setAttrib( 'class', 'span3' )
            ->setRequired( false )
            ->setLabel( 'Fax' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $nocfax );

        $nocemail = $this->createElement( 'text', 'nocemail' );
        $nocemail->addValidator('emailAddress' )
            ->setAttrib( 'class', 'span3' )
            ->addValidator( 'stringLength', false, array( 0, 255, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'E-Mail' );
        $this->addElement( $nocemail );


        $this->addDisplayGroup(
            array( 'nocphone', 'nocfax', 'nocemail' ),
            'nocDisplayGroup'
        );
        $this->getDisplayGroup( 'nocDisplayGroup' )->setLegend( 'NOC Details' );

        $officephone = $this->createElement( 'text', 'officephone' );
        $officephone->addValidator( 'stringLength', false, array( 0, 255, 'UTF-8' ) )
            ->setAttrib( 'class', 'span3' )
            ->setRequired( false )
            ->setLabel( 'Phone' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $officephone );

        $officefax = $this->createElement( 'text', 'officefax' );
        $officefax->addValidator( 'stringLength', false, array( 0, 255, 'UTF-8' ) )
            ->setAttrib( 'class', 'span3' )
            ->setRequired( false )
            ->setLabel( 'Fax' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $officefax );

        $officeemail = $this->createElement( 'text', 'officeemail' );
        $officeemail->addValidator('emailAddress' )
            ->setAttrib( 'class', 'span3' )
            ->addValidator( 'stringLength', false, array( 0, 255, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'E-Mail' );
        $this->addElement( $officeemail );

        $this->addDisplayGroup(
            array( 'officephone', 'officefax', 'officeemail' ),
            'officeDisplayGroup'
        );
        $this->getDisplayGroup( 'officeDisplayGroup' )->setLegend( 'Office Details' );


        $notes = $this->createElement( 'textarea', 'notes' );
        $notes->setLabel( 'Notes' )
            ->setRequired( false )
            ->setAttrib( 'class', 'span3' )
            ->addFilter( new OSS_Filter_StripSlashes() )
            ->setAttrib( 'cols', 60 )
            ->setAttrib( 'rows', 5 );
        $this->addElement( $notes );

        $this->addElement( self::createSubmitElement( 'submit', _( 'Add' ) ) );
        $this->addElement( $this->createCancelElement() );
    }



    /**
     * Create a SELECT / dropdown element of all location names indexed by their id.
     *
     * @param string $name The element name
     * @return Zend_Form_Element_Select The select element
     */
    public static function getPopulatedSelect( $name = 'locationid' )
    {
        $loc = new Zend_Form_Element_Select( $name );

        $maxId = self::populateSelectFromDatabase( $loc, '\\Entities\\Location', 'id', 'name', 'name', 'ASC' );

        $loc->setRegisterInArrayValidator( true )
            ->setRequired( true )
            ->setLabel( _( 'Location' ) )
            ->setAttrib( 'class', 'span3 chzn-select' )
            ->addValidator( 'between', false, array( 1, $maxId ) )
            ->setErrorMessages( array( _( 'Please select a location' ) ) );

        return $loc;
    }
}
