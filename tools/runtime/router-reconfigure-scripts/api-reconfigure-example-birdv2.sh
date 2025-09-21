#! /usr/bin/env bash
#
# Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee.
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

###########################################################################################
###########################################################################################
###
### CONFIGURE ME HERE
###
### This is where YOU need to set your specific IXP Manager installation details.
### Typically you only need to edit the first three settings below.
###
###########################################################################################
###########################################################################################

APIKEY="your-api-key"
URLROOT="https://ixp.example.com"

# prevent errors by limiting this server/script to the following space separated handles
ALLOWED_HANDLES="rs1-ipv4 rs1-ipv6"


# --- the following should be fine on a typical Debian / Ubuntu system:

URL_LOCK="${URLROOT}/api/v4/router/get-update-lock"
URL_CONF="${URLROOT}/api/v4/router/gen-config"
URL_RELEASE="${URLROOT}/api/v4/router/release-update-lock"
URL_DONE="${URLROOT}/api/v4/router/updated"

BIRDBIN="/usr/sbin/bird"
ETCPATH="/usr/local/etc/bird"
RUNPATH="/var/run/bird"
LOGPATH="/var/log/bird"
LOCKPATH="/tmp/ixp-manager-locks"
LOCK="${LOCKPATH}/$(basename $0).lock"








###########################################################################################
###########################################################################################
###
### FUNCTIONS
###
###########################################################################################
###########################################################################################


## Get a lock, if enabled, for the router from IXP Manager
##
## Globals used: $LOCKING_ENABLED, $APIKEY, $URL_LOCK
## Inheritied variables: $handle
function get_ixpmanager_lock() {

    local cmd

    if [[ $LOCKING_ENABLED -ne 1 ]]; then
       debug "[fn get_ixpmanager_lock] skipping lock, disabled"
        return 0
    fi

    cmd="curl --fail -s -X POST -H \"X-IXP-Manager-API-Key: ${APIKEY}\" ${URL_LOCK}/${handle} >/dev/null"

    debug "[fn get_ixpmanager_lock] $cmd"
    eval $cmd

    if [[ $? -ne 0 ]]; then
        verbose "[CANNOT LOCK] " "ERROR" "NL"
        echo "ABORTING: router $handle not available for update"
        exit 200
    fi

    verbose "[LOCKED] " "OK"

    return 0
}


## Release a lock, if enabled, for the router from IXP Manager
##
## Globals used: $LOCKING_ENABLED, $APIKEY, $URL_RELEASE
## Inheritied variables: $handle
function release_ixpmanager_lock() {
    ### Tell IXP Manager that the config never started and release the lock

    local cmd

    if [[ $LOCKING_ENABLED -ne 1 ]]; then
        debug "[fn release_ixpmanager_lock] skipping unlock, disabled"
        return 0
    fi

    cmd="curl --fail -s -X POST -H \"X-IXP-Manager-API-Key: ${APIKEY}\" ${URL_RELEASE}/${handle} >/dev/null"
    debug "[fn release_ixpmanager_lock] $cmd"

    until eval $cmd; do
        verbose "[UNLOCKING...] " "WARNING"
        sleep 60
    done

    verbose "[UNLOCKED] " "OK"

}


## Mark update successfully done and release the lock, if enabled
##
## Globals used: $LOCKING_ENABLED, $APIKEY, $URL_DONE
## Inheritied variables: $handle
function notify_ixpmanager_done() {
    ### Tell IXP Manager that the config completed

    local cmd

    cmd="curl --fail -s -X POST -H \"X-IXP-Manager-API-Key: ${APIKEY}\" ${URL_DONE}/${handle} >/dev/null"
    debug "[fn notify_ixpmanager_done] $cmd"

    until eval $cmd; do
        verbose "[NOTIFYING IXPMANAGER...] " "WARNING"
        sleep 60
    done

    verbose "[IXPMANAGER NOTIFIED] " "OK"

}


## Get a lock so this script can only run once at a time
##
## Globals used: $LOCK
function acquire_script_lock() {
  if [ -f "${LOCK}" ]; then
    another_locked_instance
  else
    debug "[fn acquire_script_lock] acquiring script lock"
    echo $$ > "${LOCK}"
    trap remove_lock EXIT
  fi
}

## Release the script lock (a lock so this script can only run once at a time)
##
## Globals used: $LOCK
function remove_lock() {
    debug "[fn remove_lock] remove script lock"
    rm -f "$LOCK"
}

## Announce that another script is running and exit
##
## Globals used: $LOCK
function another_locked_instance() {
    debug "[fn another_locked_instance]"
    colourize "ERROR" "There is another instance running and locked via ${LOCK}, exiting"
    echo
    exit 1
}


