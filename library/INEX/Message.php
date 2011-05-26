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
 * A class to contain messages to be displayed on the webpages. *
 *
 *  @package INEX_Message
 */
class INEX_Message
{

    const MESSAGE_TYPE_ERROR   = 'error';
    const MESSAGE_TYPE_ALERT   = 'alert';
    const MESSAGE_TYPE_INFO    = 'info';
    const MESSAGE_TYPE_SUCCESS = 'success';

    /**
     * A variable to hold the message
     *
     * @var string The message
     */
    protected $message = '';

    /**
     * A variable to hold the appropriate HTML class (e.g. error, success, info)
     *
     * @var string The appropriate HTML class (e.g. error, success, info)
     */
    protected $class = '';

    /**
     * A variable to indicate whether the message is HTML or not
     *
     * @var bool Is the message in HTML?
     */
    protected $isHTML = true;

    /**
     * The constructor
     *
     * @param string $request The message
     * @param string $response The HTML div class
     * @param bool $invokeArgs Is the message HTML? (default: true)
     */
    public function __construct( $message = '', $class = '', $isHTML = true )
    {
        $this->message = $message;
        $this->class   = $class;
        $this->isHTML  = $isHTML;
    }


    /**
     * Get the message as plaintext - essentially strips the tags from the
     * message if it is an HTML message
     *
     * @return string The message after strip_tags()
     */
    public function getPlaintext()
    {
        if( $this->isHTML )
        return( strip_tags( $this->message ) );
        else
        return( $this->message );
    }

    /**
     * Get the message
     *
     * @return string The message
     */
    public function getMessage()
    {
        return( $this->message );
    }

    /**
     * Get the message
     *
     * @return string The message
     */
    public function getClass()
    {
        return( $this->class );
    }
}

?>