#!/bin/sh

if [ $# -ne 4 ]; then
    echo "IXP Manager - A web application to assist in the management of IXPs"
    echo "Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee."
    echo
    echo "gen-sql-ipv4-inserts.sh - a utility script to generate SQL INSERT statements"
    echo "    to populate the IPv4 table."
    echo
    echo "Usage: $0 [vlanid] [start of address] [start octet] [end octet]"
    echo
    echo "E.g. $0 1 192.168.0. 10 20"
    echo
fi;

for i in `seq $3 $4`; do
    echo "INSERT INTO \`ipv4address\` ( \`address\`, \`vlanid\` ) VALUES ( '$2$i', '$1' );"
done

 