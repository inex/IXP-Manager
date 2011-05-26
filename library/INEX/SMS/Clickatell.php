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
 * SMS - Short Messaging System
 *
 * A class to send SMS messages via different backends
 *
 * Specific functions for Clickatell
 *
 * http://www.inex.ie/
 * (c) Copyright 2009 Internet Neutral Exchange Association Ltd
 *
 *
 *  @package INEX_SMS
 */
class INEX_SMS_Clickatell
{

    /**
     * The Clickatell API to send one off SMS messages
     */
    const URL_SEND_SMS = "http://api.clickatell.com/http/sendmsg?user=%s&password=%s&api_id=%s&to=%s&text=%s";


    /**
     * The Clickatell account username
     */
    private $username;

    /**
     * The Clickatell account password
     */
    private $password;

    /**
     * The Clickatell account API ID
     */
    private $api_id;

    /**
     * The Clickatell sender ID
     */
    private $sender_id = false;


    /**
     * Clickatell API Response text
     */
    public $apiResponse;



    /**
     * Constructor for the Clickatell SMS API.
     *
     * @param $user string The Clickatell account username
     * @param $pass string The Clickatell account password
     * @param $api_id string The Clickatell HTTP(S) gateway API ID
     */
    public function __construct( $user = null, $pass = null, $api_id = null, $sender_id = null )
    {
        if( $user !== null )
            $this->setUsername( $user );

        if( $pass !== null )
            $this->setPassword( $pass );

        if( $api_id !== null )
            $this->setApiID( $api_id );

        if( $sender_id !== null )
            $this->setSenderID( $sender_id );
    }



    /**
     * Set the Clickatell HTTP(S) gateway API ID
     *
     * @param $username string The Clickatell API ID
     */
    public function setApiID( $api_id )
    {
        $this->api_id = $api_id;
    }

    /**
     * Set the Clickatell HTTP(S) sender ID
     *
     * @param $sender_id string The Sender ID
     */
    public function setSenderID( $sender_id )
    {
        $this->sender_id = $sender_id;
    }


    /**
     * Set the Clickatell account password
     *
     * @param $username string The Clickatell password
     */
    public function setPassword( $pass )
    {
        $this->password = $pass;
    }


    /**
     * Set the Clickatell account username
     *
     * @param $username string The Clickatell username
     */
    public function setUsername( $user )
    {
        $this->username = $user;
    }


    /**
     * Send an SMS message
     *
     * @param $to string The recipient number in international format (e.g. 353861234567)
     * @param $message string The message to send
     * @return TRUE on successful, else FALSE
     */
    public function send( $to, $message )
    {
        // try and send the message via Clickatell
        $apiCall = sprintf( INEX_SMS_Clickatell::URL_SEND_SMS,
            $this->username, $this->password, $this->api_id, $to,
            urlencode( $message )
        );

        if( $this->sender_id )
        {
            $apiCall .= '&from=' . $this->sender_id;
        }

        $this->apiResponse = @file_get_contents( $apiCall );

        if( $this->apiResponse === FALSE || substr( $this->apiResponse, 0, 3 ) !== 'ID:' )
        {
            return false;
        }

        return true;
    }

}

?>
