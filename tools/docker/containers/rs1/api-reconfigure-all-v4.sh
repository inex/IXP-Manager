#!/bin/bash
#
# Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
# All Rights Reserved.
#
# This file is part of IXP Manager.
#
# IXP Manager is free software: you can redistribute it and/or modify it
# under the terms of the GNU General Public License as published by the Free
# Software Foundation, version 2.0 of the License.
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
#
# Example script used by INEX to rebuild all Bird route servers via API on demand.
#
# Author: Barry O'Donovan <barry@opensolutions.ie>

while true; do
    /usr/bin/wget -O /dev/null -q http://ixpmanager-www/api/v4/test;
    if [[ $? -eq 0 ]]; then
        break;
    fi;
    echo 'Waiting for ixpmanager/www';
    sleep 2;
done

echo "Reconfiguring all bird instances:"

# These handles should match the definitions in config/routers.php, and
# should be changed as appropriate:

for handle in rs1-ipv4 rs1-ipv6; do
    echo -ne "HANDLE: ${handle}: "
    /usr/local/sbin/api-reconfigure-v4.sh -h $handle -q
    if [[ $? -eq 0 ]]; then
        echo -ne "OK    "
    else
        echo -ne "ERROR "
    fi
    echo
done
