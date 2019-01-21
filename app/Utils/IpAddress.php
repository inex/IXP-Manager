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

use IXP\Exceptions\GeneralException;

/**
 * IpAddress Utilities
 *
 * @author Barry O'Donovan <barry@islandbridgenetworks.ie>
 */
class IpAddress
{

    /**
     * Convert an IP address to an ARPA record
     *
     * E.g.:
     *
     * * 192.0.2.45  => 45.2.0.192.in-addr.arpa.
     * * 2001:db8::1 => 1.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.8.b.d.0.1.0.0.2.ip6.arpa.
     *
     * @param string $ip The IP address
     * @param int $protocol Either 4 (IPv4) or 6 (IPv6)
     * @return string
     */
    public static function toArpa( string $ip, int $protocol ) {
        switch( $protocol ) {
            case 4:
                $parts = explode( '.', $ip );
                $arpa = sprintf( '%d.%d.%d.%d.in-addr.arpa.', $parts[3], $parts[2], $parts[1], $parts[0] );
                break;

            case 6:
                $addr = inet_pton($ip);
                $unpack = unpack('H*hex', $addr);
                $hex = $unpack['hex'];
                $arpa = implode('.', array_reverse(str_split($hex))) . '.ip6.arpa.';
                break;

            default:
                throw new GeneralException( 'Invalid protocol!' );

        }

        return $arpa;
    }


}
