<?php

// Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
// All Rights Reserved.
//
// This file is part of IXP Manager.
//
// IXP Manager is free software: you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the Free
// Software Foundation, version 2.0 of the License.
//
// IXP Manager is distributed in the hope that it will be useful, but WITHOUT
// ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
// FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
// more details.
//
// You should have received a copy of the GNU General Public License v2.0
// along with IXP Manager.  If not, see:
//
// http://www.gnu.org/licenses/gpl-2.0.html
//

// variables - you need to change these!
$key="your-api-key";
$url="https://ixp.example.com/ixp/api/v4/router/updated-before";
$threshold=86400;

// is curl available?
if( !function_exists( 'curl_init' ) ) {
    echo "UNKNOWN: curl not available - install php-curl";
    exit( 3 );
}

// get the JSON of routers last updated >$threshold seconds ago
$s = curl_init();
curl_setopt( $s, CURLOPT_URL,  $url . '/' . $threshold );
curl_setopt( $s, CURLOPT_HTTPHEADER, [ 'X-IXP-Manager-API-Key: ' . $key ] );
curl_setopt( $s, CURLOPT_HEADER, true );
$json = curl_exec($s);

if( !curl_getinfo($s,CURLINFO_HTTP_CODE) == 200 ) {
    echo "UNKNOWN: non-200 status code returned by API: " . curl_getinfo($s,CURLINFO_HTTP_CODE) . "\n";
    exit( 3 );
}

if( !( $routers = json_decode( $json ) ) ) {
    echo "UNKNOWN: could not decode JSON response from API\n";
    exit( 3 );
}

if( !count( $routers ) ) {
    echo "UNKNOWN: no routers configured in IXP Manager?\n";
    exit( 3 );
}

$ok      = [];
$bad     = [];
$unknown = [];

foreach( $routers as $handle => $r ) {
    if( !$r['last_updated_unix'] ) {
        $unknown[] = $handle;
    } else if( time()-$threshold > $r['last_updated_unix'] ) {
        $bad[] = $handle;
    } else {
        $ok = $handle;
    }
}

$resp = '';
if( count( $bad ) ) {
    $resp .= 'ERROR: the following router(s) have not updated in more than ' . $threshold . 'secs: ' . implode( ', ', $bad ) . '. ';
}
if( count( $unknown ) ) {
    $resp .= 'WARNING: the following router(s) have never updated: ' . implode( ', ', $unknown ) . '. ';
}
if( count( $ok ) ) {
    $resp .= 'OK: the following router(s) have updated within the last ' . $threshold . 'secs: ' . implode( ', ', $ok ) . '. ';
}

echo $resp . "\n";

if( count( $bad ) ) {
    exit(2);
}
if( count( $unknown ) ) {
    exit(1);
}

exit(0);
