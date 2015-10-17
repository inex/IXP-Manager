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
 * @package IXP_Form
 * @subpackage SubForm
 *
 */
class IXP_Form_SubForm_PatchPanel extends IXP_Form_SubForm
{
    /**
     *
     *
     */
    public function __construct( $options = null )
    {
        parent::__construct( $options );

        $this->setDecorators(
            array(
                array( 'ViewScript', array( 'viewScript' => 'patch-panel/form/patch-panel.tpl' ) )
            )
        );

        ////////////////////////////////////////////////
        // Create and configure elements
        ////////////////////////////////////////////////

        $name = $this->createElement( 'text', 'name' );
        $name->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setAttrib('size', 50 )
            ->setAttrib('maxlength', 255)
            ->setRequired( true )
            ->setLabel( 'Name' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new IXP_Filter_StripSlashes() )
            ->setErrorMessages( array( 'Name is required and cannot be empty' ) );

        $this->addElement( $name );


        $dbCabinets = Doctrine_Query::create()
            ->from( 'Cabinet' )
            ->orderBy( 'name ASC' )
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


        $colo_ref = $this->createElement( 'text', 'colo_ref' );
        $colo_ref->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setAttrib('size', 40 )
            ->setAttrib('maxlength', 255)
            ->setRequired( true )
            ->setLabel( 'Colo Provider\'s Ref' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new IXP_Filter_StripSlashes() );

        $this->addElement( $colo_ref );


        $cable_type = $this->createElement( 'select', 'cable_type' );
        $cable_type->setMultiOptions( PatchPanelPort::$CABLES_TYPES )
            ->setRegisterInArrayValidator( true )
            ->addValidator( 'greaterThan', true, array( 0 ) )
            ->setLabel( 'Cable Type' )
            ->setErrorMessages( array( 'Please set the cable type' ) );

        $this->addElement( $cable_type );

        $interface_type = $this->createElement( 'select', 'interface_type' );
        $interface_type->setMultiOptions( PatchPanelPort::$INTERFACE_TYPES )
            ->setRegisterInArrayValidator( true )
            ->addValidator( 'greaterThan', true, array( 0 ) )
            ->setLabel( 'Interface Type' )
            ->setErrorMessages( array( 'Please set the interface type' ) );

        $this->addElement( $interface_type );


        $allow_duplex = $this->createElement( 'checkbox', 'allow_duplex' );
        $allow_duplex->setLabel( 'Allow Duplex?' );

        $this->addElement( $allow_duplex );


        $notes = $this->createElement( 'textarea', 'notes' );
        $notes->setLabel( 'Notes' )
            ->setRequired( false )
            ->addFilter( new IXP_Filter_StripSlashes() )
            ->setAttrib( 'cols', 80 )
            ->setAttrib( 'rows', 6 );
        $this->addElement( $notes );


        $this->setElementDecorators(
            array(
                'ViewHelper'
            )
        );

    }

}

?>
