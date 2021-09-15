# <?= config( 'identity.orgname' ) ?> Operations Welcome E-mail


Dear New <?= config( 'identity.orgname' ) ?> Member,

Firstly, welcome to <?= config( 'identity.orgname' ) ?>! This is your <?= config( 'identity.orgname' ) ?> Operations welcome e-mail.

If you are unfamiliar with how IXPs work, please grab a cup of coffee and take some time to read this email -- it contains important information concerning your <?= config( 'identity.orgname' ) ?> membership.


## Connection Details


You have opted to connect to <?= config( 'identity.orgname' ) ?> using {{ $c->virtualInterfaces()->count()}} ports. We have assigned the following IP addresses and switch ports for your connections:

@foreach( $c->virtualInterfaces as $vi )

### Connection {{ $loop->iteration }}


```
@if( $vi->physicalInterfaces()->exists() && $cabinet = $vi->physicalInterfaces()->first()->switchport->switcher->cabinet )
Location:        {{$cabinet->location->name}}
Colo Cabinet ID: {{$cabinet->name}}
@endif

LAG Port:       @if( $vi->lag_framing || $vi->physicalinterfaces()->count() > 1 ) Yes, @if( $vi->physicalinterfaces()->count() === 1 )(single member)@endif comprising of: @else No @endif

@foreach( $vi->physicalinterfaces as $pi )

Switch Port:     {{$pi->switchPort->switcher->name}}.inex.ie, {{$pi->switchPort->name}}
Speed:           {{$pi->speed()}} ({{$pi->duplex}} duplex)
@endforeach

802.1q Tagged:  @if( $vi->trunk ) Yes @else No @endif

```

@foreach( $vi->vlanInterfaces as $vli )
@php ($vlanid = $vli->vlanid)

@if( in_array( $vli->vlan->number, [ 10, 30 ], true ) )
**Peering LAN1**
@elseif( in_array( $vli->vlan->number, [ 12, 32 ], true ) )
**Peering LAN2**
@elseif( in_array( $vli->vlan->number, [ 210, 230 ], true ) )
**INEX Cork**
@else
**{{$vli->vlan->name}}**
@endif


```
@if( $vi->trunk )
802.1q Tag:    {{$vli->vlan->number}}

@endif
@if( $vli->ipv6enabled )
IPv6 Address:  {{$vli->ipv6Address->address}}@isset( $netinfo[ $vlanid ][ 6 ][ 'masklen'] )/{{ $netinfo[ $vlanid ][ 6 ][ 'masklen'] }}@endisset

IPv6 Hostname: {{$vli->ipv6hostname}}
@else
IPv6:          Please contact us to enable IPv6
@endif

@if( $vli->ipv4enabled )
IPv4 Address:  {{$vli->ipv4Address->address}}@isset( $netinfo[ $vlanid ][ 4 ][ 'masklen'] )/{{ $netinfo[ $vlanid ][ 4 ][ 'masklen'] }}@endisset

IPv4 Hostname: {{$vli->ipv4hostname}}
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

@if( count( $admins ) )
<?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> users with *admin* privileges can create and manage other user accounts.

We have created your administration account(s) with the following username(s) and email address(es):

@foreach( $admins as $a )
* {{ $a->user->username }} <{{$a->user->email}}>
@endforeach


Please browse to the following page and use the 'Lost Password' facility to set a new password for this account.

{{ route('forgot-password@show-form') }}
@else
Please contact us for your account details at {{ config( 'identity.email' ) }}.
@endif

@if( config( 'auth.peeringdb.enabled' ) )
If your network is registered with PeeringDB, then members of your organisation with affiliated accounts on PeeringDB can log into IXP Manager using the *Login via PeeringDB* option.
@endif

## Quarantine Procedure

When you first connect to INEX, we perform what we call a *quarantine procedure*. This is to ensure that you are only transmitting permitted packet types into the INEX peering fabric. These types are set out in the [INEX MoU](https://www.inex.ie/become-a-member/inex-mou/) under *Technical Requirements / Connectivity* but in summary you should only be transmitting ARP, IPv4 and IPv6 packets.

To begin the quarantine procedure, we need you to:

1. order and complete the cross connect(s) to INEX
2. configure your port(s) with the IP addresses provided above
3. bring the port up
4. test connectivity by pinging the IPv4/IPv6 address of the route collectors (see below)
5. configure and bring up BGP sessions to the quarantine route collector and advertise your address space
6. Quarantine procedures now begin - allow 24hours for this

Don't worry - we're here to help with each step of the above!

To aid us and accelerate the quarantine procedure, please ensure all of the following are disabled on your port where appropriate: spanning tree, keepalives, *-discovery protocol (lldp, cdp, etc.), proxy arp, ospf/isis.

Note that the IP addresses we use and the quarantine route collector are all identical to those that are used in production so there is no reconfiguration required.



## Peering / Route Collectors


{{ config( 'identity.orgname' ) }} facilitates peering between its members, but other than the minimum current peering requirements (2 other members per LAN) does not mandate peering with any particular member apart from {{ config( 'identity.orgname' ) }} itself.

You will find a full list of members on the IXP Manager, along with the correct email addresses to use for peering requests.

When emailing other {{ config( 'identity.orgname' ) }} members about peering requests, please include all technical details relevant to the peering session, including your IP address, your AS number, and an estimate of the number of prefixes you intend to announce to that candidate peer.

The *My Peering Manager* tool in **IXP Manager** will compose mails with the above details for you automatically.

Please note that {{ config( 'identity.orgname' ) }} members are required to reply to peering requests within a reasonable time frame.  If your emails to other {{ config( 'identity.orgname' ) }} members about peering go unanswered, please let us know and we will do what we can.

{{ config( 'identity.orgname' ) }} requires that all new members peer and share routes with the {{ config( 'identity.orgname' ) }} route collectors for administrative purposes.  We would be obliged if you could set up your router(s) and make the necessary arrangements for this as soon as possible.

{{ config( 'identity.orgname' ) }}'s details are:

```
remote-as:              2128
@foreach( $c->virtualInterfaces as $vi )
@foreach( $vi->vlanInterfaces as $vli )

