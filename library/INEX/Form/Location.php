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
class INEX_Form_Location extends INEX_Form
{
    /**
     *
     *
     */
    public function __construct( $options = null, $isEdit = false, $cancelLocation )
    {
        parent::__construct( $options, $isEdit );

        ////////////////////////////////////////////////
        // Create and configure elements
        ////////////////////////////////////////////////

        $name = $this->createElement( 'text', 'name' );
        $name->addValidator( 'stringLength', false, array( 1, 255 ) )
        ->setRequired( true )
        ->setLabel( 'Name' )
        ->addFilter( 'StringTrim' )
        ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $name );


        $shortname = $this->createElement( 'text', 'shortname' );
        $shortname->addValidator( 'stringLength', false, array( 1, 255 ) )
        ->setRequired( true )
        ->setLabel( 'Short Name' )
        ->addFilter( 'StringTrim' )
        ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $shortname );

        $address = $this->createElement( 'textarea', 'address' );
        $address->addValidator( 'stringLength', false, array( 1, 255 ) )
        ->setLabel( 'Address' )
        ->addFilter( new INEX_Filter_StripSlashes() )
        ->setAttrib( 'cols', 60 )
        ->setAttrib( 'rows', 5 );

        $this->addElement( $address );

        $nocphone = $this->createElement( 'text', 'nocphone' );
        $nocphone->addValidator( 'stringLength', false, array( 0, 255 ) )
        ->setRequired( false )
        ->setLabel( 'Phone' )
        ->addFilter( 'StringTrim' )
        ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $nocphone );

        $nocfax = $this->createElement( 'text', 'nocfax' );
        $nocfax->addValidator( 'stringLength', false, array( 0, 255 ) )
        ->setRequired( false )
        ->setLabel( 'Fax' )
        ->addFilter( 'StringTrim' )
        ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $nocfax );

        $nocemail = $this->createElement( 'text', 'nocemail' );
        $nocemail->addValidator('emailAddress' )
        ->addValidator( 'stringLength', false, array( 0, 255 ) )
        ->setRequired( false )
        ->setLabel( 'E-Mail' );
        $this->addElement( $nocemail );


        $this->addDisplayGroup(
        array( 'nocphone', 'nocfax', 'nocemail' ),
            'nocDisplayGroup'
            );
            $this->getDisplayGroup( 'nocDisplayGroup' )->setLegend( 'NOC Details' );



            $officephone = $this->createElement( 'text', 'officephone' );
            $officephone->addValidator( 'stringLength', false, array( 0, 255 ) )
            ->setRequired( false )
            ->setLabel( 'Phone' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
            $this->addElement( $officephone );

            $officefax = $this->createElement( 'text', 'officefax' );
            $officefax->addValidator( 'stringLength', false, array( 0, 255 ) )
            ->setRequired( false )
            ->setLabel( 'Fax' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
            $this->addElement( $officefax );

            $officeemail = $this->createElement( 'text', 'officeemail' );
            $officeemail->addValidator('emailAddress' )
            ->addValidator( 'stringLength', false, array( 0, 255 ) )
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
            ->addFilter( new INEX_Filter_StripSlashes() )
            ->setAttrib( 'cols', 60 )
            ->setAttrib( 'rows', 5 );
            $this->addElement( $notes );


            $this->addElement( 'button', 'cancel', array( 'label' => 'Cancel', 'onClick' => "parent.location='"
            . $cancelLocation . "'" ) );
            $this->addElement( 'submit', 'commit', array( 'label' => 'Add' ) );

    }


    public static function addYUICalanderDiv( $content, $element, array $options )
    {
        return "<div class=\"yui-skin-sam\"><div id=\"{$options['id']}\"></div></div>";
    }

}

?>