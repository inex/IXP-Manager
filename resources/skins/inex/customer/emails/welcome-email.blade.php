# <?= config( 'identity.orgname' ) ?> Operations Welcome E-mail


Dear New <?= config( 'identity.orgname' ) ?> Member,

Firstly, welcome to <?= config( 'identity.orgname' ) ?>! This is your <?= config( 'identity.orgname' ) ?> Operations welcome e-mail.

If you are unfamiliar with how IXPs work, please grab a cup of coffee and take some time to read this email -- it contains important information concerning your <?= config( 'identity.orgname' ) ?> membership.


## Connection Details


You have opted to connect to <?= config( 'identity.orgname' ) ?> using {{ count( $c->getVirtualInterfaces() ) }} ports. We have assigned the following IP addresses and switch ports for your connections:

@foreach( $c->getVirtualInterfaces() as $vi )

### Connection {{ $loop->iteration }}


```
@if( $vi->getLocation() )
Location:        {{$vi->getLocation()->getName()}}
Colo Cabinet ID: {{$vi->getCabinet()->getName()}}
@endif

LAG Port:       @if( count( $vi->getPhysicalinterfaces() ) > 1 ) Yes, comprising of: @else No @endif

@foreach( $vi->getPhysicalinterfaces() as $pi )

Switch Port:     {{$pi->getSwitchPort()->getSwitcher()->getName()}}.inex.ie, {{$pi->getSwitchPort()->getName()}}
Speed:           {{$pi->resolveSpeed()}} ({{$pi->getDuplex()}} duplex)
@endforeach

802.1q Tagged:  @if( $vi->getTrunk() ) Yes @else No @endif

```

@foreach( $vi->getVlanInterfaces() as $vli )
@php ($vlanid = $vli->getVlan()->getId())

**{{$vli->getVlan()->getName()}}**

```
@if( $vi->getTrunk() )
802.1q Tag:    {{$vli->getVlan()->getNumber()}}

@endif
@if( $vli->getIpv6enabled() )
IPv6 Address:  {{$vli->getIPv6Address()->getAddress()}}@isset( $netinfo[ $vlanid ][ 6 ][ 'masklen'] )/{{ $netinfo[ $vlanid ][ 6 ][ 'masklen'] }}@endisset

IPv6 Hostname: {{$vli->getIpv6hostname()}}
@else
IPv6:          Please contact us to enable IPv6
@endif

@if( $vli->getIpv4enabled() )
IPv4 Address:  {{$vli->getIPv4Address()->getAddress()}}@isset( $netinfo[ $vlanid ][ 4 ][ 'masklen'] )/{{ $netinfo[ $vlanid ][ 4 ][ 'masklen'] }}@endisset

IPv4 Hostname: {{$vli->getIpv4hostname()}}
@else
IPv4:          Please contact us to enable IPv4
@endif
```

@endforeach

@endforeach



## Member Portal :: IXP Manager

{{ config( 'identity.orgname' ) }} provides a portal for members which provides traffic graphs for your ports, peer
to peer graphs (how much traffic you are sending and receiving from each member), the contact and peering details
of all other members, a Peering Manager tool, documentation, support information, mailing list subscription
management and much more.

Every member is assigned an administration account with which you then create individual user accounts. The
administration account is only meant for this purpose and as such, all functionality is only available through
user accounts.

@if( count($admins) )
We have created your administration account(s) with the following username(s) and email address(es):

@foreach( $admins as $a )
* {{ $a->getUsername() }} <{{$a->getEmail()}}>
@endforeach


Please browse to the following page and use the 'Lost Password' facility to set a new password for this account.

{{ url('auth/lost-password') }}
@else
Please contact us for your account details at {{ config( 'identity.email' ) }}.
@endif



## Route Servers

{{ config( 'identity.orgname' ) }} operates a route server cluster; this facility allows all members who connect to the
cluster to see all routing prefixes sent to the cluster by any other member.  I.e. it provides a quick, safe and easy
way to peer with any other route server user.

