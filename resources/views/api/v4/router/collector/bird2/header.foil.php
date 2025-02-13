<?php
/*
 * Bird Route Collector Configuration Template
 *
 *
 * You should not need to edit these files - instead use your own custom skins. If
 * you can't effect the changes you need with skinning, consider posting to the mailing
 * list to see if it can be achieved / incorporated.
 *
 * Skinning: https://ixp-manager.readthedocs.io/en/latest/features/skinning.html
 *
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */
?>
#
# Bird v2 Route Collector configuration generated by IXP Manager
#
# Do not edit this file, it will be overwritten.
#
# Generated: <?= date('Y-m-d H:i:s') . "\n" ?>
#

# For VLAN: <?= $t->vlan->name ?> (Tag: <?= $t->vlan->number ?>, Database ID: <?= $t->vlan->id ?>)


# standardise time formats:
timeformat base         iso long;
timeformat log          iso long;
timeformat protocol     iso long;
timeformat route        iso long;


log "/var/log/bird/<?= $t->handle ?>.log" all;
log syslog all;

define routerasn     = <?= $t->router->asn        ?>;
define routeraddress = <?= $t->router->peering_ip ?>;

router id <?= $t->router->router_id ?>;

# ignore interface up/down events
protocol device { }


# This function excludes weird networks
#  rfc1918, class D, class E, too long and too short prefixes
function avoid_martians() -> bool
prefix set martians;
{
<?php if( $t->router->protocol == 6 ): ?>

    martians = [
        ::/0,                   # Default (can be advertised as a route in BGP to peers if desired)
        ::/96,                  # IPv4-compatible IPv6 address - deprecated by RFC4291
        ::/128,                 # Unspecified address
        ::1/128,                # Local host loopback address
        ::ffff:0.0.0.0/96+,     # IPv4-mapped addresses
        ::224.0.0.0/100+,       # Compatible address (IPv4 format)
        ::127.0.0.0/104+,       # Compatible address (IPv4 format)
        ::0.0.0.0/104+,         # Compatible address (IPv4 format)
        ::255.0.0.0/104+,       # Compatible address (IPv4 format)
        0000::/8+,              # Pool used for unspecified, loopback and embedded IPv4 addresses
        0200::/7+,              # OSI NSAP-mapped prefix set (RFC4548) - deprecated by RFC4048
        3ffe::/16+,             # Former 6bone, now decommissioned
        2001:db8::/32+,         # Reserved by IANA for special purposes and documentation
        2002:e000::/20+,        # Invalid 6to4 packets (IPv4 multicast)
        2002:7f00::/24+,        # Invalid 6to4 packets (IPv4 loopback)
        2002:0000::/24+,        # Invalid 6to4 packets (IPv4 default)
        2002:ff00::/24+,        # Invalid 6to4 packets
        2002:0a00::/24+,        # Invalid 6to4 packets (IPv4 private 10.0.0.0/8 network)
        2002:ac10::/28+,        # Invalid 6to4 packets (IPv4 private 172.16.0.0/12 network)
        2002:c0a8::/32+,        # Invalid 6to4 packets (IPv4 private 192.168.0.0/16 network)
        fc00::/7+,              # Unicast Unique Local Addresses (ULA) - RFC 4193
        fe80::/10+,             # Link-local Unicast
        fec0::/10+,             # Site-local Unicast - deprecated by RFC 3879 (replaced by ULA)
        ff00::/8+               # Multicast
    ];

<?php else: ?>

    martians = [
        0.0.0.0/32-,            # rfc5735 Special Use IPv4 Addresses
        0.0.0.0/0{0,7},         # rfc1122 Requirements for Internet Hosts -- Communication Layers 3.2.1.3
        10.0.0.0/8+,            # rfc1918 Address Allocation for Private Internets
        100.64.0.0/10+,         # rfc6598 IANA-Reserved IPv4 Prefix for Shared Address Space
        127.0.0.0/8+,           # rfc1122 Requirements for Internet Hosts -- Communication Layers 3.2.1.3
        169.254.0.0/16+,        # rfc3927 Dynamic Configuration of IPv4 Link-Local Addresses
        172.16.0.0/12+,         # rfc1918 Address Allocation for Private Internets
        192.0.0.0/24+,          # rfc6890 Special-Purpose Address Registries
        192.0.2.0/24+,          # rfc5737 IPv4 Address Blocks Reserved for Documentation
        192.168.0.0/16+,        # rfc1918 Address Allocation for Private Internets
        198.18.0.0/15+,         # rfc2544 Benchmarking Methodology for Network Interconnect Devices
        198.51.100.0/24+,       # rfc5737 IPv4 Address Blocks Reserved for Documentation
        203.0.113.0/24+,        # rfc5737 IPv4 Address Blocks Reserved for Documentation
        224.0.0.0/4+,           # rfc1112 Host Extensions for IP Multicasting
        240.0.0.0/4+            # rfc6890 Special-Purpose Address Registries
    ];

<?php endif; ?>

    # Avoid RFC1918 and similar networks
    if net ~ martians then
        return false;

    return true;
}




##
## Route collector client configuration
##
