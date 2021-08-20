#!/usr/bin/env bash

# Installation script for IXP Manager v5 on Ubuntu LTS 18.04

# Barry O'Donovan <barry.odonovan ~at~ inex.ie>
# First version: 2016-10-19

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
# FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
# more details.
#
# You should have received a copy of the GNU General Public License v2.0
# along with IXP Manager.  If not, see:
#
# http://www.gnu.org/licenses/gpl-2.0.html

# Only one argument is available: --no-interaction
# This should ONLY be used for testing such as with Vagrant

# We need to exit on any error. This script is fully tested to run on a bare bones
# Ubuntu LTS 16.04 minimal install with functioning Internet connectivity. This is
# only how it's meant to be run!
set -e

IXPROOT=/srv/ixpmanager
DBNAME=ixpmanager
DBUSER=ixpmanager

# the version / branch to use:
IXPMANAGER_VERSION="release-v5"

touch /tmp/ixp-manager-install.log
chmod a+w /tmp/ixp-manager-install.log

if [[ -n $1 && "$1" = "--no-interaction" ]]; then
    NOINTERACTION="YES"
fi

cat <<WELCOME

===================================================================
               IXP Manager Installation Script
===================================================================

Welcome!

This installation script is for installing IXP Manager v5
on Ubuntu LTS 18.04 **only**.

This script should only be run on a newly installed Ubuntu
system.

If you do not meet these criteria, please Ctrl-c not to
end this script.

If you want to follow progress, open a new session and (as root):

    tail -f /tmp/ixp-manager-install.log

Press return to continue...
WELCOME

function log_break {
    echo -e "\n===========================================================" >>/tmp/ixp-manager-install.log
    date >>/tmp/ixp-manager-install.log
    echo -e "===========================================================\n" >>/tmp/ixp-manager-install.log
}

[[ -n $NOINTERACTION ]] || read


##################################################################
### Preflight checks and System update / upgrade
##################################################################

# Make sure user is root
if [[ $EUID -ne 0 ]]; then
   echo "ERROR: This script must be run as root" 1>&2
   exit 1
fi


echo -n "Updating local package repository... "
log_break && apt-get update -q &>> /tmp/ixp-manager-install.log
echo '[done]'

debconf-set-selections <<< "console-setup console-setup/charmap47 select UTF-8"
debconf-set-selections <<< "console-setup console-setup/codeset47 select Lat15"
export DEBIAN_FRONTEND=noninteractive

echo -n "Doing a full system upgrade to ensure latest packages are installed (be patient)... "
log_break && apt-get dist-upgrade -o "Dpkg::Options::=--force-confold" -yq &>> /tmp/ixp-manager-install.log
apt-get install -yq ubuntu-minimal openssl wget net-tools &>> /tmp/ixp-manager-install.log
apt-get autoremove -yq &>> /tmp/ixp-manager-install.log
echo '[done]'


locale-gen en_IE.UTF-8
export LANG=en_IE.UTF-8

echo -n "Adding ppa:ondrej/php... "
log_break && apt-get install -yq software-properties-common &>> /tmp/ixp-manager-install.log
add-apt-repository -y ppa:ondrej/php &>> /tmp/ixp-manager-install.log
apt-get update -q &>> /tmp/ixp-manager-install.log
echo '[done]'


IPADDRESS=$( ifconfig | awk '/inet /{print $2}' | grep -v '127.0.0.1' | head -1 )



# Make sure the destination directory is available
if [[ -e $IXPROOT ]]; then
    if [[ -d $IXPROOT/.git ]]; then
        echo "WARNING: There already exists a Git repository at the target install destination: $IXPROOT" 1>&2
        echo "If you proceed, we will assume this is for IXP Manager from a previously aborted or"
        echo "failed installation attempt and settings will be overridden."
        echo
        echo "If this is not the case, please abort now and move that directory out of the way."
        echo
        echo "Press Ctrl-c to abort now or enter to continue..."
        [[ -n $NOINTERACTION ]] || read
        log_break && echo "$IXPROOT already exists but user is pressing on..." >>/tmp/ixp-manager-install.log
    else
        echo "WARNING: There already exists a file/directory at the target install destination: $IXPROOT" 1>&2
        echo "Please move it out if the way before continuing"
        exit 1
    fi
