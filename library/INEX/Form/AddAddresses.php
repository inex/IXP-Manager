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
 * A form for editing switch ports.
 *
 * http://www.inex.ie/
 * (c) Internet Neutral Exchange Association Ltd
 */

/**
 * @package INEX_Form
 */
class INEX_Form_AddAddresses extends INEX_Form
{
    public function __construct( $options = null, $isEdit = false, $cancelLocation )
    {
        parent::__construct( $options, $isEdit );

        
        
        
        $this->setElementDecorators(
            array(
                'ViewHelper',
                'Errors',
                array( 'HtmlTag', array( 'tag' => 'dd' ) ),
                array( 'Label', array( 'tag' => 'dt' ) ),
            )
        );

        

        $vlanid = $this->createElement( 'select', 'vlanid' );

        $maxVlanId = $this->createSelectFromDatabaseTable( $vlanid, 'Vlan', 'id',
            array( 'name' ),
            'name'
        );

        $vlanid->setRegisterInArrayValidator( true )
            ->setRequired( true )
            ->setLabel( 'VLAN' )
            ->addValidator( 'between', false, array( 1, $maxVlanId ) )
            ->setErrorMessages( array( 'Please select a VLAN' ) );

        $this->addElement( $vlanid );
        

        $type = $this->createElement( 'select', 'type' );
        $type->setMultiOptions( array( 'IPv4' => 'IPv4', 'IPv6' => 'IPv6' ) )
            ->setRegisterInArrayValidator( true )
            ->setLabel( 'Address Family' );

        $this->addElement( $type );
        

        $this->addElement( 'button', 'cancel', array( 'label' => 'Cancel', 'onClick' => "parent.location='"
        . $cancelLocation . "'" ) );

        $this->addElement( 'submit', 'commit', array( 'label' => 'Add' ) );

        $this->setElementDecorators( array( 'ViewHelper' ) );
        
    }
    
    
}

?>