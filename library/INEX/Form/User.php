<?php

/**
 * Copyright (C) 2009-2012 Internet Neutral Exchange Association Limited.
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
 * Form for adding / editing users
 *
 * http://www.inex.ie/
 * (c) Internet Neutral Exchange Association Ltd
 */

/**
 *
 * @package INEX_Form
 */
class INEX_Form_User extends INEX_Form
{
    public function init()
    {
        ////////////////////////////////////////////////
        // Create and configure elements
        ////////////////////////////////////////////////

        // let's capture the user's name and add them to the contact table also
        $name = $this->createElement( 'text', 'name' );
        $name->addValidator( 'stringLength', false, array( 2, 64 ) )
            ->setRequired( true )
            ->setAttrib( 'size', 50 )
            ->setAttrib( 'class', 'span3' )
            ->setLabel( 'Name' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $name );
        
        
        
        
        $username = OSS_Form_Auth::createUsernameElement();
        
        $username->addValidator( 'stringLength', false, array( 2, 30 ) )
            ->addValidator( 'regex', true, array( '/^[a-zA-Z0-9\-_\.]+$/' ) );

        $this->addElement( $username );

        
        
        
        $this->addElement( OSS_Form_Auth::createPasswordElement() );

        
        
        
        $privileges = $this->createElement( 'select', 'privs' );
        $privileges->setMultiOptions( \Entities\User::$PRIVILEGES_TEXT )
            ->setRegisterInArrayValidator( true )
            ->setLabel( 'Privileges' )
            ->setAttrib( 'class', 'span3 chzn-select' )
            ->setErrorMessages( array( 'Please select the users privilege level' ) );

        $this->addElement( $privileges );

        
        
        $this->addElement( OSS_Form_User::createEmailElement() );
        
        
        
        $this->addElement( self::createMobileElement( 'authorisedMobile' ) );

        
        
        $cust = $this->createElement( 'select', 'custid' );
        $maxId = $this->populateSelectFromDatabase( $cust, '\\Entities\\Customer', 'id', 'name', 'name', 'ASC' );

        $cust->setRegisterInArrayValidator( true )
            ->setRequired( true )
            ->setLabel( 'Customer' )
            ->setAttrib( 'class', 'span3 chzn-select' )
            ->addValidator( 'between', false, array( 1, $maxId ) )
            ->setErrorMessages( array( 'Please select a customer' ) );

        $this->addElement( $cust );


        $disabled = $this->createElement( 'checkbox', 'disabled' );
        $disabled->setLabel( 'Disabled?' )
            ->setCheckedValue( '1' );
        $this->addElement( $disabled );

        
        $this->addElement( self::createSubmitElement( 'submit', _( 'Add' ) ) );
        $this->addElement( $this->createCancelElement() );
        
    }

    
    public static function createMobileElement( $name = 'mobile' )
    {
        $mobile = new Zend_Form_Element_Text( $name );
        
        $mobile->addValidator( 'stringLength', false, array( 0, 30 ) )
               ->setRequired( false )
               ->setLabel( 'Mobile' )
               ->setAttrib( 'placeholder', '+353 86 123 4567' )
               ->addFilter( 'StringTrim' )
               ->addFilter( new OSS_Filter_StripSlashes() );
    
        return $mobile;
    }
}

