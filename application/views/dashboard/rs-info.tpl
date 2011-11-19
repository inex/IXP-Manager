{tmplinclude file="header.tpl" pageTitle="IXP Manager :: Member Dashboard"}

<div class="yui-g">

<div id="content">

<table class="adminheading" border="0">
<tr>
    <th class="Switch">
        INEX Route Server Details
    </th>
</tr>
</table>

{tmplinclude file="message.tpl"}

<div id='ajaxMessage'></div>

<div id="overviewMessage">
	{if $user.privs eq 3}
    {elseif $rsSessionsEnabled}
        <div class="message message-success">
            You are now enabled to use INEX's robust route server cluster.<br />
            <br />
            Please note that the provisioning system updates the route servers twice daily
            so place allow up to twelve hours for your configuration to become active on
            our systems.<br />
            <br />
            Please see below for configuration details.
        </div>
    {elseif not $rsEnabled}
        <div class="message message-error">
	        You are not using INEX's robust route server cluster. Please <a href="{genUrl controller="dashboard" action="enable-route-server"}">click here to have our provisioning system create sessions</a> for you.
	    </div>
    {else}
	    <div class="message message-success">
	        You are enabled to use INEX's robust route server cluster.
	    </div>
    {/if}
</div>



<h3>Overview</h3>

<p>
Normally on a peering exchange, all connected parties will establish bilateral peering relationships
with each other member port connected to the exchange. As the number of connected parties increases,
it becomes increasingly more difficult to manage peering relationships with members of the exchange.
A typical peering exchange full-mesh eBGP configuration might look something similar to the diagram
on the left hand side.
</p>

<table border="0" align="center">
<tr>
    <td width="354">
        <img src="{genUrl}/images/route-server-peering-fullmesh.png" title=" IXP full mesh peering relationships" alt="[ IXP full mesh peering relationships ]" width="345" height="317" />
    </td>
    <td width="25"></td>
    <td width="354">
        <img src="{genUrl}/images/route-server-peering-rsonly.png" title=" IXP route server peering relationships" alt="[  IXP route server peering relationships ]" width="345" height="317" />
    </td>
</tr>
<tr>
    <td align="center">
        <em> IXP full mesh peering relationships </em>
    </td>
    <td></td>
    <td align="center">
        <em> IXP route server peering relationships</em>
    </td>
</tr>
</table>

<p>
<br />
The full-mesh BGP session relationship scenario requires that each BGP speaker configure and manage
BGP sessions to every other BGP speaker on the exchange. In this example, a full-mesh setup requires
7 BGP sessions per member router, and this increases every time a new member connects to the exchange.
</p>

<p>
However, by using a route server for all peering relationships, the number of BGP sessions per router
stays at two: one for each route server. Clearly this is a more sustainable way of maintaining IXP
peering relationships with a large number of participants.
</p>


<h3>Should I use this service?</h3>

<p>
The INEX route server cluster is aimed at:
</p>

<ul>
    <li> small to medium sized members of the exchange who don't have the time or resources to
         aggressively manage their peering relationships
    </li>
    <li> larger members of the exchange who have an open peering policy, but where it may not
         be worth their while managing peering relationships with smaller members of the exchange.
    </li>
</ul>

<p>
As a rule of thumb: <strong>If you don't have any good reasons not to use the route server cluster, you should probably use it.</strong>
</p>

