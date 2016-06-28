#!/usr/bin/env bash

apt update

# Defaults for MySQL and phpMyAdmin:
debconf-set-selections <<< 'mysql-server mysql-server/root_password password password'
debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password password'
echo 'phpmyadmin phpmyadmin/dbconfig-install boolean true' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/app-password-confirm password password' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/mysql/admin-pass password password' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/mysql/app-pass password password' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2' | debconf-set-selections

apt full-upgrade -y

apt install -y apache2 php7.0 php7.0-intl php7.0-mysql php-rrd php7.0-cgi php7.0-cli php7.0-snmp php7.0-curl php7.0-mcrypt \
    php-memcached libapache2-mod-php7.0 mysql-server mysql-client php-mysql joe memcached snmp nodejs nodejs-legacy npm     \
    build-essential php7.0-mbstring php7.0-xml phpmyadmin php-gettext bgpq3 screen joe curl git php-memcache unzip php-zip

# ubuntu/xenial64 oddity:
ln -s /etc/php/mods-available/rrd.ini /etc/php/7.0/apache2/conf.d/20-rrd.ini

if ! [ -L /var/www ]; then
  rm -rf /var/www
  ln -fs /vagrant/public /var/www
fi

curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
npm install -g bower

export MYSQL_PWD=password

mysql -u root <<END_SQL
DROP DATABASE IF EXISTS \`ixp\`;
CREATE DATABASE \`ixp\` CHARACTER SET = 'utf8mb4' COLLATE = 'utf8mb4_unicode_ci';
GRANT ALL ON \`ixp\`.* TO \`ixp\`@\`127.0.0.1\` IDENTIFIED BY 'password';
GRANT ALL ON \`ixp\`.* TO \`ixp\`@\`localhost\` IDENTIFIED BY 'password';
FLUSH PRIVILEGES;
END_SQL

if [[ -f /vagrant/ixpmanager-preferred.sql.bz2 ]]; then
    bzcat /vagrant/ixpmanager-preferred.sql.bz2 | mysql -u root ixp
elif [[ -f /vagrant/database/vagrant-base.sql ]]; then
    cat /vagrant/database/vagrant-base.sql | mysql -u root ixp
fi

cat >/vagrant/.env <<END_ENV
DB_HOST=127.0.0.1
DB_DATABASE=ixp
DB_USERNAME=ixp
DB_PASSWORD=password
END_ENV

cat >/vagrant/public/.htaccess <<END_HTACCESS
SetEnv APPLICATION_ENV vagrant
END_HTACCESS


cd /vagrant
su - ubuntu -c "cd /vagrant && composer install"
su - ubuntu -c "cd /vagrant && bower --config.interactive=false -f update"

cat >/etc/apache2/sites-available/000-default.conf <<END_APACHE
<VirtualHost *:80>
    DocumentRoot /vagrant/public

    <Directory /vagrant/public>
        Options FollowSymLinks
        AllowOverride None
        Require all granted

        SetEnv APPLICATION_ENV vagrant

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

cp /vagrant/application/configs/application.ini.vagrant /vagrant/application/configs/application.ini
a2enmod rewrite
chmod -R a+rwX /vagrant/storage /vagrant/var
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
