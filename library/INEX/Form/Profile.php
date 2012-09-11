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
 * A form to allow a user to change their profile.
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   INEX
 * @package    INEX_Form
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class INEX_Form_Profile extends INEX_Form
{
    public function init()
    {
        $this->setAttrib( 'id', 'profile' )
            ->setAttrib( 'name', 'profile' )
            ->setAction( OSS_Utils::genUrl( 'profile', 'change-profile' ) );
        
        $username = OSS_Form_Auth::createUsernameElement();
        $username->setAttrib( 'readonly', 'readonly' )
                 ->setAttrib( 'class', 'span6' );
        $this->addElement( $username );

        
        $mobile = INEX_Form_User::createMobileElement()
                    ->setAttrib( 'class', 'span6' );
        $this->addElement( $mobile );

        
        $email = OSS_Form_User::createEmailElement();
        $email->setAttrib( 'readonly', 'readonly' )
                 ->setAttrib( 'class', 'span9' );
        $this->addElement( $email );

        $this->addElement( self::createSubmitElement( 'submit', _( 'Update' ) ) );
    }

}

