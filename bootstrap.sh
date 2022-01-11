#!/usr/bin/env bash

# Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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

## VAGRANT provisioning script - IXP Manager v5 / 18.04 LTS / php7.3
##
## Barry O'Donovan 2015-2021

apt update

# Defaults for MySQL and phpMyAdmin:
debconf-set-selections <<< 'mysql-server mysql-server/root_password password password'
debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password password'
echo 'phpmyadmin phpmyadmin/dbconfig-install boolean true' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/app-password-confirm password password' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/mysql/admin-pass password password' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/mysql/app-pass password password' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2' | debconf-set-selections
echo 'mrtg mrtg/conf_mods boolean true' | debconf-set-selections

apt-get install -y software-properties-common
add-apt-repository -y ppa:ondrej/php
apt-get update

apt full-upgrade -y
apt autoremove -y

apt-get install -y apache2 php8.0 php8.0-intl php8.0-mysql php-rrd php8.0-cgi php8.0-cli     \
    php8.0-snmp php8.0-curl php8.0-memcached libapache2-mod-php8.0 mysql-server mysql-client \
    php8.0-mysql memcached snmp php8.0-mbstring php8.0-xml php8.0-gd bgpq3 php8.0-memcache   \
    unzip php8.0-zip git php8.0-yaml php8.0-ds php8.0-bcmath libconfig-general-perl joe      \
    libnetaddr-ip-perl mrtg  libconfig-general-perl libnetaddr-ip-perl rrdtool librrds-perl  \
    phpmyadmin

if ! [ -L /var/www ]; then
  rm -rf /var/www
  ln -fs /vagrant/public /var/www
fi

curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

export MYSQL_PWD=password

mysql -u root <<END_SQL
DROP DATABASE IF EXISTS \`ixp\`;
CREATE DATABASE \`ixp\` CHARACTER SET = 'utf8mb4' COLLATE = 'utf8mb4_unicode_ci';
CREATE USER \`ixp\`@\`127.0.0.1\` IDENTIFIED BY 'password';
CREATE USER \`ixp\`@\`localhost\` IDENTIFIED BY 'password';
GRANT ALL ON \`ixp\`.* TO \`ixp\`@\`127.0.0.1\`;
GRANT ALL ON \`ixp\`.* TO \`ixp\`@\`localhost\`;
FLUSH PRIVILEGES;
END_SQL

if [[ -f /vagrant/ixpmanager-preferred.sql.bz2 ]]; then
    bzcat /vagrant/ixpmanager-preferred.sql.bz2 | mysql -u root ixp
elif [[ -f /vagrant/database/vagrant-base.sql ]]; then
    cat /vagrant/database/vagrant-base.sql | mysql -u root ixp
fi

if [[ -f /vagrant/.env ]]; then
    cp /vagrant/.env /vagrant/.env.by-vagrant.$$
fi

cat /vagrant/.env.vagrant > /vagrant/.env
php /vagrant/artisan key:generate --force


cd /vagrant
su - vagrant -c "cd /vagrant && composer install"

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


# enable scheduler
#echo -e "\n\n# IXP Manager cron jobs:\n*  *   * * *   www-data    /usr/bin/php /vagrant/artisan schedule:run\n\n" >>/etc/crontab

cd /vagrant
