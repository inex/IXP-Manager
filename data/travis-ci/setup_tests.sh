#!/bin/bash

# install requirements
sudo apt-get install php5-memcache
sudo apt-get install php5-snmp

# install Doctrine ORM
sudo apt-get install php-pear
sudo pear channel-discover pear.symfony.com
sudo pear channel-discover pear.doctrine-project.org
sudo pear install doctrine/DoctrineORM
cd /usr/share/php/Doctrine
sudo ln -s ../Symfony
cd -

# install Apache
sudo apt-get install apache2
sudo a2enmod rewrite

