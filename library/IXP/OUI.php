<?php


/*
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
 * OUI functions
 *
 * @author Barry O'Donovan <barry@opensolutions.ie>
 * @package IXP_OUI3
 */
class IXP_OUI 
{
    /**
     * Where to get the OUI list from
     * @var string Where to get the OUI list from
     */
    public $file = 'http://standards.ieee.org/develop/regauth/oui/oui.txt';

    /**
     * Raw OUI data 
     */
    private $raw = null;

    /**
     * Processed OUIs array as [ 'oui' => 'organisation', ... ]
     */
    private $ouis = null;

    /**
     * Constructor
     *
     * @param string $file Where to get the OUI list from
     */
    public function __construct( $file = false )
    {
        if( $file )
            $this->file = $file;
    }

    /**
     * Load the raw OUI data from the specificed location
     * 
     * @return IXP_OUI An instance of this class for fluent interfaces
     */
    public function loadList()
    {
        $this->raw = @file_get_contents( $this->file );

        if( $this->raw === false )
            throw new IXP_Exception( 'IXP_OUI - could not load OUI list from ' . $this->file );

        return $this;
    }

    public function processRawData( $data = false )
    {
        if( $data == false && $this->raw === null )
            throw new IXP_Exception( 'IXP_OUI - cannot process when no data has been loaded or provided' );

        if( $data == false )
            $data = $this->raw;

        $this->ouis = [];
        foreach( explode( "\n", $data ) as $line )
        {
            if( preg_match( "/^\s*([0-9A-F]{6})\s+\(base 16\)\s+(.*)$/", $line, $matches ) )
                $this->ouis[ strtolower( $matches[1] ) ] = $matches[2];
        }

        return $this->ouis;
    }
}