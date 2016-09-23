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
 * Form: adding switch ports
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Form
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXP_Form_Switch_AddPorts extends IXP_Form
{
    public function init()
    {
        $this->setDecorators( [ [ 'ViewScript', [ 'viewScript' => 'switch-port/forms/add-ports.phtml' ] ] ] );
        
        $this->addElement( IXP_Form_Switch::getPopulatedSelect( 'switchid' ) );
                
        $deftype = $this->createElement( 'select', 'deftype' );
        $deftype->setMultiOptions( \Entities\SwitchPort::$TYPES )
            ->setAttrib( 'class', 'chzn-select span3' )
            ->setRegisterInArrayValidator( true )
            ->setLabel( 'Default Type' );
        $this->addElement( $deftype );
        
        $numfirst = $this->createElement( 'text', 'numfirst' );
        $numfirst->addValidator('int')
            ->addValidator( 'greaterThan', false, array( 0 ) )
            ->setRequired( true )
            ->setAttrib( 'class', 'span3' )
            ->setLabel( 'Number of First Port' );
        $this->addElement( $numfirst  );
        
        $numports = $this->createElement( 'text', 'numports' );
        $numports->addValidator('int')
            ->addValidator( 'greaterThan', false, array( 0 ) )
            ->setRequired( true )
            ->setAttrib( 'class', 'span3' )
            ->setLabel( 'Number of Ports' );
        $this->addElement( $numports  );
        
        $this->addElement( self::createSubmitElement( 'submit', _( 'Add Ports' ) ) );
        $this->addElement( $this->createCancelElement() );
    }
}
