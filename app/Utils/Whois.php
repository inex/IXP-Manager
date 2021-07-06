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

/**
 * Whois
 *
 * A Whois implementation
 *
 * @package IXP\Utils
 */
class Whois
{
    /**
     * @var string Whois server hostname
     */
    private $host;

    /**
     * @var int Whois server port
     */
    private $port;


    /**
     * Whois constructor.
     * @param string    $host       Whois server hostname
     * @param int       $port       Whois server port
     */
    public function __construct( string $host, int $port )
    {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * Do a whois lookup
     *
     * @param string $lookup   What to ask the whois server
     * @param bool $htmlencode If true, return output of htmlspecialchars()
     *
     * @return string
     */
    public function whois( string $lookup, bool $htmlencode = true ): string
    {
        // Whois specification:
        //
        // Connect to the service host
        //    TCP: service port 43 decimal
        // Send a single "command line", ending with <CRLF>.
        // Receive information in response to the command line.  The
        // server closes its connections as soon as the output is finished.

        // Open a socket to PeeringDB's Whois service
        if( !( $sock = fsockopen( $this->host, $this->port ) ) ) {
            return "Error: could not connect to {$this->host}:{$this->port}\n\nCheck internet connectivity and {$this->host} status.";
        }

        // look up the ASN
        fwrite( $sock, $lookup . "\n" );

        // load the result (streaming text)
        $data = '';
        while( !feof( $sock ) ) {
            $data .= fgets( $sock, 4096 );
        }
        fclose( $sock );

        // assume this is for display on a website
        if( $htmlencode ) {
            return clean( $data );
        }

        return $data;
    }

    /**
     * @return string
     */
    public function host(): string
    {
        return $this->host;
    }
}