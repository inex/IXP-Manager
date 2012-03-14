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
 * A form to allow a user to change their profile.
 *
 * @package INEX_Form
 */
class INEX_Form_Profile extends INEX_Form
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

        $username = $this->createElement( 'text', 'username',
            array(
            	'readonly' => 'readonly'
            )
        );
        $username->setLabel( 'Username' );

        $this->addElement( $username );

        $mobile = $this->createElement( 'text', 'mobile' );
        $mobile->addValidator( 'stringLength', false, array( 9, 30 ) )
            ->addValidator( 'digits', false )
            ->setRequired( true )
            ->setLabel( 'Mobile Number' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $mobile );

        $email = $this->createElement( 'text', 'email' );
        $email->addValidator( 'stringLength', false, array( 0, 255 ) )
            ->addValidator( 'emailAddress', false, array( 'domain' => true, 'mx' => true ) )
            ->setRequired( true )
            ->setLabel( 'E-Mail' )
            ->addFilter( 'StringTrim' )
            ->addFilter( 'StringToLower' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $email );

        $submit = $this->createElement( 'submit', 'submit', array( 'label' => 'Change' ) );
        $this->addElement( $submit );

    }


}

