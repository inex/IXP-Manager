<?php

namespace IXP\Utils;

/*
 * Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use IXP\Exceptions\GeneralException as Exception;
use IXP\Exceptions\ConfigurationException;

/**
 * Interface for the BQPQ3 command line utility
 *
 * @see http://snar.spb.ru/prog/bgpq3/
 *
 * @author Barry O'Donovan <barry@opensolutions.ie>
 */
class Bgpq3
{
    /**
     * Full executable path of the BGPQ3 utility
     * @var string Full executable path of the BGPQ3 utility
     */
    private $path = null;

    /**
     * Whois server - defaults to BGPQ's own default
     * @var string Whois server - defaults to BGPQ's own default
     */
    private $whois = false;

    /**
     * Whois server sources - defaults to BGPQ's own default
     * @var string Whois server sources - defaults to BGPQ's own default
     */

    private $sources = false;

    /**
     * Constructor
     *
     * @param string $path The full executable path of the BGPQ3 utility
     * @param string $whois Whois server - defaults to BGPQ's own default
     * @param string $sources Whois server sources - defaults to BGPQ's own default
     * @throws ConfigurationException
     */
    public function __construct( $path, $whois = null, $sources = null )
    {
        if( !$path || !is_file( $path ) || !is_executable( $path ) ) {
            throw new ConfigurationException('You must set the configuration option IXP_IRRDB_BGPQ3_PATH and it must be the absolute path to the executable bgpq3 utility.');
        }


        $this->path = $path;

        if( $whois )
            $this->whois = $whois;

        if( $sources )
            $this->sources = $sources;
    }

    /**
     * Get the IRRDB prefix list (based on route[6]: objects) for a given AS
     * number / macro and protocol.
     *
     * Returns an array of prefixes (or empty array).
     *
     * @param string $asmacro As number (of the form as1234) or AS macro
     * @param int $proto The IP protocol - 4 or 6.
     * @throws Exception On a JSON decoding error
     * @return array The array of prefixes (or empty array).
     */
    public function getPrefixList( $asmacro, $proto = 4 )
    {
        $minSubnetSize = config( 'ixp.irrdb.min_v' . $proto . '_subnet_size' );
        $json = $this->execute( '-l pl -j -m ' . $minSubnetSize . ' ' . escapeshellarg( $asmacro ), $proto );
        $array = json_decode( $json, true );

        if( $array === null )
            throw new Exception( "Could not decode JSON response from BGPQ" );

        if( !isset( $array[ 'pl' ] ) )
            throw new Exception( "Named prefix list [pl] expected in decoded JSON but not found!" );

        $prefixes = [];
        // we're going to ignore the 'exact' for now.
        foreach( $array[ 'pl' ] as $ar )
            $prefixes[] = $ar['prefix'];

        return $prefixes;
    }

    /**
     * Get the IRRDB ASN list (based on route[6]: objects) for a given AS
     * number / macro and protocol.
     *
     * Returns an array of ASNs that may appear in any as path for the
     * route paths (or empty array).
     *
     * @param string $asmacro As number (of the form as1234) or AS macro
     * @param int $proto The IP protocol - 4 or 6.
     * @return array The array of prefixes (or empty array).
     */
    public function getAsnList( $asmacro, $proto = 4 )
    {
        $json = $this->execute( '-3j -l pl -f 999 ' . escapeshellarg( $asmacro ), $proto );
        $array = json_decode( $json, true );

        if( $array === null )
            throw new Exception( "Could not decode JSON response from BGPQ when fetching ASN list" );

        if( !isset( $array[ 'pl' ] ) )
            throw new Exception( "Named prefix list [pl] expected in decoded JSON but not found when fetching ASN list!" );

        $asns = [];

        foreach( $array[ 'pl' ] as $asn )
            $asns[] = $asn;

        return $asns;
    }

    /**
     * Ececute the BGPQ command line utility using the defined (or default)
     * whois host and sources.
     *
     * @param string $cmd The query part ot the BGPQ command. I.e. other switches besides -6, -h, -S.
     * @param int $proto The protocol. If 6, adds the -6 switch
     * @throws Exception If return code from BGPQ3 is != 0
     * @return string The output from the shell command.
     */
    private function execute( $cmd, $proto = 4 )
    {
        if( $this->whois )
            $cmd = '-h ' . escapeshellarg( $this->whois ) . ' ' . $cmd;

        if( $this->sources )
            $cmd = '-S ' . escapeshellarg( $this->sources ) . ' ' . $cmd;

        if( $proto == 6 )
            $cmd = '-6 ' . $cmd;

        $cmd = $this->path . ' ' . $cmd;

        $output = [];
        $return_var = 0;

        exec( $cmd, $output, $return_var );

        if( $return_var != 0 )
            throw new Exception( 'Error executing BGPQ3 with: ' . $cmd );

        return implode( "\n", $output );
    }


    /**
     * The whois server to query
     *
     * @param string $whois The whois server to query
     * @return Bgpq3 For fluent interfaces
     */
    public function setWhois( $whois )
    {
        $this->whois = $whois;
        return $this;
    }

    /**
     * The whois server sources
     *
     * @param string $sources The whois server sources
     * @return Bgpq3 For fluent interfaces
     */
    public function setSources( $sources )
    {
        $this->sources = $sources;
        return $this;
    }

    /**
     * The executable path to the BGPQ executable
     *
     * @param string $path The executable path to the BGPQ executable
     * @return Bgpq3 For fluent interfaces
     */
    public function setPath( $path )
    {
        $this->path = $path;

        return $this;
    }

}