## Get (and check) router configuration from IXP Manager
##
## Globals used: $APIKEY, $URL_CONF, $BIRDBIN
## Inheritied variables: $handle
## Passed parameters:
##   $1 - destination file
function get_configuration() {

    local cmd dest
    dest=$1

    cmd="curl --fail -s -H \"X-IXP-Manager-API-Key: ${APIKEY}\" ${URL_CONF}/${handle} >${dest}"

    debug "[fn get_configuration] $cmd"
    eval $cmd

    # We want to be safe here so check the generated file to see whether it
    # looks valid
    if [[ $? -ne 0 ]]; then
        verbose "[CONFIGURATION NOT DOWNLOADED] " "ERROR" "NL"
        echo "ERROR: non-zero return from curl for $handle when generating $dest"
        release_ixpmanager_lock
        exit 2
    fi

    if [[ ! -e $dest || ! -s $dest ]]; then
        verbose "[CONFIGURATION NOT DOWNLOADED] " "ERROR" "NL"
        echo "ERROR: $dest does not exist or is zero size for $handle"
        release_ixpmanager_lock
        exit 3
    fi

    if [[ $( cat $dest | grep "END_OF_CONFIG_MARKER_FOR_${handle}" | wc -l ) -ne 1 ]]; then
        verbose "[CONFIGURATION CORRUPT] " "ERROR" "NL"
        echo "ERROR: END_OF_CONFIG_MARKER_FOR_${handle} not found in config file $dest - something has gone wrong..."
        # do not release the lock - this could be a proper issue
        exit 4
    fi

    # parse and check the config
    cmd="${BIRDBIN} -p -c $dest"
    debug "[fn get_configuration] $cmd"
    eval $cmd &>/dev/null
    if [[ $? -ne 0 ]]; then
        verbose "[CONFIGURATION INVALID] " "ERROR" "NL"
        echo "ERROR: non-zero return from ${BIRDBIN} when parsing $dest"
        # do not release the lock - this could be a proper issue
        exit 7
    fi

    verbose "[CONFIGURATION DOWNLOADED] " "OK"
    return 0
}

## See if we need, or are being forced, to reload
##
## Globals used: $URL_CONF, $FORCE_RELOAD
## Passed parameters:
##   $1 - BIRD configuration file
##   $2 - destination file
function determine_if_reload_is_requred() {

    local cfile dest reload_required DIFF

    reload_required=1
    cfile=$1
    dest=$2

    if [[ -f $cfile ]]; then
        cat $cfile    | egrep -v '^#.*$' >${cfile}.filtered
        cat $dest     | egrep -v '^#.*$' >${dest}.filtered

        diff ${cfile}.filtered ${dest}.filtered >/dev/null
        DIFF=$?

        rm -f ${cfile}.filtered ${dest}.filtered

        if [[ $DIFF -eq 0 ]]; then
            reload_required=0
            rm -f $dest
        else
            # back up the current one and replace
            cp "${cfile}" "${cfile}.old"
            mv $dest $cfile
        fi
    else
        mv $dest $cfile
    fi

    debug "[fn determine_if_reload_is_requred] \$reload_required=${reload_required}"

    # are we forcing a reload?
    if [[ $FORCE_RELOAD -eq 1 ]]; then
        reload_required=1
        debug "[fn determine_if_reload_is_requred] reload enforced by script switch"
    fi

    return $reload_required
}

## Check to see if the BIRD daemon is running
##
## Globals used: $BIRDBIN
## Passed parameters:
##   $1 - BIRD daemon socket
function is_bird_running() {
    local cmd bird_running socket

    socket=$1

    cmd="${BIRDBIN}c -s $socket show memory"
    eval $cmd &>/dev/null
    bird_running=$?

    debug "[fn is_bird_running] $cmd \$bird_running=${bird_running}"

    if [[ $bird_running -ne 0 ]]; then
        verbose "[BIRD NOT RUNNING] " "WARNING"
    fi

    #NB: value of $bird_running is zero if it is running
    return $bird_running
}

## Start BIRD
##
## Globals used: $BIRDBIN
## Passed parameters:
##   $1 - BIRD configuration file
##   $2 - BIRD daemon socket
function start_bird() {
    local cmd cfile socket

    cfile=$1
    socket=$2

    cmd="${BIRDBIN} -c ${cfile} -s $socket"

    debug "[fn start_bird] $cmd"
    eval $cmd &>/dev/null

    if [[ $? -ne 0 ]]; then
        verbose "[BIRD NOT STARTED] " "ERROR" "NL"
        echo "ERROR: ${BIRDBIN} was not running for $dest and could not be started"
        # do not release the lock - this could be a proper issue
        exit 5
    fi

    return 0
}

