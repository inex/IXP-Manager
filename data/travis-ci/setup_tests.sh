#!/bin/bash

# Travis CI setup script for testing IXP Manager
# 
# Copyright (C) 2014-2016 Internet Neutral Exchange Association Company Limited By Guarantee.
# Author: Barry O'Donovan <barry@opensolutions.ie>
#
# License: http://www.gnu.org/licenses/gpl-2.0.html

# let us know where we are in case anything goes wrong
pwd 

# install requirements
sudo apt-get update >/dev/null
# sudo apt-get upgrade
sudo apt-get install php5-memcache php5-snmp php-pear
phpenv config-add data/travis-ci/configs/ixp-php.ini

# install Doctrine ORM
sudo pear channel-discover pear.symfony.com
sudo pear channel-discover pear.doctrine-project.org
sudo pear install doctrine/DoctrineORM
echo cd /usr/share/php/Doctrine
cd /usr/share/php/Doctrine
echo sudo ln -s ../Symfony
sudo ln -s ../Symfony
echo cd /home/travis/build/inex/IXP-Manager
cd /home/travis/build/inex/IXP-Manager
echo phpenv rehash
phpenv rehash

# Set up IXP Manager
sudo cp data/travis-ci/configs/* application/configs
sudo cp data/travis-ci/htaccess-none-skin public/.htaccess

mysql -e "CREATE DATABASE myapp_test CHARACTER SET = 'utf8mb4' COLLATE = 'utf8mb4_unicode_ci';"
bzcat data/travis-ci/travis_ci_test_db.sql.bz2  | mysql --default-character-set=utf8mb4 -h 127.0.0.1 -u travis myapp_test 
sudo cp data/travis-ci/phpunit.xml ./phpunit.xml

php -S 127.0.0.1:8080 -t public/  &>php-built-in.log &


