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

APIKEY="r8sFfkGamCjrbbLC12yIoCJooIRXzY9CYPaLVz92GFQyGqLq"
URLROOT="http://127.0.0.1"

# prevent errors by limiting this server/script to the following space separated handles
ALLOWED_HANDLES="as112-vix1-ipv4 as112-vix1-ipv6 as112-vix2-ipv4 as112-vix2-ipv6"

# --- the following should be fine on a typical Debian / Ubuntu system:

URL_LOCK="${URLROOT}/api/v4/router/get-update-lock"
URL_CONF="${URLROOT}/api/v4/router/gen-config"
URL_RELEASE="${URLROOT}/api/v4/router/release-update-lock"
URL_DONE="${URLROOT}/api/v4/router/updated"

BIRD_RUN_USER=bird
BIRD_RUN_GROUP=bird

BIRDBIN="/usr/sbin/bird"
ETCPATH="/usr/local/etc/bird"
RUNPATH="/run/bird"
LOGPATH="/var/log/bird"
LOCKPATH="/tmp/ixp-manager-locks"



###########################################################################################
###########################################################################################
###
### Parse command line arguments, handle and set some necessary variables
###
###########################################################################################
###########################################################################################

# Parse arguments
export DEBUG=0
export FORCE_RELOAD=0
export LOCKING_ENABLED=1

function show_help {
    cat <<END_HELP
$0 [-d] [-f] [-s] -h <handle> [-i <systemd_service>] [-?]

    -d    Enable debug mode, show all commands as they are run
    -f    Force reload of BIRD, even if config is unchnaged
    -h    Router handle to update (required)
    -i    Name of BIRD systemd service. (e.g. bird-ipv4). If specified, BIRD will be started via systemd
    -s    Skip lock - reads config, even if router is paused or locked

END_HELP
}


while getopts "?dfsh:i:" opt; do
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
        i)  service=$OPTARG
            ;;                
        s)  export LOCKING_ENABLED=0
            ;;
    esac
done

if [[ -z "$handle" ]]; then
    echo ERROR: handle is required
    exit 1
fi

if [[ -z "$handle" ]]; then
    echo ERROR: handle is required
    exit 1
fi

# check we're allowed to use this handle here
if [[ "$ALLOWED_HANDLES" != *"$handle"* ]]; then
  echo "$handle not allowed here. Should be one of $ALLOWED_HANDLES."
  exit 1
fi

if [[ -z "$URLROOT" ]]; then
    echo ERROR: URLROOT is required
    exit 1
fi

if [[ -z "$APIKEY" ]]; then
    echo ERROR: APIKEY is required
    exit 1
fi

if [ -n "$BIRD_RUN_USER" ]; then
    if ! getent passwd $BIRD_RUN_USER >/dev/null; then
        echo "Configured user '$BIRD_RUN_USER' doesn't exist."
        exit 1
    fi
fi

if [ -n "$BIRD_RUN_GROUP" ]; then
    if ! getent group $BIRD_RUN_GROUP >/dev/null; then
        echo "Configured group '$BIRD_RUN_GROUP' doesn't exist."
        exit 1
    fi
fi


[ ! -f "$ETCPATH" ] && mkdir -p $ETCPATH ; chown --silent "$BIRD_RUN_USER:$BIRD_RUN_GROUP" "$ETCPATH" && chmod 775 "$ETCPATH"
[ ! -f "$LOGPATH" ] && mkdir -p $LOGPATH ; chown --silent "$BIRD_RUN_USER:$BIRD_RUN_GROUP" "$LOGPATH" && chmod 755 "$LOGPATH"
[ ! -f "$RUNPATH" ] && mkdir -p $RUNPATH ; chown --silent "$BIRD_RUN_USER:$BIRD_RUN_GROUP" "$RUNPATH" && chmod 755 "$RUNPATH"
[ ! -f "$LOCKPATH" ] && mkdir -p $LOCKPATH ; chown --silent "$BIRD_RUN_USER:$BIRD_RUN_GROUP" "$LOCKPATH" && chmod 775 "$LOCKPATH"


cfile="${ETCPATH}/bird-${handle}.conf"
dest="${cfile}.$$"
socket="${RUNPATH}/bird-${handle}.ctl"


###########################################################################################
###########################################################################################
###
### Script locking - only allow one instance of this script per handle
###
###########################################################################################
###########################################################################################

LOCK="${LOCKPATH}/${handle}.lock"

remove_lock() {
    rm -f "$LOCK"
}

another_locked_instance() {
    echo "There is another instance running for ${handle} and locked via ${LOCK}, exiting"
    exit 1
}

if [ -f "${LOCK}" ]; then
  another_locked_instance
else
  echo $$ > "${LOCK}"
  trap remove_lock EXIT
fi



###########################################################################################
###########################################################################################
###
### Get a lock from IXP Manager to update the router

release_ixpmanager_lock() {
  ### Tell IXP Manager that the config never started and release the lock

    if [[ $LOCKING_ENABLED -eq 1 && -n "$URL_RELEASE" ]]; then

        cmd="curl --fail -s -X POST -H \"X-IXP-Manager-API-Key: ${APIKEY}\" ${URL_RELEASE}/${handle} >/dev/null"
        if [[ $DEBUG -eq 1 ]]; then echo $cmd; fi

        until eval $cmd; do
            echo "Warning - could not release lock on IXP Manager via API - sleeping 60 secs and trying again"
            sleep 60
        done

    fi
}