@if( in_array( $vli->vlan->number, [ 10, 30 ], true ) )

Peering LAN1
@if( $vli->ipv6enabled )
    IPv6 address:       2001:7F8:18::f:0:1
    IPv6 session MD5:   {{ $vli->ipv6bgpmd5secret }}

@endif
@if( $vli->ipv4enabled )
    IPv4 address:       185.6.36.126
    IPv4 session MD5:   {{ $vli->ipv4bgpmd5secret }}
@endif
@elseif( in_array( $vli->vlan->number, [ 12, 32 ], true ) )

Peering LAN2
@if( $vli->ipv6enabled )
    IPv6 address:       2001:7F8:18:12::9999
    IPv6 session MD5:   {{ $vli->ipv6bgpmd5secret }}

@endif
@if( $vli->ipv4enabled )
    IPv4 address:       194.88.240.126
    IPv4 session MD5:   {{ $vli->ipv4bgpmd5secret }}
@endif
@elseif( in_array( $vli->vlan->number, [ 210, 230 ], true ) )

INEX Cork
@if( $vli->ipv6enabled )
    IPv6 address:       2001:7F8:18:210::126
    IPv6 session MD5:   {{ $vli->ipv6bgpmd5secret }}

@endif
@if( $vli->ipv4enabled )
    IPv4 address:       185.1.69.126
    IPv4 session MD5:   {{ $vli->ipv4bgpmd5secret }}
