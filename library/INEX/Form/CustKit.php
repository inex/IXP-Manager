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
class INEX_Form_CustKit extends INEX_Form
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
        ->setLabel( 'Customer' )
        ->addValidator( 'between', false, array( 1, $maxId ) )
        ->setErrorMessages( array( 'Please select a customer' ) );

        $this->addElement( $cust );





        $dbCabinets = Doctrine_Query::create()
        ->from( 'Cabinet' )
        ->execute();

        $cabinets = array( '0' => '' );
        $maxId = 0;

        foreach( $dbCabinets as $c )
        {
            $cabinets[ $c['id'] ] = "{$c->Location->name} :: {$c['name']}";
            if( $c['id'] > $maxId ) $maxId = $c['id'];
        }

        $cabinet = $this->createElement( 'select', 'cabinetid' );
        $cabinet->setMultiOptions( $cabinets );
        $cabinet->setRegisterInArrayValidator( true )
        ->setLabel( 'Cabinet' )
        ->addValidator( 'between', false, array( 1, $maxId ) )
        ->setErrorMessages( array( 'Please select a cabinet' ) );

        $this->addElement( $cabinet );






        $descr = $this->createElement( 'textarea', 'descr' );
        $descr->setLabel( 'Description' )
        ->setRequired( false )
        ->addFilter( new INEX_Filter_StripSlashes() )
        ->setAttrib( 'cols', 60 )
        ->setAttrib( 'rows', 5 );
        $this->addElement( $descr );


        $this->addElement( 'button', 'cancel', array( 'label' => 'Cancel', 'onClick' => "parent.location='"
        . $cancelLocation . "'" ) );
        $this->addElement( 'submit', 'commit', array( 'label' => 'Add' ) );

    }

}

?>