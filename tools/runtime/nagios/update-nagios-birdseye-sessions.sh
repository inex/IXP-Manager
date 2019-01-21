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
# Parse arguments
RELOAD=0
DEBUG=0

while getopts "d?:" opt; do
    case "$opt" in
        d)  DEBUG=1
            ;;
    esac
done

if [ ! -f nagios-global-vars.sh ]; then
        echo "Configurations file not found...aborting"
        exit 0
    else
    source nagios-global-vars.sh
fi

#######################################################################################################
# Process BIRD BGP sessions for customers

for birdsrcs in $BIRDTYPE; do

    for proto in $PROTOCOLS; do

        for vlanid in $VLANS; do

        if [ $DEBUG -ne 0 ]; then echo -n "Processing bird type ID: $birdsrcs - vlan-${vlanid} - IPv${proto}.... "; fi

        curl --fail -s -H "X-IXP-Manager-API-Key: ${KEY}"  \
            ${URL}/birdseye-bgp-sessions/${vlanid}/${proto}/${birdsrcs} >${CONFPATH}/birdseye-sessions-vlan${vlanid}-type-${birdsrcs}-IPv${proto}.cfg.$$

        if [[ $? -ne 0 ]]; then
            rm -f ${CONFPATH}/birdseye-sessions-vlan${vlanid}-type-${birdsrcs}-IPv${proto}.cfg.$$
            echo FAILED ${CONFPATH}/birdseye-sessions-vlan${vlanid}-type-${birdsrcs}-IPv${proto}.cfg
            continue
        fi

        cd ${CONFPATH}

        if [[ ! -f birdseye-sessions-vlan${vlanid}-type-${birdsrcs}-IPv${proto}.cfg ]]; then
            mv birdseye-sessions-vlan${vlanid}-type-${birdsrcs}-IPv${proto}.cfg.$$ birdseye-sessions-vlan${vlanid}-type-${birdsrcs}-IPv${proto}.cfg
            if [ $DEBUG -ne 0 ]; then echo "created -> reload scheduled [DONE]"; fi
            RELOAD=1
        else
                cat birdseye-sessions-vlan${vlanid}-type-${birdsrcs}-IPv${proto}.cfg    | egrep -v '^#.*$' >birdseye-sessions-vlan${vlanid}-type-${birdsrcs}-IPv${proto}.cfg.filtered
            cat birdseye-sessions-vlan${vlanid}-type-${birdsrcs}-IPv${proto}.cfg.$$ | egrep -v '^#.*$' >birdseye-sessions-vlan${vlanid}-type-${birdsrcs}-IPv${proto}.cfg.$$.filtered

            diff birdseye-sessions-vlan${vlanid}-type-${birdsrcs}-IPv${proto}.cfg.filtered birdseye-sessions-vlan${vlanid}-type-${birdsrcs}-IPv${proto}.cfg.$$.filtered >/dev/null
            DIFF=$?

                rm -f birdseye-sessions-vlan${vlanid}-type-${birdsrcs}-IPv${proto}.cfg.filtered birdseye-sessions-vlan${vlanid}-type-${birdsrcs}-IPv${proto}.cfg.$$.filtered

            if [[ $DIFF -eq 0 ]]; then
                rm birdseye-sessions-vlan${vlanid}-type-${birdsrcs}-IPv${proto}.cfg.$$
                if [ $DEBUG -ne 0 ]; then echo "unchanged -> skipping [DONE]"; fi
            else
                mv birdseye-sessions-vlan${vlanid}-type-${birdsrcs}-IPv${proto}.cfg.$$ birdseye-sessions-vlan${vlanid}-type-${birdsrcs}-IPv${proto}.cfg
                RELOAD=1
                if [ $DEBUG -ne 0 ]; then echo "changed -> updated -> reload scheduled [DONE]"; fi
            fi
        fi
        done

    done

done

if [ ! -f ${SCRIPTPATH}/nagios-service.sh ]; then
        echo -e "nagios services file not found\nno nagios reload will be applied"
    else
        source ${SCRIPTPATH}/nagios-service.sh
fi