@endif
@endif
@endforeach
@endforeach
```


**No prefixes are ever announced to you or any other peer by the route collector and it will not forward traffic.**

**Looking glasses for the route servers are available as follows:**

@foreach( $c->virtualInterfaces as $vi )
@foreach( $vi->vlanInterfaces as $vli )
@if( in_array( $vli->vlan->number, [ 10, 30 ], true ) )
* Peering LAN1 [[Production IPv4](https://www.inex.ie/ixp/lg/rc1-lan1-ipv4)] [[Production IPv6](https://www.inex.ie/ixp/lg/rc1-lan1-ipv6)] [[Quarantine IPv4](https://www.inex.ie/ixp/lg/rc1q-lan1-ipv4)] [[Quarantine IPv6](https://www.inex.ie/ixp/lg/rc1q-lan1-ipv6)]
@elseif( in_array( $vli->vlan->number, [ 12, 32 ], true ) )
* Peering LAN2 [[Production IPv4](https://www.inex.ie/ixp/lg/rc1-lan2-ipv4)] [[Production IPv6](https://www.inex.ie/ixp/lg/rc1-lan2-ipv6)] [[Quarantine IPv4](https://www.inex.ie/ixp/lg/rc1q-lan2-ipv4)] [[Quarantine IPv6](https://www.inex.ie/ixp/lg/rc1q-lan2-ipv6)]
@elseif( in_array( $vli->vlan->number, [ 210, 230 ], true ) )
* INEX Cork [[Production IPv4](https://www.inex.ie/ixp/lg/rc1-cork-ipv4)] [[Production IPv6](https://www.inex.ie/ixp/lg/rc1-cork-ipv6)] [[Quarantine IPv4](https://www.inex.ie/ixp/lg/rc1q-cork-ipv4)] [[Quarantine IPv6](https://www.inex.ie/ixp/lg/rc1q-cork-ipv6)]
@endif
@endforeach
@endforeach



## Route Servers

{{ config( 'identity.orgname' ) }} operates a route server cluster; this facility allows all members who connect to the cluster to see all routing prefixes sent to the cluster by any other member.  I.e. it provides a quick, safe and easy way to peer with any other route server member.

The service is designed to be reliable. It operates on two physical servers, each located in a different data centre. The service is available on all networks (Peering LAN1, Peering LAN2, and INEX Cork), on both ipv4 and ipv6.  The route servers also filter inbound routing prefixes based on published RIPE IRR policies, which means that using the route servers for peering is generally much safer than peering directly with other members.

See https://www.inex.ie/technical/route-servers/ for full details.

@if( !$c->routeServerClient(4) && !$c->routeServerClient(6) )
**Route server sessions are not currently configured on your account. If you wish to peer with the route servers (recommended for most members) then please let us know and we will configure your sessions.**
@else
Your connection to the route servers will be brought live during standard provisioning of your new port(s).

It would aid the provisioning process if you could configure BGP sessions as follows **but leave them in the shutdown state until advised otherwise**.

```
remote-as:                43760
@foreach( $c->virtualInterfaces as $vi )
@foreach( $vi->vlanInterfaces as $vli )
@if( $vli->rsclient && in_array( $vli->vlan->number, [ 10, 30 ], true ) )

Peering LAN1
@if( $vli->ipv6enabled )
    IPv6 Route Server 1: 2001:7F8:18::8
    IPv6 Route Server 2: 2001:7F8:18::9
    IPv6 session MD5:     {{ $vli->ipv6bgpmd5secret }}

@endif
@if( $vli->ipv4enabled )
    IPv4 Route Server 1: 185.6.36.8
    IPv4 Route Server 2: 185.6.36.9
    IPv4 session MD5:     {{ $vli->ipv4bgpmd5secret }}
@endif
@elseif( $vli->rsclient && in_array( $vli->vlan->number, [ 12, 32 ], true ) )

Peering LAN2
@if( $vli->ipv6enabled )
    IPv6 Route Server 1: 2001:7F8:18:12::8
    IPv6 Route Server 2: 2001:7F8:18:12::9
    IPv6 session MD5:     {{ $vli->ipv6bgpmd5secret }}

@endif
@if( $vli->ipv4enabled )
    IPv4 Route Server 1: 194.88.240.8
    IPv4 Route Server 2: 194.88.240.9
    IPv4 session MD5:     {{ $vli->ipv4bgpmd5secret }}
@endif
@elseif( $vli->rsclient && in_array( $vli->vlan->number, [ 210, 230 ], true ) )

INEX Cork
@if( $vli->ipv6enabled )
    IPv6 Route Server 1: 2001:7F8:18:210::8
    IPv6 Route Server 2: 2001:7F8:18:210::9
    IPv6 session MD5:     {{ $vli->ipv6bgpmd5secret }}

@endif
@if( $vli->ipv4enabled )
    IPv4 Route Server 1: 185.1.69.8
    IPv4 Route Server 2: 185.1.69.9
    IPv4 session MD5:   {{ $vli->ipv4bgpmd5secret }}
