<?php

/*
 * Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee.
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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

require_once 'Net/DNS2.php';

echo <<<END
  ___   _____ __   __   _____   _____         _
 / _ \ /  ___/  | /  | / __  \ |_   _|       | |
/ /_\ \\ `--.`| | `| | `' / /'   | | ___  ___| |_ ___ _ __
|  _  | `--. \| |  | |   / /     | |/ _ \/ __| __/ _ \ '__|
| | | |/\__/ /| |__| |_./ /___   | |  __/\__ \ ||  __/ |
\_| |_/\____/\___/\___/\_____/   \_/\___||___/\__\___|_|

(c) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee.

Part of the IXP Manager project - see https://github.com/inex/IXP-Manager

This script tests AS112 servers as follows:

- hostname.as112.net TXT records
- hostname.as112.arpa TXT records

And 10 random PTR queries for each of the following:

- 10.in-addr.arpa PTR records
- 168.192.in-addr.arpa PTR records
- 172.in-addr.arpa PTR records
- 254.169.in-addr.arpa PTR records

Starting...


END;




$nsservers = [ '192.175.48.1', '192.175.48.6', '192.175.48.42', '192.31.196.1',
    '2620:4f:8000::1', '2620:4f:8000::6', '2620:4f:8000::42', '2001:4:112::1',
];

$time = 0.0;
$count = 0;
$errors = [];
$warnings = [];

$start = microtime( true );





// db.hostname.as112.arpa
foreach( $nsservers as $ns ) {
    
    echo "Testing " . str_pad( $ns . ':', 20 );
    
    foreach( [ true, false ] as $tcp ) {
        
        $resp = dns( $ns, 'hostname.as112.net', 'TXT', $tcp );
        
        if( $resp && checkHostnameAs112( $ns, $resp ) ) {
            $count++; $time += $resp->response_time;
            echo '.';
        } else {
            echo 'X';
            $errors[] = "hostname.as112.net/TXT failed on $ns (tcp: " . ( $tcp ? 'yes' : 'no' ) . ")";
        }
        
        $resp = dns( $ns, 'hostname.as112.arpa', 'TXT' );
        
        if( $resp && checkHostnameAs112( $ns, $resp, true ) ) {
            $count++; $time += $resp->response_time;
            echo '.';
        } else {
            echo 'X';
            $errors[] = "hostname.as112.arpa/TXT failed on $ns (tcp: " . ( $tcp ? 'yes' : 'no' ) . ")";
        }
        
        for( $i = 0; $i < 10; $i++ ) {
            
            // random checks
            [ $a, $b, $c, $d ] = [ rand( 0, 255 ), rand( 0, 255 ), rand( 0, 255 ), rand( 16, 31 ) ];
            
            echo checkNxDomain( $ns, sprintf( '%d.%d.%d.10.in-addr.arpa',   $a, $b, $c ), $tcp ) ? '.' : 'X';
            echo checkNxDomain( $ns, sprintf( '%d.%d.168.192.in-addr.arpa', $a, $b     ), $tcp ) ? '.' : 'X';
            echo checkNxDomain( $ns, sprintf( '%d.%d.%d.172.in-addr.arpa',  $a, $b, $d ), $tcp ) ? '.' : 'X';
            echo checkNxDomain( $ns, sprintf( '%d.%d.254.169.in-addr.arpa', $a, $b, $c ), $tcp ) ? '.' : 'X';
        }
        
        
    }
    
    echo "\n";
}

echo "\n\nDone in " . number_format( microtime( true ) - $start, 2 ) . " seconds ($count queries in $time secs).\n";

if( count( $warnings ) ) {
    
    echo <<<END

        There are some warnings. The checker is hardcoded to find the following TXT strings:
        
                TXT     "Name of Facility or similar" "City, Country"
                TXT     "See http://www.as112.net/ for more information."
                TXT     "Unique IP: 203.0.113.1."
        
        However, the following TXT strings were found. These are not considered errors, as we are receiving TXT records and so the assumption is that you have a different TXT record configured.
        
        Warnings:
        
        END;
    
    foreach( $warnings as $e ) {
        echo "- $e\n";
    }
}


if( count( $errors ) ) {
    echo "\nErrors:\n";
    foreach( $errors as $e ) {
        echo "- $e\n";
    }
}

function dns( string $ns, string $qname, string $qtype, bool $tcp = false ): object|false {
    global $errors;

    $resolver = new Net_DNS2_Resolver( [ 'nameservers' => [ $ns ] ] );
    
    $resolver->use_tcp = $tcp;
    
    try {
        return $resolver->query( $qname, $qtype );
    } catch( Net_DNS2_Exception $e ) {
        return false;
    }
}

function checkHostnameAs112( string $ns, object $resp, bool $arpa = false ): bool {
    
    global $warnings;
    
    // we need to ensure we have:
    //
    //        TXT     "Name of Facility or similar" "City, Country"
    //        TXT     "See http://www.as112.net/ for more information."
    //        TXT     "Unique IP: 203.0.113.1."
    
    if( !is_array( $resp->answer ) || !count( $resp->answer ) === 3 ) {
        return false;
    }
    
    // name, see as122, unique ip
    $check = [ false, false, false ];
    
    foreach( $resp->answer as $a ) {
        
        if( $a->type !== 'TXT' ) {
            return false;
        }
        
        if( count( $a->text ) === 2 ) {
            
            if( $a->text[ 0 ] === 'Name of Facility or similar' && $a->text[ 1 ] === 'City, Country' ) {
                $check[ 0 ] = true;
            } else if( $a->text[ 1 ] === 'Name of Facility or similar' && $a->text[ 0 ] === 'City, Country' ) {
                $check[ 0 ] = true;
            } else {
                $warnings[] = "[$ns] $a->name TXT returned {$a->text[ 0 ]} and {$a->text[ 1 ]}";
                $check[ 0 ] = true;
            }
            
        } else if( count( $a->text ) === 1 ) {
            
            if( $a->text[ 0 ] === 'See http://www.as112.net/ for more information.' ) {
                $check[ 1 ] = true;
            } else if( $a->text[ 0 ] === 'Unique IP: 203.0.113.1.' ) {
                $check[ 2 ] = true;
            } else {
                $warnings[] = "[$ns] $a->name TXT returned {$a->text[ 0 ]}";
                $check[ 1 ] = true;
                $check[ 2 ] = true;
            }
            
        } else {
            return false;
        }
        
    }
    
    if( $arpa ) {
        $check[2] = true;
    }
    
    return $check[0] && $check[1] && $check[2];
}

function checkNxDomain( string $ns, string $qname, bool $tcp = false ): bool {
    
    global $time, $count, $errors;
    
    try {
        $resolver = new Net_DNS2_Resolver( [ 'nameservers' => [ $ns ] ] );
        $resolver->use_tcp = $tcp;
        $resolver->query( $qname, 'PTR' );
    } catch( Net_DNS2_Exception $e ) {
        
        $r = $e->getResponse();
        $a = $r->authority[0] ?? false;
        $time += $r->response_time;
        $count++;
        
        if( $a && $a->type === 'SOA' && $a->ttl === 604800 && $a->serial === 1 && $r->answer_from === $ns ) {
            return true;
        }
    }
    
    $errors[] = "$qname/PTR failed on $ns (tcp: " . ( $tcp ? 'yes' : 'no' ) . ")";
    
    return false;
}
