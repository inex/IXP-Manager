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
     *
     * @return string
     *
     * @throws
     */
    public static function toArpa( string $ip, int $protocol ): string
    {
        switch( $protocol ) {
            case 4:
                $parts = explode( '.', $ip );
                $arpa = sprintf( '%d.%d.%d.%d.in-addr.arpa.', $parts[ 3 ], $parts[ 2 ], $parts[ 1 ], $parts[ 0 ] );
                break;
            case 6:
                $addr = inet_pton( $ip );
                $unpack = unpack('H*hex', $addr );
                $hex = $unpack[ 'hex' ];
                $arpa = implode('.', array_reverse( str_split( $hex ) ) ) . '.ip6.arpa.';
                break;
            default:
                throw new GeneralException( 'Invalid protocol!' );
        }

        return $arpa;
    }

    /**
     * Try to get the clients real IP address even when behind a proxy.
     *
     * Source: https://stackoverflow.com/questions/33268683/how-to-get-client-ip-address-in-laravel-5/41769505#41769505
     *
     * @return string
     */
    public static function getIp(): string
    {
        foreach( [ 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR' ] as $key ) {
            if( array_key_exists( $key, $_SERVER ) === true ) {
                foreach( explode(',', $_SERVER[ $key ] ) as $ip ) {
                    $ip = trim( $ip );
                    if( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) !== false ) {
                        return $ip;
                    }
                }
            }
        }
        return request()->getClientIp();
    }
}