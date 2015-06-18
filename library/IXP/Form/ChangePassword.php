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
 * A form to allow a user to change his password
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Form
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXP_Form_ChangePassword extends IXP_Form
{
    public function init()
    {
        $this->setAttrib( 'id', 'change_password' )
            ->setAttrib( 'name', 'change_password' )
            ->setAction( OSS_Utils::genUrl( 'profile', 'change-password' ) );

        $this->addElement(
            OSS_Form_Auth::createPasswordElement( 'current_password' )
                ->setLabel( _( 'Current Password' ) )
                ->setAttrib( 'class', 'span6' )
        );
        
        $this->addElement(
            OSS_Form_Auth::createPasswordElement( 'new_password' )
                ->removeValidator( 'stringLength' )
                ->addValidator( 'stringLength', false, array( 8, 255, 'UTF-8' ) )
                ->setLabel( _( 'New Password' ) )
                ->setAttrib( 'class', 'span6' )
        );
        
        $this->addElement(
            OSS_Form_Auth::createPasswordConfirmElement( 'confirm_password', 'new_password' )
                ->setAttrib( 'class', 'span6' )
        );
        
        $this->addElement( OSS_Form::createSubmitElement( 'submit', _( 'Change Password' ) ) );
    }
}

