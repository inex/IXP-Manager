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
 * Form: adding / editing contacts
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Form
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXP_Form_Contact extends IXP_Form
{
    public function init()
    {
        $this->setDecorators( [ [ 'ViewScript', [ 'viewScript' => 'contact/forms/edit.phtml' ] ] ] );

        $name = $this->createElement( 'text', 'name' );
        $name->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setRequired( true )
            ->setLabel( 'Name' )
            //->setAttrib( 'class', 'span3' )
            ->addFilter( 'StringTrim' )
            ->addFilter( 'StripTags' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $name );

        $position = $this->createElement( 'text', 'position' );
        $position->addValidator( 'stringLength', false, array( 1, 50, 'UTF-8' ) )
            ->setRequired( false )
            ->setLabel( 'Position' )
            //->setAttrib( 'class', 'span3' )
            ->addFilter( 'StringTrim' )
            ->addFilter( 'StripTags' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $position );

        $this->addElement( IXP_Form_Customer::getPopulatedSelect( 'custid' ) );
        $this->getElement( 'custid' )
            ->setAttrib( 'class', "chzn-select" );

        $this->addElement( OSS_Form_User::createEmailElement( 'email' ) );
        $this->getElement( 'email' )
            ->setRequired( false )
            ->setAttrib( 'class', "" );

        $phone = $this->createElement( 'text', 'phone' );
        $phone->addValidator( 'stringLength', false, array( 1, 32, 'UTF-8' ) )
            ->setLabel( _( 'Phone' ) )
            //->setAttrib( 'class', 'span3' )
            ->addFilter( 'StringTrim' )
            ->addFilter( 'StripTags' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $phone );

        $mobile = $this->createElement( 'text', 'mobile' );
        $mobile->addValidator( 'stringLength', false, array( 1, 32, 'UTF-8' ) )
            ->setLabel( _( 'Mobile' ) )
            ->addFilter( 'StringTrim' )
            ->addFilter( 'StripTags' )
            //->setAttrib( 'class', 'span3' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $mobile );

        $notes = $this->createElement( 'textarea', 'notes' );
        $notes->setLabel( 'Notes' )
            ->setRequired( false )
            ->setAttrib( 'style', 'width: 80%;' )
            ->addFilter( new OSS_Filter_StripSlashes() )
            ->addFilter( 'StripTags' )
            ->setAttrib( 'cols', 60 )
            ->setAttrib( 'rows', 5 );
        $this->addElement( $notes );

        $facilityaccess = $this->createElement( 'checkbox', 'facilityaccess' );
        $facilityaccess->setLabel( 'Facility Access' )
            ->addValidator( 'InArray', false, array( array( 0, 1 ) ) )
            ->addFilter( 'Int' )
            ->setValue( 1 );
        $this->addElement( $facilityaccess );

        $mayauthorize = $this->createElement( 'checkbox', 'mayauthorize' );
        $mayauthorize->setLabel( 'May Authorize' )
            ->addValidator( 'InArray', false, array( array( 0, 1 ) ) )
            ->addFilter( 'Int' )
            ->setValue( 1 );
        $this->addElement( $mayauthorize );

        $this->addElement( self::createSubmitElement( 'submit', _( 'Add' ) ) );
        $this->addElement( $this->createCancelElement() );

        $role = $this->createElement( 'hidden', 'role' );
        $role->setRequired( false );
        $this->addElement( $role );

        $group = $this->createElement( 'hidden', 'group' );
        $group->setRequired( false );
        $this->addElement( $group );

        $login = $this->createElement( 'checkbox', 'login' );
        $login->setLabel( 'can login to portal' )
            ->addValidator( 'InArray', false, array( array( 0, 1 ) ) )
            ->addFilter( 'Int' );
        $this->addElement( $login );

        $username = OSS_Form_Auth::createUsernameElement();
        $username->addValidator( 'stringLength', false, array( 2, 30, 'UTF-8' ) )
            ->addValidator( 'regex', true, array( '/^[a-zA-Z0-9\-_\.]+$/' ) )
            ->setRequired( false )
            ->setAttrib( 'class', '' );
        $this->addElement( $username );

        $password = $this->createElement( 'text', 'password' );
        $password->addValidator( 'stringLength', false, array( 8, 30, 'UTF-8' ) )
            ->setRequired( false )
            //->setAttrib( 'class', 'span3' )
            ->setLabel( 'Password' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $password );

        $privileges = $this->createElement( 'select', 'privs' );
        $privileges->setMultiOptions( \Entities\User::$PRIVILEGES_TEXT )
            ->setRegisterInArrayValidator( true )
            ->setRequired( false )
            ->setLabel( 'Privileges' )
            ->setAttrib( 'class', 'chzn-select' )
            ->setAttrib( 'chzn-fix-width', '1' )
            ->setErrorMessages( array( 'Please select the users privilege level' ) );
        $this->addElement( $privileges );

        $disabled = $this->createElement( 'checkbox', 'disabled' );
        $disabled->setLabel( 'Disabled?' )
            ->addValidator( 'InArray', false, array( array( 0, 1 ) ) )
            ->addFilter( 'Int' );
        $this->addElement( $disabled );
    }

}
