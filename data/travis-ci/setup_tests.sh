#!/bin/bash

# Travis CI setup script for testing IXP Manager
#
# Copyright (C) 2014 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
# Author: Barry O'Donovan <barry@opensolutions.ie>
#
# License: http://www.gnu.org/licenses/gpl-2.0.html

# let us know where we are in case anything goes wrong
pwd

# install requirements
# sudo apt-get update >/dev/null
# sudo apt-get upgrade
#sudo apt-get install php-memcache php7.0-snmp php-pear
#phpenv config-add data/travis-ci/configs/ixp-php.ini

echo cd /home/travis/build/inex/IXP-Manager
cd /home/travis/build/inex/IXP-Manager || exit
echo phpenv rehash
phpenv rehash

echo cp .env.travisci .env
cp .env.travisci .env

# Set up IXP Manager
sudo touch public/.htaccess

echo composer install --no-interaction --prefer-dist --no-suggest
composer install --no-interaction --prefer-dist --no-suggest


echo php artisan dusk:chrome-driver
php artisan dusk:chrome-driver

bzcat data/travis-ci/travis_ci_test_db.sql.bz2  | mysql --default-character-set=utf8mb4 -h 127.0.0.1 -u travis myapp_test


echo google-chrome-stable --headless --disable-gpu --remote-debugging-port=9222 http://localhost
google-chrome-stable --headless --disable-gpu --remote-debugging-port=9222 http://localhost &


./artisan serve  &>php-built-in.log &

