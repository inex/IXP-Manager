
Dear New INEX Member,

Firstly, welcome to INEX! This is your INEX operations welcome e-mail.

Please take some time to read this email -- it contains important information concerning your INEX membership.

Several sections of this email require your input; these are all clearly marked by the heading "Your Input".  We would appreciate if you could provide us with all the details requested as this will allow us to provide you and other INEX members with a better quality of service.


Connection Details
==================

You have opted to connect to INEX using {$connections|@count} Ethernet ports.

Unless you have been advised otherwise, you have been assigned an untagged port on the primary peering LAN(s).

We have assigned the following IP addresses and switch-ports for your exclusive use:

{foreach from=$connections item=connection}
Connection {counter name=numconnections}
------------

Switch Port:     {$connection.Physicalinterface.0.Switchport.SwitchTable.name}.inex.ie, {$connection.Physicalinterface.0.Switchport.name}
Speed:           {$connection.Physicalinterface.0.speed}Mbps
Duplex:          {$connection.Physicalinterface.0.duplex}
Location:        {$connection.Physicalinterface.0.Switchport.SwitchTable.Cabinet.Location.name}
Colo Cabinet ID: {$connection.Physicalinterface.0.Switchport.SwitchTable.Cabinet.name}


{foreach from=$connection.Vlaninterface item=interface}
{assign var='vlanid' value=$interface.vlanid}
{$interface.Vlan.name}:

IPv4 Address:  {$interface.Ipv4address.address}/{$networkInfo.$vlanid.4.masklen}
IPv4 Hostname: {$interface.ipv4hostname}
IPv6 Address:  {if $interface.Ipv6address.address}{$interface.Ipv6address.address}/{$networkInfo.$vlanid.6.masklen}{else}(please contact us to enable IPv6){/if}

IPv6 Hostname: {$interface.ipv6hostname}
{/foreach}


{/foreach}

Your Input
----------

As a matter of policy, INEX hard-wires all switch ports to a specific speed and duplex setting.  Should you require a different duplex setting to the one specified above, or should you require your reverse DNS hostnames changed for either IPv4 or IPv6, please contact operations@inex.ie

**If you are connecting to INEX via a switch using UTP, please ensure to use a cross over cable on your end.**


Member Portal :: IXP Manager
============================

INEX provides a portal for members which provides traffic graphs for your ports, the ability to self-provision some services, the contact and peering details of all other members, a Peering Manager tool, documentation, support information, mailing list subscription management and much more.

Every member is assigned an Administration account with which you then create individual user accounts. The Administration account is only meant for this purpose and as such, all functionality is only available through user accounts.

{if $custadmin}
We have created your administration account with the username: {$custadmin.username}

The email associated with this is {$custadmin.email}. Please browse to the following page and use the 'Forgotten Password' facility to set a new password for this account.

https://www.inex.ie/ixp/auth/forgotten-password
{else}
Please contact us for your account details at operations@inex.ie.
{/if}



Route Servers
=============

INEX operates a Route Server cluster; this facility allows all members who connect to the cluster to see all routing prefixes sent to the cluster by any other member.  I.e. it provides an quick, safe and easy way to peer with any other route server user.

The INEX route server cluster is aimed at:

o  small to medium sized members of the exchange who don't have the time or resources to aggressively manage their peering relationships.

o  larger members of the exchange who have an open peering policy, but where it may not be worth their while managing peering relationships with smaller members of the exchange.

If you don't have any good reasons not to use the route server cluster, you should probably use it.

