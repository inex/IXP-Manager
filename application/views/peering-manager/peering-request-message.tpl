
Dear {$peer.name} Peering Team,

We are {$customer.name} ({$customer.corpwww}) and we are fellow members of INEX, Ireland's IXP.

We would like to arrange peering session(s) with you on the following interface(s):

{foreach $pp as $p}

{$p.my.vlanname|underline}

{if $p.my.ipv4enabled and $p.your.ipv4enabled}
Our IPv4 Address: {$p.my.ipv4address}
{if $p.my.ipv6enabled and $p.your.ipv6enabled}
Our IPv6 Address: {$p.my.ipv6address}
{/if}
Our AS Number:    {$customer.autsys}
{if $customer.peeringmacro}Our AS Macro:     {$customer.peeringmacro}
{/if}

Your IPv4 Address: {$p.your.ipv4address}
{if $p.my.ipv6enabled and $p.your.ipv6enabled}
Your IPv6 Address: {$p.your.ipv6address}
{/if}
Your AS Number:    {$peer.autsys}
{/if}

{/foreach}


{"NOC Details for "|cat:$customer.name|underline:"="}

The following are our NOC details for your reference:

NOC Hours:     {$customer.nochours}
NOC Phone:     {$customer.nocphone}
{if $customer.noc24hphone}NOC 24h Phone: {$customer.noc24hphone}
{/if}
{if $customer.nocfax}NOC Fax:       {$customer.nocfax}
{/if}
NOC Email:     {$customer.nocemail}
{if $customer.nocwww}NOC WWW:       {$customer.nocwww}{/if}


Kind regards,
The {$customer.name} Peering Team


--
  
INEX (https://www.inex.ie/) is Ireland's IXP. This email was composed with the assistance of INEX's Peering Manager which is part of your member area at: {$config.identity.ixp.url}.


