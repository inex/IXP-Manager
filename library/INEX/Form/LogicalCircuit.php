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
class INEX_Form_LogicalCircuit extends INEX_Form
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

        $this->setDecorators(
            array(
                array( 'ViewScript', array( 'viewScript' => 'logical-circuit/form/add-edit.tpl' ) )
            )
        );



        $our_ref = $this->createElement( 'text', 'our_ref' );
        $our_ref->addValidator( 'stringLength', false, array( 1, 255 ) )
            ->setRequired( true )
            ->setLabel( 'Our Reference' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() )
            ->addErrorMessage( 'Our Reference is required (and must be a string and less than 255 characters)' );

        $this->addElement( $our_ref );



        $installed = $this->createElement( 'text', 'installed' );
        $installed->addValidator( 'stringLength', false, array( 10, 10 ) )
                  ->addValidator( 'regex', false, array('/^\d\d\d\d-\d\d-\d\d/' ) )
                  ->setRequired( true )
                  ->setLabel( 'Installed (YYYY-MM-DD)' )
                  ->addFilter( 'StringTrim' )
                  ->addErrorMessage( 'An installed data is required (and must be of the form YYYY-MM-DD)' );

        $this->addElement( $installed );

        $removed = $this->createElement( 'text', 'removed' );
        $removed->addValidator( 'stringLength', false, array( 10, 10 ) )
                ->addValidator( 'regex', false, array('/^\d\d\d\d-\d\d-\d\d/' ) )
                ->setRequired( false )
                ->setLabel( 'Removed (YYYY-MM-DD)' )
                ->addFilter( 'StringTrim' );

        $this->addElement( $removed );



        $cust = $this->createElement( 'select', 'custid' );

        $maxId = $this->createSelectFromDatabaseTable( $cust, 'Cust', 'id', 'name', 'name' );

        $cust->setRegisterInArrayValidator( true )
            ->setRequired( true )
            ->setLabel( 'Customer' )
            ->addValidator( 'between', false, array( 1, $maxId ) )
            ->setErrorMessages( array( 'Please select a customer' ) );

        $this->addElement( $cust );


        $notes = $this->createElement( 'textarea', 'notes' );
        $notes->setLabel( 'Notes' )
              ->setRequired( false )
              ->addFilter( new INEX_Filter_StripSlashes() )
              ->setAttrib( 'cols', 60 )
              ->setAttrib( 'rows', 5 );

        $this->addElement( $notes );


        $this->addElement( 'button', 'cancel', array( 'label' => 'Cancel', 'onClick' => "parent.location='" . Zend_Controller_Front::getInstance()->getBaseUrl() . "/logical-circuit/list'" ) );

        $this->addElement( 'submit', 'commit', array( 'label' => 'Add New Circuit' ) );

        $this->setElementDecorators(
            array(
                'ViewHelper'
            )
        );

        $this->setSubFormDecorators(
            array( 'FormElements' )
        );

    }


}

?>