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
# Denis Nolan <denis.nolan -at- inex.ie>, July 2017
# Barry O'Donovan <barry.odonovan -at- inex.ie>

# Example script for updating Nagios target configuration from IXP Manager

# See: http://docs.ixpmanager.org/features/nagios/


## SET THE FOLLOWING VARIABLES APPROPRIATELY


KEY="your-ixp-manager-apikey-here"
URL="https://ixp.example.com/api/v4/nagios"

# where to store the Nagios host/service configuration files:
CONFPATH="/etc/nagios/conf.d"

# Main Nagios configuration file:
NAGIOSCONF="/etc/nagios/nagios.cfg"

# nagios binary:
NAGIOS="/usr/local/bin/nagios"

# Command to make Nagios reload its configuration:
NAGIOS_RELOAD="/usr/local/etc/rc.d/nagios reload"

# List of infrastructure IDs to create switch targets for:
INFRA=""

# List of VLANs to generate customer reachability / host targets for
VLANS=""

# List if protocols:
PROTOCOLS="4 6"

# BIRDTYPE: 1 = route servers | 2 = route collectors | 3 = as112
# To create BGP session checks for all routers, set this to:
# BIRDTYPE="1 2 3"
BIRDTYPE=""



### END "SET THE FOLLOWING VARIABLES APPROPRIATELY" ###



# Parse arguments
RELOAD=0
DEBUG=0

while getopts "d?:" opt; do
    case "$opt" in
        d)  DEBUG=1
            ;;
    esac
done

#######################################################################################################
# Process VLANS for customer reachability checks

for vlanid in $VLANS; do

    for proto in $PROTOCOLS; do

        if [ $DEBUG -ne 0 ]; then echo -n "Processing vlan ID: $vlanid - protocol IPv$proto.... "; fi

        curl --fail -s -H "X-IXP-Manager-API-Key: ${KEY}"  \
            ${URL}/customers/${vlanid}/${proto} >${CONFPATH}/customers-vlan${vlanid}-ipv${proto}.cfg.$$

        if [[ $? -ne 0 ]]; then
            rm -f ${CONFPATH}/customers-vlan${vlanid}-ipv${proto}.cfg.$$
            echo FAILED customers-vlan${vlanid}-ipv${proto}.cfg
            continue
        fi

        cd ${CONFPATH}

        if [[ ! -f customers-vlan${vlanid}-ipv${proto}.cfg ]]; then
            mv customers-vlan${vlanid}-ipv${proto}.cfg.$$ customers-vlan${vlanid}-ipv${proto}.cfg
            if [ $DEBUG -ne 0 ]; then echo "created -> reload scheduled [DONE]"; fi
            RELOAD=1
        else
            cat customers-vlan${vlanid}-ipv${proto}.cfg    | egrep -v '^#.*$' >customers-vlan${vlanid}-ipv${proto}.cfg.filtered
            cat customers-vlan${vlanid}-ipv${proto}.cfg.$$ | egrep -v '^#.*$' >customers-vlan${vlanid}-ipv${proto}.cfg.$$.filtered

            diff customers-vlan${vlanid}-ipv${proto}.cfg.filtered customers-vlan${vlanid}-ipv${proto}.cfg.$$.filtered >/dev/null
            DIFF=$?

            rm -f customers-vlan${vlanid}-ipv${proto}.cfg.filtered customers-vlan${vlanid}-ipv${proto}.cfg.$$.filtered

            if [[ $DIFF -eq 0 ]]; then
                rm customers-vlan${vlanid}-ipv${proto}.cfg.$$
                if [ $DEBUG -ne 0 ]; then echo "unchanged -> skipping [DONE]"; fi
            else
                mv customers-vlan${vlanid}-ipv${proto}.cfg.$$ customers-vlan${vlanid}-ipv${proto}.cfg
                RELOAD=1
                if [ $DEBUG -ne 0 ]; then echo "changed -> updated -> reload scheduled [DONE]"; fi
            fi
        fi

    done

done

#######################################################################################################
# Process IXP switching infrastructure

