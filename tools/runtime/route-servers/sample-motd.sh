#! /bin/sh

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
# FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
# more details.
#
# You should have received a copy of the GNU General Public License v2.0
# along with IXP Manager.  If not, see:
#
# http://www.gnu.org/licenses/gpl-2.0.html

# Here is a sample motd (message of the day) that sysadmins at INEX see when 
# logging into a route server.
#
# Place this file at /etc/update-motd.d/99-ixp-manager-motd
#

cat <<END_MOTD

========================== Route Server #1 ===============================

To start / reconfigure all Bird BGP daemons, execute:

sudo /usr/local/sbin/reconfigure-rs1-all.sh

To start / reconfigure a single instance:

sudo /usr/local/sbin/reconfigure-rs1.sh -h [handle]

where handle is rs1-lan[12]ipv[46] (e.g. rs1-lan2-ipv6)

Bird control:

sudo birdc  -s /var/run/bird/bird-rs1-lan1-ipv4.ctl
sudo birdc6 -s /var/run/bird/bird-rs1-lan1-ipv6.ctl
sudo birdc  -s /var/run/bird/bird-rs1-lan2-ipv4.ctl
sudo birdc6 -s /var/run/bird/bird-rs1-lan2-ipv6.ctl

===========================================================================

END_MOTD
