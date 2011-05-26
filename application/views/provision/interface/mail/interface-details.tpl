
Dear INEX Member,

Please take some time to read this email -- it contains important information concerning your newly provisioning INEX port.


Interface Details
==================

We have provisioned the following new interface for your use.

INEX operates multiple peering LANs (e.g. our primary LANs, dedicated VoIP peering LANs, etc). Unless you have been advised otherwise, you have been assigned an untagged port on the primary peering LAN(s). Please contact us for more information on the other LANs.

We have assigned the following IP addresses and switch-ports for your exclusive use:

Switch Port:     {$progress.Physicalinterface.Switchport.SwitchTable.name}.inex.ie, {$progress.Physicalinterface.Switchport.name}
Speed:           {$progress.Physicalinterface.speed}Mbps
Duplex:          {$progress.Physicalinterface.duplex}
Location:        {$progress.Physicalinterface.Switchport.SwitchTable.Cabinet.Location.name}
Colo Cabinet ID: {$progress.Physicalinterface.Switchport.SwitchTable.Cabinet.name}


{assign var='vlanid' value=$progress.Vlaninterface.vlanid}
{$progress.Vlaninterface.Vlan.name}:

IPv4 Address:  {$progress.Vlaninterface.Ipv4address.address}/{$networkInfo.$vlanid.4.masklen}
IPv4 Hostname: {$progress.Vlaninterface.ipv4hostname}
IPv6 Address:  {if $progress.Vlaninterface.Ipv6address.address}{$progress.Vlaninterface.Ipv6address.address}/{$networkInfo.$vlanid.6.masklen}{else}(please contact us to enable IPv6){/if}

IPv6 Hostname: {$progress.Vlaninterface.ipv6hostname}



Your Input
----------

As a matter of policy, INEX hard-wires all switch ports to a specific speed and duplex setting.  Should you require a different duplex setting to the one specified above, or should you require your reverse DNS hostnames changed for either IPv4 or IPv6, please contact operations@inex.ie


Route Servers
=============

INEX operates a Route Server cluster; this facility allows all members who connect to the cluster to see all routing prefixes sent to the cluster by any other member.  I.e. it provides an quick, safe and easy way to peer with any other route server user.

The INEX route server cluster is aimed at:

o  small to medium sized members of the exchange who don't have the time or resources to aggressively manage their peering relationships.

o  larger members of the exchange who have an open peering policy, but where it may not be worth their while managing peering relationships with smaller members of the exchange.

If you don't have any good reasons not to use the route server cluster, you should probably use it.

