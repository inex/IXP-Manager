<?php

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


// Helpder functions


// https://github.com/LukeTowers/Purifier/blob/master/src/helpers.php - 20210304 - MIT
if (!function_exists('clean')) {
    function clean($dirty, $config = null)
    {
        return app('purifier')->clean($dirty, $config);
    }
}



if( !function_exists( 'd2r' ) ) {
    function d2r( $entity, $namespace = 'Entities' ) {
        return app('Doctrine\ORM\EntityManagerInterface')->getRepository($namespace.'\\'.$entity);
    }
}

if( !function_exists( 'resolve_dns_a' ) ) {

    /**
     * Do a DNS A record lookup on a hostname
     *
     * @param string $hostname
     * @return string|null The IP address or null
     */
    function resolve_dns_a( string $hostname ) {
        $a = dns_get_record( $hostname, DNS_A );

        if( empty( $a ) )
            return null;

        return $a[0]['ip'];
    }
}

if( !function_exists( 'resolve_dns_aaaa' ) ) {

    /**
     * Do a DNS AAAA record lookup on a hostname
     *
     * @param string $hostname
     * @return string|null The IP address or null
     */
    function resolve_dns_aaaa( string $hostname ) {
        $a = dns_get_record( $hostname, DNS_AAAA );

        if( empty( $a ) )
            return null;

        return $a[0]['ipv6'];
    }
}


if( !function_exists( 'ixp_min_auth' ) ) {

    /**
     * Check is a logged/public user meets the minimum authentication level provided
     *
     * @param int $minauth
     * @return bool
     */
    function ixp_min_auth( int $minauth ) {

        if( Auth::check() ) {
            return Auth::user()->getPrivs() >= $minauth;
        }

        return $minauth === 0;
    }
}


if( !function_exists( 'ixp_get_client_ip' ) ) {

    /**
     * Try to get the clients real IP address even when behind a proxy.
     *
     * Source: https://stackoverflow.com/questions/33268683/how-to-get-client-ip-address-in-laravel-5/41769505#41769505
     *
     * @return string
     */
    function ixp_get_client_ip()
    {
        // look for public:
        foreach( [ 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR' ] as $key ) {
            if( array_key_exists( $key, $_SERVER ) === true ) {
                foreach( explode(',', $_SERVER[$key] ) as $ip ) {
                    $ip = trim($ip);
                    if( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) !== false ) {
                        return $ip;
                    }
                }
            }
        }

        // accept private:
        foreach( [ 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR' ] as $key ) {
            if( array_key_exists( $key, $_SERVER ) === true ) {
                foreach( explode(',', $_SERVER[$key] ) as $ip ) {
                    $ip = trim($ip);
                    if( filter_var( $ip, FILTER_VALIDATE_IP ) !== false ) {
                        return $ip;
                    }
                }
            }
        }

        if( request() && request()->getClientIp() ) {
            return request()->getClientIp();
        }

        return '';
    }
}

if( !function_exists( 'rrd_graph' ) ) {

    function rrd_graph( $a, $b ) { return []; }

}

