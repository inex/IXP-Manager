#! /bin/bash
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
#

KEY="daEpzL0faxeFHDPGP3JmXDutMQTMZxOlzk7LErYnIGUBK2Zt"
URL="http://ixpmanager-www/api/v4/router/gen-config"
URL_DONE="http://ixpmanager-www/api/v4/router/updated"
ETCPATH="/usr/local/etc/bird"
RUNPATH="/var/run/bird"
LOGPATH="/var/log/bird"
BIN="/usr/local/sbin/bird"

# Parse arguments
export DEBUG=0

function show_help {
    echo "$0 [-d] -h <handle> [-?]"
}


while getopts "?qdh:" opt; do
    case "$opt" in
        \?)
            show_help
            exit 0
            ;;
        d)  export DEBUG=1
            ;;
        h)  handle=$OPTARG
            ;;
    esac
done

if [[ -z "$handle" ]]; then
    echo ERROR: handle is required
    exit 1
fi

mkdir -p $ETCPATH
mkdir -p $LOGPATH
mkdir -p $RUNPATH

cfile="${ETCPATH}/bird-${handle}.conf"
dest="${cfile}.$$"
socket="${RUNPATH}/bird-${handle}.ctl"

cmd="curl --fail -s -H \"X-IXP-Manager-API-Key: ${KEY}\" ${URL}/${handle} >${dest}"

if [[ $DEBUG -eq 1 ]]; then echo $cmd; fi
eval $cmd

# We want to be safe here so check the generated file to see whether it
# looks valid

if [[ $? -ne 0 ]]; then
    echo "ERROR: non-zero return from curl when generating $dest"
    exit 2
fi

if [[ ! -e $dest || ! -s $dest ]]; then
    echo "ERROR: $dest does not exist or is zero size"
    exit 3
fi

if [[ $( cat $dest | grep "protocol bgp pb_" | wc -l ) -lt 2 ]]; then
    echo "ERROR: fewer than 2 BGP protocol definitions in config file $dest - something has gone wrong..."
    exit 4
fi

# parse and check the config
cmd="${BIN} -p -c $dest"
if [[ $DEBUG -eq 1 ]]; then echo $cmd; fi
eval $cmd &>/dev/null
if [[ $? -ne 0 ]]; then
    echo "ERROR: non-zero return from bird when parsing $dest"
    exit 7
fi

# config file should be okay; back up the current one
if [[ -e ${cfile} ]]; then
    cp "${cfile}" "${cfile}.old"
fi
mv $dest $cfile

# are we running or do we need to be started?
cmd="${BIN}c -s $socket show memory"
if [[ $DEBUG -eq 1 ]]; then echo $cmd; fi
eval $cmd &>/dev/null

if [[ $? -ne 0 ]]; then
    cmd="${BIN} -c ${cfile} -s $socket"

    if [[ $DEBUG -eq 1 ]]; then echo $cmd; fi
    eval $cmd &>/dev/null

    if [[ $? -ne 0 ]]; then
        echo "ERROR: bird was not running for $dest and could not be started"
        exit 5
    fi
else
    cmd="${BIN}c -s $socket configure"
    if [[ $DEBUG -eq 1 ]]; then echo $cmd; fi
    eval $cmd &>/dev/null

    if [[ $? -ne 0 ]]; then
        echo "ERROR: Reconfigure failed for $dest"

        if [[ -e ${cfile}.old ]]; then
            echo "Trying to revert to previous"
            mv ${cfile}.conf $dest
            mv ${cfile}.old ${cfile}
            cmd="${BIN}c -s $socket configure"
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

# tell IXP Manager the router has been updated:
cmd="curl -s -X POST -H \"X-IXP-Manager-API-Key: ${KEY}\" ${URL_DONE}/${handle} >/dev/null"
if [[ $DEBUG -eq 1 ]]; then echo $cmd; fi
eval $cmd

if [[ $? -ne 0 ]]; then
    echo "Warning - could not inform IXP Manager via updated API"
fi

exit 0
