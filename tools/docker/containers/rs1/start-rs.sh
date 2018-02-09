#! /bin/bash

/etc/init.d/bird stop
/etc/init.d/bird6 stop
/etc/init.d/lighttpd stop

/usr/local/sbin/api-reconfigure-all-v4.sh

mkdir -p /var/run/lighttpd /var/log/lighttpd
chown www-data: /var/run/lighttpd /var/log/lighttpd

/usr/sbin/lighttpd -D -f /etc/lighttpd/lighttpd.conf
