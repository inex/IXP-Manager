#! /usr/bin/env bash
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
# Denis Nolan <denis.nolan -at- inex.ie>, July 2017
# Barry O'Donovan <barry.odonovan -at- inex.ie>

# Example script for updating Nagios target configuration from IXP Manager

# See: http://docs.ixpmanager.org/features/nagios/

if [[ $RELOAD -eq 1 ]]; then
    if [ $DEBUG -ne 0 ]; then
        echo "Nagios reloading..."
        $NAGIOS -v $NAGIOSCONF && $NAGIOS_RELOAD
    else
        $NAGIOS -v >/dev/null && $NAGIOS_RELOAD &>/dev/null 2>&1
    fi
else
    if [ $DEBUG -ne 0 ]; then
        echo "Nagios not reloading as no reload scheduled."
    fi
fi

exit 0