The service is designed to be reliable. It operates on two physical servers, each located in a different data centre. The service is available on all INEX networks (public peering lans #1 and #2, and voip peering lans #1 and #2), on both ipv4 and ipv6.  The route servers also filter inbound routing prefixes based on published RIPE IRR policies, which means that using the route servers for peering is generally much safer than peering directly with other members.

See https://www.inex.ie/ixp/dashboard/rs-info for more information.

Your Input
----------

If you wish to use the INEX route server system, please email operations@inex.ie or you can enable the sessions yourself via the IXP Manager.


Hosting Routers in the INEX Cage
================================

If your company does not have a presence in any of the INEX's co-location facilities, INEX can provide rack space for the purpose of housing routers, NTU's and other equipment whose purpose is to facilitate the transmission of IP packets through the exchange.  INEX also permits its members to install modems for the purpose of out-of-band access to their equipment.  Each member is assigned 4U of cabinet space by default.  If your company needs more space than this, INEX needs to know about it.


Your Input
----------

If you wish to house router equipment in the INEX cage, please inform INEX operations immediately and provide full details on all the equipment you intend to house in the facility.  We can liaise with you directly to get your equipment installed safely.

If you wish to bring a PSTN phone line into the INEX cage to connect a modem to your routing equipment, please let INEX Operations know immediately.

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


Mailing Lists
=============

To encourage co-operation between INEX members, INEX provides mailing list services.  There are currently three mailing lists:

Name:                   tech@inex.ie
Purpose:                INEX technical discussion
Subscription Policy:    individuals, roles, aliases,
                        no limit per organisation
Distribution:           private

Name:                   committee@inex.ie
Purpose:                The INEX Executive Committee
Subscription Policy:    elected committee members only
Distribution:           private & confidential

Name:                   members@inex.ie
Purpose:                Non-technical discussions relevant to INEX
Subscription Policy:    individuals only, no limit per organisation
Distribution:           private & confidential


Your Input
----------

Should you wish to subscribe to either or both mailing lists, please create a user account on the IXP Manager (as explained above) and then browse to the 'Profile' page where you can subscribe to the above and more mailing lists.

----------


INEX Operations
===============

Technical Operations for INEX are provided by Network Ability Ltd (with additional support from Open Source Solutions Ltd). Technical support contact details for INEX can be found at:

        https://www.inex.ie/ixp/dashboard/static/page/support

In general, the best way to contact INEX operations is by email at: operations@inex.ie.  If there is an emergency requiring immediate assistance, please contact one of us on the mobile phones listed on the web page.


Peering
=======

INEX facilitates peering between its members, but other than the minimum current peering requirements (4 members or 10%, whichever is larger) does not mandate peering with any particular member apart from INEX itself.

You will find a full list of members on the IXP Manager, along with the correct email addresses to use for peering requests.

When emailing other INEX members about peering requests, please include all technical details relevant to the peering session, including your IP address, your AS number, and an estimate of the number of prefixes you intend to announce to that candidate peer.  Several members require written legal contracts to be signed as a part of their peering procedures.  If you require a written contract, please specify this on your peering request; similarly, it may be often useful to indicate your willingness (or otherwise) to sign legal contracts when approaching other members about peering.

The My Peering Manager tool in the IXP Manager will compose mails with the above details for you automatically.

Please note that INEX members are required to reply to peering requests within a reasonable time frame.  If your emails to other INEX members about peering go unanswered, please let us know and we will do what we can.

INEX requires that all new members peer and share routes with the INEX route collectors for administrative purposes.  We would be obliged if you could set up your router(s) and make the necessary arrangements for this as soon as possible.

INEX's details are:

remote-as:      AS2128
AS Macro:       AS-INEXIE

Peering VLAN #1
        IPv4 address:       193.242.111.126
        IPv4 session MD5:   {$connections.0.Vlaninterface.0.ipv4bgpmd5secret}

        IPv6 address:       2001:7F8:18::F:0:1
        IPv6 session MD5:   {$connections.0.Vlaninterface.0.ipv6bgpmd5secret}

Peering VLAN #2
        IPv4 address  :     194.88.240.126
        IPv4 session MD5:   {$connections.0.Vlaninterface.0.ipv4bgpmd5secret}

        IPv6 address:       2001:7F8:18:12::9999
        IPv6 session MD5:   {$connections.0.Vlaninterface.0.ipv6bgpmd5secret}


INEX currently announces two prefixes over IPv4 and one prefix over IPv6 from AS2128:

        193.242.111.0/24
        194.88.240.0/23

        2001:7F8:18::/48


NOC Details
===========

For the convenience of its members, INEX maintains a list of NOC and peering contact details for its members.  These details are held on a private INEX database, and are available only from the IXP Manager on the following URL:

        https://www.inex.ie/ixp/dashboard/members-details-list

This area of the INEX website is password protected and SSL secured. Passwords are only provided to current INEX members.  This information is considered private and will not be passed on to other third parties by INEX.

We would appreciate if you could take the time to ensure that the following details we hold on file are correct:

Your Input
----------

Member name:                    {$connections.0.Cust.name}
Primary corporate web page:     {$connections.0.Cust.corpwww}
Peering Email Address:          {$connections.0.Cust.peeringemail}
NOC Phone number:               {$connections.0.Cust.nocphone}
NOC Fax number:                 {$connections.0.Cust.nocfax}
General NOC email address:      {$connections.0.Cust.nocemail}
NOC Hours:                      {$connections.0.Cust.nochours}
Dedicated NOC web page:         {$connections.0.Cust.nocwww}
AS Number:                      {$connections.0.Cust.autsys}

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

        https://www.inex.ie/ixp/dashboard/static/page/switches


Monitoring
==========

By default, INEX actively monitors all ports on its peering LANs using ICMP PING for both connectivity and host latency.  This monitoring causes about 25 PING packets to be sent to each IP address on the peering LAN every 5 minutes.  If you do not wish for your router to be actively monitored, please mail operations@inex.ie and we can disable this facility.


IRC
===

INEX member staff and other INEX member employees may regularly be seen on irc.inex.ie (port 6697, SSL only), channel #inex-ops. This channel is password protected; the password for the channel may be found on the following web page:

        https://www.inex.ie/ixp/dashboard/static/page/misc-benefits

Although this IRC server is secured with SSL, INEX does not recommend swapping passwords or any other private / confidential information on this facility.


AS112 Service
=============

For the benefit of its members, INEX hosts an AS112 nameserver which answers bogus requests to private IP address space.  This service is available as a regular peering host on both INEX peering LANs.  Its IP addreses are: 193.242.111.6 and 194.88.240.6.  Should you wish to peer directly with the AS112 server, please contact INEX operations, and we can set up a peering session on the unit.  Otherwise, AS112 is also visible on the INEX route server system.

Please see https://www.inex.ie/ixp/dashboard/as112 for more details and further explanation.



PeeringDB
=========

PeeringDB ( http://www.peeringdb.com/ ) facilitates the exchange of information related to peering. Specifically, what networks are peering, where they are peering, and if they are likely to peer with you.

More and more organisations are using PeeringDB to make decisions on where they should open POPs, provision new links, etc.

We would very much appreciate it if you could mark your new INEX peering under the "Public Peering Locations" section of your PeeringDB page. We are listed as 'INEX'. If you do not yet have a PeeringDB account, we would suggest that you register for one on their site.


Welcome to INEX, Ireland's Internet hub.


INEX Operations
INEX - Internet Neutral Exchange Association


