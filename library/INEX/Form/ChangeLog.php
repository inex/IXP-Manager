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
class INEX_Form_ChangeLog extends INEX_Form
{
    /**
     *
     *
     */
    public function __construct( $options = null, $isEdit = false )
    {
        parent::__construct( $options );


        $this->setDecorators(
            array(
                array(
                    'ViewScript',
                    array(
                        'viewScript' => 'change-log/forms/edit.tpl'
                    )
                )
            )
        );
        
        
        ////////////////////////////////////////////////
        // Create and configure title element
        ////////////////////////////////////////////////

        $title = $this->createElement( 'text', 'title', array( 'size' => '100' ) );
        $title->addValidator( 'stringLength', false, array( 1, 255 ) )
            ->setRequired( true )
            ->setLabel( 'Summary' )
            ->setAttrib( 'placeholder', 'One line summary for display in a list of changes' )
            ->addFilter( 'StringTrim' )
            ->setAttrib( 'class', 'span12' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $title );

        $details = $this->createElement( 'textarea', 'details' );
        $details->setLabel( 'Details' )
            ->setRequired( false )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() )
            ->setAttrib( 'cols', 100 )
            ->setAttrib( 'rows', 10 );
        $this->addElement( $details );

        $visibility = $this->createElement( 'select', 'visibility' );
        $visibility->setMultiOptions(
            User::$PRIVILEGES
        );
        $visibility->setRegisterInArrayValidator( true )
            ->setLabel( 'Visibility' )
            ->setValue( User::AUTH_SUPERUSER )
            ->setErrorMessages( array( 'Please select the minimum privileges a user needs to see this change log' ) );

        $this->addElement( $visibility );


        $livedate = $this->createElement( 'text', 'livedate' );
        $livedate->addValidator( 'stringLength', false, array( 10, 10 ) )
            ->addValidator( 'regex', false, array('/^\d\d\d\d-\d\d-\d\d/' ) )
            ->setRequired( true )
            ->setAttrib( 'placeholder', 'YYYY-MM-DD' )
            ->setLabel( 'Live Date' )
            ->addFilter( 'StringTrim' );
        $this->addElement( $livedate );


        $this->addElement( 'button', 'cancel', array( 'label' => 'Cancel', 'onClick' => "parent.location='" . Zend_Controller_Front::getInstance()->getBaseUrl() . "/customer/list'" ) );

        $this->addElement( 'submit', 'commit', array( 'label' => 'Add' ) );

    }

}

?>