<?php

namespace IXP\Utils;

/*
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

use IXP\Exceptions\GeneralException;

/**
 * OUI functions
 *
 * Originally written 17 Feb 2014
 *
 * @author Barry O'Donovan <barry@opensolutions.ie>
 */
class OUI
{
    /**
     * Where to get the OUI list from
     * @var string Where to get the OUI list from
     */
    public $file = 'http://standards.ieee.org/develop/regauth/oui/oui.txt';

    /**
     * Raw OUI data
     * @var string
     */
    private $raw = null;

    /**
     * Processed OUIs array as [ 'oui' => 'organisation', ... ]
     * @var array
     */
    private $ouis = null;

    /**
     * Constructor
     *
     * @param string|null $file Where to get the OUI list from
     */
    public function __construct( string $file = null )
    {
        if( $file ) {
            $this->file = $file;
        }
    }

    /**
     * Load the raw OUI data from the specified location
     *
     * @return OUI An instance of this class for fluent interfaces
     *
     * @throws GeneralException
     */
    public function loadList(): OUI
    {
        $this->raw = @file_get_contents( $this->file );

        if( $this->raw === false ) {
            throw new GeneralException( 'IXP\\Utils\\OUI - could not load OUI list from ' . $this->file );
        }

        return $this;
    }

    /**
     * @param false $data
     *
     * @return array
     *
     * @throws GeneralException
     */
    public function processRawData( $data = false ): array
    {
        if( !$data && $this->raw === null ) {
            throw new GeneralException( 'IXP\\Utils\\OUI - cannot process when no data has been loaded or provided' );
        }

        if( !$data ) {
            $data = $this->raw;
        }

        $this->ouis = [];
        foreach( explode( "\n", $data ) as $line ) {
            if( preg_match( "/^\s*([0-9A-F]{6})\s+\(base 16\)\s+(.*)$/", $line, $matches ) )
                $this->ouis[ strtolower( trim( $matches[1] ) ) ] = trim( $matches[2] );
        }

        return $this->ouis;
    }
}