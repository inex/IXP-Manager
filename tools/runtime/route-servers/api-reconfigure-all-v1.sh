#!/bin/bash
#
# Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee.
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

# Example script used by INEX to rebuild all Bird route servers via API on demand.


# NB: THIS IS AN OLDER VERSION OF THE SCRIPT FOR USE ON THE OLD API (apiv1).
# THIS IS OFFICIAL DEPRECATED AND WILL BE REMOVED IN A LATER RELEASE.


# Edit as appropriate

echo "Reconfiguring all bird instances:"

for vlanid in x y z; do
    echo -ne "VLAN ID ${vlanid}: "
    for proto in 4 6; do
        echo -ne "\tIPv${proto}: "
        /usr/local/sbin/api-reconfigure-example.sh -v $vlanid -p $proto -q
        if [[ $? -eq 0 ]]; then
            echo -ne "OK    "
        else
            echo -ne "ERROR "
        fi
    done
    echo
done
