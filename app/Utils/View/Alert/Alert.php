<?php

namespace IXP\Utils\View\Alert;

/**
 * A class to encapsulate Bootstrap v3 messages.
 *
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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
 * Alert
 *
 * @author Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
 */
class Alert
{

    public const SUCCESS = 'success';
    public const INFO    = 'info';
    public const WARNING = 'warning';
    public const DANGER  = 'danger';

    public const CLASSES = [
        self::SUCCESS,
        self::INFO,
        self::WARNING,
        self::DANGER,
    ];

    /**
     * The message
     * @var string
     */
    protected $message;

    /**
     * The class
     * @var string
     */
    private $class = '';

    /**
     * The constructor
     * @param string $message
     * @param string $class
     */
    public function __construct( string $message, string $class = self::INFO )
    {
        $this->message = $message;

        if( !in_array( $class, self::CLASSES ) ) {
            $this->class = self::INFO;
        } else {
            $this->class = $class;
        }
    }

    /**
     * Get the message
     * @return string
     */
    public function message(): string
    {
        return $this->message;
    }

    /**
     * Get the class
     * @return string the class
     */
    public function class(): string
    {
        return $this->class;
    }
}