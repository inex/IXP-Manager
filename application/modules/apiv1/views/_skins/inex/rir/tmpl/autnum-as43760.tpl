password: {$options.rir.ripe_password}

aut-num:        AS43760
as-name:        INEX-RS
descr:          Internet Neutral Exchange Association Company Limited By Guarantee
remarks:        -------------------------------------------------------
remarks:
remarks:        INEX Route Server Routing Policy:
remarks:
remarks:        prevent announcement of a prefix to a peer    0:peer-as
remarks:        announce a route to a certain peer            43760:peer-as
remarks:        prevent announcement of a prefix to all peers 0:43760
remarks:        announce a route to all peers                 43760:43760
remarks:
remarks:        Notes:
remarks:        - we use a per-client RIB
remarks:        - local-preference is not modified in our RIBs
remarks:        - AS43760 is stripped from the AS path sent to clients
remarks:        - MEDs and next-hop are not modified
remarks:        - communities are stripped from all announcements to
remarks:          clients
remarks:        - we filter inbound routing prefixes based on IRR
remarks:          information pulled from whois.ripe.net.  Please check
remarks:          your public routing policy before complaining that
remarks:          we're ignoring your prefixes.  This particularly
remarks:          applies to IPv6 prefixes.
remarks:        - community 43760:43760 is really just a NOP
remarks:
remarks:        -------------------------------------------------------
org:            ORG-INEA1-RIPE
admin-c:        INO7-RIPE
tech-c:         INO7-RIPE
mnt-by:         RIPE-NCC-END-MNT
mnt-by:         INEX-NOC
mnt-routes:     INEX-NOC

{foreach $rsclients.clients as $asn => $cdetails}
    {$cust = $customers[$cdetails.id]}
    {foreach $cdetails.vlans as $vlanid => $vli}
    	{foreach $vli as $vliid => $interface}
		    {foreach $protocols as $proto}
	            {if not isset( $interface.$proto ) }
	                {continue}
	            {/if}
	            {foreach $rsclients.vlans.$vlanid.servers.$proto as $serverip}
	                {if $proto eq 4}

import:         from AS{$cust->getAutsys()} {$interface.$proto} at {$serverip}
                accept {$cust->resolveAsMacro( $proto, 'AS' )}  # {$cust->getName()}
export:         to AS{$cust->getAutsys()} {$interface.$proto} at {$serverip}
                announce AS-SET-INEX-RS
                {else}

mp-import:      afi ipv6.unicast
                from AS{$cust->getAutsys()} {$interface.$proto} at {$serverip}
                accept {$cust->resolveAsMacro( $proto, 'AS' )}  # {$cust->getName()}
mp-export:      afi ipv6.unicast
                to AS{$cust->getAutsys()} {$interface.$proto} at {$serverip}
                announce AS-SET-INEX-RS
	                {/if}
	            {/foreach}
	        {/foreach}
        {/foreach}
    {/foreach}
{/foreach}

status:         ASSIGNED
source:         RIPE
