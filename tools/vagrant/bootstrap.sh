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

## VAGRANT provisioning script - IXP Manager v7 / 24.04 LTS / php8.3
##
## Barry O'Donovan 2015-2024

echo "Updating packages...."
apt-get update &>/dev/null
#apt-get dist-upgrade -y

# Defaults for MySQL and phpMyAdmin:
echo 'mysql-server mysql-server/root_password password password' | debconf-set-selections
echo 'mysql-server mysql-server/root_password_again password password' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/dbconfig-install boolean true' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/app-password-confirm password password' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/mysql/admin-pass password password' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/mysql/app-pass password password' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2' | debconf-set-selections
echo 'mrtg mrtg/conf_mods boolean true' | debconf-set-selections
echo 'mrtg mrtg/create_www boolean true' | debconf-set-selections
echo 'mrtg mrtg/fix_permissions boolean true' | debconf-set-selections

echo "Installng MySQL..."
apt-get install -y mysql-server mysql-client  &>/dev/null

echo "Installing apache, php, etc..."
apt-get install -y apache2 php8.3 php8.3-intl php8.3-mysql php-rrd php8.3-cgi php8.3-cli     \
    php8.3-snmp php8.3-curl php8.3-memcached libapache2-mod-php8.3 bash-completion \
    php8.3-mysql memcached snmp php8.3-mbstring php8.3-xml php8.3-gd bgpq3 php8.3-memcache   \
    unzip php8.3-zip git php8.3-yaml php8.3-bcmath libconfig-general-perl joe      \
    libnetaddr-ip-perl mrtg  libconfig-general-perl libnetaddr-ip-perl rrdtool librrds-perl  \
    phpmyadmin  &>/dev/null

# php8.3-ds -> add back when fixed in 24.04



####################################################################################
#######
####### MySQL
#######

echo "Having MySQL listen on all interfaces"
sed -i 's/^bind-address\s\+=\s\+127.0.0.1/#bind-address            = 127.0.0.1/' /etc/mysql/mysql.conf.d/mysqld.cnf
systemctl restart mysql.service &>/dev/null

echo "Setting up MySQL and databases..."

cat >/etc/mysql/ixpmanager.cnf <<END_MYSQLCNF
[client]
user = "ixp"
password = "password"
host = "127.0.0.1"

END_MYSQLCNF

cat >/etc/mysql/root-client.cnf <<END_MYSQLCNF
[client]
user = "root"
password = "password"
host = "127.0.0.1"

END_MYSQLCNF

mysql --defaults-extra-file=/etc/mysql/root-client.cnf <<"END_SQL"
DROP DATABASE IF EXISTS ixp;
CREATE DATABASE ixp CHARACTER SET = 'utf8mb4' COLLATE = 'utf8mb4_unicode_ci';
CREATE USER IF NOT EXISTS ixp@`%` IDENTIFIED BY 'password';
GRANT ALL ON ixp.* TO ixp@`%`;
GRANT SUPER,SYSTEM_USER ON *.* TO ixp@`%`;

DROP DATABASE IF EXISTS ixp_ci;
CREATE DATABASE ixp_ci CHARACTER SET = 'utf8mb4' COLLATE = 'utf8mb4_unicode_ci';
CREATE USER IF NOT EXISTS ixp_ci@`%` IDENTIFIED BY 'ixp_ci';
GRANT ALL ON ixp_ci.* TO ixp_ci@`%`;
GRANT SUPER,SYSTEM_USER ON *.* TO ixp_ci@`%`;

FLUSH PRIVILEGES;
END_SQL



if [[ -f /vagrant/ixpmanager-preferred.sql.bz2 ]]; then
    bzcat /vagrant/ixpmanager-preferred.sql.bz2 | mysql --defaults-extra-file=/etc/mysql/root-client.cnf ixp
elif [[ -f /vagrant/tools/vagrant/vagrant-base.sql ]]; then
    cat /vagrant/tools/vagrant/vagrant-base.sql | mysql --defaults-extra-file=/etc/mysql/root-client.cnf ixp
fi

cat /vagrant/data/ci/ci_test_db.sql  | mysql --defaults-extra-file=/etc/mysql/root-client.cnf ixp_ci


####################################################################################
#######
####### Composer and packages
#######

echo "Installing composer.phar..."
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer &>/dev/null

if [[ -f /vagrant/.env ]]; then
    cp /vagrant/.env /vagrant/.env.by-vagrant.$(date +%Y%m%d-%H%M%S)
fi

cat /vagrant/tools/vagrant/envfile > /vagrant/.env


cd /vagrant
echo "Installing composer packages..."
su - vagrant -c "cd /vagrant && COMPOSER_ALLOW_SUPERUSER=1 composer install" &>/dev/null

echo "Installing / migrating database..." &>/dev/null
php /vagrant/artisan migrate --force &>/dev/null



