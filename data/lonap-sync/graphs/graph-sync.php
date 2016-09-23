#!/usr/bin/env php
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
                   
if( $argc < 5 )
{
    echo "ERROR: Missing Parameters.\nUsage: graph-sync.php --mrtg <filename> --rrd <filename> [--out-mrtg <file>]\n";
    exit( 1 );
}

$mrtg = false;
$rrd = false;
$out = "php://stdout";
for( $i = 1; $i < $argc; $i++ )
{
    switch( $argv[$i] )
    {
        case "--mrtg":
        case "-m":
            $mrtg = $argv[++$i];
            break;
         
        case "--rrd":
        case "-r":
            $rrd = $argv[++$i];
            break;
            
        case "--out-mrtg":
        case "-o":
            $out = $argv[++$i];
            break;
        
        default:
            echo "ERROR: Unknown option '{$argv[$i]}'. \n";
            echo "Usage: graph-sync.php --mrtg <filename> --rrd <filename> [--out-mrtg <file>]\n";
            exit( 1 );
    }
        
}

if( !$mrtg || !$rrd )
{
    echo "ERROR: Missing Parameters.\nUsage: graph-sync.php --mrtg <filename> --rrd <filename> [--out-mrtg <file>]\n";
    exit( 1 );
}

if( !( $handle = @fopen( $mrtg, "r" ) ) )
    die( "ERROR: cannot open $mrtg\n" );

//script overwrites $out file use a to append $out file.
$dir = dirname( $out );
if( !is_dir( $dir ) )
    mkdir( $dir, 0700, true );

if( !( $fout = @fopen( $out, "w" ) ) )
    die( "ERROR: cannot open $out\n" );

fputs( $fout, fgets( $handle, 4096 ) ); // copy (and skip) first line

$end = false;
$previous = false;

while( ( $buffer = fgets( $handle, 4096 ) ) !== false )
{
    $row = explode( ' ', $buffer );

    if( !$end )
    {
        $end = $row[0];
        $previous = $buffer;
        fputs( $fout, $buffer );
        continue;
    }

    if( $end < rrd_last( $rrd ) && $row[1] == "0" && $row[2] == "0" )
    {
        $avg = getData( $rrd, 'AVERAGE', $row[0], $end );
        $max = getData( $rrd, 'MAX', $row[0], $end );
            
        fputs( $fout, "{$row[0]} {$avg['in']} {$avg['out']} {$max['in']} {$max['out']}\n" );

        /*echo "MRTG: $buffer";
        echo "RRD:  {$end} {$avg['in']} {$avg['out']} {$max['in']} {$max['out']}\n";
 
        echo sprintf( "DIFF: {$end} %0.3f %0.3f %0.3f %0.3f\n",
            abs( ( ( $avg['in']  - $row[1] ) * 100 ) / $row[1] ),
            abs( ( ( $avg['out'] - $row[2] ) * 100 ) / $row[2] ),
            abs( ( ( $max['in']  - $row[3] ) * 100 ) / $row[3] ),
            abs( ( ( $max['out'] - $row[4] ) * 100 ) / $row[4] )
        );

        echo "\n";*/
    }
    else
        fputs( $fout, $buffer );

    $end = $row[0];
    $previous = $buffer;
}

if( !feof( $handle ) )
    echo "Error: unexpected fgets() fail\n";

fclose( $handle );
fclose( $fout );


function getData( $rrd, $type, $start, $end  )
{
    // centre around the start point
    $s = intval( $start - ( ( $end - $start ) / 2 ) );
    $e = $s + ( $end - $start );
    $r = $e - $s;

    // ensure start and end is divisable by the resolution
    $s = intval( $s / $r ) * $r;

    if( $e % $r != 0 )
        $e = intval( ceil( $e / $r ) ) * $r;

    $opts = [ $type, "--start", $s, "--end", $e, "--resolution", $r ];
    $dss = rrd_fetch( $rrd, $opts );

    // consolodate the values we find
    $data = [];
    $data['in']  = 0;
    $data['out'] = 0;

    if( $dss && isset( $dss['data']['ds0'] ) )
    {
        switch( $type )
        {
            case 'AVERAGE':
                foreach( $dss['data']['ds0'] as $ds )
                    $data['in'] += !is_nan( $ds ) ? $ds : 0;

                foreach( $dss['data']['ds1'] as $ds )
                    $data['out'] += !is_nan( $ds ) ? $ds : 0;

                $data['in']  /= count( $dss['data']['ds0'] );
                $data['out'] /= count( $dss['data']['ds1'] );


                return array_map( 'intval', $data );
                break;

            case 'MAX':
                foreach( $dss['data']['ds0'] as $ds )
                    if( $data['in'] < $ds ) $data['in'] = $ds;

                foreach( $dss['data']['ds1'] as $ds )
                    if( $data['out'] < $ds ) $data['out'] = $ds;

                return array_map( 'intval', $data );
                break;
        }
    }

    return false;
}
