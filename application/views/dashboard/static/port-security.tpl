{include file="header.tpl"}

{if $user.privs eq 3}
    <ul class="breadcrumb">
        <li>
            <a href="{genUrl}">Home</a> <span class="divider">/</span>
        </li>
        <li>
            Documentation <span class="divider">/</span>
        </li>
        <li class="active">
            Port Security
        </li>
    </ul>
{else}
    <div class="page-content">
        <div class="page-header">
            <h1>INEX Port Security Policies</h1>
        </div>
{/if}



<p>For the purposes of ensuring layer 2 stability, INEX implements three
port security policies.</p>

<ul>

	<li>
	   <strong>Broadcast Traffic Storm Control</strong> — INEX restricts
		broadcast traffic received on any particular port to be no more than
		0.33% of the total traffic received on that port.<br />
		<br />
		While it is normal to see a small amount of Layer 2 broadcast traffic
		for certain types of traffic (ARP), large amounts of broadcast traffic
		typically indicate a problem with the connecting router or switch,
		caused by incorrect configuration, failed hardware or
		hardware/microcode bugs. Because broadcast traffic frames are forwarded
		to all ports on a flat layer 2 LAN, this sort of traffic could
		potentially disrupt service for other connections into the INEX switch
		fabric. Beyond the limit of 0.33%, inbound broadcast traffic is simply
		dropped, and will not be forwarded to the relevant INEX port.
        <br />
        <br />
	</li>

    <li>
        <strong>Multicast Traffic Storm Control</strong> — on ports which are
        not enabled for multicast traffic, INEX throttles multicast traffic
        received on any particular port to be no more than 0.33% of the total
        traffic received on that port.<br />
        <br />
        It is also normal to see small amounts of inbound multicast
	    traffic on ports (e.g. IPv6 neighbour discovery), where the port has
	    not been enabled for regular multicast traffic. However, as with
	    broadcast traffic, excessive amounts of multicast traffic on a
	    non-multicast enabled port are indicative of configuration problems.
	    For this reason, the INEX switches are configured to drop multicast
	    frames which exceed the 0.33% rate limit.
        <br />
        <br />
	</li>

	<li>
	    <strong>One MAC Address per Port</strong> — INEX expects that all traffic
	    coming in from a particular port will all
	    be configured with the same source MAC address, which INEX switches
	    will then dynamically associate with that port.<br />
	    <br />
	    If frames are seen on a
	    port with a source MAC address which differs from the dynamically
	    learned address, then the port will either shut the port down
	    automatically or else drop frames with the unknown MAC address. If the
	    port is shut down, it will automatically be re-enabled after 300
	    seconds (5 minutes). INEX ports will relearn a new dynamic MAC
	    addresses after 5 minutes of inactivity, allowing scheduled maintenance
	    with relatively little interruption.<br />
        <br />
    	INEX provides access to a range of
	    flat layer 2 networks, over which providers may run IP traffic. Because
	    of this, there is no reason to allow more than a single MAC address per
	    configured port. When multiple MAC addresses are seen on a particular
	    port, it generally means one of the following things:

	    <ul>
	       <li> the connected router or switch has been misconfigured to forward link-local frames to
	            the INEX peering
	       </li>
	       <li> LAN member has accidentally set up a traffic loop
	            between two INEX switch ports
	       </li>
	       <li> a metro ethernet provider has
	           accidentally leaked bogus frames into a member's connection link
	       </li>
	       <li> the member is using faulty hardware
	       </li>
	    </ul>

	    Because several of these possibilities
	    could cause catastrophic layer 2 network instability affecting all INEX
	    members on a particular peering LAN, and because all of them can be
	    obviated by using a one MAC-address per port policy, INEX aggressively
	    implements and polices this policy.

	</li>
</ul>


<h3>Multicast PIM / IGMP snooping</h3>

<p>
In order not to flood 3rd party ports with unnecessary multicast traffic,
INEX implements both PIM and IGMP snooping. This procedure ensures that
only ports which issue PIM or IGMP join or leave messages will actually
receive specific multicast traffic flows. This policy increases both
port security and link utilisation efficiency.
</p>

<h3>Broadcast Traffic Monitoring </h3>

<p>
For the purposes of ensuring that no unnecessary broadcast
traffic is forwarded to an INEX peering LAN, INEX monitors all peering
LANs for broadcast traffic, and archives this data.
</p>

<p>
This data consists purely of traffic which is broadcast to all INEX peering LAN ports.
Whenever unauthorised traffic is detected, the INEX operations team is
notified, who will normally follow the issue up with the source of the
traffic.
</p>

<p>
Traffic monitoring is implemented using <code>ixpwatch</code>, a program
designed and coded by the London Internet Exchange (LINX).
</p>


{include file="footer.tpl"}