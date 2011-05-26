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
 * A form to allow a user to change his password
 *
 * @package INEX_Form
 */
class INEX_Form_ProfilePassword extends INEX_Form
{
    /**
     * Constructor for a form with a 'Password' and 'Confirm' field.
     *
     * @param array $options See Zend_From
     * @param bool $isEdit Not used but inherited
     * @param string $cancelLocation Not used but inherited
     */
    public function __construct( $options = null, $isEdit = false, $cancelLocation = '' )
    {
        parent::__construct( $options, $isEdit );

        ////////////////////////////////////////////////
        // Create and configure elements
        ////////////////////////////////////////////////

        $oldpassword = $this->createElement( 'password', 'oldpassword' );
        $oldpassword->addValidator( 'stringLength', false, array( 1, 30 ) )
            ->setRequired( true )
            ->setLabel( 'Current Password' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $oldpassword );

        $password1 = $this->createElement( 'password', 'password1' );
        $password1->addValidator( 'stringLength', false, array( 8, 30 ) )
            ->addValidator( 'regex', true, array( '/^[a-zA-Z0-9\!\£\$\%\^\&\*\(\)\-\=\_\+\{\}\[\]\;\'\#\:\@\~\,\.\/\<\>\?\|]+$/' ) )
            ->setRequired( true )
            ->setLabel( 'New Password' )
            ->setErrorMessages( array( 'The password must between 8 and 30 characters and cannot contain a double quote - " - character.' ) )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $password1 );

        $password2 = $this->createElement( 'password', 'password2' );
        $password2->addValidator( 'stringLength', false, array( 8, 30 ) )
            ->setRequired( true )
            ->setLabel( 'Confirm New Password' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $password2 );

        $submit = $this->createElement( 'submit', 'submit', array( 'label' => 'Change' ) );
        $this->addElement( $submit );


        $this->addDisplayGroup(
            array( 'oldpassword', 'password1', 'password2', 'submit' ),
            'passwordDisplayGroup'
        );

        $this->getDisplayGroup( 'passwordDisplayGroup' )->setLegend( 'Change Your Password' );

    }


}

?>