if [[ $LOCKING_ENABLED -eq 1 ]]; then

    cmd="curl --fail -s -X POST -H \"X-IXP-Manager-API-Key: ${APIKEY}\" ${URL_LOCK}/${handle} >/dev/null"

    if [[ $DEBUG -eq 1 ]]; then echo $cmd; fi
    eval $cmd

    if [[ $? -ne 0 ]]; then
        echo "ABORTING: router not available for update"
        exit 200
    fi

fi

###########################################################################################
###########################################################################################
###
### Get the configuration from IXP Manager
###
###########################################################################################
###########################################################################################

cmd="curl --fail -s -H \"X-IXP-Manager-API-Key: ${APIKEY}\" ${URL_CONF}/${handle} >${dest}"

if [[ $DEBUG -eq 1 ]]; then echo $cmd; fi
eval $cmd

# We want to be safe here so check the generated file to see whether it
# looks valid
if [[ $? -ne 0 ]]; then
    echo "ERROR: non-zero return from curl when generating $dest"
    release_ixpmanager_lock
    exit 2
fi

if [[ ! -e $dest || ! -s $dest ]]; then
    echo "ERROR: $dest does not exist or is zero size"
    release_ixpmanager_lock
    exit 3
fi

if [[ $( cat $dest | grep "protocol bgp pb_" | wc -l ) -lt 2 ]]; then
    echo "ERROR: fewer than 2 BGP protocol definitions in config file $dest - something has gone wrong..."
    # do not release the lock - this could be a proper issue
    exit 4
fi

# parse and check the config
cmd="${BIRDBIN} -p -c $dest"
if [[ $DEBUG -eq 1 ]]; then echo $cmd; fi
eval $cmd &>/dev/null
if [[ $? -ne 0 ]]; then
    echo "ERROR: non-zero return from ${BIRDBIN} when parsing $dest"
    # do not release the lock - this could be a proper issue
    exit 7
fi



###########################################################################################
###########################################################################################
###
### Apply the configuration and start Bird if necessary
###
###########################################################################################
###########################################################################################

# config file should be okay; If everything is up and running, do we need a reload?

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
    else
        # back up the current one and replace
        cp "${cfile}" "${cfile}.old"
        mv $dest $cfile
    fi
else
    mv $dest $cfile
fi

# are we forcing a reload?
if [[ $FORCE_RELOAD -eq 1 ]]; then
    RELOAD_REQUIRED=1
fi


# are we running or do we need to be started?
cmd="${BIRDBIN}c -s $socket show memory"
if [[ $DEBUG -eq 1 ]]; then echo $cmd; fi
eval $cmd &>/dev/null

if [[ $? -ne 0 ]]; then

    # use systemd?
    
    if [[ -n "$service" ]]; then

      # check if service is present and enabled:
      s_status=$(systemctl is-enabled $service)

      if [[ $? -ne 0 ]]; then
         echo "ERROR: systemctl returned status $s_status for service $service"
         # do not release the lock - this could be a proper issue
         exit 5
      fi

      cmd="systemctl start $service"

    else

      cmd="${BIRDBIN} -c ${cfile} -s $socket"

    fi

    if [[ $DEBUG -eq 1 ]]; then echo $cmd; fi
    eval $cmd &>/dev/null

    if [[ $? -ne 0 ]]; then
        echo "ERROR: ${BIRDBIN} was not running for $dest and could not be started"
        # do not release the lock - this could be a proper issue
        exit 5
    fi
elif [[ $RELOAD_REQUIRED -eq 1 ]]; then
    cmd="${BIRDBIN}c -s $socket configure"
    if [[ $DEBUG -eq 1 ]]; then echo $cmd; fi
    eval $cmd &>/dev/null

    if [[ $? -ne 0 ]]; then
        echo "ERROR: Reconfigure failed for $dest"
        # do not release the lock - this could be a proper issue

        if [[ -e ${cfile}.old ]]; then
            echo "Trying to revert to previous"
            mv ${cfile}.conf $dest.failed
            mv ${cfile}.old ${cfile}
            cmd="${BIRDBIN}c -s $socket configure"
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
else
    if [[ $DEBUG -eq 1 ]]; then
        echo "Bird running and no reload required so skipping configure";
    fi
fi


###########################################################################################
###########################################################################################
###
### Tell IXP Manager that the config is complete and release the lock
###
###########################################################################################
###########################################################################################

if [[ $LOCKING_ENABLED -eq 1 ]]; then

    # tell IXP Manager the router has been updated:
    cmd="curl --fail -s -X POST -H \"X-IXP-Manager-API-Key: ${APIKEY}\" ${URL_DONE}/${handle} >/dev/null"
    if [[ $DEBUG -eq 1 ]]; then echo $cmd; fi

    until eval $cmd; do
        echo "Warning - could not inform IXP Manager via updated API - sleeping 60 secs and trying again"
        sleep 60
    done

fi

exit 0
