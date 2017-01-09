#! /bin/bash
#
# Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee.
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
#
# Example script used by INEX to rebuild all Bird route servers via API on demand.
#
# Author: Barry O'Donovan <barry@opensolutions.ie>

# Example script used by INEX to regularly (or on demand) rebuild and
# reconfigure our Bird route server configuration.

# NB: THIS IS AN OLDER VERSION OF THE SCRIPT FOR USE ON THE OLD API (apiv1).
# THIS IS OFFICIAL DEPRECATED AND WILL BE REMOVED IN A LATER RELEASE.


# Edit as appropriate and run as:
#    api-reconfigure-example.sh -v <vlan id> -p <protocol 4/6>

KEY="your-api-key"
URL="https://www.ixp.com/apiv1/router/server-conf/key/${KEY}"
ETCPATH="/usr/local/etc/bird"
RUNPATH="/var/run/bird"
BIN="/usr/sbin/bird"
CONFNAME="rs2"

# Parse arguments
export DEBUG=0

function show_help {
    echo "$0 [-d] -v <vlan id> -p <protocol 4/6> [-h|-?]"
}


while getopts "h?qdv:p:" opt; do
    case "$opt" in
        h|\?)
            show_help
            exit 0
            ;;
        d)  export DEBUG=1
            ;;
        v)  vlanid=$OPTARG
            ;;
        p)  proto=$OPTARG
            ;;
    esac
done

if [[ -z "$vlanid" ]]; then
    echo ERROR: VLAN ID parameter -v is required
    exit 1
fi

if [[ -z "$proto" ]]; then
    echo ERROR: Protocol parameter -p is required
    exit 1
fi

mkdir -p $RUNPATH
mkdir -p $ETCPATH

# to generate the appropriate bird commands:
if [[ $proto = "6" ]]; then
    PROTOCOL="6"
else
    PROTOCOL=""
fi

dest="${ETCPATH}/bird-vlanid${vlanid}-ipv${proto}.conf.$$"

cmd="wget -q -O $dest \
    \"${URL}/target/bird/vlanid/${vlanid}/proto/${proto}/config/${CONFNAME}-vlanid${vlanid}-ipv${proto}\""

if [[ $DEBUG -eq 1 ]]; then echo $cmd; fi
eval $cmd

# We want to be bullet proof here so we really want to check the generated file to try
# and ensure it is valid

if [[ $? -ne 0 ]]; then
    echo "ERROR: non-zero return from wget when generating $dest"
    exit 2
fi

if [[ ! -e $dest || ! -s $dest ]]; then
    echo "ERROR: $dest does not exist or is zero size"
    exit 3
fi

if [[ $( cat $dest | grep "protocol bgp pb_" | wc -l ) -lt 2 ]]; then
    echo "ERROR: <2 BGP protocol defintions in config file $dest - something has gone wrong..."
    exit 4
fi

# parse and check the config
cmd="${BIN}${PROTOCOL} -p -c $dest"
if [[ $DEBUG -eq 1 ]]; then echo $cmd; fi
eval $cmd &>/dev/null
if [[ $? -ne 0 ]]; then
    echo "ERROR: non-zero return from bird${PROTOCOL} when parsing $dest"
    exit 7
fi

# config file should be okay; back up the current one
if [[ -e ${ETCPATH}/bird-vlanid${vlanid}-ipv${proto}.conf ]]; then
    cp "${ETCPATH}/bird-vlanid${vlanid}-ipv${proto}.conf" "${ETCPATH}/bird-vlanid${vlanid}-ipv${proto}.conf.old"
fi
mv $dest ${ETCPATH}/bird-vlanid${vlanid}-ipv${proto}.conf

# are we running or do we need to be started?
cmd="${BIN}c${PROTOCOL} -s ${RUNPATH}/bird-vlanid${vlanid}-ipv${proto}.ctl show memory"
if [[ $DEBUG -eq 1 ]]; then echo $cmd; fi
eval $cmd &>/dev/null

if [[ $? -ne 0 ]]; then
    cmd="${BIN}${PROTOCOL} -c ${ETCPATH}/bird-vlanid${vlanid}-ipv${proto}.conf -s ${RUNPATH}/bird-vlanid${vlanid}-ipv${proto}.ctl"

    if [[ $DEBUG -eq 1 ]]; then echo $cmd; fi
    eval $cmd &>/dev/null

    if [[ $? -ne 0 ]]; then
        echo "ERROR: bird{$PROTOCOL} was not running for $dest and could not be started"
        exit 5
    fi
else
    cmd="${BIN}c${PROTOCOL} -s ${RUNPATH}/bird-vlanid${vlanid}-ipv${proto}.ctl configure"
    if [[ $DEBUG -eq 1 ]]; then echo $cmd; fi
    eval $cmd &>/dev/null

    if [[ $? -ne 0 ]]; then
        echo "ERROR: Reconfigure failed for $dest"

        if [[ -e ${ETCPATH}/bird-vlanid${vlanid}-ipv${proto}.conf.old ]]; then
            echo "Trying to revert to previous"
            mv ${ETCPATH}/bird-vlanid${vlanid}-ipv${proto}.conf $dest
            mv ${ETCPATH}/bird-vlanid${vlanid}-ipv${proto}.conf.old ${ETCPATH}/bird-vlanid${vlanid}-ipv${proto}.conf
            cmd="${BIN}c${PROTOCOL} -s ${RUNPATH}/bird-vlanid${vlanid}-ipv${proto}.ctl configure"
            if [[ $DEBUG -eq 1 ]]; then echo $cmd; fi
            eval $cmd &>/dev/null
            if [[ $? -eq 0 ]]; then
                echo Successfully reverted
            else
                echo Reversion failed
                exit 6
            fi
        fi
    fi

fi

exit 0
