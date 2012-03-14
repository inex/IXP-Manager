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
class INEX_Form_User extends INEX_Form
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

        $username = $this->createElement( 'text', 'username' );
        $username->addValidator( 'stringLength', false, array( 2, 30 ) )
            ->setRequired( true )
            ->setAttrib( 'size', 30 )
            ->setLabel( 'Username' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() )
            ->addFilter( 'StringToLower' )
            ->addValidator( 'stringLength', false, array( 6, 30 ) )
            ->addValidator( 'regex', true, array( '/^[a-zA-Z0-9]+$/' ) );

        $this->addElement( $username );

        $password = $this->createElement( 'text', 'password' );
        $password->addValidator( 'stringLength', false, array( 8, 30 ) )
            ->addValidator( 'regex', true, array( '/^[a-zA-Z0-9\!\£\$\%\^\&\*\(\)\-\=\_\+\{\}\[\]\;\'\#\:\@\~\,\.\/\<\>\?\|]+$/' ) )
            ->setRequired( true )
            ->setLabel( 'Password' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() )
            ->setErrorMessages( array( 'The password must between 8 and 30 characters and cannot contain a double quote - " - character.' ) );

        $this->addElement( $password );


        $privileges = $this->createElement( 'select', 'privs' );
        $privileges->setMultiOptions( User::$PRIVILEGES )
            ->setRegisterInArrayValidator( true )
            ->setLabel( 'Privileges' )
            ->setErrorMessages( array( 'Please select the users privilege level' ) );

        $this->addElement( $privileges );


        $email = $this->createElement( 'text', 'email' );
        $email->addValidator( 'stringLength', false, array( 1, 255 ) )
              ->addValidator( 'emailAddress', false, array( 'mx' => true, 'deep' => true, 'domain' => true ) )
              ->setRequired( true )
              ->setLabel( 'E-mail' )
              ->addFilter( 'StringTrim' )
              ->addFilter( 'StringToLower' )
              ->setAttrib( 'size', 60 )
              ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $email );

        $mobile = $this->createElement( 'text', 'authorisedMobile' );
        $mobile->addValidator( 'stringLength', false, array( 1, 30 ) )
               ->addValidator( 'regex', true, array( '/^[1-9]+[0-9]*$/' ) )
               ->setRequired( true )
               ->setLabel( 'Authorised Mobile (in the format: 353861234567)' )
               ->addFilter( 'StringTrim' )
               ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $mobile );



        $dbCusts = Doctrine_Query::create()
            ->from( 'Cust c' )
            ->orderBy( 'c.name ASC' )
            ->execute();

        $custs = array( '0' => '' );
        $maxId = 0;

        foreach( $dbCusts as $c )
        {
            $custs[ $c['id'] ] = "{$c['name']}";
            if( $c['id'] > $maxId ) $maxId = $c['id'];
        }

        $cust = $this->createElement( 'select', 'custid' );
        $cust->setMultiOptions( $custs );
        $cust->setRegisterInArrayValidator( true )
            ->setRequired( true )
            ->setLabel( 'Customer' )
            ->addValidator( 'between', false, array( 1, $maxId ) )
            ->setErrorMessages( array( 'Please select a customer' ) );

        $this->addElement( $cust );





        $disabled = $this->createElement( 'checkbox', 'disabled' );
        $disabled->setLabel( 'Disabled?' )
            ->setCheckedValue( '1' );
        $this->addElement( $disabled );

        $commit = $this->createElement( 'hidden', 'commit' );
        $commit->setValue( '1' );
        $this->addElement( $commit );

        $cancel = $this->createElement( 'button', 'cancel' );
        $cancel->setLabel( 'Cancel' )
               ->setAttrib( 'onClick', "parent.location='{$cancelLocation}'" );
        $this->addElement( $cancel );

        $submit = $this->createElement( 'submit', 'submit' );
        $submit->setLabel( 'Add' );
        $this->addElement( $submit );
    }

}

?>