The service is designed to be reliable. It operates on two physical servers, each located in a different data centre.
The service is available on all networks (public peering lans #1 and #2, and INEX Cork), on both ipv4 and ipv6.  The
route servers also filter inbound routing prefixes based on published RIPE IRR policies, which means that using the
route servers for peering is generally much safer than peering directly with other members.

See https://www.inex.ie/technical/route-servers/ for full details.

Your connection to the route servers will be brought live during standard provisioning of your new port(s).

It would aid the provisioning process if you could configure BGP sessions as follows **but leave them in the
shutdown state until advised otherwise**.

```
remote-as:                43760

@foreach( $c->getVirtualInterfaces() as $vi )
@foreach( $vi->getVlanInterfaces() as $vli )

@if( $vli->getRsclient() && $vli->getVlan()->getNumber() == 10 )

Peering VLAN #1
@if( $vli->getIpv6enabled() )
    IPv6 Route Server #1: 2001:7F8:18::8
    IPv6 Route Server #2: 2001:7F8:18::9
    IPv6 session MD5:     {{ $vli->getIpv6bgpmd5secret() }}

@endif
@if( $vli->getIpv4enabled() )
    IPv4 Route Server #1: 185.6.36.8
    IPv4 Route Server #2: 185.6.36.9
    IPv4 session MD5:     {{ $vli->getIpv4bgpmd5secret() }}
@endif
@elseif( $vli->getVlan()->getNumber() == 12)

Peering VLAN #2
@if( $vli->getIpv6enabled() )
    IPv6 Route Server #1: 2001:7F8:18:12::8
    IPv6 Route Server #2: 2001:7F8:18:12::9
    IPv6 session MD5:     {{ $vli->getIpv6bgpmd5secret() }}

@endif
@if( $vli->getIpv4enabled() )
    IPv4 Route Server #1: 194.88.240.8
    IPv4 Route Server #2: 194.88.240.9
    IPv4 session MD5:     {{ $vli->getIpv4bgpmd5secret() }}
@endif
@elseif( $vli->getVlan()->getNumber() == 210)

INEX Cork
@if( $vli->getIpv6enabled() )
    IPv6 Route Server #1: 2001:7F8:18:210::8
    IPv6 Route Server #2: 2001:7F8:18:210::9
    IPv6 session MD5:     {{ $vli->getIpv6bgpmd5secret() }}

@endif
@if( $vli->getIpv4enabled() )
    IPv4 Route Server #1: 185.1.69.8
    IPv4 Route Server #2: 185.1.69.9
    IPv4 session MD5:   {{ $vli->getIpv4bgpmd5secret() }}
@endif
@endif

@endforeach
@endforeach
```


## External Connections to {{ config( 'identity.orgname' ) }}

Cross-connect termination points are provided under separate cover including LoAs (letters of authority - normally
required by data centres before they will install a cross connect to {{ config( 'identity.orgname' ) }}).

Getting cross connects installed is normally the most common delay in bringing your new service live. Please order
it/them as soon as possible and from your co-location / metro ethernet provider.

Please also notify {{ config( 'identity.orgname' ) }} Operations once it is complete as we often do not get notified
by the co-location providers.



## Mailing Lists


To encourage co-operation between {{ config( 'identity.orgname' ) }} members, {{ config( 'identity.orgname' ) }} provides two mailing list services:

Name:                   tech@inex.ie
Purpose:                INEX technical discussion
Subscription Policy:    individuals, roles, aliases,
no limit per organisation
Distribution:           private

Name:                   members@inex.ie
Purpose:                Non-technical discussions relevant to INEX
Subscription Policy:    individuals only, no limit per organisation
Distribution:           private & confidential

Should you wish to subscribe to either or both mailing lists, please create a user account on the IXP Manager (as explained above)
and then browse to the 'Profile' page where you can subscribe to the above and more mailing lists.



## {{ config( 'identity.orgname' ) }} Operations

Technical support contact details for {{ config( 'identity.orgname' ) }} can be found at:

https://www.inex.ie/support/

In general, the best way to contact {{ config( 'identity.orgname' ) }} operations is by email at: operations@inex.ie.
If there is a genuine emergency requiring immediate assistance, please use the 24/7 phone number listed on the web page.


## Peering


{{ config( 'identity.orgname' ) }} facilitates peering between its members, but other than the minimum current peering
requirements (2 other members per LAN) does not mandate peering with any particular member apart from
{{ config( 'identity.orgname' ) }} itself.

You will find a full list of members on the IXP Manager, along with the correct email addresses to use for peering requests.

When emailing other {{ config( 'identity.orgname' ) }} members about peering requests, please include all technical details
relevant to the peering session, including your IP address, your AS number, and an estimate of the number of prefixes you
intend to announce to that candidate peer.

The *My Peering Manager* tool in **IXP Manager** will compose mails with the above details for you automatically.

Please note that {{ config( 'identity.orgname' ) }} members are required to reply to peering requests within a
reasonable time frame.  If your emails to other {{ config( 'identity.orgname' ) }} members about peering go unanswered,
please let us know and we will do what we can.

{{ config( 'identity.orgname' ) }} requires that all new members peer and share routes with the
{{ config( 'identity.orgname' ) }} route collectors for administrative purposes.  We would be obliged if you
could set up your router(s) and make the necessary arrangements for this as soon as possible.

{{ config( 'identity.orgname' ) }}'s details are:

```
remote-as:              2128

@foreach( $c->getVirtualInterfaces() as $vi )
@foreach( $vi->getVlanInterfaces() as $vli )



@if( $vli->getVlan()->getNumber() == 10 )

Peering VLAN #1
@if( $vli->getIpv6enabled() )
    IPv6 address:       2001:7F8:18::F:0:1
    IPv6 session MD5:   {{ $vli->getIpv6bgpmd5secret() }}

@endif
@if( $vli->getIpv4enabled() )
    IPv4 address:       185.6.36.126
    IPv4 session MD5:   {{ $vli->getIpv4bgpmd5secret() }}
@endif
@elseif( $vli->getVlan()->getNumber() == 12)

Peering VLAN #2
@if( $vli->getIpv6enabled() )
    IPv6 address:       2001:7F8:18:12::9999
    IPv6 session MD5:   {{ $vli->getIpv6bgpmd5secret() }}

@endif
@if( $vli->getIpv4enabled() )
    IPv4 address:       194.88.240.126
    IPv4 session MD5:   {{ $vli->getIpv4bgpmd5secret() }}
@endif
@elseif( $vli->getVlan()->getNumber() == 210)

INEX Cork
@if( $vli->getIpv6enabled() )
    IPv6 address:       2001:7F8:18:210::126
    IPv6 session MD5:   {{ $vli->getIpv6bgpmd5secret() }}

@endif
@if( $vli->getIpv4enabled() )
    IPv4 address:       185.1.69.126
    IPv4 session MD5:   {{ $vli->getIpv4bgpmd5secret() }}
@endif
@endif

@endforeach
@endforeach
```


No prefixes are announced by the route collector and it will not forward traffic.


NOC Details
===========

For the convenience of its members, {{ config( 'identity.orgname' ) }} maintains a list of NOC and peering
contact details for its members.  These details are held on a private {{ config( 'identity.orgname' ) }}
database, and are available only from the IXP Manager on the following URL:

* {{ route('customer@details') }}

The sensitive areas of the {{ config( 'identity.orgname' ) }} website is password protected and SSL secured.
Passwords are only provided to current {{ config( 'identity.orgname' ) }} members.  This information is
considered private and will not be passed on to other third parties by {{ config( 'identity.orgname' ) }}.

We would appreciate if you could take the time to ensure that the following details we hold on file are correct:

```
Member name:                    {{ $c->getName() }}
Primary corporate web page:     {{ $c->getCorpwww() }}
Peering Email Address:          {{ $c->getPeeringemail() }}
NOC Phone number:               {{ $c->getNocphone() }}
General NOC email address:      {{ $c->getNocemail() }}
NOC Hours:                      {{ $c->getNochours() }}
Dedicated NOC web page:         {{ $c->getNocwww() }}
AS Number:                      {{ $c->getAutsys() }}
```



Router and Switch Configuration
=================================

If you are new to internet exchanges, we would ask you to note that all members are expected to adhere to the technical requirements
of the INEX MoU.  In particular, we would draw your attention to section 2 of these requirements which outline what types of traffic
may and may not be forwarded to the INEX peering LAN.

For Cisco IOS based routers, we recommend the following interface configuration commands:

```
no ip redirects
no ip proxy-arp
no ip directed-broadcast
no mop enabled
no cdp enable
udld port disable
```

If you intend to use IPv6 with a Cisco IOS based router, please also add the following interface commands:

```
no ipv6 redirects
ipv6 nd suppress-ra
```

As part of standard provisioning for your new connection(s), INEX operations will put your new port through our quarantine
process. This involves checking your port to ensure:

* no *-discover protocols are enabled (CDP, LLDP, etc)
* keepalive, MOP, etc are disabled
* there are no spanning tree BPDUs
* no 802.1q tagging (or, if your port is to be tagged, then tagged appropriately)

We will also get you to connect to the INEX quarantine route collector and advertise your prefixes over BGP. The details
are exactly the same as the production route collector details above.

If you are connecting to INEX through a switch, please additionally read: https://www.inex.ie/ixp/content/1/switches

It would aid us greatly if you could prepare your new connection(s) for quarantine by configuring the appropriate
IP addresses from the *Connection Details* section above and set up the route collector BGP session as per the details
in the *Peering* section above.



IRC
===

INEX member staff and other INEX member employees may regularly be seen on `irc.inex.ie` (port 6697, SSL only), channel `#inex-ops`.

Although this IRC server is secured with SSL, INEX does not recommend swapping passwords or any other private / confidential information on this facility.


AS112 Service
=============

For the benefit of its members, {{ config( 'identity.orgname' ) }} hosts an AS112 nameserver which answers bogus requests
to private IP address space.  This service is available as a regular peering host on all {{ config( 'identity.orgname' ) }}
peering LANs.

Full details are at: https://www.inex.ie/technical/as112-service/

Your session to the AS112 service will be brought up during the standard provisioning of your new connection(s). To
aid the process, it would help if you could preconfigure the following BGP session(s):


```
remote-as:              112

@foreach( $c->getVirtualInterfaces() as $vi )
@foreach( $vi->getVlanInterfaces() as $vli )



@if( $vli->getVlan()->getNumber() == 10 )

Peering VLAN #1
@if( $vli->getIpv6enabled() )
    IPv6 address:       2001:7F8:18::6
    IPv6 session MD5:   <none>

@endif
@if( $vli->getIpv4enabled() )
    IPv4 address:       185.6.36.6
    IPv4 session MD5:   <none>
@endif
@elseif( $vli->getVlan()->getNumber() == 12)

Peering VLAN #2
@if( $vli->getIpv6enabled() )
    IPv6 address:       2001:7F8:18:12::6
    IPv6 session MD5:   <none>

@endif
@if( $vli->getIpv4enabled() )
    IPv4 address:       194.88.240.6
    IPv4 session MD5:   <none>
@endif
@elseif( $vli->getVlan()->getNumber() == 210)

INEX Cork
@if( $vli->getIpv6enabled() )
    IPv6 address:       2001:7F8:18:210::6
    IPv6 session MD5:   <none>

@endif
@if( $vli->getIpv4enabled() )
    IPv4 address:       185.1.69.6
    IPv4 session MD5:   <none>
@endif
@endif

@endforeach
@endforeach
```

PeeringDB
=========

PeeringDB ( http://www.peeringdb.com/ ) facilitates the exchange of information related to peering. Specifically,
what networks are peering, where they are peering, and if they are likely to peer with you.

More and more organisations are using PeeringDB to make decisions on where they should open POPs, provision new links, etc.

We would very much appreciate it if you could mark your new INEX peering under the *Public Peering Locations*
section of your PeeringDB page. If you do not yet have a PeeringDB account, we would suggest that you register
for one on their site.

INEX's PeeringDB entries are:

- INEX LAN1: https://www.peeringdb.com/ix/48
- INEX LAN2: https://www.peeringdb.com/ix/387
- INEX Cork: https://www.peeringdb.com/ix/1262

Note that a number of INEX members now require any network they peer with to have a PeeringDB entry.


Welcome to INEX, Ireland's Internet hub.


INEX Operations
INEX - Internet Neutral Exchange Association Company Limited By Guarantee
