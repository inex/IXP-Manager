# <?= config( 'identity.orgname' ) ?> Operations Welcome E-mail


**NB: this is a sample IXP Manager welcome email. You should copy this file and skin it as
per the instructions at: http://docs.ixpmanager.org/features/skinning/ **


Dear New <?= config( 'identity.orgname' ) ?> Member,

Firstly, welcome to <?= config( 'identity.orgname' ) ?>! This is your <?= config( 'identity.orgname' ) ?> Operations welcome e-mail.

If you are unfamiliar with how IXPs work, take some time to read this email -- it contains important information concerning your <?= config( 'identity.orgname' ) ?> membership.


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

See https://xxx/ for full details.




## External Connections to {{ config( 'identity.orgname' ) }}

Cross-connect termination points are provided under separate cover including LoAs (letters of authority - normally
required by data centres before they will install a cross connect to {{ config( 'identity.orgname' ) }}).

Getting cross connects installed is normally the most common delay in bringing your new service live. Please order
it/them as soon as possible and from your co-location / metro ethernet provider.




## {{ config( 'identity.orgname' ) }} Operations

Technical support contact details for {{ config( 'identity.orgname' ) }} can be found at:

https://www.example.com/support



## Peering


You will find a full list of members on the IXP Manager, along with the correct email addresses to use for peering requests.

When emailing other {{ config( 'identity.orgname' ) }} members about peering requests, please include all technical details
relevant to the peering session, including your IP address, your AS number, and an estimate of the number of prefixes you
intend to announce to that candidate peer.

The *My Peering Manager* tool in **IXP Manager** will compose mails with the above details for you automatically.



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



AS112 Service
=============

For the benefit of its members, {{ config( 'identity.orgname' ) }} hosts an AS112 nameserver which answers bogus requests
to private IP address space.  This service is available as a regular peering host on all {{ config( 'identity.orgname' ) }}
peering LANs.

Full details are at: https://www.example.com/


PeeringDB
=========

PeeringDB ( http://www.peeringdb.com/ ) facilitates the exchange of information related to peering. Specifically,
what networks are peering, where they are peering, and if they are likely to peer with you.

More and more organisations are using PeeringDB to make decisions on where they should open POPs, provision new links, etc.

We would very much appreciate it if you could mark your new peering under the *Public Peering Locations*
section of your PeeringDB page. If you do not yet have a PeeringDB account, we would suggest that you register
for one on their site.

Our PeeringDB entries are:

- IXP LAN1: https://www.peeringdb.com/ix/nnn
- ...
- ...

Note that a number of members now require any network they peer with to have a PeeringDB entry.


Welcome to {{ config( 'identity.orgname' ) }}
