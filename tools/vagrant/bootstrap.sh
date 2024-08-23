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

apt-get update
apt-get dist-upgrade -y

# Defaults for MySQL and phpMyAdmin:
debconf-set-selections <<< 'mysql-server mysql-server/root_password password password'
debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password password'
echo 'phpmyadmin phpmyadmin/dbconfig-install boolean true' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/app-password-confirm password password' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/mysql/admin-pass password password' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/mysql/app-pass password password' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2' | debconf-set-selections
echo 'mrtg mrtg/conf_mods boolean true' | debconf-set-selections
echo 'mrtg mrtg/create_www boolean true' | debconf-set-selections
echo 'mrtg mrtg/fix_permissions boolean true' | debconf-set-selections


apt-get install -y mysql-server mysql-client

apt-get install -y apache2 php8.3 php8.3-intl php8.3-mysql php-rrd php8.3-cgi php8.3-cli     \
    php8.3-snmp php8.3-curl php8.3-memcached libapache2-mod-php8.3 bash-completion \
    php8.3-mysql memcached snmp php8.3-mbstring php8.3-xml php8.3-gd bgpq3 php8.3-memcache   \
    unzip php8.3-zip git php8.3-yaml php8.3-ds php8.3-bcmath libconfig-general-perl joe      \
    libnetaddr-ip-perl mrtg  libconfig-general-perl libnetaddr-ip-perl rrdtool librrds-perl  \
    phpmyadmin


sed -i 's/^bind-address\s\+=\s\+127.0.0.1/#bind-address            = 127.0.0.1/' /etc/mysql/mysql.conf.d/mysqld.cnf

systemctl restart mysql.service

if ! [ -L /var/www ]; then
  rm -rf /var/www
  ln -fs /vagrant/public /var/www
fi

curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

export MYSQL_PWD=password

cat >/etc/mysql/ixpmanager.cnf <<END_MYSQLCNF
[client]
user = "ixp"
password = "password"
host = "127.0.0.1"

END_MYSQLCNF

mysql -u root <<END_SQL
DROP DATABASE IF EXISTS \`ixp\`;
CREATE DATABASE \`ixp\` CHARACTER SET = 'utf8mb4' COLLATE = 'utf8mb4_unicode_ci';
CREATE USER IF NOT EXISTS \`ixp\`@\`%\` IDENTIFIED BY 'password';
CREATE USER IF NOT EXISTS \`root\`@\`%\` IDENTIFIED BY 'password';
GRANT ALL ON *.* TO \`root\`@\`%\`;
GRANT ALL ON \`ixp\`.* TO \`ixp\`@\`%\`;
FLUSH PRIVILEGES;
END_SQL

if [[ -f /vagrant/ixpmanager-preferred.sql.bz2 ]]; then
    bzcat /vagrant/ixpmanager-preferred.sql.bz2 | mysql -u root ixp
elif [[ -f /vagrant/tools/vagrant/vagrant-base.sql ]]; then
    cat /vagrant/tools/vagrant/vagrant-base.sql | mysql -u root ixp
fi

if [[ -f /vagrant/.env ]]; then
    cp /vagrant/.env /vagrant/.env.by-vagrant.$(date +%Y%m%d-%H%M%S)
fi

cat /vagrant/tools/vagrant/envfile > /vagrant/.env
php /vagrant/artisan key:generate --force


cd /vagrant
su - vagrant -c "cd /vagrant && COMPOSER_ALLOW_SUPERUSER=1 composer install"

php /vagrant/artisan migrate --force



####################################################################################
#######
####### Apache
#######



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

sed -i 's/export APACHE_RUN_USER=www-data/export APACHE_RUN_USER=vagrant/' /etc/apache2/envvars
sed -i 's/export APACHE_RUN_GROUP=www-data/export APACHE_RUN_GROUP=vagrant/' /etc/apache2/envvars

systemctl restart apache2.service

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

apt-get install -y python3-pip python3-venv python3-full
mkdir /srv/venv
python3 -m venv /srv/venv/
cd /srv/venv/
./bin/pip install snmpsim
mkdir /srv/snmpclients
cp /vagrant/tools/vagrant/snmpwalks/*snmprec /srv/snmpclients/
chown -R vagrant: /srv/snmpclients

/srv/venv/bin/snmpsim-command-responder --data-dir=/srv/snmpclients/                    \
      --agent-udpv4-endpoint=127.0.0.1:161 --quiet --daemonize --process-user root      \
      --process-group root --pid-file /tmp/snmpsim.pid --logging-method null

sed -i 's/127.0.0.1 localhost/127.0.0.1 localhost swi1-fac1-1 swi1-fac2-1 swi2-fac1-1/' /etc/hosts




####################################################################################
#######
####### Route Servers / Collectors

apt-get -y install bird2
/usr/bin/systemctl stop bird.service
/usr/bin/systemctl disable bird.service

IPS=`mysql --defaults-extra-file=/etc/mysql/ixpmanager.cnf --skip-column-names  --silent --raw ixp \
  -e 'SELECT DISTINCT ipaddr.address FROM ipv4address as ipaddr JOIN vlaninterface AS vli ON vli.ipv4addressid = ipaddr.id'`

for ip in $IPS; do /usr/sbin/ip address add $ip/24 dev lo; done

IPS=`mysql --defaults-extra-file=/etc/mysql/ixpmanager.cnf --skip-column-names  --silent --raw ixp \
  -e 'SELECT DISTINCT ipaddr.address FROM ipv6address as ipaddr JOIN vlaninterface AS vli ON vli.ipv6addressid = ipaddr.id'`

for ip in $IPS; do /usr/sbin/ip address add $ip/64 dev lo; done

# mysql --defaults-extra-file=/etc/mysql/ixpmanager.cnf --skip-column-names  --silent --raw ixp \
#  -e 'SELECT CONCAT( peering_ip, ' ', handle ) FROM routers'




####################################################################################
#######

# enable scheduler
#echo -e "\n\n# IXP Manager cron jobs:\n*  *   * * *   www-data    /usr/bin/php /vagrant/artisan schedule:run\n\n" >>/etc/crontab

cd /vagrant
