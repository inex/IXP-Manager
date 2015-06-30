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
 * A form to allow a user to change his profile.
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Form
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXP_Form_Profile extends IXP_Form
{
    public function init()
    {
        $this->setDecorators( [ [ 'ViewScript', [ 'viewScript' => 'profile/forms/profile.phtml' ] ] ] );

        $this->setAttrib( 'id', 'profile' )
            ->setAttrib( 'name', 'profile' )
            ->setAction( OSS_Utils::genUrl( 'profile', 'change-profile' ) );

        $name = $this->createElement( 'text', 'name' );
        $name->addValidator( 'stringLength', false, array( 1, 255, 'UTF-8' ) )
            ->setRequired( true )
            ->setLabel( 'Name' )
            ->setAttrib( 'class', 'span6' )
            ->addFilter( 'StringTrim' )
            ->addFilter( 'StripTags' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $name );

        $position = $this->createElement( 'text', 'position' );
        $position->addValidator( 'stringLength', false, array( 1, 50, 'UTF-8' ) )
            ->setRequired( true )
            ->setLabel( 'Position' )
            ->setAttrib( 'class', 'span6' )
            ->addFilter( 'StripTags' )
            ->addFilter( 'StringTrim' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $position );

        $email = OSS_Form_User::createEmailElement();
        $email->setAttrib( 'class', 'span9' );
        $this->addElement( $email );

        $mobile = IXP_Form_User::createMobileElement()
                    ->setAttrib( 'class', 'span6' );
        $this->addElement( $mobile );

        $phone = $this->createElement( 'text', 'phone' );
        $phone->addValidator( 'stringLength', false, array( 1, 32, 'UTF-8' ) )
            ->setLabel( _( 'Phone' ) )
            ->setAttrib( 'class', 'span6' )
            ->addFilter( 'StringTrim' )
            ->addFilter( 'StripTags' )
            ->addFilter( new OSS_Filter_StripSlashes() );
        $this->addElement( $phone );

        $this->addElement(
            OSS_Form_Auth::createPasswordElement( 'current_password' )
                ->setLabel( _( 'Current Password' ) )
                ->setAttrib( 'class', 'span6' )
        );

        $this->addElement( self::createSubmitElement( 'submit', _( 'Update' ) ) );
    }

}
