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

if [ ! -f ${SCRIPTPATH}/nagios-service.sh ]; then
        echo -e "nagios services file not found\nno nagios reload will be applied"
    else
        source ${SCRIPTPATH}/nagios-service.sh
fi
