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

## SET THE FOLLOWING VARIABLES APPROPRIATELY

KEY="your-ixp-manager-apikey-here"
URL="https://www.example.com/ixp/api/v4/nagios"

# where to store the Nagios host/service configuration files:
CONFPATH="/usr/local/etc/nagios/conf.d"

# Main Nagios configuration file:
NAGIOSCONF="/etc/nagios/nagios.cfg"

# nagios binary:
NAGIOS="/usr/local/bin/nagios"

# Command to make Nagios reload its configuration:
NAGIOS_RELOAD="/usr/local/etc/rc.d/nagios reload"

# List of infrastructure IDs to create switch targets for:
# INFRA="1 2"
INFRA=""

# List of VLANs to generate customer reachability / host targets for
# VLANS="1 2"
VLANS=""

# List IP protocols:
PROTOCOLS="4 6"

# BIRDTYPE: 1 = route servers | 2 = route collectors | 3 = as112
# To create BGP session checks for all routers, set this to:
# BIRDTYPE="1 2 3"
BIRDTYPE=""

# BIRDTEMPLATE: set to name of custom template name, otherwise leave
# as default
BIRDTEMPLATE="default"

### END "SET THE FOLLOWING VARIABLES APPROPRIATELY" ###

# SCRIPTPATH - to ensure scripts are always run in the same directory
SCRIPTPATH=$PWD
