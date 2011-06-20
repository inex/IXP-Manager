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
class INEX_Form_SwitchPort_AddPorts extends INEX_Form
{
    public function __construct( $options = null, $isEdit = false, $cancelLocation )
    {
        parent::__construct( $options, $isEdit );

        
        $this->setAttrib( 'accept-charset', 'UTF-8' );
        $this->setAttrib( 'class', 'form' );

        $this->setDecorators( 
            array( 
                array( 
                	'ViewScript', 
                    array( 
                    	'viewScript' => 'switch-port/add-ports-form.tpl' 
             	   ) 
         	   )     
     	   ) 
 	   );
        
        
        $this->setElementDecorators(
            array(
                'ViewHelper',
                'Errors',
                array( 'HtmlTag', array( 'tag' => 'dd' ) ),
                array( 'Label', array( 'tag' => 'dt' ) ),
            )
        );

        

        $switchid = $this->createElement( 'select', 'switchid' );

        $maxSwitchId = $this->createSelectFromDatabaseTable( $switchid, 'SwitchTable', 'id',
            array( 'name' ),
            'name'
        );

        $switchid->setRegisterInArrayValidator( true )
            ->setRequired( true )
            ->setLabel( 'Switch' )
            ->addValidator( 'between', false, array( 1, $maxSwitchId ) )
            ->setErrorMessages( array( 'Please select a switch' ) );

        $this->addElement( $switchid );
        

        $deftype = $this->createElement( 'select', 'deftype' );
        $deftype->setMultiOptions( Switchport::$TYPE_TEXT )
            ->setRegisterInArrayValidator( false )
            ->setLabel( 'Default Type' );

        $this->addElement( $deftype );
        

        $this->addElement( 'button', 'cancel', array( 'label' => 'Cancel', 'onClick' => "parent.location='"
        . $cancelLocation . "'" ) );

        $this->addElement( 'submit', 'commit', array( 'label' => 'Add' ) );

        $this->setElementDecorators( array( 'ViewHelper' ) );
        
    }
    
    
}

?>