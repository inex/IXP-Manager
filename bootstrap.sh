#!/usr/bin/env bash

apt-get update
debconf-set-selections <<< 'mysql-server mysql-server/root_password password password'
debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password password'
apt-get install -y apache2 php5 php5-intl php5-mysql php5-rrd php5-cgi php5-cli php5-snmp php5-curl php5-mcrypt \
    php5-memcache libapache2-mod-php5 mysql-server mysql-client joe memcached snmp
if ! [ -L /var/www ]; then
  rm -rf /var/www
  ln -fs /vagrant/public /var/www
fi

curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

mysql -u root -ppassword <<END_SQL
DROP DATABASE IF EXISTS \`ixp\`;
CREATE DATABASE \`ixp\` CHARACTER SET = 'utf8mb4' COLLATE = 'utf8mb4_unicode_ci';
GRANT ALL ON \`ixp\`.* TO \`ixp\`@\`127.0.0.1\` IDENTIFIED BY 'password';
GRANT ALL ON \`ixp\`.* TO \`ixp\`@\`localhost\` IDENTIFIED BY 'password';
FLUSH PRIVILEGES;
END_SQL

if [[ -f /vagrant/ixpmanager-preferred.sql.bz2 ]]; then
    bzcat /vagrant/ixpmanager-preferred.sql.bz2 | mysql -u root -ppassword ixp
else if [[ -f /vagrant/database/vagrant-base.sql.bz2 ]]; then
    bzcat /vagrant/database/vagrant-base.sql.bz2 | mysql -u root -ppassword ixp
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



ln -s /etc/php5/mods-available/mcrypt.ini /etc/php5/apache2/conf.d/20-mcrypt.ini
ln -s /etc/php5/mods-available/mcrypt.ini /etc/php5/cgi/conf.d/20-mcrypt.ini
ln -s /etc/php5/mods-available/mcrypt.ini /etc/php5/cli/conf.d/20-mcrypt.ini

cd /vagrant
composer install


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

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
END_APACHE

cp /vagrant/application/configs/application.ini.vagrant /vagrant/application/configs/application.ini
a2enmod rewrite
chmod -R a+rwX /vagrant/storage /vagrant/var
service apache2 restart