The service is designed to be reliable. It operates on two physical servers, each located in a different data centre. The service is available on all INEX networks (public peering lans #1 and #2, and voip peering lans #1 and #2), on both ipv4 and ipv6.  The route servers also filter inbound routing prefixes based on published RIPE IRR policies, which means that using the route servers for peering is generally much safer than peering directly with other members.


Your Input
----------

If you wish to use the INEX route server system, please email operations@inex.ie.


-----------


External Connections to the INEX Infrastructure
===============================================

Many INEX members already have a presence in the co-location facilities which house INEX points of presence.  If you are such a member and wish to keep your routing equipment in your own cabinet space, INEX is happy to accept cross-connects from your cabinet to the INEX cage.  Please note that all connections into the INEX cage are paid for directly by INEX members, not by INEX itself.

INEX considers metro ethernet connections as standard external connections. When ordering your metro ethernet circuit, please specify the correct INEX termination point to your provider.

Cross-connect cabinet termination points are provided in the "Connection Details" section above.


Your Input
----------

If you want to connect to INEX using a cross-connect, you must order it directly from your co-lo / metro ethernet provider and inform INEX Operations immediately.  Please ensure that you include full details on the connection type and termination cabinet when ordering the cross-connect from your co-lo provider.

----------


Peering
=======

INEX facilitates peering between its members, but other than the minimum current peering requirements (4 members or 10%, whichever is larger) does not mandate peering with any particular member apart from INEX itself.

You will find a full list of members on the INEX members web page, along with the correct email addresses to use for peering requests.

When emailing other INEX members about peering requests, please include all technical details relevant to the peering session, including your IP address, your AS number, and an estimate of the number of prefixes you intend to announce to that candidate peer.  Several members require written legal contracts to be signed as a part of their peering procedures.  If you require a written contract, please specify this on your peering request; similarly, it may be often useful to indicate your willingness (or otherwise) to sign legal contracts when approaching other members about peering.

Please note that INEX members are required to reply to peering requests within a reasonable time frame.  If your emails to other INEX members about peering go unanswered, please let us know and we will do what we can.

INEX requires that all new members peer and share routes with the INEX route collectors for administrative purposes.  We would be obliged if you could set up your router(s) and make the necessary arrangements for this as soon as possible.

INEX's details are:

remote-as:      AS2128
AS Macro:       AS-INEXIE

Peering VLAN #1
        IPv4 address:       193.242.111.126
        IPv4 session MD5:   {$progress.Vlaninterface.ipv4bgpmd5secret}

        IPv6 address:       2001:7F8:18::F:0:1
        IPv4 session MD5:   {$progress.Vlaninterface.ipv6bgpmd5secret}

Peering VLAN #2
        IPv4 address  :     194.88.240.126
        IPv4 session MD5:   {$progress.Vlaninterface.ipv4bgpmd5secret}

        IPv6 address:       2001:7F8:18:12::F:0:1
        IPv4 session MD5:   {$progress.Vlaninterface.ipv6bgpmd5secret}


INEX currently announces two prefixes over IPv4 and one prefix over IPv6 from AS2128:

        193.242.111.0/24
        194.88.240.0/23

        2001:7F8:18::/48


NOC Details
===========

For the convenience of its members, INEX maintains a list of NOC and peering contact details for its members.  These details are held on a private INEX database, and are available only from the following URL:

        https://www.inex.ie/members/memberlist

This area of the INEX website is password protected and SSL secured. Passwords are only provided to current INEX members.  This information is considered private and will not be passed on to other third parties by INEX.

We would appreciate if you could take the time to ensure that the following details we hold on file are correct:

Your Input
----------

Member name:                    {$progress.Cust.name}
Primary corporate web page:     {$progress.Cust.corpwww}
Peering Email Address:          {$progress.Cust.peeringemail}
NOC Phone number:               {$progress.Cust.nocphone}
NOC Fax number:                 {$progress.Cust.nocfax}
General NOC email address:      {$progress.Cust.nocemail}
NOC Hours:                      {$progress.Cust.nochours}
Dedicated NOC web page:         {$progress.Cust.nocwww}
AS Number:                      {$progress.Cust.autsys}

----------


Router Configuration
====================

If you are new to internet exchanges, we would ask you to note that all members are expected to adhere to the technical requirements of the INEX MoU.  In particular, we would draw your attention to section 2 of these requirements which outline what types of traffic may and may not be forwarded to the INEX peering LAN.

For Cisco IOS based routers, we recommend the following interface configuration commands:

 no ip redirects
 no ip proxy-arp
 no ip directed-broadcast
 no mop enabled
 no cdp enable
 udld port disable

If you intend to use IPv6 with a Cisco IOS based router, please also consider the following interface commands:

 no ipv6 redirects
 ipv6 nd suppress-ra


Connecting Switches to INEX
===========================

Many members choose to connect their INEX port to a layer 2 switch and then forward their peering traffic to a router virtual interface hosted elsewhere on their network.  While connecting layer 2 switches to the INEX peering LAN is not actively discouraged, incorrect configuration can cause serious and unexpected connectivity problems.

The primary concern is to ensure that only traffic from the router subinterface is presented to the INEX port. INEX implements per port mac address counting: if more than 1 mac address is seen on any switch port at any time, that port will automatically be disabled for a cooling off period, and your connectivity to INEX will temporarily be lost.

This policy prevents two potential problems: firstly, it ensures that layer 2 traffic loops are prevented and secondly, it ensures that no other traffic escapes to the INEX peering LAN which shouldn't be seen there.

If you choose to connect your INEX port or ports to a switch, it is critically important to assign one unique vlan for each INEX connection.  If you share an INEX facing VLAN between multiple INEX ports or share a INEX-facing VLAN with any other network, your connection will automatically be shut down due to the security mechanisms implemented by INEX.

Please also note that by default, several switch models send link-local traffic to all ports.  On Cisco switches, this can be disabled using the following interface commands:

interface GigabitEthernetx/x
 spanning-tree bpdufilter enable
 no keepalive
 no cdp enable
 udld port disable

For further details please see the following URL:

        https://www.inex.ie/members/connectingswitches

Note also that as we hard code speed and duplex settings, if you are connecting at 100Mbps and connecting from a switch, you must ensure to use a cross over cable.

Monitoring
==========

By default, INEX actively monitors all ports on its peering LANs using ICMP PING for both connectivity and host latency.  This monitoring causes about 25 PING packets to be sent to each IP address on the peering LAN every 5 minutes.  If you do not wish for your router to be actively monitored, please mail operations@inex.ie and we can disable this facility.



PeeringDB
=========

PeeringDB ( http://www.peeringdb.com/ ) facilitates the exchange of information related to peering. Specifically, what networks are peering, where they are peering, and if they are likely to peer with you.

More and more organisations are using PeeringDB to make decisions on where they should open POPs, provision new links, etc.

We would very much appreciate it if you could mark your new INEX peering under the "Public Peering Locations" section of your PeeringDB page. We are listed as 'INEX'. If you do not yet have a PeeringDB account, we would suggest that you register for one on their site.


Welcome to INEX, Ireland's Internet hub.


INEX Operations
INEX - Internet Neutral Exchange Association