<p>
The service is designed to be reliable. It operates on two physical servers, each located in a
different data centre. The service is available on all INEX networks (public peering lans #1 and #2,
and voip peering lans #1 and #2), on both ipv4 and ipv6. Each server runs a separate routing daemon
per vlan and per L3 protocol. Should a single BGP server die for some unlikely reason, no other BGP
server is likely to be affected. If one of the physical servers becomes unavailable, the second server
will continue to provide BGP connectivity.
</p>

<p>
INEX has also implemented inbound prefix filtering on its route-server cluster. This uses internet
routing registry data from the RIPE IRR database to allow connected members announce only the address
prefixes which they have registered publicly.
</p>

<p>
INEX uses Quagga running on FreeBSD for its route server cluster. Quagga is widely used at Internet
exchanges for route server clusters (e.g. LINX, AMS-IX, DE-CIX), and has been found to be reliable
in production.
</p>


<h3>How do I use the service?</h3>

<p>
In order to use the service, you should first instruct the route servers to create sessions for you:
</p>

<div id="overviewMessage">
	{if $user.privs eq 3}
    {elseif not $rsEnabled}
        <div class="message message-error">
            You are not enabled to use INEX's route server cluster.
            Please <a href="{genUrl controller="dashboard" action="enable-route-server"}">click here to have our provisioning system create sessions</a> for you.
        </div>
    {else}
        <div class="message message-success">
            You are enabled to use INEX's robust route server cluster.
        </div>
    {/if}
</div>

<p>
If enabled, the route servers are set up to accept BGP connections from your router. Once this has
been done, you will need to configure a BGP peering session to the correct internet address. The
IP addresses of the route servers are listed as follows:
</p>

<center>

<table class="ltbr2" cellspacing="0" border="0" width="700">
<thead>
    <tr>
        <th width="30%">Peering LAN</th>
        <th colspan="2">Route Server #1</th>
        <th colspan="2">Route Server #2</th>
    </tr>
    <tr>
        <th width="30%"></th>
        <th>IPv4 Address</th>
        <th>IPv6 Address</th>
        <th>IPv4 Address</th>
        <th>IPv6 Address</th>
    </tr>
</thead>
<tbody>
	<tr>
		<td>Public Peering LAN #1</td>
		<td>193.242.111.8</td>
		<td>2001:7f8:18::8</td>
		<td>193.242.111.9</td>
		<td>2001:7f8:18::9</td>
	</tr>
	<tr>
		<td>Public Peering LAN #2</td>
		<td>194.88.240.8</td>
		<td>2001:7f8:18:12::8</td>
		<td>194.88.240.9</td>
		<td>2001:7f8:18:12::9</td>
	</tr>
	<tr>
		<td>VoIP Peering LAN #1</td>
		<td>194.88.241.8</td>
		<td>2001:7f8:18:70::8</td>
		<td>194.88.241.9</td>
		<td>2001:7f8:18:70::9</td>
	</tr>
	<tr>
		<td>VoIP Peering LAN #2</td>
		<td>194.88.241.72</td>
		<td>2001:7f8:18:72::8</td>
		<td>194.88.241.73</td>
		<td>2001:7f8:18:72::9</td>
	</tr>
</tbody>
</table>

</center>

<p>
<br /><br />
For Cisco routers, you will need something like the following bgp configuration:
</p>

<pre>
    router bgp 99999
     no bgp enforce-first-as

     ! Route server #1

     neighbor 193.242.111.8 remote-as 43760
     neighbor 193.242.111.8 description INEX Route Server
     address-family ipv4
     neighbor 193.242.111.8 password s00persekr1t
     neighbor 193.242.111.8 activate
     neighbor 193.242.111.8 filter-list 100 out

     ! Route server #2

     neighbor 193.242.111.9 remote-as 43760
     neighbor 193.242.111.9 description INEX Route Server
     address-family ipv4
     neighbor 193.242.111.9 password s00persekr1t
     neighbor 193.242.111.9 activate
     neighbor 193.242.111.9 filter-list 100 out
</pre>

<p>
You should also use <code>route-maps</code> (or <code>distribute-lists</code>) to control
outgoing prefix announcements to allow only the prefixes which you indend to announce.
</p>

<p>
Note that the route server system depends on information in the RIPE IRR database. If you
have not published correct <code>route:</code> and <code>route6:</code> objects in this database,
your prefix announcements will be ignored by the route server and your peers will not route their
traffic to you via the exchange.
</p>

<h3>Community based prefix filtering</h3>

<p>
The INEX route server system also provides well known communities to allow members to
control the distribution of their prefixes. These communities are defined as follows:
</p>

<table class="ltbr2" cellspacing="0" border="0" width="500">
    <thead>
    <tr>
        <th>Description</th>
        <th>Community</th>
    </tr>
    </thead>

    <tbody>
    <tr>
        <td>Prevent announcement of a prefix to a peer</td>
        <td><code>0:peer-as</code></td>
    </tr>
    <tr>
        <td>Announce a route to a certain peer</td>
        <td><code>43760:peer-as</code></td>
    </tr>
    <tr>
        <td>Prevent announcement of a prefix to all peers</td>
        <td><code>0:43760</code></td>
    </tr>
    <tr>
        <td>Announce a route to all peers</td>
        <td><code>43760:43760</code></td>
    </tr>
    </tbody>
</table>


<p>
<br /><br />
So, for example, to instruct the route server to distribute a particular prefix only to
AS64111 and AS64222, the prefix should be tagged with communities: 0:43760, 43760:64111
and 43760:64222.
</p>

<p>
Alternatively, to announce a prefix to all INEX members, excluding AS64333, the prefix
should be tagged with community 0:64333.
</p>

</div>
</div>

{tmplinclude file="footer.tpl"}
