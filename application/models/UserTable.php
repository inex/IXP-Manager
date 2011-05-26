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
 *
 * Auto-generated Doctrine ORM File
 *
 * @category ORM
 * @package IXP_ORM_Models
 * @copyright Copyright 2008 - 2010 Internet Neutral Exchange Association Limited <info (at) inex.ie>
 * @author Barry O'Donovan <barryo (at) inex.ie>
 */
class UserTable extends Doctrine_Table
{

   /**
    * Creates a random password string of a given length. Not the fastest
    * way of generating random passwords, but ensures that it contains
    * both lowercase and uppercase letters and digits, so complies with
    * our password strength "policy".
    *
    * Some letters are excluded from the character set: 1, 0, O, I, l
    *
    * @param int $len The length of the password to be generated.
    * @return string The password string.
    */
    public static function createRandomPassword( $len = 8 )
    {
        $chars = "23456789abcdefghijkmnopqrstuvwxyz23456789ABCDEFGHJKLMNPQRSTUVWXYZ23456789";

        while( true )
        {
            $password = substr( str_shuffle( $chars ), 0, $len );

            // "/[a-zA-Z0-9]/" is NOT the same!
            if( (preg_match("/[a-z]/", $password)) && (preg_match("/[A-Z]/", $password)) && (preg_match("/[0-9]/", $password)) )
                return $password;
        }
    }
}
