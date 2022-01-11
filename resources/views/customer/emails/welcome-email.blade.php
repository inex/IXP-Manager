# <?= config( 'identity.orgname' ) ?> Operations Welcome E-mail


**NB: this is a sample IXP Manager welcome email. You should copy this file and skin it as
per the instructions at: http://docs.ixpmanager.org/features/skinning/ **


Dear New <?= config( 'identity.orgname' ) ?> Member,

Firstly, welcome to <?= config( 'identity.orgname' ) ?>! This is your <?= config( 'identity.orgname' ) ?> Operations welcome e-mail.

If you are unfamiliar with how IXPs work, take some time to read this email -- it contains important information concerning your <?= config( 'identity.orgname' ) ?> membership.


## Connection Details


You have opted to connect to <?= config( 'identity.orgname' ) ?> using {{ $c->virtualInterfaces()->count() }} port(s). We have assigned the following IP address(es) and switch port(s) for your connection(s):

@foreach( $c->virtualInterfaces as $vi )

### Connection {{ $loop->iteration }}


```
@if( $vi->location )
Location:        {{$vi->location->name}}
Colo Cabinet ID: {{$vi->cabinet->name}}
@endif

LAG Port:       @if( $vi->lag_framing || $vi->physicalinterfaces()->count() > 1 ) Yes, @if( $vi->physicalinterfaces()->count() === 1 )(single member)@endif comprising of: @else No @endif

@foreach( $vi->physicalinterfaces as $pi )

Switch Port:     {{$pi->switchPort->switcher->name}}.inex.ie, {{$pi->switchPort->name}}
Speed:           {{$pi->speed()}} ({{$pi->duplex}} duplex)
@endforeach


802.1q Tagged:  @if( $vi->trunk ) Yes @else No @endif

```

@foreach( $vi->vlanInterfaces as $vli )
@php ($vlanid = $vli->vlan_id)

**{{$vli->vlan->name}}**

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
IPv4 Address:  {{$vli->ipv4address->address}}@isset( $netinfo[ $vlanid ][ 4 ][ 'masklen'] )/{{ $netinfo[ $vlanid ][ 4 ][ 'masklen'] }}@endisset

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

@if( count($admins) )
Customer users with *admin* privileges can create and manage other user accounts.

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

The sensitive areas of the {{ config( 'identity.orgname' ) }} website are password protected and SSL secured.
Passwords are only provided to current {{ config( 'identity.orgname' ) }} members.  This information is
considered private and will not be passed on to other third parties by {{ config( 'identity.orgname' ) }}.

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



AS112 Service
=============

For the benefit of its members, {{ config( 'identity.orgname' ) }} hosts an AS112 nameserver which answers bogus requests
to private IP address space.  This service is available as a regular peering host on all {{ config( 'identity.orgname' ) }}
peering LANs.

Full details are at: https://www.example.com/


PeeringDB
=========

PeeringDB ( https://www.peeringdb.com/ ) facilitates the exchange of information related to peering. Specifically,
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