@endif
@endif
@endforeach
@endforeach
```

@endif

**Looking glasses for the route servers are available as follows:**

@foreach( $c->virtualInterfaces as $vi )
@foreach( $vi->vlanInterfaces as $vli )
@if( in_array( $vli->vlan->number, [ 10, 30 ], true ) )
* Peering LAN1 [[RS1 IPv4](https://www.inex.ie/ixp/lg/rs1-lan1-ipv4)] [[RS1 IPv6](https://www.inex.ie/ixp/lg/rs1-lan1-ipv6)] [[RS2 IPv4](https://www.inex.ie/ixp/lg/rs2-lan1-ipv4)] [[RS2 IPv6](https://www.inex.ie/ixp/lg/rs2-lan1-ipv6)]
@elseif( in_array( $vli->vlan->number, [ 12, 32 ], true ) )
* Peering LAN2 [[RS1 IPv4](https://www.inex.ie/ixp/lg/rs1-lan2-ipv4)] [[RS1 IPv6](https://www.inex.ie/ixp/lg/rs1-lan2-ipv6)] [[RS2 IPv4](https://www.inex.ie/ixp/lg/rs2-lan2-ipv4)] [[RS2 IPv6](https://www.inex.ie/ixp/lg/rs2-lan2-ipv6)]
@elseif( in_array( $vli->vlan->number, [ 210, 230 ], true ) )
* INEX Cork [[RS1 IPv4](https://www.inex.ie/ixp/lg/rs1-cork-ipv4)] [[RS1 IPv6](https://www.inex.ie/ixp/lg/rs1-cork-ipv6)] [[RS2 IPv4](https://www.inex.ie/ixp/lg/rs2-cork-ipv4)] [[RS2 IPv6](https://www.inex.ie/ixp/lg/rs2-cork-ipv6)]
@endif
@endforeach
@endforeach


## External Connections to {{ config( 'identity.orgname' ) }}

Cross-connect termination points are provided under separate cover including LoAs (letters of authority - normally required by data centres before they will install a cross connect to {{ config( 'identity.orgname' ) }}).

Getting cross connects installed is normally the most common delay in bringing your new service live. Please order it/them as soon as possible and from your co-location / metro ethernet provider.

Please also notify {{ config( 'identity.orgname' ) }} Operations once it is complete as we often do not get notified by the co-location providers.



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

Should you wish to subscribe to either or both mailing lists, please create a user account on the IXP Manager (as explained above) and then browse to the 'Profile' page where you can subscribe to the above and more mailing lists.



## {{ config( 'identity.orgname' ) }} Operations

Technical support contact details for {{ config( 'identity.orgname' ) }} can be found at:

https://www.inex.ie/support/

In general, the best way to contact {{ config( 'identity.orgname' ) }} operations is by email at: operations@inex.ie.

If there is a genuine emergency requiring immediate assistance, please use the 24/7 phone number listed on the web page.




NOC Details
===========

For the convenience of its members, {{ config( 'identity.orgname' ) }} maintains a list of NOC and peering contact details for its members.  These details are held on a private {{ config( 'identity.orgname' ) }} database, and are available only from the IXP Manager on the following URL:

* {{ route('customer@details') }}

The sensitive areas of the {{ config( 'identity.orgname' ) }} website is password protected and SSL secured. Passwords are only provided to current {{ config( 'identity.orgname' ) }} members.  This information is considered private and will not be passed on to other third parties by {{ config( 'identity.orgname' ) }}.

We would appreciate if you could take the time to ensure that the following details we hold on file are correct:

```
Member name:                    {{ $c->name }}
Primary corporate web page:     {{ $c->corpwww }}
Peering Email Address:          {{ $c->peeringemail }}
NOC Phone number:               {{ $c->nocphone }}
General NOC email address:      {{ $c->nocemail }}
NOC Hours:                      {{ $c->nochours }}
Dedicated NOC web page:         {{ $c->nocwww }}
AS Number:                      {{ $c->autsys }}
```



Router and Switch Configuration
=================================

If you are new to internet exchanges, we would ask you to note that all members are expected to adhere to the technical requirements of the INEX MoU.  In particular, we would draw your attention to section 2 of these requirements which outline what types of traffic may and may not be forwarded to the INEX peering LAN.

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

As part of standard provisioning for your new connection(s), INEX operations will put your new port through our quarantine process. This involves checking your port to ensure:

* no *-discover protocols are enabled (CDP, LLDP, etc)
* keepalive, MOP, etc are disabled
* there are no spanning tree BPDUs
* no 802.1q tagging (or, if your port is to be tagged, then tagged appropriately)

We will also get you to connect to the INEX quarantine route collector and advertise your prefixes over BGP. The details are exactly the same as the production route collector details above.

If you are connecting to INEX through a switch, please additionally read: https://www.inex.ie/ixp/content/1/switches

It would aid us greatly if you could prepare your new connection(s) for quarantine by configuring the appropriate IP addresses from the *Connection Details* section above and set up the route collector BGP session as per the details in the *Peering* section above.



IRC
===

INEX member staff and other INEX member employees may regularly be seen on `irc.inex.ie` (port 6697, SSL only), channel `#inex-ops`.

