#! /usr/bin/env bash


KEY="your-ixp-manager-apikey-here"
URL="https://www.example.com/ixp/api/v4/nagios"
CONFPATH="/usr/local/etc/nagios/conf-folder"
ETCPATH="/usr/local/etc/nagios"
NAGIOS="/usr/local/bin/nagios"
NAGIOS_RELOAD="/usr/local/etc/rc.d/nagios reload"
INFRA=""
VLANS=""
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

# Process VLANS

for vlanid in $VLANS; do

    for proto in $PROTOCOLS; do

        if [ $DEBUG -ne 0 ]; then echo -n "Processing $vlanid - Protocol IPv$proto.... "; fi

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

# Process IXP switching infrastructure

for infraid in $INFRA; do

        if [ $DEBUG -ne 0 ]; then echo -n "Processing IXP Infrastructure ID $infraid.... "; fi

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

if [[ $RELOAD -eq 1 ]]; then
    if [ $DEBUG -ne 0 ]; then
        echo "Nagios reloading..."
        $NAGIOS -v $ETCPATH/nagios.cfg && $NAGIOS_RELOAD
    else
        $NAGIOS -v >/dev/null && $NAGIOS_RELOAD &>/dev/null 2>&1
    fi
else
    if [ $DEBUG -ne 0 ]; then
        echo "Nagios not reloading as no reload scheduled."
    fi
fi

exit 0
