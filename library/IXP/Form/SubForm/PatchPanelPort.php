<?php

/*
 * Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee.
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

/**
 *
 * @package IXP_Form
 * @subpackage SubForm
 *
 */
class IXP_Form_SubForm_PatchPanelPort extends IXP_Form_SubForm
{
    /**
     *
     *
     */
    public function __construct( $options = null, $isEdit = false, $cancelLocation )
    {
        parent::__construct( $options, $isEdit );

        $this->setDecorators(
            array(
                array( 'ViewScript', array( 'viewScript' => 'patch-panel/form/patch-panel-port.tpl' ) )
            )
        );

        ////////////////////////////////////////////////
        // Create and configure elements
        ////////////////////////////////////////////////

        $port = $this->createElement( 'hidden', 'port' );
        $this->addElement( $port );


        $side = $this->createElement( 'hidden', 'side' );
        $this->addElement( $side );

        $colo_ref = $this->createElement( 'text', 'colo_ref' );
        $colo_ref->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setAttrib('size', 40 )
            ->setAttrib('maxlength', 255)
            ->setRequired( true )
            ->setLabel( 'Colo Provider\'s Ref' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new IXP_Filter_StripSlashes() )
            ->setErrorMessages( array( 'Colocation reference is required' ) );

        $this->addElement( $colo_ref );

        $type = $this->createElement( 'select', 'type' );
        $type->setMultiOptions( PatchPanelPort::$INTERFACE_TYPES )
            ->setRegisterInArrayValidator( true )
            ->addValidator( 'greaterThan', true, array( 0 ) )
            ->setLabel( 'Interface Type' )
            ->setErrorMessages( array( 'Please set the interface type' ) );

        $this->addElement( $type );



        $cable_type = $this->createElement( 'select', 'cable_type' );
        $cable_type->setMultiOptions( PatchPanelPort::$CABLES_TYPES )
            ->setRegisterInArrayValidator( true )
            ->addValidator( 'greaterThan', true, array( 0 ) )
            ->setLabel( 'Cable Type' )
            ->setErrorMessages( array( 'Please set the cable type' ) );

        $this->addElement( $cable_type );


        $this->addElement( 'button', 'cancel', array( 'label' => 'Cancel', 'onClick' => "parent.location='"
        . $cancelLocation . "'" ) );
        $this->addElement( 'submit', 'commit', array( 'label' => 'Add' ) );

        $this->setElementDecorators(
            array(
                'ViewHelper'
            )
        );

    }

}

?>
