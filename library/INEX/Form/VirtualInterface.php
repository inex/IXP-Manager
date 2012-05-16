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
class INEX_Form_VirtualInterface extends INEX_Form
{

    public function __construct( $options = null, $isEdit = false, $cancelLocation )
    {
        parent::__construct( $options, $isEdit );

        ////////////////////////////////////////////////
        // Create and configure elements
        ////////////////////////////////////////////////

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
            ->setAttrib( 'class', 'chzn-select' )
            ->addValidator( 'between', false, array( 1, $maxId ) )
            ->setErrorMessages( array( 'Please select a customer' ) );
        $this->addElement( $cust );

        $name = $this->createElement( 'text', 'name' );
        $name->addValidator( 'stringLength', false, array( 0, 255 ) )
            ->setRequired( false )
            ->setLabel( 'Virtual Interface Name' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $name );


        $descr = $this->createElement( 'text', 'description' );
        $descr->setLabel( 'Description' )
            ->addValidator( 'stringLength', false, array( 0, 255 ) )
            ->setRequired( false )
            ->addFilter( new INEX_Filter_StripSlashes() )
            ->addFilter( 'StringTrim' );
        $this->addElement( $descr );


        $channel = $this->createElement( 'text', 'channelgroup' );
        $channel->addValidator( 'int' )
            ->setLabel( 'Channel Group Number' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $channel );

        $mtu = $this->createElement( 'text', 'mtu' );
        $mtu->addValidator( 'int' )
            ->setLabel( 'MTU' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new INEX_Filter_StripSlashes() );
        $this->addElement( $mtu );


        $trunk = $this->createElement( 'checkbox', 'trunk' );
        $trunk->setLabel( 'Is 802.1q Trunk?' )
            ->setCheckedValue( '1' );
        $this->addElement( $trunk );


        $this->addDisplayGroup(
            array(
            	'custid', 'name', 'description', 'channelgroup', 'mtu', 'trunk'
            ),
            'virtualInterfaceDisplayGroup'
        );
            
        $this->getDisplayGroup( 'virtualInterfaceDisplayGroup' )->setLegend( 'Customer Connection Details' );

        
        $this->addElement( 'button', 'cancel',
            array(
            	'label' => 'Cancel',
            	'onClick' => "parent.location='" . $cancelLocation . "'"
            )
        );

        $this->addElement( 'submit', 'commit', array( 'label' => 'Add' ) );
    }

}