fi

# Make sure the database doesn't already exist
if [[ -d /var/lib/mysql/$DBNAME ]]; then
    echo "ERROR: A database called $DBNAME already exists!" 1>&2
    echo "If you proceed, we will DROP this database and reinstall."
    echo "*** ALL DATA WILL BE LOST ***"
    echo "Press Ctrl-c to abort now or enter to continue..."
    [[ -n $NOINTERACTION ]] || read
    log_break && echo "Database $DBNAME already exists but user is pressing on..." >>/tmp/ixp-manager-install.log
fi

# # is something listening on port 80 already? If so, we do not have a clean site:
if [[ $( netstat -lnt | awk '$6 == "LISTEN" && $1 ~ "^tcp[6]*" && $4 ~ ":80$"' | wc -l ) -ne 0 ]]; then
    echo "ERROR: Something is listening in port 80. This is not a bare bones minimal install of Ubuntu" 1>&2
    echo "If you proceed, we will assume this is Apache and overwrite the default site configuration."
    echo "Press Ctrl-c to abort now or enter to continue..."
    [[ -n $NOINTERACTION ]] || read
    log_break && echo "Already listening on tcp/80 but user is pressing on..." >>/tmp/ixp-manager-install.log
fi



##################################################################
### Get user information
##################################################################

echo -e "\n\n##################################################################\n\n"
echo -e "We now need to gather some information from you about this installation:\n\n"
echo -e "(these details can all be changed later - just hitting enter will set defaults)\n\n"

# check to see if we have values from a previous run
if [[ -f $IXPROOT/.ixp-manager-installer-settingsrc ]]; then
    . $IXPROOT/.ixp-manager-installer-settingsrc
elif [[ -f /tmp/.ixp-manager-installer-settingsrc ]]; then
    . /tmp/.ixp-manager-installer-settingsrc
else
    IXPNAME="Somecity Internet Exchange Point"
    IXPSNAME="SCIX"
    IXPCITY="SomeCity"
    IXPCOUNTRY="Country"
    IXPASN="65535"
    IXPPEEREMAIL="peering@example.com"
    IXPNOCEMAIL="noc@example.com"
    IXPNOCPHONE="+555 1 555 1234"
    IXPWWW="http://www.example.com/"
    NAME="Joe Bloggs"
    USEREMAIL="root@localhost"
    USERNAME="jbloggs"

    # generate some passwords (securely):
    MYSQL_ROOT_PW="$( openssl rand -base64 12 )"
    MYSQL_IXPM_PW="$( openssl rand -base64 12 )"
    IXPM_ADMIN_PW="$( openssl rand -base64 12 )"
fi

function get_user_input() {
    local USERINPUT=""
    local __resultvar=$1
    [[ -n $NOINTERACTION ]] || read -p "$2 [$3]: " USERINPUT
    if [[ -z $USERINPUT ]]; then
        USERINPUT="$3"
        echo "  - no input provided, defaulting to: ${USERINPUT}"
        echo
    fi
    eval $__resultvar="\$USERINPUT"
}

get_user_input IXPNAME      "Long name of your IXP"          "$IXPNAME"
get_user_input IXPSNAME     "Short name of your IXP"         "$IXPSNAME"
get_user_input IXPCITY      "Your city"                      "$IXPCITY"
get_user_input IXPCOUNTRY   "Your country"                   "$IXPCOUNTRY"
get_user_input IXPASN       "AS number of your IXP"          "$IXPASN"
get_user_input IXPPEEREMAIL "Your peering email"             "$IXPPEEREMAIL"
get_user_input IXPNOCEMAIL  "Your NOC email"                 "$IXPNOCEMAIL"
get_user_input IXPNOCPHONE  "Your NOC contact phone number"  "$IXPNOCPHONE"
get_user_input IXPWWW       "Your IXP website"               "$IXPWWW"

