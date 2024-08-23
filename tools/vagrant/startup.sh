#!/usr/bin/env bash

# Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee.
# All Rights Reserved.
#
# This file is part of IXP Manager.
#
# IXP Manager is free software: you can redistribute it and/or modify it
# under the terms of the GNU General Public License as published by the Free
# Software Foundation, version v2.0 of the License.
#
# IXP Manager is distributed in the hope that it will be useful, but WITHOUT
# ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
# FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
# more details.
#
# You should have received a copy of the GNU General Public License v2.0
# along with IXP Manager.  If not, see:
#
# http://www.gnu.org/licenses/gpl-2.0.html

## VAGRANT startup script - IXP Manager v7 / 24.04 LTS / php8.3
##
## Barry O'Donovan 2015-2024


## This script should be run on a reboot to set up everything bootstrap.sh would normally do.
##
## It will be added to /etc/rc.local and should run automatically.


####################################################################################
#######
####### snmpsim

chown -R vagrant: /srv/snmpclients

/srv/venv/bin/snmpsim-command-responder --data-dir=/srv/snmpclients/                    \
      --agent-udpv4-endpoint=127.0.0.1:161 --quiet --daemonize --process-user root      \
      --process-group root --pid-file /tmp/snmpsim.pid --logging-method null


####################################################################################
#######
####### Route Servers / Collectors / AS112 / Clients

IPS=`mysql --defaults-extra-file=/etc/mysql/ixpmanager.cnf --skip-column-names  --silent --raw ixp \
  -e 'SELECT DISTINCT ipaddr.address FROM ipv4address as ipaddr JOIN vlaninterface AS vli ON vli.ipv4addressid = ipaddr.id'`

for ip in $IPS; do /usr/sbin/ip address add $ip/24 dev lo; done

IPS=`mysql --defaults-extra-file=/etc/mysql/ixpmanager.cnf --skip-column-names  --silent --raw ixp \
  -e 'SELECT DISTINCT ipaddr.address FROM ipv6address as ipaddr JOIN vlaninterface AS vli ON vli.ipv6addressid = ipaddr.id'`

for ip in $IPS; do /usr/sbin/ip address add $ip/64 dev lo; done

/vagrant/tools/vagrant/scripts/rs-api-reconfigure-all.sh
/vagrant/tools/vagrant/scripts/rc-reconfigure.sh
/vagrant/tools/vagrant/scripts/as112-reconfigure-bird2.sh

mkdir -p /srv/clients
chown -R vagrant: /srv/clients
php /vagrant/artisan vagrant:generate-client-router-configurations
chmod a+x /srv/clients/start-reload-clients.sh
/srv/clients/start-reload-clients.sh

php /vagrant/artisan vagrant:generate-birdseye-configurations
chown -R vagrant: /srv/birdseye


####################################################################################
#######
####### Graphing mrtg
#######

/vagrant/tools/vagrant/scripts/update-mrtg.sh





####################################################################################
#######
####### Done!
#######

cd /vagrant

cat <<"END_ASCII"

 _   _                             _    ______               _
| | | |                           | |   | ___ \             | |
| | | | __ _  __ _ _ __ __ _ _ __ | |_  | |_/ /___  __ _  __| |_   _
| | | |/ _` |/ _` | '__/ _` | '_ \| __| |    // _ \/ _` |/ _` | | | |
\ \_/ / (_| | (_| | | | (_| | | | | |_  | |\ \  __/ (_| | (_| | |_| |
 \___/ \__,_|\__, |_|  \__,_|_| |_|\__| \_| \_\___|\__,_|\__,_|\__, |
              __/ |                                             __/ |
             |___/                                             |___/


 _     ______ _____ _
| |    |  ___|  __ \ |
| |    | |_  | |  \/ |
| |    |  _| | | __| |
| |____| |   | |_\ \_|
\_____/\_|    \____(_)

END_ASCII

