<?php
/**
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
 * Lost Password form
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Form
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2
 */
class IXP_Form_Auth_LostPassword extends IXP_Form
{

    public function init()
    {
        $this->setAttrib( 'id', 'auth_lost_password' )
            ->setAttrib( 'name', 'auth_lost_password' );

        $this->addElement( OSS_Form_Auth::createUsernameElement() );
        $this->addElement( OSS_Form::createSubmitElement( 'submit', _( 'Reset Password' ) ) );
        $this->addElement( OSS_Form_Auth::createReturnToLoginElement() );
    }

}