## Reconfigure a running BIRD daemon
##
## Globals used: $BIRDBIN
## Passed parameters:
##   $1 - configuration file
##   $2 - socket
##   $3 - download destination file
function reconfigure_bird() {
    local cmd cfile socket dest

    cfile=$1
    socket=$2
    dest=$3

    cmd="${BIRDBIN}c -s $socket configure"
    debug "[fn reconfigure_bird] $cmd"
    eval $cmd &>/dev/null

    if [[ $? -ne 0 ]]; then
        verbose "[RECONFIGURE FAILED] " "ERROR" "NL"
        echo "ERROR: Reconfigure failed for $handle/$dest"

        # do not release the lock - this could be a proper issue

        if [[ -e ${cfile}.old ]]; then
            echo "  -> Trying to revert to previous"
            mv ${cfile} ${dest}.failed
            mv ${cfile}.old ${cfile}
            cmd="${BIRDBIN}c -s $socket configure"
            debug "[fn reconfigure_bird] $cmd"
            eval $cmd &>/dev/null
            if [[ $? -eq 0 ]]; then
                echo "  -> Successfully reverted"
            else
                echo "  -> Reversion failed"
                exit 6
            fi
        fi
    fi

    return 0
}

## Colour a string and output it
## Parameters:
##   $1 - colour code - ERROR, WARNING or OK
##   $2 - message (no new line emitted)
function colourize() {

    local type message colour

    type=$1
    message=$2

    case "$type" in
        "ERROR")
            colour="\033[0;31m";;
        "WARNING")
            colour="\033[0;33m";;
        "OK")
            colour="\033[0;32m";;
        *)
            colour="\033[0m";;
    esac

    printf "${colour}${message}\033[0m"
}

function debug() {
    if [[ $DEBUG -eq 1 ]]; then echo "DEBUG: ${1}"; fi
}

function verbose() {

    if [[ $VERBOSE -eq 1 ]]; then

        if [[ -n $2 ]]; then
            colourize "${2}" "${1}"
        else
            echo -n "${1}"
        fi

        if [[ -n $3 ]]; then
            echo
        fi
    fi

}


###########################################################################################
###########################################################################################
###
### Parse command line arguments, handle and set some necessary variables
###
###########################################################################################
###########################################################################################

# Parse arguments
export DEBUG=0
export VERBOSE=0
export FORCE_RELOAD=0
export LOCKING_ENABLED=1

function show_help {
    cat <<END_HELP
$0 [-d] [-f] [-s] [-v] -h <handle> [-?]

    -d    Enable debug mode, show all commands as they are run
    -v    Enable verbose mode (script has no output by default on successful run)
    -f    Force reload of BIRD, even if config is unchnaged
    -h    Router handle to update (required)
    -s    Skip lock - downloads config, even if router is paused or locked

END_HELP
}


while getopts "?dfsvh:" opt; do
    case "$opt" in
        \?)
            show_help
            exit 0
            ;;
        d)  export DEBUG=1
            ;;
        f)  export FORCE_RELOAD=1
            ;;
        h)  handle=$OPTARG
            ;;
        s) export LOCKING_ENABLED=0
            ;;
        v)  export VERBOSE=1
            ;;
    esac
done

if [[ -z "$handle" ]]; then
    echo ERROR: handle is required
    exit 1
fi

# check we're allowed to use this handle here
if [[ "$ALLOWED_HANDLES" != *"$handle"* ]]; then
  echo "$handle not allowed here. Should be one of $ALLOWED_HANDLES."
  exit 1
fi

# if debug enabled, then verbose should be too
if [[ $DEBUG -eq 1 ]] && [[ $VERBOSE -eq 1 ]]; then
    VERBOSE=0
    echo "WARNING: either verbose or debug mode should be use, verbose disabled"
fi

if [[ $VERBOSE -eq 1 ]]; then
    verbose "${handle}: "
fi


mkdir -p $ETCPATH
mkdir -p $LOGPATH
mkdir -p $RUNPATH
mkdir -p $LOCKPATH


cfile="${ETCPATH}/bird-${handle}.conf"
dest="${cfile}.$$"
socket="${RUNPATH}/bird-${handle}.ctl"


###########################################################################################
###########################################################################################
###
### Main script function
###
###########################################################################################
###########################################################################################

# only one instance of this script can run at a time
acquire_script_lock

# Get a lock from IXP Manager to update the router
# aborts with exit code 200 if unavailable
get_ixpmanager_lock

# Get and validate the configuration from IXP Manager
# aborts with various codes if there are issues
get_configuration $dest

# Apply the configuration and start Bird if necessary
determine_if_reload_is_requred $cfile $dest
RELOAD_REQUIRED=$?

# are we running or do we need to be started?
is_bird_running $socket

if [[ $? -ne 0 ]]; then
    start_bird $cfile $socket
    verbose "[BIRD STARTED] " "OK"
    if [[ $VERBOSE -ne 1 ]]; then
        echo "NOTICE: bird daemon was not running and has been started"
    fi
elif [[ $RELOAD_REQUIRED -eq 1 ]]; then
    reconfigure_bird $cfile $socket $dest
    verbose "[BIRD RECONFIGURED] " "OK"
else
    verbose "[BIRD RUNNING] [NO RECONFIG REQUIRED] " "OK"
fi

# Tell IXP Manager that the config is complete and release the lock
notify_ixpmanager_done

verbose "" "OK" "NL"

# all done
exit 0
