#!/bin/bash

pwd 

# install requirements
sudo apt-get update
# sudo apt-get upgrade
sudo apt-get install php5-memcache php5-snmp php-pear apache2 libapache2-mod-php5

# install Doctrine ORM
sudo pear channel-discover pear.symfony.com
sudo pear channel-discover pear.doctrine-project.org
# no non-interactive option: sudo pear upgrade-all
sudo pear install doctrine/DoctrineORM
echo cd /usr/share/php/Doctrine
cd /usr/share/php/Doctrine
echo sudo ln -s ../Symfony
sudo ln -s ../Symfony
echo cd /home/travis/build/inex/IXP-Manager
cd /home/travis/build/inex/IXP-Manager

# setup Apache
sudo a2enmod rewrite
sudo rm /etc/apache2/sites-enabled/*
sudo cp data/travis-ci/apache.conf /etc/apache2/sites-enabled/000-default.conf
sudo service apache2 restart

# Set up IXP Manager
sudo cp data/travis-ci/configs/* application/configs
sudo cp data/travis-ci/htaccess-noskin public/.htaccess

sudo chown -R www-data: .
sudo chmod -R u+rX .
sudo chmod -R u+w ./var

