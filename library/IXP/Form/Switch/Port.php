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
 * Form: adding / editing a switch port
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Form
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXP_Form_Switch_Port extends IXP_Form
{
    public function init()
    {
        $this->addElement( IXP_Form_Switch::getPopulatedSelect( 'switchid' ) );

        $name = $this->createElement( 'text', 'name' );
        $name->setLabel( 'Name' )
             ->setAttrib( 'class', 'span3' );
        $this->addElement( $name );

        $type = $this->createElement( 'select', 'type' );
        $type->setMultiOptions( \Entities\SwitchPort::$TYPES )
            ->setRegisterInArrayValidator( true )
            ->setLabel( 'Type' )
            ->setAttrib( 'class', 'span3 chzn-select' )
            ->setErrorMessages( array( 'Please set the port type' ) );
        $this->addElement( $type );

        $active = $this->createElement( 'checkbox', 'active' );
        $active->setLabel( 'Active' )
            ->setValue( '1' )
            ->addValidator( 'InArray', false, [ [ 0, 1 ] ] )
            ->addFilter( 'Int' );
        $this->addElement( $active );

        $this->addElement( self::createSubmitElement( 'submit', _( 'Add' ) ) );
        $this->addElement( $this->createCancelElement() );
    }
}
