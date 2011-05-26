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
 * INEX Internet Exchange Point Application :: INEX IXP
 *
 * Copyright (c) 2009 Internet Neutral Exchange Limited <http://www.inex.ie/>
 * All rights reserved.
 *
 * @category INEX
 * @version $Id: DoctrineAdapter.php 447 2011-05-26 13:53:52Z barryo $
 * @package INEX_Auth
 * @copyright Copyright (c) 2009 Internet Neutral Exchange Limited <http://www.inex.ie/>
 *
 */


/**
 * Doctrine Adapter for Zend_Auth
 *
 * @category INEX
 * @package INEX_Bootstrap_Resources
 * @copyright Copyright (c) 2009 Internet Neutral Exchange Limited <http://www.inex.ie/>
 */
class INEX_Auth_DoctrineAdapter implements Zend_Auth_Adapter_Interface
{
    /**
     * The username
     *
     * @var string The username
     */
    private $username = null;

    /**
     * The password
     *
     * @var string The password
     */
    private $password = null;

    /**
     * Sets username and password for authentication
     *
     * @throws Zend_Auth_Adapter_Exception If parameters are incorrect / not present
     * @return void
     */
    public function __construct( $username, $password )
    {
        if( $username == null || $username == '' || $password == null || $password == '' )
            throw new Zend_Auth_Adapter_Exception( "No username / password specified" );

        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Performs an authentication attempt
     *
     * @throws Zend_Auth_Adapter_Exception If authentication cannot
     *                                     be performed
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        $user = Doctrine_Query::create()
            ->from( 'User u' )
            ->leftJoin( 'u.Cust c' )
            ->where( 'u.username = ?', $this->username )
            ->fetchOne();

        // $user === false if no record

        $result = array(
            'code'  => Zend_Auth_Result::FAILURE,
            'identity' => array(
                'username' => $this->username
            ),
            'messages' => array()
        );

        if( !$user )
        {
            $result['code']       = Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
            $result['messages'][] = 'Username / password invalid';
        }
        else if( $user->disabled == 1 )
        {
            $result['code'] = Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
            $result['messages'][] = 'Your account has been disabled';
        }
        else if( $user['Cust']['dateleave'] != null && $user['Cust']['dateleave'] != '0000-00-00' )
        {
            $result['code'] = Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
            $result['messages'][] = 'Your organisation is no longer a member of the exchange';
        }
        else if( $user->password != $this->password )
        {
            $result['code'] = Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
            $result['messages'][] = 'Username / password invalid';
        }
        else if( $user->password == $this->password )
        {
            $result['code']     = Zend_Auth_Result::SUCCESS;
            $result['identity'] = array(
                'username' => $this->username,
                'user'     => $user->toArray()
            );

            // password is plaintext in the database so let's not include this is session files
            $result['identity']['user']['password'] = 'xxxxxxxx';
        }

        return new Zend_Auth_Result( $result['code'], $result['identity'], $result['messages'] );
    }

}

?>