####################################################################################
#######
####### Apache
#######

echo "Setting up Apache..."


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

a2enmod rewrite &>/dev/null

sed -i 's/export APACHE_RUN_USER=www-data/export APACHE_RUN_USER=vagrant/' /etc/apache2/envvars
sed -i 's/export APACHE_RUN_GROUP=www-data/export APACHE_RUN_GROUP=vagrant/' /etc/apache2/envvars

systemctl restart apache2.service &>/dev/null

####################################################################################
#######
####### Useful screen settings for barryo:
#######


cat >/home/vagrant/.screenrc <<END_SCREEN
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



####################################################################################
#######
####### snmpsim
#######

echo "Setting up snmpsim..."
apt-get install -y python3-pip python3-venv python3-full &>/dev/null
mkdir /srv/venv
python3 -m venv /srv/venv/ &>/dev/null
cd /srv/venv/
./bin/pip install snmpsim &>/dev/null
mkdir /srv/snmpclients
cp /vagrant/tools/vagrant/snmpwalks/*snmprec /srv/snmpclients/
chown -R vagrant: /srv/snmpclients

/srv/venv/bin/snmpsim-command-responder --data-dir=/srv/snmpclients/                    \
      --agent-udpv4-endpoint=127.0.0.1:161 --quiet --daemonize --process-user root      \
      --process-group root --pid-file /tmp/snmpsim.pid --logging-method null

sed -i 's/127.0.0.1 localhost/127.0.0.1 localhost swi1-fac1-1 swi1-fac2-1 swi2-fac1-1/' /etc/hosts




####################################################################################
#######
####### Route Servers / Collectors / AS112 / Clients

echo "Setting up router testbed..."

apt-get -y install bird2 &>/dev/null
/usr/bin/systemctl stop bird.service &>/dev/null
/usr/bin/systemctl disable bird.service &>/dev/null

IPS=`mysql --defaults-extra-file=/etc/mysql/ixpmanager.cnf --skip-column-names  --silent --raw ixp \
  -e 'SELECT DISTINCT ipaddr.address FROM ipv4address as ipaddr JOIN vlaninterface AS vli ON vli.ipv4addressid = ipaddr.id'`

for ip in $IPS; do /usr/sbin/ip address add $ip/24 dev lo; done

IPS=`mysql --defaults-extra-file=/etc/mysql/ixpmanager.cnf --skip-column-names  --silent --raw ixp \
  -e 'SELECT DISTINCT ipaddr.address FROM ipv6address as ipaddr JOIN vlaninterface AS vli ON vli.ipv6addressid = ipaddr.id'`

for ip in $IPS; do /usr/sbin/ip address add $ip/64 dev lo; done

mysql --defaults-extra-file=/etc/mysql/ixpmanager.cnf --skip-column-names  --silent --raw ixp \
  -e 'SELECT CONCAT( peering_ip, " ", handle ) FROM routers' >> /etc/hosts

/vagrant/tools/vagrant/scripts/rs-api-reconfigure-all.sh
/vagrant/tools/vagrant/scripts/rc-reconfigure.sh
/vagrant/tools/vagrant/scripts/as112-reconfigure-bird2.sh

mkdir -p /srv/clients
chown -R vagrant: /srv/clients
php /vagrant/artisan vagrant:generate-client-router-configurations
chmod a+x /srv/clients/start-reload-clients.sh
/srv/clients/start-reload-clients.sh


####################################################################################
#######
####### Birdseye Looking Glass
#######

echo "Setting up Birdseye / looking glasses..."

git clone https://github.com/inex/birdseye.git /srv/birdseye &>/dev/null
cd /srv/birdseye
git config --global --add safe.directory /srv/birdseye
git checkout php83 &>/dev/null

cat >/etc/apache2/sites-enabled/birdseye.conf <<END_APACHE
Listen 81

<VirtualHost *:81>
    DocumentRoot /srv/birdseye/public

    <Directory /srv/birdseye/public>
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

    ErrorLog ${APACHE_LOG_DIR}/birdseye-error.log
    CustomLog ${APACHE_LOG_DIR}/birdseye-access.log combined
</VirtualHost>

END_APACHE

systemctl restart apache2.service

php /vagrant/artisan vagrant:generate-birdseye-configurations
su - vagrant -c "cd /srv/birdseye && COMPOSER_ALLOW_SUPERUSER=1 composer install" &>/dev/null
chown -R vagrant: /srv/birdseye

echo -e "\nvagrant        ALL=(ALL)       NOPASSWD: /srv/birdseye/bin/birdc\n" >>/etc/sudoers



####################################################################################
#######

# enable scheduler
#echo -e "\n\n# IXP Manager cron jobs:\n*  *   * * *   www-data    /usr/bin/php /vagrant/artisan schedule:run\n\n" >>/etc/crontab

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

