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

/**
 * Form: adding / editing contacts
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Form
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXP_Form_Contact extends IXP_Form
{
    public function init()
    {
        $this->setDecorators( [ [ 'ViewScript', [ 'viewScript' => 'contact/forms/edit.phtml' ] ] ] );
        
        $name = $this->createElement( 'text', 'name' );
        $name->addValidator( 'stringLength', false, array( 1, 255 ) )
            ->setRequired( true )
            ->setLabel( 'Name' )
            ->setAttrib( 'class', 'span3' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $name );
        
        $position = $this->createElement( 'text', 'position' );
        $position->addValidator( 'stringLength', false, array( 1, 50 ) )
            ->setRequired( true )
            ->setLabel( 'Position' )
            ->setAttrib( 'class', 'span3' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $position );

        $this->addElement( IXP_Form_Customer::getPopulatedSelect( 'custid' ) );

        $this->addElement( OSS_Form_User::createEmailElement( 'email' ) );
        $this->getElement( 'email' )->setRequired( false );

        $phone = $this->createElement( 'text', 'phone' );
        $phone->addValidator( 'stringLength', false, array( 1, 32 ) )
            ->setLabel( _( 'Phone' ) )
            ->setAttrib( 'class', 'span3' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $phone );

        $mobile = $this->createElement( 'text', 'mobile' );
        $mobile->addValidator( 'stringLength', false, array( 1, 32 ) )
            ->setLabel( _( 'Mobile' ) )
            ->addFilter( 'StringTrim' )
            ->setAttrib( 'class', 'span3' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $mobile );
        
        $notes = $this->createElement( 'textarea', 'notes' );
        $notes->setLabel( 'Notes' )
            ->setRequired( false )
            ->setAttrib( 'class', 'span3' )
            ->addFilter( new OSS_Filter_StripSlashes() )
            ->setAttrib( 'cols', 60 )
            ->setAttrib( 'rows', 5 );
        $this->addElement( $notes );

        $facilityaccess = $this->createElement( 'checkbox', 'facilityaccess' );
        $facilityaccess->setLabel( 'Facility Access' )
            ->setCheckedValue( '1' );
        $this->addElement( $facilityaccess );

        $mayauthorize = $this->createElement( 'checkbox', 'mayauthorize' );
        $mayauthorize->setLabel( 'May Authorize' )
            ->setCheckedValue( '1' );
        $this->addElement( $mayauthorize );

        $this->addElement( self::createSubmitElement( 'submit', _( 'Add' ) ) );
        $this->addElement( $this->createCancelElement() );
        
        $role = $this->createElement( 'hidden', 'role' );
        $role->setRequired( false );
        $this->addElement( $role );
        
        $group = $this->createElement( 'hidden', 'group' );
        $group->setRequired( false );
        $this->addElement( $group );
    }
    
}
