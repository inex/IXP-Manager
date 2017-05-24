#!/bin/sh

# In INEX's system, this script lived here:
PATH=/opt/local/bin:${PATH}

# The script output files to:
builddir=/var/db/routeserver

# The script will look for the route-collector-ios-config.tt file here:
configdir=/opt/local/etc/rc

# Path to RANCID's clogin command to push the config to IOS
clogin=/usr/local/libexec/rancid/clogin

# The config template to use
rctoken=route-collector-ios-config

if [ 'X'$1 = 'Xupload' ]; then
	upload=1
else
	upload=0
fi

echo configuring: ${rctoken}
build-tt-member-configuration.pl				\
		${configdir}/${rctoken}.tt	| \
	cat							\
		> ${builddir}/${rctoken}.conf.new

mv ${builddir}/${rctoken}.conf ${builddir}/${rctoken}.conf.old
mv ${builddir}/${rctoken}.conf.new ${builddir}/${rctoken}.conf

cmp -s ${builddir}/${rctoken}.conf.old ${builddir}/${rctoken}.conf

if [ 'X'$? = 'X0' ]; then
	upload=0
fi

if [ 'X'${upload} = 'X1' ]; then
	su - rancid -c "${clogin} -x ${builddir}/${rctoken}.conf rc1.example.com"
fi

exit
