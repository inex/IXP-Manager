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
class INEX_Form_Contact extends INEX_Form
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
            ->setAttrib( 'class', 'chzn-select' )
            ->setLabel( 'Customer' )
            ->addValidator( 'between', false, array( 1, $maxId ) )
            ->setErrorMessages( array( 'Please select a customer' ) );

        $this->addElement( $cust );



        
        $email = $this->createElement( 'text', 'email' );
        $email->addValidator( 'stringLength', false, array( 1, 64 ) )
            ->addValidator( 'emailAddress' )
            ->setLabel( 'E-mail' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $email );





        $phone = $this->createElement( 'text', 'phone' );
        $phone->addValidator( 'stringLength', false, array( 1, 32 ) )
            ->setLabel( 'Phone' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $phone );




        $mobile = $this->createElement( 'text', 'mobile' );
        $mobile->addValidator( 'stringLength', false, array( 1, 32 ) )
            ->setLabel( 'Mobile' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $mobile );





        $facilityaccess = $this->createElement( 'checkbox', 'facilityaccess' );
        $facilityaccess->setLabel( 'Facility Access' )
            ->setCheckedValue( '1' );
        $this->addElement( $facilityaccess );




        $mayauthorize = $this->createElement( 'checkbox', 'mayauthorize' );
        $mayauthorize->setLabel( 'May Authorize' )
            ->setCheckedValue( '1' );
        $this->addElement( $mayauthorize );




        $this->addElement( 'button', 'cancel', array( 'label' => 'Cancel', 'onClick' => "parent.location='"
            . $cancelLocation . "'" ) );
        
        $this->addElement( 'submit', 'commit', array( 'label' => $isEdit ? 'Save' : 'Add' ) );

    }

}

