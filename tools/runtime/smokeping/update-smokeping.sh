#! /usr/bin/env bash
#
# Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee.
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

# See: http://docs.ixpmanager.org/features/smokeping/

KEY="my-ixp-manager-api-key"
URL="https://ixp.example.com/api/v4/vlan/smokeping"
ETCPATH="/etc/smokeping"
SMOKEPING="/usr/bin/smokeping"
SMOKEPING_RC="/rc.d/smokeping"
VLANS="1 2"
PROTOCOLS="4 6"
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
    	    ${URL}/${vlanid}/${proto} >${ETCPATH}/targets-vlan${vlanid}-ipv${proto}.cfg.$$

        if [[ $? -ne 0 ]]; then
            rm -f ${ETCPATH}/targets-vlan${vlanid}-ipv${proto}.cfg.$$
            echo FAILED targets-vlan${vlanid}-ipv${proto}.cfg
            continue
        fi

        cd ${ETCPATH}

        if [[ ! -f targets-vlan${vlanid}-ipv${proto}.cfg ]]; then
            mv targets-vlan${vlanid}-ipv${proto}.cfg.$$ targets-vlan${vlanid}-ipv${proto}.cfg
            if [ $DEBUG -ne 0 ]; then echo "created -> reload scheduled [DONE]"; fi
            RELOAD=1
        else
	        cat targets-vlan${vlanid}-ipv${proto}.cfg    | egrep -v '^#.*$' >targets-vlan${vlanid}-ipv${proto}.cfg.filtered
            cat targets-vlan${vlanid}-ipv${proto}.cfg.$$ | egrep -v '^#.*$' >targets-vlan${vlanid}-ipv${proto}.cfg.$$.filtered

    	    diff targets-vlan${vlanid}-ipv${proto}.cfg.filtered targets-vlan${vlanid}-ipv${proto}.cfg.$$.filtered >/dev/null
    	    DIFF=$?

    	    rm -f targets-vlan${vlanid}-ipv${proto}.cfg.filtered targets-vlan${vlanid}-ipv${proto}.cfg.$$.filtered

    	    if [[ $DIFF -eq 0 ]]; then
    	        rm targets-vlan${vlanid}-ipv${proto}.cfg.$$
    	        if [ $DEBUG -ne 0 ]; then echo "unchanged -> skipping [DONE]"; fi
     	    else
    	        mv targets-vlan${vlanid}-ipv${proto}.cfg.$$ targets-vlan${vlanid}-ipv${proto}.cfg
    	        RELOAD=1
    	        if [ $DEBUG -ne 0 ]; then echo "changed -> updated -> reload scheduled [DONE]"; fi
    	    fi
    	fi

    done

done

if [[ $RELOAD -eq 1 ]]; then
    if [ $DEBUG -ne 0 ]; then
        echo "Smokeping reloading..."
        $SMOKEPING --check && $SMOKEPING_RC reload
    else
        $SMOKEPING --check >/dev/null && $SMOKEPING_RC reload &>/dev/null 2>&1
    fi
else
    if [ $DEBUG -ne 0 ]; then
        echo "Smokeping not reloading as no reload scheduled."
    fi
fi

exit 0
