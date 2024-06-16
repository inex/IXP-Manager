#! /usr/bin/env bash

# Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee.
# All Rights Reserved.
#
# This file is part of IXP Manager.
#
# IXP Manager is free software: you can redistribute it and/or modify it
# under the terms of the GNU General Public License as published by the Free
# Software Foundation, version v2.0 of the License.
#
# IXP Manager is distributed in the hope that it will be useful, but WITHOUT
# ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
# FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
# more details.
#
# You should have received a copy of the GNU General Public License v2.0
# along with IXP Manager.  If not, see:
#
# http://www.gnu.org/licenses/gpl-2.0.html

###########################################################################################
###########################################################################################
###
### CONFIGURE ME HERE
###
### This is where YOU need to set your specific IXP Manager installation details.
### Typically you only need to edit the first three.
###
###########################################################################################
###########################################################################################

HANDLES="handle1-ipv4 handle1-ipv6"
APIKEY="your-api-key"
URLROOT="https://ixp.example.com"
BIRDBIN="/usr/sbin/bird"


# --- the following should be fine on a typical Debian / Ubuntu system:

URL_LOCK="${URLROOT}/api/v4/router/get-update-lock"
URL_CONF="${URLROOT}/api/v4/router/gen-config"
URL_DONE="${URLROOT}/api/v4/router/updated"

ETCPATH="/usr/local/etc/bird"
RUNPATH="/var/run/bird"
LOGPATH="/var/log/bird"
LOCKPATH="/tmp/ixp-manager-locks"



###########################################################################################
###########################################################################################
###
### Parse command line arguments, handle and set some necessary variables
###
###########################################################################################
###########################################################################################

mkdir -p $ETCPATH
mkdir -p $LOGPATH
mkdir -p $RUNPATH
mkdir -p $LOCKPATH

if [[ -n $1 && $1 = '--quiet' ]]; then
    export QUIET=1
else
    export QUIET=0
    echo -en "\nRoute Collector BGPd Lisenters\n==============================\n\n"
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



###########################################################################################
###########################################################################################
###
### Script locking - only allow one instance of this script
###
###########################################################################################
###########################################################################################

SCRIPTNAME=$(basename "$0")
LOCK="${LOCKPATH}/${SCRIPTNAME}.lock"

remove_lock() {
    rm -f "$LOCK"
}

another_locked_instance() {
    echo "There is another instance running for ${SCRIPTNAME} and locked via ${LOCK}, exiting"
    exit 1
}

if [ -f "${LOCK}" ]; then
  another_locked_instance
else
  echo $$ > "${LOCK}"
  trap remove_lock EXIT
fi




for handle in $HANDLES; do

    # files:
    cfile="${ETCPATH}/bird-${handle}.conf"
    dest="${cfile}.$$"
    socket="${RUNPATH}/bird-${handle}.ctl"


    log  "Instance for ${handle}:\tLock: "

    ### Get a lock from IXP Manager to update the router
    cmd="curl --fail -s -X POST -H \"X-IXP-Manager-API-Key: ${APIKEY}\" ${URL_LOCK}/${handle} >/dev/null"

    if [[ $DEBUG -eq 1 ]]; then echo $cmd; fi
    eval $cmd

    if [[ $? -ne 0 ]]; then
        log "UNAVAILABLE\n"
        continue
    fi

    log  "LOCKED \tConfig: "

    ### Get the configuration from IXP Manager

    cmd="curl --fail -s -H \"X-IXP-Manager-API-Key: ${APIKEY}\" ${URL_CONF}/${handle} >${dest}"

    if [[ $DEBUG -eq 1 ]]; then echo $cmd; fi
    eval $cmd

    if [[ $? -eq 0 ]]; then
        log "DOWNLOADED \tReconfig: "
    else
        log "ERROR\n"
        continue
    fi


    if [[ ! -e $dest || ! -s $dest ]]; then
        echo "ERROR: $dest does not exist or is zero size"
        continue
    fi

    # parse and check the config
    cmd="${BIRDBIN} -p -c $dest"
    if [[ $DEBUG -eq 1 ]]; then echo $cmd; fi
    eval $cmd &>/dev/null
    if [[ $? -ne 0 ]]; then
        echo "ERROR: non-zero return from ${BIRDBIN} when parsing $dest"
        continue
    fi



    ### Apply the configuration and start Bird if necessary



    RELOAD_REQUIRED=1
    if [[ -f $cfile ]]; then
        cat $cfile    | egrep -v '^#.*$' >${cfile}.filtered
        cat $dest     | egrep -v '^#.*$' >${dest}.filtered

        diff ${cfile}.filtered ${dest}.filtered >/dev/null
        DIFF=$?

        rm -f ${cfile}.filtered ${dest}.filtered

        if [[ $DIFF -eq 0 ]]; then
            RELOAD_REQUIRED=0
            rm -f $dest
            log "UNCHANGED \tBIRD: "
        else
            # back up the current one and replace
            cp "${cfile}" "${cfile}.old"
            mv $dest $cfile
            log "CHANGED   \tBIRD: "
        fi
    fi

    # are we running or do we need to be started?
    cmd="${BIRDBIN}c -s $socket show memory"
    if [[ $DEBUG -eq 1 ]]; then echo $cmd; fi
    eval $cmd &>/dev/null

    if [[ $? -ne 0 ]]; then
        cmd="${BIRDBIN} -c ${cfile} -s $socket"

        if [[ $DEBUG -eq 1 ]]; then echo $cmd; fi
        eval $cmd &>/dev/null

        if [[ $? -ne 0 ]]; then
            echo "ERROR: ${BIRDBIN} was not running for $dest and could not be started"
            continue
        fi

        log "STARTED \tIXP Manager Updated: "

    elif [[ $RELOAD_REQUIRED -eq 1 ]]; then
        cmd="${BIRDBIN}c -s $socket configure"
        if [[ $DEBUG -eq 1 ]]; then echo $cmd; fi
        eval $cmd &>/dev/null

        if [[ $? -ne 0 ]]; then
            echo "ERROR: Reconfigure failed for $dest"

            if [[ -e ${cfile}.old ]]; then
                echo "Trying to revert to previous"
                mv ${cfile}.conf $dest
                mv ${cfile}.old ${cfile}
                cmd="${BIRDBIN}c -s $socket configure"
                if [[ $DEBUG -eq 1 ]]; then echo $cmd; fi
                eval $cmd &>/dev/null
                if [[ $? -eq 0 ]]; then
                    echo Successfully reverted
                else
                    echo Reversion failed
                    continue
                fi
            fi
        fi

        log "RECONFIGURED \tIXP Manager Updated: "

    else
        if [[ $DEBUG -eq 1 ]]; then
            echo "Bird running and no reload required so skipping configure";
        fi

        log "NO RECONFIG  \tIXP Manager Updated: "
    fi






    ### Tell IXP Manager that the config is complete and release the lock

    # tell IXP Manager the router has been updated:
    cmd="curl --fail -s -X POST -H \"X-IXP-Manager-API-Key: ${APIKEY}\" ${URL_DONE}/${handle} >/dev/null"
    if [[ $DEBUG -eq 1 ]]; then echo $cmd; fi

    until eval $cmd; do
        echo "Warning - could not inform IXP Manager via updated API - sleeping 60 secs and trying again"
        sleep 60
    done

    log "DONE\n"
done

log "\n"
