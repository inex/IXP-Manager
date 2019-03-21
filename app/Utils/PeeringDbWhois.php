<?php

namespace IXP\Utils;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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
 * PeeringDbWhois
 *
 * A Whois implementation for looking up networks by ASN in PeeringDB
 *
 * @package IXP\Utils
 */
class PeeringDbWhois
{

    /**
     * Do an ASN lookup on PeeringDB
     *
     * @param int  $as         The AS number to lookup
     * @param bool $htmlencode If true, return output of htmlspecialchars()
     * @return string
     */
    public function whois( int $as, bool $htmlencode = true ): string
    {
        // Whois specification:
        //
        // Connect to the service host
        //    TCP: service port 43 decimal
        // Send a single "command line", ending with <CRLF>.
        // Receive information in response to the command line.  The
        // server closes its connections as soon as the output is finished.

        // Open a socket to PeeringDB's Whois service
        if( !( $sock = fsockopen( 'whois.peeringdb.com', 43 ) ) ) {
            return "Error: could not connect to whois.peeringdb.com:43\n\nCheck internet connectivity and PeeringDB status.";
        }

        // look up the ASN
        fputs( $sock, sprintf( "AS%d\r\n", $as ) );

        // load the result (streaming text)
        $data = '';
        while( !feof( $sock ) ) {
            $data .= fgets( $sock, 4096 );
        }
        fclose( $sock );

        // nicer error message than PeeringDB's
        if( strpos( strtolower( $data ), "network matching query does not exist" ) !== false ) {
            return "AS{$as} does not appear to have a record in PeeringDB.";
        }

        // assume this is for display on a website
        if( $htmlencode ) {
            return clean( $data );
        }

        return $data;
    }


}