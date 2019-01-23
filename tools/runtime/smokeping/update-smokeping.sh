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

# Example script for updating smokeping target configuration from IXP Manager

# Script correct of API usage in IXP Manager >= v4.8

# See: https://docs.ixpmanager.org/grapher/smokeping/

KEY="my-ixp-manager-api-key"
URL="https://ixp.example.com/api/v4/grapher/config?backend=smokeping"
ETCPATH="/etc/smokeping"
SMOKEPING="/usr/bin/smokeping"
SMOKEPING_RELOAD="/etc/rc.d/smokeping reload"
VLANS="1 2"
PROTOCOLS="ipv4 ipv6"


# flag used to indicate if a reload is necessary below
RELOAD=0

# Parse arguments
DEBUG=0

while getopts "d?:" opt; do
    case "$opt" in
        d)  DEBUG=1
            ;;
    esac
done


for vlanid in $VLANS; do

    for proto in $PROTOCOLS; do

        if [ $DEBUG -ne 0 ]; then echo -n "Processing $vlanid - $proto.... "; fi

    	curl --fail -s -H "X-IXP-Manager-API-Key: ${KEY}"  \
    	    "${URL}&vlanid=${vlanid}&protocol=${proto}" >${ETCPATH}/targets-vlan${vlanid}-${proto}.cfg.$$

        if [[ $? -ne 0 ]]; then
            rm -f ${ETCPATH}/targets-vlan${vlanid}-${proto}.cfg.$$
            echo FAILED targets-vlan${vlanid}-${proto}.cfg
            continue
        fi

        cd ${ETCPATH}

        if [[ ! -f targets-vlan${vlanid}-${proto}.cfg ]]; then
            mv targets-vlan${vlanid}-${proto}.cfg.$$ targets-vlan${vlanid}-${proto}.cfg
            if [ $DEBUG -ne 0 ]; then echo "created -> reload scheduled [DONE]"; fi
            RELOAD=1
        else
	        cat targets-vlan${vlanid}-${proto}.cfg    | egrep -v '^#.*$' >targets-vlan${vlanid}-${proto}.cfg.filtered
            cat targets-vlan${vlanid}-${proto}.cfg.$$ | egrep -v '^#.*$' >targets-vlan${vlanid}-${proto}.cfg.$$.filtered

    	    diff targets-vlan${vlanid}-${proto}.cfg.filtered targets-vlan${vlanid}-${proto}.cfg.$$.filtered >/dev/null
    	    DIFF=$?

    	    rm -f targets-vlan${vlanid}-${proto}.cfg.filtered targets-vlan${vlanid}-${proto}.cfg.$$.filtered

    	    if [[ $DIFF -eq 0 ]]; then
    	        rm targets-vlan${vlanid}-${proto}.cfg.$$
    	        if [ $DEBUG -ne 0 ]; then echo "unchanged -> skipping [DONE]"; fi
     	    else
    	        mv targets-vlan${vlanid}-${proto}.cfg.$$ targets-vlan${vlanid}-${proto}.cfg
    	        RELOAD=1
    	        if [ $DEBUG -ne 0 ]; then echo "changed -> updated -> reload scheduled [DONE]"; fi
    	    fi
    	fi

    done

done

if [[ $RELOAD -eq 1 ]]; then
    if [ $DEBUG -ne 0 ]; then
        echo "Smokeping reloading..."
        $SMOKEPING --check && $SMOKEPING_RELOAD
    else
        $SMOKEPING --check >/dev/null && $SMOKEPING_RELOAD &>/dev/null 2>&1
    fi
else
    if [ $DEBUG -ne 0 ]; then
        echo "Smokeping not reloading as no reload scheduled."
    fi
fi

exit 0
