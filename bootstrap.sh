#!/usr/bin/env bash

# Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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

## VAGRANT provisioning script - IXP Manager v6 / 20.04 LTS / php8.0
##
## Barry O'Donovan 2015-2021



apt-get update

debconf-set-selections <<< "console-setup console-setup/charmap47 select UTF-8"
debconf-set-selections <<< "console-setup console-setup/codeset47 select Lat15"
export DEBIAN_FRONTEND=noninteractive

apt-get dist-upgrade -o "Dpkg::Options::=--force-confold" -yq
apt-get install -yq ubuntu-minimal openssl wget net-tools

locale-gen en_IE.UTF-8
export LANG=en_IE.UTF-8



# Defaults for MySQL:
debconf-set-selections <<< "mysql-server mysql-server/root_password password password"
debconf-set-selections <<< "mysql-server mysql-server/root_password_again password password"

apt-get install -yq software-properties-common
add-apt-repository -y ppa:ondrej/php

echo -n "Installing PHP, Apache, MySQL, etc. Please be very patient..."

# Prevent mrtg from prompting
echo mrtg mrtg/conf_mods boolean true | debconf-set-selections

apt install -qy apache2 php8.0 php8.0-intl php-rrd php8.0-cgi php8.0-cli   \
    php8.0-snmp php8.0-curl  php-memcached libapache2-mod-php8.0 mysql-server           \
    mysql-client php8.0-mysql memcached snmp php8.0-mbstring php8.0-xml php8.0-gd       \
    php8.0-bcmath bgpq3 php-memcache unzip php8.0-zip git php-yaml                      \
    php-ds libconfig-general-perl libnetaddr-ip-perl mrtg  libconfig-general-perl       \
    libnetaddr-ip-perl rrdtool librrds-perl curl composer joe


if ! [ -L /var/www ]; then
  rm -rf /var/www
  ln -fs /vagrant/public /var/www
fi

curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

export MYSQL_PWD=password

mysql -u root <<END_SQL
DROP DATABASE IF EXISTS \`ixpmanager\`;
CREATE DATABASE \`ixpmanager\` CHARACTER SET = 'utf8mb4' COLLATE = 'utf8mb4_unicode_ci';
CREATE USER \`ixpmanager\`@\`127.0.0.1\` IDENTIFIED BY 'ixpmanager';
GRANT ALL ON \`ixpmanager\`.* TO \`ixpmanager\`@\`127.0.0.1\`;
GRANT ALL ON \`ixpmanager\`.* TO \`ixpmanager\`@\`localhost\`;
FLUSH PRIVILEGES;
END_SQL

if [[ -f /vagrant/ixpmanager-preferred.sql.bz2 ]]; then
    bzcat /vagrant/ixpmanager-preferred.sql.bz2 | mysql -u root ixpmanager
elif [[ -f /vagrant/database/vagrant-base.sql ]]; then
    cat /vagrant/database/vagrant-base.sql | mysql -u root ixpmanager
fi

if [[ -f /vagrant/.env ]]; then
    cp /vagrant/.env /vagrant/.env.by-vagrant.$$
fi


cd /vagrant

cat >/etc/apache2/sites-available/000-default.conf <<END_APACHE
<VirtualHost *:80>
    DocumentRoot /vagrant/public

    <Directory /vagrant/public>
        Options FollowSymLinks
        AllowOverride None
        Require all granted

        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} -s [OR]
        RewriteCond %{REQUEST_FILENAME} -l [OR]
        RewriteCond %{REQUEST_FILENAME} -d
        RewriteRule ^.*$ - [NC,L]
        RewriteRule ^.*$ /index.php [NC,L]
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
END_APACHE

a2enmod rewrite
chmod -R a+rwX /vagrant/storage /vagrant/bootstrap/cache
service apache2 restart

# Useful screen settings for barryo:
cat >/home/ubuntu/.screenrc <<END_SCREEN
termcapinfo xterm* ti@:te@
vbell off
startup_message off
defutf8 on
defscrollback 2048
nonblock on
hardstatus on
hardstatus alwayslastline
hardstatus string '%{= kG}%-Lw%{= kW}%50> %n%f* %t%{= kG}%+Lw%<'
screen -t bash     0
altscreen on
END_SCREEN

cd /vagrant