echo -e "\n\n##################################################################\n\n"

echo -e "Thank you. We now need details for the first user:\n\n"

get_user_input NAME         "Your name"                      "$NAME"
get_user_input USEREMAIL    "Your email"                     "$USEREMAIL"
get_user_input USERNAME     "Your username"                  "$USERNAME"

echo -e "\n\n##################################################################\n\n"

##################################################################
### Remove chars that might cause issues for MySQL / bash
##################################################################

IXPNAME=${IXPNAME//[\'\\\"]/}
IXPSNAME=${IXPSNAME//[\'\\\"]/}
IXPCITY=${IXPCITY//[\'\\\"]/}
IXPCOUNTRY=${IXPCOUNTRY//[\'\\\"]/}
IXPASN=${IXPASN//[\'\\\"]/}
IXPPEEREMAIL=${IXPPEEREMAIL//[\'\\\"]/}
IXPNOCEMAIL=${IXPNOCEMAIL//[\'\\\"]/}
IXPNOCPHONE=${IXPNOCPHONE//[\'\\\"]/}
IXPWWW=${IXPWWW//[\'\\\"]/}
NAME=${NAME//[\'\\\"]/}
USEREMAIL=${USEREMAIL//[\'\\\"]/}
USERNAME=${USERNAME//[\'\\\"]/}


##################################################################
### Store this information for a future runthrough
##################################################################

cat >/tmp/.ixp-manager-installer-settingsrc <<END_SETTINGS
# IXP Manager install script - temp storage of settings
IXPNAME="${IXPNAME}"
IXPSNAME="${IXPSNAME}"
IXPCITY="${IXPCITY}"
IXPCOUNTRY="${IXPCOUNTRY}"
IXPASN="${IXPASN}"
IXPPEEREMAIL="${IXPPEEREMAIL}"
IXPNOCEMAIL="${IXPNOCEMAIL}"
IXPNOCPHONE="${IXPNOCPHONE}"
IXPWWW="${IXPWWW}"
NAME="${NAME}"
USEREMAIL="${USEREMAIL}"
USERNAME="${USERNAME}"
MYSQL_ROOT_PW="${MYSQL_ROOT_PW}"
MYSQL_IXPM_PW="${MYSQL_IXPM_PW}"
IXPM_ADMIN_PW="${IXPM_ADMIN_PW}"
END_SETTINGS

chown root: /tmp/.ixp-manager-installer-settingsrc
chmod 0600 /tmp/.ixp-manager-installer-settingsrc

log_break
echo -e "User input:\n\n" >>/tmp/ixp-manager-install.log

echo "IXPSNAME:     ${IXPSNAME}"     >>/tmp/ixp-manager-install.log
echo "IXPCITY:      ${IXPCITY}"      >>/tmp/ixp-manager-install.log
echo "IXPCOUNTRY:   ${IXPCOUNTRY}"   >>/tmp/ixp-manager-install.log
echo "IXPASN:       ${IXPASN}"       >>/tmp/ixp-manager-install.log
echo "IXPPEEREMAIL: ${IXPPEEREMAIL}" >>/tmp/ixp-manager-install.log
echo "IXPNOCEMAIL:  ${IXPNOCEMAIL}"  >>/tmp/ixp-manager-install.log
echo "IXPNOCPHONE:  ${IXPNOCPHONE}"  >>/tmp/ixp-manager-install.log
echo "IXPWWW:       ${IXPWWW}"       >>/tmp/ixp-manager-install.log
echo "NAME:         ${NAME}"         >>/tmp/ixp-manager-install.log
echo "USEREMAIL:    ${USEREMAIL}"    >>/tmp/ixp-manager-install.log
echo "USERNAME:     ${USERNAME}"     >>/tmp/ixp-manager-install.log

##################################################################
### Gather and log system information
##################################################################

# gather starting details for bug fixing
log_break && uname -a >>/tmp/ixp-manager-install.log
log_break && cat /etc/lsb-release >>/tmp/ixp-manager-install.log
log_break && netstat -lpn >>/tmp/ixp-manager-install.log
log_break && ps -ef >>/tmp/ixp-manager-install.log
log_break && df -h >>/tmp/ixp-manager-install.log
log_break && free >>/tmp/ixp-manager-install.log
log_break && cat /proc/cpuinfo >>/tmp/ixp-manager-install.log
log_break && dpkg -l >>/tmp/ixp-manager-install.log

##################################################################
### MySQL Password
##################################################################

# Defaults for MySQL:
debconf-set-selections <<< "mysql-server mysql-server/root_password password ${MYSQL_ROOT_PW}"
debconf-set-selections <<< "mysql-server mysql-server/root_password_again password ${MYSQL_ROOT_PW}"

##################################################################
### Packages
##################################################################

echo -n "Installing PHP, Apache, MySQL, etc. Please be very patient..."

# Prevent mrtg from prompting
echo mrtg mrtg/conf_mods boolean true | debconf-set-selections

log_break && apt-get install -qy apache2 php7.3 php7.3-intl php-rrd php7.3-cgi php7.3-cli php7.3-snmp php7.3-curl                \
    php-memcached libapache2-mod-php7.3 mysql-server mysql-client php7.3-mysql memcached snmp                                    \
    php7.3-mbstring php7.3-xml php7.3-gd php7.3-bcmath php-gettext bgpq3 php-memcache unzip php7.3-zip git php-yaml php-ds       \
    libconfig-general-perl libnetaddr-ip-perl mrtg  libconfig-general-perl libnetaddr-ip-perl rrdtool librrds-perl curl          \
        &>> /tmp/ixp-manager-install.log
echo '[done]'

# First time you run PHP it may output some setup messages - bury these
php -r 'phpinfo();' &>/dev/null


##################################################################
### Clone IXP Manager
##################################################################

log_break

if [[ -d $IXPROOT/.git ]]; then
    echo -n "Found existing IXP Manager GitHub repository, not pulling / updating..."
    cd $IXPROOT
    git fetch &>> /tmp/ixp-manager-install.log
else
    echo -n "Cloning IXP Manager GitHub repository..."
    git clone https://github.com/inex/IXP-Manager.git $IXPROOT &>> /tmp/ixp-manager-install.log
    cd $IXPROOT
    git checkout $IXPMANAGER_VERSION &>> /tmp/ixp-manager-install.log
fi

# Make www-data the owner for now
chown -R www-data: $IXPROOT

echo '[done]'

##################################################################
### Move settings to the IXP Manager directory
##################################################################

if [[ -f /tmp/.ixp-manager-installer-settingsrc ]]; then
    mv /tmp/.ixp-manager-installer-settingsrc $IXPROOT/.ixp-manager-installer-settingsrc
fi

##################################################################
### Bcrypt hashed password for user
##################################################################

log_break
echo -n "Create Bcrypt hashed password for user..."
echo "Create Bcrypt hashed password for user..." &>> /tmp/ixp-manager-install.log
ADMIN_PW_SALT="$( openssl rand -base64 16 )"
HASH_PW=$( php -r "echo escapeshellarg( crypt( '${IXPM_ADMIN_PW}', sprintf( '\$2a\$%02d\$%s', 10, substr( '${ADMIN_PW_SALT}', 0, 22 ) ) ) );" )
echo "HASH_PW:      ${HASH_PW}" >>/tmp/ixp-manager-install.log
echo '[done]'

##################################################################
### MySQL Setup
##################################################################

echo -n "Creating database and database users..."
mysql -u root "-p${MYSQL_ROOT_PW}" <<END_SQL
DROP DATABASE IF EXISTS \`${DBNAME}\`;
CREATE DATABASE \`${DBNAME}\` CHARACTER SET = 'utf8mb4' COLLATE = 'utf8mb4_unicode_ci';
GRANT ALL ON \`${DBNAME}\`.* TO \`${DBUSER}\`@\`127.0.0.1\` IDENTIFIED BY '${MYSQL_IXPM_PW}';
GRANT ALL ON \`${DBNAME}\`.* TO \`${DBUSER}\`@\`localhost\` IDENTIFIED BY '${MYSQL_IXPM_PW}';
FLUSH PRIVILEGES;
END_SQL
echo '[done]'


##################################################################
### COMPOSER
##################################################################
echo -n "Installing / updating composer - PHP's package manager..."
log_break && echo -n "Installing composer - PHP's package manager... " &>> /tmp/ixp-manager-install.log
cd $IXPROOT

curl -so $IXPROOT/composer.phar https://getcomposer.org/download/1.7.3/composer.phar && \
    chmod a+x $IXPROOT/composer.phar && \
    $IXPROOT/composer.phar selfupdate

echo '[done]'
echo '[done]' &>> /tmp/ixp-manager-install.log


##################################################################
### IXP Manager basic env file
##################################################################

echo -n "Creating .env file..."

cat >$IXPROOT/.env <<END_ENV
#######################################################################################
#
# IXP Manager V4+ configuration.
#
# Initial settings from the installer script on $( date )
#

APP_KEY=

# set this to false in production:
APP_DEBUG=false

# Web address - required for sending emails via CLI scripts, etc.
APP_URL="http://${IPADDRESS}"

# See http://php.net/manual/en/timezones.php for a list of timezones:
APP_TIMEZONE="UTC"

# Laravel log format (strorage/log). See config/log.php
APP_LOG="single"

# MySQL Connection Details
DB_HOST="127.0.0.1"
DB_DATABASE="${DBNAME}"
DB_USERNAME="${DBUSER}"
DB_PASSWORD="${MYSQL_IXPM_PW}"

#######################################################################################
# Identity. Used throughout IXP Manager in various ways.
# This has grown organically and we intend to clean this up in a coming release and
# documenting where and how each one is spceifically used.
IDENTITY_LEGALNAME="${IXPNAME}"
IDENTITY_CITY="${IXPCITY}"
IDENTITY_COUNTRY="${IXPCOUNTRY}"
IDENTITY_ORGNAME="\${IDENTITY_LEGALNAME}"
IDENTITY_NAME="\${IDENTITY_LEGALNAME}"
IDENTITY_EMAIL="${IXPNOCEMAIL}"
IDENTITY_TESTEMAIL="\${IDENTITY_EMAIL}"
IDENTITY_WATERMARK="${IXPNAME}"
IDENTITY_SUPPORT_EMAIL="\${IDENTITY_EMAIL}"
IDENTITY_SUPPORT_PHONE="${IXPNOCPHONE}"
IDENTITY_SUPPORT_HOURS="24x7"
IDENTITY_BILLING_EMAIL="\${IDENTITY_EMAIL}"
IDENTITY_BILLING_PHONE="${IXPNOCEMAIL}"
IDENTITY_BILLING_HOURS="24x7"
IDENTITY_SITENAME="${IXPSNAME} IXP Manager"
IDENTITY_CORPORATE_URL="${IXPWWW}"
IDENTITY_LOGO="/srv/ixpmanager/public/images/ixp-manager.png"
IDENTITY_BIGLOGO="http://www.ixpmanager.org/images/logos/ixp-manager.png"
IDENTITY_BIGLOGO_OFFSET="offset4"
IDENTITY_DEFAULT_VLAN=1

#######################################################################################
# See: https://github.com/inex/IXP-Manager/wiki/Euro-IX-Member-Data-Export
# Think carefully before making this private. IXPs should be open.
IXP_API_JSONEXPORTSCHEMA_PUBLIC=true


#######################################################################################
# See config/ixp.php
IXP_RESELLER_ENABLED=false
IXP_AS112_UI_ACTIVE=true

#######################################################################################
# See config/mail.php
MAIL_HOST=localhost
MAIL_PORT=25
MAIL_PRETEND=false

#######################################################################################
### Graphing - see https://ixp-manager.readthedocs.org/en/latest/features/grapher.html
GRAPHER_BACKENDS="dummy"

#GRAPHER_BACKEND_MRTG_WORKDIR="/tmp"
#GRAPHER_BACKEND_MRTG_LOGDIR="http://stats.example.com/mrtg"
#GRAPHER_BACKEND_SFLOW_ROOT="http://sflow.example.com/sflow"
#GRAPHER_CACHE_ENABLED=true


#######################################################################################
### Skinning: see https://ixp-manager.readthedocs.org/en/latest/features/skinning.html
# VIEW_SKIN="myskin"

#######################################################################################
# See config/cache.php
CACHE_DRIVER=memcached

#######################################################################################
# See config/session.php
SESSION_DRIVER=file

#######################################################################################
# see config/doctrine.php
DOCTRINE_PROXY_AUTOGENERATE=true
DOCTRINE_CACHE=memcached
DOCTRINE_CACHE_NAMESPACE=IXPMANAGERNAMESPACE


IXP_IRRDB_BGPQ3_PATH="/usr/bin/bgpq3"

END_ENV

chown www-data: $IXPROOT/.env
log_break && cat $IXPROOT/.env &>> /tmp/ixp-manager-install.log

echo '[done]'


##################################################################
### Install PHP packages
##################################################################

echo -n "Running composer to install PHP dependencies (please be patient)... "
cd $IXPROOT
log_break
sudo -u www-data bash -c "HOME=$IXPROOT && cd $IXPROOT && ./composer.phar --no-ansi --no-interaction --no-dev --prefer-dist install &>> /tmp/ixp-manager-install.log"
echo '[done]'


##################################################################
### Generate application key
##################################################################
echo -n "Generating application key... "
cd $IXPROOT
log_break && php artisan  key:generate &>> /tmp/ixp-manager-install.log
echo '[done]'


##################################################################
### Set up database
##################################################################

echo -n "Setting up IXP Manager database... "
cd $IXPROOT
log_break && php artisan doctrine:schema:create &>> /tmp/ixp-manager-install.log
log_break && php artisan migrate --force &>> /tmp/ixp-manager-install.log
echo '[done]'

echo -n "Creating IXP Manager database views... "
cd $IXPROOT
log_break && mysql -u root "-p${MYSQL_ROOT_PW}" $DBNAME <tools/sql/views.sql
echo '[done]'



##################################################################
### Set up initial database entities
##################################################################

# We really need a wizard for this but for now:
echo -n "Creating initial database entities..."

mysql -u root "-p${MYSQL_ROOT_PW}" $DBNAME <<END_SQL
INSERT INTO ixp ( name, shortname, address1, country )
    VALUES ( '${IXPNAME}', '${IXPSNAME}', '${IXPCITY}', '${IXPCOUNTRY}' );
SET @ixpid = LAST_INSERT_ID();

INSERT INTO infrastructure ( ixp_id, name, shortname, isPrimary )
    VALUES ( @ixpid, 'Infrastructure #1', '#1', 1 );
SET @infraid = LAST_INSERT_ID();

INSERT INTO company_registration_detail ( registeredName ) VALUES ( '${IXPNAME}' );
SET @crdid = LAST_INSERT_ID();

INSERT INTO company_billing_detail ( billingContactName, invoiceMethod, billingFrequency )
    VALUES ( '${NAME}', 'EMAIL', 'NOBILLING' );
SET @cbdid = LAST_INSERT_ID();

INSERT INTO cust ( name, shortname, type, abbreviatedName, autsys, maxprefixes, peeringemail, nocphone, noc24hphone,
        nocemail, nochours, nocwww, peeringpolicy, corpwww, datejoin, status, activepeeringmatrix, isReseller,
        company_registered_detail_id, company_billing_details_id )
    VALUES ( '${IXPNAME}', '${IXPSNAME}', 3, '${IXPSNAME}', '${IXPASN}', 100, '${IXPPEEREMAIL}', '${IXPNOCPHONE}',
        '${IXPNOCPHONE}', '${IXPNOCEMAIL}', '24x7', '', 'mandatory', '${IXPWWW}', NOW(), 1, 1, 0, @crdid, @cbdid );
SET @custid = LAST_INSERT_ID();

INSERT INTO customer_to_ixp ( customer_id, ixp_id ) VALUES ( @custid, @ixpid );

INSERT INTO user ( custid, name, username, password, email, privs, disabled, created )
    VALUES ( @custid, '${NAME}', '${USERNAME}', ${HASH_PW}, '${USEREMAIL}', 3, 0, NOW() );
SET @userid = LAST_INSERT_ID();

INSERT INTO customer_to_users ( customer_id, user_id, privs, created_at )
    VALUES ( @custid, @userid, 3, NOW() );

INSERT INTO contact ( custid, name, email, created )
    VALUES ( @custid, '${NAME}', '${USEREMAIL}', NOW() );
END_SQL

# And seed the database:
cd $IXPROOT
php artisan db:seed --class=IRRDBs --force
php artisan db:seed --class=Vendors --force
php artisan db:seed --class=ContactGroups --force

echo '[done]'


##################################################################
### Set up Apache and set file system permissions
##################################################################

echo -n "Setting up Apache... "

cat >/etc/apache2/sites-available/000-default.conf <<END_APACHE
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot ${IXPROOT}/public

    <Directory ${IXPROOT}/public>
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

log_break && cat /etc/apache2/sites-available/000-default.conf &>> /tmp/ixp-manager-install.log
a2dismod mpm_worker mpm_event &>> /tmp/ixp-manager-install.log
a2enmod rewrite php7.3 &>> /tmp/ixp-manager-install.log

service apache2 restart &>> /tmp/ixp-manager-install.log
echo '[done]'

##################################################################
### File System Permissions
##################################################################

chown -R root: ${IXPROOT}
chown -R www-data: ${IXPROOT}/storage ${IXPROOT}/bootstrap/cache ${IXPROOT}/database/Proxies \
    ${IXPROOT}/vendor ${IXPROOT}/public/logos   &>> /tmp/ixp-manager-install.log
chmod -R ug+rwX,o+rX ${IXPROOT} &>> /tmp/ixp-manager-install.log

# favicon
cp ${IXPROOT}/public/favicon.ico.dist ${IXPROOT}/public/favicon.ico


##################################################################
### Local config options
##################################################################

# enable contact groups
cp ${IXPROOT}/config/contact_group.php.dist ${IXPROOT}/config/contact_group.php




##################################################################
### Scheduler
##################################################################

# enable scheduler
echo -e "\n\n# IXP Manager cron jobs:\n*  *   * * *   www-data    /usr/bin/php /vagrant/artisan schedule:run\n\n" >>/etc/crontab


##################################################################
### Completion Details
##################################################################

tee /root/ixp-manager-install-details.txt <<END_SUCCESS

##################################################################
###  Congratulations!
##################################################################

Your new IXP Manager installation can be accessed via:

    http://${IPADDRESS}/

using the following login details:

Username: ${USERNAME}        Password: ${IXPM_ADMIN_PW}

During the installation, we also installed MySQL and set the
root password to: $MYSQL_ROOT_PW

If you plan to use this in production, you should:

 - edit the $IXPROOT/.env file 
 - secure your server with an iptables firewall
 - install an SSL certificate and redirect HTTP access to HTTPS
 - complete the installation of the many features of IXP Manager such
   as route server generation, member stats, peer to peer graphs, etc.
   These are all documented at: https://ixp-manager.readthedocs.org/
 - PLEASE TELL US! We'd like to add you to the users list at
   http://www.ixpmanager.org/users.php - just drop us an email to
   operations@inex.ie.

If your happy that the installer has successfully completed, you should:
    rm $IXPROOT/.ixp-manager-installer-settingsrc

A copy of this message (with passwords) can be found at:
    /root/ixp-manager-install-details.txt

Enjoy!

END_SUCCESS

chown root: /root/ixp-manager-install-details.txt
chmod u=rw,go-rwx /root/ixp-manager-install-details.txt

chown root: $IXPROOT/.ixp-manager-installer-settingsrc
chmod 0600 $IXPROOT/.ixp-manager-installer-settingsrc

echo "(the above message and password details have been copied to /root/ixp-manager-install-details.txt)"
