#! /usr/bin/env bash
#
# Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
# All Rights Reserved.
#
# This file is part of IXP Manager.
#
# IXP Manager is free software: you can redistribute it and/or modify it
# under the terms of the GNU General Public License as published by the Free
# Software Foundation, version 2.0 of the License.
#
# IXP Manager is distributed in the hope that it will be useful, but WITHOUT
# ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
# FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
# more details.
#
# You should have received a copy of the GNU General Public License v2.0
# along with IXP Manager.  If not, see:
#
# http://www.gnu.org/licenses/gpl-2.0.html

# Example script for updating DNS zone files with ARPA entries from IXP Manager

KEY="your-ixp-manager-api-keu"
URL="https://ixp.example.com/api/v4/dns/arpa"
ZONEPATH="/usr/local/etc/namedb/zones/includes"
VLANIDS="1 2 3"
PROTOCOLS="4 6"
SOAPATH="/usr/local/etc/namedb/zones/includes"
SOAFILES="soa-0-2-192.in-addr.arpa.inc"
CHECKZONE="/usr/local/sbin/named-checkzone"
ZONEFILES=("/usr/local/etc/namedb/zones/0.2.192.in-addr.arpa" "/usr/local/etc/namedb/zones/0.0.0.0.8.b.d.0.1.0.0.2.ip6.arpa")
ZONES=("0.2.192.in-addr.arpa" "0.0.0.0.8.b.d.0.1.0.0.2.ip6.arpa")
ETCBIND="/usr/local/etc/namedb/"
SERIAL_REGEX="([0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9])[[:space:]]+;[[:space:]]+Serial"
SERIAL=""

mkdir -p $ZONEPATH
mkdir -p $SOAPATH

function calculate_serial () {
    local FILE=$1
    local SERIAL_LINE

    if [ "$( egrep $SERIAL_REGEX $FILE | wc -l )" -ne 1 ]; then
        echo No or more than one serial found, this should not happen!
        exit 1
    fi

    SERIAL_LINE=$( egrep $SERIAL_REGEX $FILE )

    SERIAL_REGEX="([0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9])"
    if [[ $SERIAL_LINE =~ $SERIAL_REGEX ]]; then

        SERIAL=${BASH_REMATCH[1]}

        if [ "${SERIAL:0:8}" = "$( date +%Y%m%d)" ]; then
            SERIAL=$(( $SERIAL + 1 ))

            if [ "${SERIAL:0:8}" != "$( date +%Y%m%d)" ]; then
                # no more increments left for today, wait 'til tomorrow :-P
                SERIAL="$( date +%Y%m%d)99"
            fi
        else
            SERIAL="$( date +%Y%m%d)00"
        fi

    else
        echo No serial found in secondary test, this should not happen!
        exit 1
    fi

    # SERIAL set in global variable.
    return 0
}


for v in $VLANIDS; do
    for p in $PROTOCOLS; do

        cmd="/usr/local/bin/curl --fail -s -H \"X-IXP-Manager-API-Key: ${KEY}\" ${URL}/${v}/${p} >$ZONEPATH/reverse-vlan-$v-ipv$p.include.$$"
        eval $cmd

        if [[ $? -ne 0 ]]; then
            echo "ERROR: non-zero return from DNS ARPA API call for vlan ID $v with protocol $p"
            continue
        fi

        diff $ZONEPATH/reverse-vlan-$v-ipv$p.include $ZONEPATH/reverse-vlan-$v-ipv$p.include.$$ >/dev/null
        if [[ $? -eq 0 ]]; then
            rm $ZONEPATH/reverse-vlan-$v-ipv$p.include.$$
            continue
        fi

        mv $ZONEPATH/reverse-vlan-$v-ipv$p.include.$$ $ZONEPATH/reverse-vlan-$v-ipv$p.include

    done
done

for f in $SOAFILES; do
    calculate_serial $SOAPATH/$f
    sed -E -i '.bup' "s/[0-9]{10}[[:space:]]+;[[:space:]]+Serial/${SERIAL}      ; Serial/" $SOAPATH/$f
done

checkzone=0
for i in ${!ZONES[@]}; do
    output=$(${CHECKZONE} -w $ETCBIND ${ZONES[$i]} ${ZONEFILES[$i]})
    if [[ $? -ne 0 ]]; then
	checkzone=1
        echo "Error in Zone: ${ZONES[$i]}"
        echo $output
    fi
done

if [[ $checkzone -eq 0 ]]; then
    /usr/local/sbin/rndc reload >/dev/null
fi
