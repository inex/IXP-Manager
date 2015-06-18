<?php

/*
 * Copyright (C) 2009-2012 Internet Neutral Exchange Association Limited.
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
 * Adding / editing console server connections
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Form
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXP_Form_ConsoleServerConnection extends IXP_Form
{
    public function init()
    {
        $description = $this->createElement( 'text', 'description' );
        $description->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setRequired( true )
            ->setAttrib( 'class', 'span3' )
            ->setLabel( 'Description' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $description );


        $this->addElement( IXP_Form_Customer::getPopulatedSelect( 'custid' ) );
        $this->addElement( IXP_Form_Switch::getPopulatedSelect( 'switchid', \Entities\Switcher::TYPE_CONSOLESERVER ) );
        
        $port = $this->createElement( 'text', 'port' );
        $port->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setRequired( true )
            ->setLabel( 'Port' )
            ->setAttrib( 'class', 'span3' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $port );

        $speed = $this->createElement( 'text', 'speed' );
        $speed->addValidator( 'int' )
            ->setLabel( 'Speed' )
            ->addFilter( 'StringTrim' )
            ->setAttrib( 'class', 'span3' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $speed );

        $parity = $this->createElement( 'text', 'parity' );
        $parity->addValidator( 'int' )
            ->setLabel( 'Parity' )
            ->setAttrib( 'class', 'span3' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $parity );

        $stopbits = $this->createElement( 'text', 'stopbits' );
        $stopbits->addValidator( 'int' )
            ->setLabel( 'Stopbits' )
            ->setAttrib( 'class', 'span3' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $stopbits );

        $flowcontrol = $this->createElement( 'text', 'flowcontrol' );
        $flowcontrol->addValidator( 'int' )
            ->setLabel( 'Flow Control' )
            ->setAttrib( 'class', 'span3' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $flowcontrol );

        $autobaud = $this->createElement( 'text', 'autobaud' );
        $autobaud->addValidator( 'int' )
            ->setAttrib( 'class', 'span3' )
            ->setLabel( 'Autobaud' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $autobaud );

        $notes = $this->createElement( 'textarea', 'notes' );
        $notes->setLabel( 'Notes' )
            ->setRequired( false )
            ->addFilter( new OSS_Filter_StripSlashes() )
            ->setAttrib( 'cols', 60 )
            ->setAttrib( 'class', 'span3' )
            ->setAttrib( 'rows', 5 );
        $this->addElement( $notes );


        $this->addElement( self::createSubmitElement( 'submit', _( 'Add' ) ) );
        $this->addElement( $this->createCancelElement() );
    }

}

