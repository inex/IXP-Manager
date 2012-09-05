<?php
/**
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
 * The Login Form
 */
class INEX_Form_Auth_Login extends INEX_Form
{

    public function init()
    {
        $this->setAttrib( 'id', 'auth_login' )
            ->setAttrib( 'name', 'auth_login' );

        $this->addElement( OSS_Form_Auth_Login::createUsernameElement() );
        $this->addElement( OSS_Form_Auth_Login::createPasswordElement() );
        $this->addElement( OSS_Form_Auth_Login::createRememberMeElement() );
        

/*        $lost_pswd = $this->createElement( "button", "lost_pswd", [
            "label" => "Lost Password",
            "class" => "have-link",
            "attribs" => [
                "data-href" => OSS_Utils::genUrl( "login", "lost-password" )
            ]
        ] );

        $lost_usrn = $this->createElement( "button", "lost_usrn", [
            "label" => "Lost Username",
            "class" => "have-link",
            "attribs" => [
                "data-href" => OSS_Utils::genUrl( "login", "lost-username" )
            ]
        ] );
*/
        $this->addElement( "submit", "Login" );
        
        //    ->addElement( $lost_pswd )
        //    ->addElement( $lost_usrn );

    }

}
