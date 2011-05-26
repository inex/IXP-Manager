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
 * A form for editing switch ports.
 *
 * http://www.inex.ie/
 * (c) Internet Neutral Exchange Association Ltd
 */

/**
 * @package INEX_Form
 */
class INEX_Form_SwitchPort extends INEX_Form
{
    public function __construct( $options = null, $isEdit = false, $cancelLocation )
    {
        parent::__construct( $options, $isEdit );

        $name = $this->createElement( 'text', 'name',
            array(
            	'readonly' => 'readonly',
                'size' => '50'
            )
        );
        $name->setLabel( 'Name' );
        $this->addElement( $name );


        $type = $this->createElement( 'select', 'type' );
        $type->setMultiOptions( Switchport::$TYPE_TEXT )
        ->setRegisterInArrayValidator( true )
        ->addValidator( 'greaterThan', true, array( 0 ) )
        ->setLabel( 'Type' )
        ->setErrorMessages( array( 'Please set the port type' ) );

        $this->addElement( $type );

        $switchid = $this->createElement( 'hidden', 'switchid' );
        $this->addElement( $switchid );

        $this->addElement( 'button', 'cancel', array( 'label' => 'Cancel', 'onClick' => "parent.location='"
        . $cancelLocation . "'" ) );

        $this->addElement( 'submit', 'commit', array( 'label' => 'Add' ) );

    }

}

?>