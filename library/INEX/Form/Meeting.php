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


/*
 *
 *
 * http://www.inex.ie/
 * (c) Internet Neutral Exchange Association Ltd
 */

/**
 *
 * @package INEX_Form
 */
class INEX_Form_Meeting extends INEX_Form
{
    /**
     *
     *
     */
    public function __construct( $options = null, $isEdit = false )
    {
        parent::__construct( $options );

        $this->setAttrib( 'accept-charset', 'UTF-8' );
        $this->setAttrib( 'class', 'form' );

        ////////////////////////////////////////////////
        // Create and configure title element
        ////////////////////////////////////////////////

        $title = $this->createElement( 'text', 'title', array( 'size' => '100' ) );
        $title->addValidator( 'stringLength', false, array( 1, 255 ) )
            ->setRequired( true )
            ->setLabel( 'Title' )
            ->setValue( 'Members\' Meeting' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $title );

        $before_text = $this->createElement( 'textarea', 'before_text' );
        $before_text->setLabel( 'Preamble' )
            ->setRequired( false )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() )
            ->setAttrib( 'cols', 120 )
            ->setAttrib( 'class', 'span9' )
            ->setAttrib( 'rows', 10 );
        $this->addElement( $before_text );

        $after_text = $this->createElement( 'textarea', 'after_text' );
        $after_text->setLabel( 'Postamble' )
            ->setRequired( false )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() )
            ->setAttrib( 'cols', 120 )
            ->setAttrib( 'class', 'span9' )
            ->setAttrib( 'rows', 10 );
        $this->addElement( $after_text );

        $date = $this->createElement( 'text', 'date' );
        $date->addValidator( 'stringLength', false, array( 10, 10 ) )
            ->addValidator( 'regex', false, array('/^\d\d\d\d-\d\d-\d\d/' ) )
            ->setRequired( true )
            ->setLabel( 'Date' )
            ->setValue( 'YYYY-MM-DD' )
            ->addFilter( 'StringTrim' );
        $this->addElement( $date );

        $time = $this->createElement( 'text', 'time' );
        $time->addValidator( 'stringLength', false, array( 5, 8 ) )
            ->addValidator( 'regex', false, array('/^\d\d:\d\d(:\d\d){0,1}/' ) )
            ->setRequired( true )
            ->setValue( 'HH:MM' )
            ->setLabel( 'Time' )
            ->addFilter( 'StringTrim' );
        $this->addElement( $time );

        $venue = $this->createElement( 'text', 'venue', array( 'size' => '100' ) );
        $venue->addValidator( 'stringLength', false, array( 1, 255 ) )
            ->setRequired( true )
            ->setLabel( 'Venue' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $venue );

        $venue_url = $this->createElement( 'text', 'venue_url', array( 'size' => '100' ) );
        $venue_url->addValidator( 'stringLength', false, array( 0, 255 ) )
            ->setRequired( false )
            ->setLabel( 'Venue URL' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() )
            ->setValue( '' );

        $this->addElement( $venue_url );


        $this->addElement( 'button', 'cancel', array( 'label' => 'Cancel', 'onClick' => "parent.location='" . Zend_Controller_Front::getInstance()->getBaseUrl() . "/customer/list'" ) );

        $this->addElement( 'submit', 'commit', array( 'label' => 'Add' ) );

    }

}

?>