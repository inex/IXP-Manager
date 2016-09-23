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
 * Form: adding / editing meetings
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Form
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXP_Form_Meeting extends IXP_Form
{
    public function init()
    {

        $title = $this->createElement( 'text', 'title', array( 'size' => '100' ) );
        $title->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setRequired( true )
            ->setLabel( 'Title' )
            ->setValue( 'Members\' Meeting' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $title );

        $before_text = $this->createElement( 'textarea', 'before_text' );
        $before_text->setLabel( 'Preamble' )
            ->setRequired( false )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() )
            ->setAttrib( 'cols', 120 )
            ->setAttrib( 'class', 'span9' )
            ->setAttrib( 'rows', 10 );
        $this->addElement( $before_text );

        $after_text = $this->createElement( 'textarea', 'after_text' );
        $after_text->setLabel( 'Postamble' )
            ->setRequired( false )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() )
            ->setAttrib( 'cols', 120 )
            ->setAttrib( 'class', 'span9' )
            ->setAttrib( 'rows', 10 );
        $this->addElement( $after_text );

        $date = $this->createElement( 'text', 'date' );
        $date->addValidator( 'stringLength', false, array( 10, 10, 'UTF-8' ) )
            ->addValidator( 'regex', false, array('/^\d\d\d\d-\d\d-\d\d/' ) )
            ->setRequired( true )
            ->setLabel( 'Date' )
            ->setValue( 'YYYY-MM-DD' )
            ->addFilter( 'StringTrim' );
        $this->addElement( $date );

        $time = $this->createElement( 'text', 'time' );
        $time->addValidator( 'stringLength', false, array( 5, 8, 'UTF-8' ) )
            ->addValidator( 'regex', false, array('/^\d\d:\d\d(:\d\d){0,1}/' ) )
            ->setRequired( true )
            ->setValue( 'HH:MM' )
            ->setLabel( 'Time' )
            ->addFilter( 'StringTrim' );
        $this->addElement( $time );

        $venue = $this->createElement( 'text', 'venue', array( 'size' => '100' ) );
        $venue->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setRequired( true )
            ->setLabel( 'Venue' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );

        $this->addElement( $venue );

        $venue_url = $this->createElement( 'text', 'venue_url', array( 'size' => '100' ) );
        $venue_url->addValidator( 'stringLength', false, array( 0, 255, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'Venue URL' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() )
            ->setValue( '' );

        $this->addElement( $venue_url );


        $this->addElement( self::createSubmitElement( 'submit', _( 'Add' ) ) );
        $this->addElement( $this->createCancelElement() );
    }
    
    /**
     * Create a SELECT / dropdown element of all meetings indexed by their id.
     *
     * @param string $name The element name
     * @return Zend_Form_Element_Select The select element
     */
    public static function getPopulatedSelect( $name = 'meeting_id' )
    {
        $e = new Zend_Form_Element_Select( $name );
    
        $maxId = self::populateSelectFromDatabase( $e, '\\Entities\\Meeting', 'id', [ 'title' => [ 'type' => 'STRING' ], 'date' => [ 'type' => 'DATE', 'format' => 'Y-m-d' ] ], 'title', 'ASC' );
    
        $e->setRegisterInArrayValidator( true )
            ->setRequired( true )
            ->setLabel( _( 'Meeting' ) )
            ->setAttrib( 'class', 'span3 chzn-select' )
            ->addValidator( 'between', false, array( 1, $maxId ) )
            ->setErrorMessages( array( _( 'Please select a meeting' ) ) );
    
        return $e;
    }
    
}
