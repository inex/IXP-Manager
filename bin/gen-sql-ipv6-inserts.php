#!/usr/bin/php
<?php

if( count( $argv ) != 5 )
{
    echo "IXP Manager - A web application to assist in the management of IXPs\n";
    echo "Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee.\n";
    echo "\n";
    echo "gen-sql-ipv6-inserts.php - a utility script to generate SQL INSERT statements\n";
    echo "    to populate the IPv6 table.";
    echo "\n";
    echo "Usage: $0 [vlanid] [start of address] [last group start] [last group end]\n";
    echo "\n";
    echo "E.g. $0 1 2001:db8:85a3::8a2e:370: 0 ffff\n";
    echo "\n";
    exit(1);
}                                                                                                                                                                                           

$s = hexdec( $argv[3] );
$e = hexdec( $argv[4] );

for( $i = $s; $i <= $e; $i++ )
    echo "INSERT INTO `ipv6address` ( `address`, `vlanid` ) VALUES ( '{$argv[2]}" . dechex( $i ) ."', '{$argv[1]}' );\n";

   