for infraid in $INFRA; do

    if [ $DEBUG -ne 0 ]; then echo -n "Processing IXP  ID: $infraid.... "; fi

	curl --fail -s -H "X-IXP-Manager-API-Key: ${KEY}"  \
	    ${URL}/switches/${infraid} >${CONFPATH}/switches-infraid-${infraid}.cfg.$$

	if [[ $? -ne 0 ]]; then
	    rm -f ${CONFPATH}/switches-infraid-${infraid}.cfg.$$
	    echo FAILED switches-infraid-${infraid}.cfg
	    continue
	fi

	cd ${CONFPATH}

	if [[ ! -f switches-infraid-${infraid}.cfg ]]; then
	    mv switches-infraid-${infraid}.cfg.$$ switches-infraid-${infraid}.cfg
	    if [ $DEBUG -ne 0 ]; then echo "created -> reload scheduled [DONE]"; fi
	    RELOAD=1
	else
        cat switches-infraid-${infraid}.cfg    | egrep -v '^#.*$' >switches-infraid-${infraid}.cfg.filtered
	    cat switches-infraid-${infraid}.cfg.$$ | egrep -v '^#.*$' >switches-infraid-${infraid}.cfg.$$.filtered

	    diff switches-infraid-${infraid}.cfg.filtered switches-infraid-${infraid}.cfg.$$.filtered >/dev/null
	    DIFF=$?

        rm -f switches-infraid-${infraid}.cfg.filtered switches-infraid-${infraid}.cfg.$$.filtered

	    if [[ $DIFF -eq 0 ]]; then
	        rm switches-infraid-${infraid}.cfg.$$
	        if [ $DEBUG -ne 0 ]; then echo "unchanged -> skipping [DONE]"; fi
 	    else
	        mv switches-infraid-${infraid}.cfg.$$ switches-infraid-${infraid}.cfg
	        RELOAD=1
	        if [ $DEBUG -ne 0 ]; then echo "changed -> updated -> reload scheduled [DONE]"; fi
	    fi
	fi
done



#######################################################################################################
# Process BIRD BGP sessions for customers

for birdsrcs in $BIRDTYPE; do

    for proto in $PROTOCOLS; do

        if [ $DEBUG -ne 0 ]; then echo -n "Processing bird type ID: $birdsrcs.... "; fi

        curl --fail -s -H "X-IXP-Manager-API-Key: ${KEY}"  \
            ${URL}/birdseye-bgp-sessions/${vlanid}/${proto}/${birdsrcs} >${CONFPATH}/birdseye-sessions-type-${birdsrcs}-IPv${proto}.cfg.$$

        if [[ $? -ne 0 ]]; then
            rm -f ${CONFPATH}/birdseye-sessions-type-${birdsrcs}-IPv${proto}.cfg.$$
            echo FAILED ${CONFPATH}/birdseye-sessions-type-${birdsrcs}-IPv${proto}.cfg
            continue
        fi

        cd ${CONFPATH}

        if [[ ! -f birdseye-sessions-type-${birdsrcs}-IPv${proto}.cfg ]]; then
            mv birdseye-sessions-type-${birdsrcs}-IPv${proto}.cfg.$$ birdseye-sessions-type-${birdsrcs}-IPv${proto}.cfg
            if [ $DEBUG -ne 0 ]; then echo "created -> reload scheduled [DONE]"; fi
            RELOAD=1
        else
                cat birdseye-sessions-type-${birdsrcs}-IPv${proto}.cfg    | egrep -v '^#.*$' >birdseye-sessions-type-${birdsrcs}-IPv${proto}.cfg.filtered
            cat birdseye-sessions-type-${birdsrcs}-IPv${proto}.cfg.$$ | egrep -v '^#.*$' >birdseye-sessions-type-${birdsrcs}-IPv${proto}.cfg.$$.filtered

            diff birdseye-sessions-type-${birdsrcs}-IPv${proto}.cfg.filtered birdseye-sessions-type-${birdsrcs}-IPv${proto}.cfg.$$.filtered >/dev/null
            DIFF=$?

                rm -f birdseye-sessions-type-${birdsrcs}-IPv${proto}.cfg.filtered birdseye-sessions-type-${birdsrcs}-IPv${proto}.cfg.$$.filtered

            if [[ $DIFF -eq 0 ]]; then
                rm birdseye-sessions-type-${birdsrcs}-IPv${proto}.cfg.$$
                if [ $DEBUG -ne 0 ]; then echo "unchanged -> skipping [DONE]"; fi
            else
                mv birdseye-sessions-type-${birdsrcs}-IPv${proto}.cfg.$$ birdseye-sessions-type-${birdsrcs}-IPv${proto}.cfg
                RELOAD=1
                if [ $DEBUG -ne 0 ]; then echo "changed -> updated -> reload scheduled [DONE]"; fi
            fi
        fi
    done
done

if [[ $RELOAD -eq 1 ]]; then
    if [ $DEBUG -ne 0 ]; then
        echo "Nagios reloading..."
        $NAGIOS -v $NAGIOSCONF && $NAGIOS_RELOAD
    else
        $NAGIOS -v >/dev/null && $NAGIOS_RELOAD &>/dev/null 2>&1
    fi
else
    if [ $DEBUG -ne 0 ]; then
        echo "Nagios not reloading as no reload scheduled."
    fi
fi

exit 0
