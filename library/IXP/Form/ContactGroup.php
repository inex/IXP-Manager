<?php

/*
 * Copyright (C) 2009-2013 Internet Neutral Exchange Association Limited.
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
 * Form: adding / editing contact groups
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Form
 * @copyright  Copyright (c) 2009 - 2013, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXP_Form_ContactGroup extends IXP_Form
{
    public function init()
    {

        $name = $this->createElement( 'text', 'name' );
        $name->addValidator( 'stringLength', false, array( 1, 20 ) )
            ->setRequired( true )
            ->setLabel( 'Name' )
            ->setAttrib( 'class', 'span3' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $name );
        
        $description = $this->createElement( 'text', 'description' );
        $description->addValidator( 'stringLength', false, array( 1, 255 ) )
            ->setRequired( true )
            ->setLabel( 'Description' )
            ->setAttrib( 'class', 'span3' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $description );

        $type = $this->createElement( 'text', 'type' );
        $type->addValidator( 'stringLength', false, array( 1, 20 ) )
            ->setRequired( true )
            ->setLabel( 'Type' )
            ->setAttrib( 'class', 'span3' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $type );

        $active = $this->createElement( 'checkbox', 'active' );
        $active->setLabel( 'Active' )
            ->setValue( '1' )
            ->addValidator( 'InArray', false, array( array( 0, 1 ) ) )
            ->addFilter( 'Int' );     
        $this->addElement( $active );
        
        $limit = $this->createElement( 'text', 'limited_to' );
        $limit->addValidator( 'digits', true )
            ->setRequired( true )
            ->setLabel( 'Users Limit' )
            ->setAttrib( 'class', 'span2' )
            ->addFilter( 'Int' );
        $this->addElement( $limit );


        $this->addElement( self::createSubmitElement( 'submit', _( 'Add' ) ) );
        $this->addElement( $this->createCancelElement() );
    }
    
}
