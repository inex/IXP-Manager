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
class INEX_Form_Vendor extends INEX_Form
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



        $this->addElement( 'button', 'cancel', array( 'label' => 'Cancel', 'onClick' => "parent.location='"
        . $cancelLocation . "'" ) );
        $this->addElement( 'submit', 'commit', array( 'label' => 'Add' ) );

    }

    /**
     * Create a SELECT / dropdown element of all vendor names indexed by their id.
     *
     * @param string $name The element name
     * @return Zend_Form_Element_Select The select element
     */
    public static function getPopulatedSelect( $name = 'vendorid' )
    {
        $v = new Zend_Form_Element_Select( $name );
        
        $maxId = self::populateSelectFromDatabase( $v, '\\Entities\\Vendor', 'id', 'name', 'name', 'ASC' );
        
        $v->setRegisterInArrayValidator( true )
            ->setRequired( true )
            ->setLabel( _( 'Vendor' ) )
            ->setAttrib( 'class', 'span3 chzn-select' )
            ->addValidator( 'between', false, array( 1, $maxId ) )
            ->setErrorMessages( array( _( 'Please select a vendor' ) ) );
        
        return $v;
    }
}

