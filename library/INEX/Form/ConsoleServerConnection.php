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
class INEX_Form_ConsoleServerConnection extends INEX_Form
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

        $description = $this->createElement( 'text', 'description' );
        $description->addValidator( 'stringLength', false, array( 1, 255 ) )
        ->setRequired( true )
        ->setLabel( 'Description' )
        ->addFilter( 'StringTrim' )
        ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $description );






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









        $dbSwitches = Doctrine_Query::create()
        ->from( 'SwitchTable' )
        ->execute();

        $switches = array( '0' => '' );
        $maxId = 0;

        foreach( $dbSwitches as $c )
        {
            $switches[ $c['id'] ] = "{$c['name']}";
            if( $c['id'] > $maxId ) $maxId = $c['id'];
        }

        $switch = $this->createElement( 'select', 'switchid' );
        $switch->setMultiOptions( $switches )
        ->setRegisterInArrayValidator( true )
        ->setRequired( true )
        ->setLabel( 'Console Server' )
        ->addValidator( 'between', false, array( 1, $maxId ) )
        ->setErrorMessages( array( 'Please select a console server' ) );

        $this->addElement( $switch );




        $port = $this->createElement( 'text', 'port' );
        $port->addValidator( 'stringLength', false, array( 1, 255 ) )
        ->setRequired( true )
        ->setLabel( 'Port' )
        ->addFilter( 'StringTrim' )
        ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $port );







        $speed = $this->createElement( 'text', 'speed' );
        $speed->addValidator( 'int' )
        ->setLabel( 'Speed' )
        ->addFilter( 'StringTrim' )
        ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $speed );




        $parity = $this->createElement( 'text', 'parity' );
        $parity->addValidator( 'int' )
        ->setLabel( 'Parity' )
        ->addFilter( 'StringTrim' )
        ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $parity );




        $stopbits = $this->createElement( 'text', 'stopbits' );
        $stopbits->addValidator( 'int' )
        ->setLabel( 'Stop Bits' )
        ->addFilter( 'StringTrim' )
        ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $stopbits );




        $flowcontrol = $this->createElement( 'text', 'flowcontrol' );
        $flowcontrol->addValidator( 'int' )
        ->setLabel( 'Flow Control' )
        ->addFilter( 'StringTrim' )
        ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $flowcontrol );




        $autobaud = $this->createElement( 'text', 'autobaud' );
        $autobaud->addValidator( 'int' )
        ->setLabel( 'Autobaud' )
        ->addFilter( 'StringTrim' )
        ->addFilter( new INEX_Filter_StripSlashes() );

        $this->addElement( $autobaud );





        $notes = $this->createElement( 'textarea', 'notes' );
        $notes->setLabel( 'Notes' )
        ->setRequired( false )
        ->addFilter( new INEX_Filter_StripSlashes() )
        ->setAttrib( 'cols', 60 )
        ->setAttrib( 'rows', 5 );
        $this->addElement( $notes );


        $this->addElement( 'button', 'cancel', array( 'label' => 'Cancel', 'onClick' => "parent.location='"
        . $cancelLocation . "'" ) );
        $this->addElement( 'submit', 'commit', array( 'label' => 'Add' ) );

    }

}

?>