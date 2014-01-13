#!/bin/bash

# install requirements
sudo apt-get update
# sudo apt-get upgrade
sudo apt-get install php5-memcache php5-snmp php-pear apache2 libapache2-mod-php5

# install Doctrine ORM
sudo pear channel-discover pear.symfony.com
sudo pear channel-discover pear.doctrine-project.org
# no non-interactive option: sudo pear upgrade-all
sudo pear install doctrine/DoctrineORM
cd /usr/share/php/Doctrine
sudo ln -s ../Symfony
cd -

# setup Apache
sudo a2enmod rewrite
sudo rm /etc/apache2/sites-enabled/000-default.conf
sudo cp data/travis/apache.conf /etc/apache2/sites-enabled/000-default.conf
sudo service apache2 restart

sudo chown -R www-data: .
sudo chmod -R u+rX .
sudo chmod -R u+w ./var

# Set up IXP Manager
cp data/travis-ci/configs/* application/configs
cp data/travis-ci/htaccess-noskin public/.htaccess