Although this IRC server is secured with SSL, INEX does not recommend swapping passwords or any other private / confidential information on this facility.


AS112 Service
=============

For the benefit of its members, {{ config( 'identity.orgname' ) }} hosts an AS112 nameserver which answers bogus requests to private IP address space.  This service is available as a regular peering host on all {{ config( 'identity.orgname' ) }} peering LANs.

Full details are at: https://www.inex.ie/technical/as112-service/

@if( !$c->isAS112Client() )
**The AS112 service is not currently configured on your account. If you wish to avail of the AS112 service (recommended for most members) then please let us know and we will switch it on.**
@else
Your session to the AS112 service will be brought up during the standard provisioning of your new connection(s). To aid the process, it would help if you could preconfigure the following BGP session(s):


```
remote-as:              112
@foreach( $c->virtualInterfaces as $vi )
@foreach( $vi->vlanInterfaces as $vli )
@if( $vli->as112client && in_array( $vli->vlan->number, [ 10, 30 ], true ) )


Peering LAN1
@if( $vli->ipv6enabled )
    IPv6 address:       2001:7F8:18::6
    IPv6 session MD5:   <none>

@endif
@if( $vli->ipv4enabled )
    IPv4 address:       185.6.36.6
    IPv4 session MD5:   <none>
@endif
@elseif( $vli->as112client && in_array( $vli->vlan->number, [ 12, 32 ], true ) )


Peering LAN2
@if( $vli->ipv6enabled )
    IPv6 address:       2001:7F8:18:12::6
    IPv6 session MD5:   <none>

@endif
@if( $vli->ipv4enabled )
    IPv4 address:       194.88.240.6
    IPv4 session MD5:   <none>
@endif
@elseif( $vli->as112client && in_array( $vli->vlan->number, [ 210, 230 ], true ) )

INEX Cork
@if( $vli->ipv6enabled )
    IPv6 address:       2001:7F8:18:210::6
    IPv6 session MD5:   <none>

@endif
@if( $vli->ipv4enabled )
    IPv4 address:       185.1.69.6
    IPv4 session MD5:   <none>
@endif
@endif
@endforeach
@endforeach
```

**Looking glasses for the AS112 service are available as follows:**

@foreach( $c->virtualInterfaces as $vi )
@foreach( $vi->vlanInterfaces as $vli )
@if( in_array( $vli->vlan->number, [ 10, 30 ], true ) )
* Peering LAN1 [[IPv4](https://www.inex.ie/ixp/lg/as112-lan1-ipv4)] [[IPv6](https://www.inex.ie/ixp/lg/as112-lan1-ipv6)]
@elseif( in_array( $vli->vlan->number, [ 12, 32 ], true ) )
* Peering LAN2 [[IPv4](https://www.inex.ie/ixp/lg/as112-lan2-ipv4)] [[IPv6](https://www.inex.ie/ixp/lg/as112-lan2-ipv6)]
@elseif( in_array( $vli->vlan->number, [ 210, 230 ], true ) )
* INEX Cork [[IPv4](https://www.inex.ie/ixp/lg/as112-cork-ipv4)] [[IPv6](https://www.inex.ie/ixp/lg/as112-cork-ipv6)]
@endif
@endforeach
@endforeach

@endif



PeeringDB
=========

PeeringDB (https://www.peeringdb.com/) facilitates the exchange of information related to peering. Specifically, what networks are peering, where they are peering, and if they are likely to peer with you.

More and more organisations are using PeeringDB to make decisions on where they should open POPs, provision new links, etc.

We would very much appreciate it if you could mark your new INEX peering under the *Public Peering Locations* section of your PeeringDB page. If you do not yet have a PeeringDB account, we would suggest that you register for one on their site.

INEX's PeeringDB entries are:

- INEX LAN1: https://www.peeringdb.com/ix/48
- INEX LAN2: https://www.peeringdb.com/ix/387
- INEX Cork: https://www.peeringdb.com/ix/1262

Note that a number of INEX members now require any network they peer with to have a PeeringDB entry.


Welcome to INEX, Ireland's Internet hub.


INEX Operations
INEX - Internet Neutral Exchange Association CLG
