#!/bin/sh

if [ $# -ne 4 ]; then
    echo "IXP Manager - A web application to assist in the management of IXPs"
    echo "Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee."
    echo
    echo "gen-sql-ipv6-decimal-inserts.sh - a utility script to generate SQL INSERT statements"
    echo "    to populate the IPv6 table with DECIMAL NUMBERS ONLY. useful for making like for"
    echo "    last octet addresses with IPv4."
    echo
    echo "Usage: $0 [vlanid] [start of address] [start octet] [end octet]"
    echo
    echo "E.g. $0 1 2001:db8:85a3::8a2e:370: 10 20"
    echo
fi;

for i in `seq $3 $4`; do
    echo "INSERT INTO \`ipv6address\` ( \`address\`, \`vlanid\` ) VALUES ( '$2$i', '$1' );"
done

 