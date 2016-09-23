#!/bin/sh
#
# Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee.
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

PATH=/usr/local/bin:$PATH:.
export PATH

ixpconfig="/usr/local/etc/ixpmanager.conf"
destdir="/usr/local/etc"
sourcett="/usr/local/etc/tt/routeserver-bird-config.tt"
destfilepfx="bird"

bird_reload_4="/usr/local/sbin/birdc"
bird_reload_6="/usr/local/sbin/birdc6"

vlan=10
routeserver=`grep rs_identity ${ixpconfig} | cut -d= -f2 | awk '{print $1}'`

basename=`basename $0`
tmpfile=`mktemp /tmp/${basename}.XXXXXXXX`

if [ 'X'$1 = 'Xreload' ]; then
	reload=1
else
	reload=0
fi

for protocol in 4 6; do
	destfile=${destdir}/${destfilepfx}-rs${routeserver}-vlan${vlan}-ipv${protocol}.conf
	rstmpfile=`mktemp ${destfile}.XXXXXXXX`

	echo configuring: ${destfile}
	build-tt-member-configuration.pl	\
		--vlan ${vlan}			\
		--protocol ${protocol}		\
		--routeserver ${routeserver}	\
		${sourcett}			> ${rstmpfile} 2> ${tmpfile}

	if [ -s ${tmpfile} ]; then
		echo $0: configuration errors. aborting for ${destfile}
		echo --
		cat ${tmpfile}
	else 
		# check if file length is zero.
		if [ "X"`cat ${rstmpfile} | wc -c | awk '{print $1}'` = "X0" ]; then
			echo $0: configuration produced zero length config file. aborting for ${destfile}
		else 
			mv ${destfile} ${destfile}.old
			mv ${rstmpfile} ${destfile}

			if [ ${reload} ]; then
				if [ ${protocol} -eq 4 ]; then
					${bird_reload_4} configure
				elif [ ${protocol} -eq 6 ]; then
					${bird_reload_6} configure
				fi
			fi
		fi
	fi
	rm -f ${tmpfile} ${rstmpfile}
done
