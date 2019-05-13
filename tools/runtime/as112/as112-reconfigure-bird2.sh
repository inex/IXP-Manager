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
#
# Barry O'Donovan <barry@islandbridgenetworks.ie> 2016

# Example script for updating AS112 Bird BGP configs


# For keys, see: http://docs.ixpmanager.org/features/api/
KEY="my-ixp-manager-api-key"
URL="https://ixp.example.com/api/v4/router/gen-config"
URL_DONE="https://ixp.example.com/api/v4/router/updated"
ETCPATH="/usr/local/etc/bird"
RUNPATH="/var/run/bird"
LOGPATH="/var/log/bird"
BIN="/usr/sbin/bird"

mkdir -p $ETCPATH
mkdir -p $LOGPATH
mkdir -p $RUNPATH

if [[ -n $1 && $1 = '--quiet' ]]; then
    export QUIET=1
else
    export QUIET=0
    echo -en "\nIXP AS112 BGPd Lisenters\n==============================\n\n"
    echo -e "Verbose mode enabled. Issue --quiet for non-verbose mode (--debug also available)\n"
fi

if [[ -n $1 && $1 = '--debug' ]]; then
    export QUIET=1
    export DEBUG=1
else
    export DEBUG=0
fi

function log {
    if [[ $QUIET -eq 0 && $DEBUG -eq 0 ]]; then
        echo -en $1
    fi
}

# These are the handles as configured in your IXP Manager - see: http://docs.ixpmanager.org/features/routers/
#
# This script assumes v6 versions end in -ipv6

for handle in my-as112-router1-ipv4 my-as112-router1-ipv6; do

    log  "Instance for ${handle}:\tConfig: "

    cmd="curl --fail -s -H \"X-IXP-Manager-API-Key: ${KEY}\" ${URL}/${handle} >${ETCPATH}/bird-${handle}.conf"

    if [[ $DEBUG -eq 1 ]]; then echo $cmd; fi
    eval $cmd

    if [[ $? -eq 0 ]]; then
        log "DONE \tDaemon: "
    else
        log "ERROR\n"
        continue
    fi

    # are we running or do we need to be started?
    cmd="${BIN}c -s ${RUNPATH}/bird-${handle}.ctl configure"
    if [[ $DEBUG -eq 1 ]]; then echo $cmd; fi
    eval $cmd &>/dev/null

    if [[ $? -eq 0 ]]; then
        log "RECONFIGURED \tIXP Manager Updated:"
    else
        cmd="${BIN} -c ${ETCPATH}/bird-${handle}.conf -s ${RUNPATH}/bird-${handle}.ctl"

        if [[ $DEBUG -eq 1 ]]; then echo $cmd; fi
        eval $cmd &>/dev/null

        if [[ $? -eq 0 ]]; then
            log "STARTED \tIXP Manager Updated:"
        else
            log "ERROR\n"
            continue
        fi
    fi

    # tell IXP Manager the router has been updated:
    cmd="curl -s -X POST -H \"X-IXP-Manager-API-Key: ${KEY}\" ${URL_DONE}/${handle} >/dev/null"
    if [[ $DEBUG -eq 1 ]]; then echo $cmd; fi
    eval $cmd

    if [[ $? -eq 0 ]]; then
        log "DONE"
    else
        log "ERROR\n"
        continue
    fi

    log "\n"
done

log "\n"
